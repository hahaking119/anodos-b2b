<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.archive.zip');
require_once JPATH_COMPONENT.'/models/helpers/updater.php';
require_once JPATH_COMPONENT.'/models/helpers/partner.php';
require_once JPATH_COMPONENT.'/models/helpers/stock.php';
require_once JPATH_COMPONENT.'/models/helpers/vendor.php';
require_once JPATH_COMPONENT.'/models/helpers/category.php';
require_once JPATH_COMPONENT.'/models/helpers/product.php';
require_once JPATH_COMPONENT.'/models/helpers/price.php';
require_once JPATH_COMPONENT.'/models/helpers/currency.php';

class UpdaterMerlionMsk {

	// Объявляем переменные
	protected $partnerAlias = 'merlion';
	protected $partnerName = 'Merlion';
	protected $updater;
	protected $startTime;
	protected $partner;
	protected $stock = array();
	protected $priceType = array();
	protected $currency = array();
	protected $msg;

	public function getMsg() {
		return $this->msg;
	}

	protected function addMsg($msg) {
		$this->msg .= $msg."\n";
	}

	// Точка входа
	public function update($id) {

		// Получаем объект загрузчика и время из базы
		$this->updater = Updater::getUpdater($id);
		$this->startTime = Updater::getStartTime();

		// Получаем объект партнера
		$this->partner = Partner::getPartnerFromAlias($this->partnerAlias);
		if (!isset($this->partner->id)) {
			$this->partner = Partner::addPartner($this->partnerName, $this->partnerAlias, 0);
			if (!isset($this->partner->id)) {
				$this->addMsg('Error #'.__LINE__." - Не возможно добавить партнера.");
				return false;
			} else {
				$this->addMsg("Добавлен партнер: {$this->partnerName}.");
			}
		}

		// Проверяем привязку загрузчика к контрагенту
		if ($this->updater->partner_id != $this->partner->id) {
			Updater::linkToPartner($this->updater->id, $this->partner->id);
		}

		// Получаем объект склада
		$alias = 'merlion-msk-stock';
		$name = 'Московский склад Merlion';
		$this->stock[$alias] = Stock::getStockFromAlias($alias);
		if (!isset($this->stock[$alias]->id)) {
			$this->stock[$alias] = Stock::addStock($name, $alias, $this->partner->id, 0);
			if (!isset($this->stock[$alias]->id)) {
				$this->addMsg('Error #'.__LINE__." - Не возможно добавить склад: {$name}.");
				return false;
			} else {
				$this->addMsg("Добавлен склад: {$this->stock[$alias]->name}.");
			}
		}

		// Получаем объект типа цены
		$alias = 'rdp';
		$name = 'Рекомендованная диллерская цена (RDP, вход)';
		$this->priceType[$alias] = Price::getPriceTypeFromAlias('rdp');
		if (!isset($this->priceType[$alias]->id)) {
			$this->priceType[$alias] = Price::addPriceType($name, $alias, 1, 0, 0);
			if (!isset($this->priceType[$alias]->id)) {
				$this->addMsg('Error #'.__LINE__." - Не возможно добавить тип цены: {$name}.");
				return false;
			} else {
				$this->addMsg("Добавлен тип цены: {$this->priceType[$alias]->name}.");
			}
		}

		// Получаем id валюты USD
		$alias = 'USD';
		$this->currency[$alias] = Currency::getCurrencyFromAlias($alias);
		if (!isset($this->currency[$alias])) {
			$this->addMsg('Error #'.__LINE__." - Нет валюты: {$alias}.");
			return false;
		}

		// Получаем имя папки для загрузки
		$dir = $this->getDir();
		if (!$dir) {
			$this->addMsg('Error #'.__LINE__.' - Не задана папка загрузки.');
			return false;
		}

		// Загружаем прайс во временную папку
		if (!$this->loadToDir($dir)) {
			$this->addMsg('Error #'.__LINE__.' - Ошибка загрузки прайса в локальную папку.');
			return false;
		}

		// Находим загруженный прайс
		$file = $this->getFile($dir);
		if (!$file) {
			$this->addMsg('Error #'.__LINE__.' - Не найден распакованный прайс в локальной папке.');
			return false;
		}

		// Загружаем данные в массив
		if (!$this->getData($file)) {
			$this->addMsg('Error #'.__LINE__.' - Пожалуй, массив данных не удалось загрузить');
			return false;
		}

		// Помечаем неактуальными устаревшие данные в базе
		Price::clearSQL($this->stock['merlion-msk-stock']->id, $this->startTime);
		Stock::clearSQL($this->stock['merlion-msk-stock']->id, $this->startTime);

		// Отмечаем время обновления
		Updater::setUpdated($this->updater->id);

		// Выполняем постобработку данных
		$this->postUpdate();

		// Выводим сообщение о завершении обработки
		$this->addMsg('<div class="uk-alert uk-alert-success">Обработка завершена.</div>');
		return true;
	}

	// Создает папку для загрузки, возвращает ее имя
	protected function getDir() {
		$dir = JFactory::getApplication()->getCfg('tmp_path').'/'.$this->updater->alias.'/';
		if (!JFolder::exists($dir)) {
			JFolder::create($dir);
		} else {
			// TODO удалить все содержимое
		}
		return $dir;
	}

	// Загружает данные с сайта контрагента
	protected function loadToDir($dir) {

		// Инициализируем cURL и логинимся
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, 'https://iz.merlion.ru/');

			// Пробуем получить вывод в файл
			$fp = fopen($dir.'Page.html', "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);

			// Отключаем проверку сертификатов
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			// При значении true CURL включает в вывод заголовки.
			curl_setopt($ch, CURLOPT_HEADER, false);

			// Включаем куки
			curl_setopt($ch, CURLOPT_COOKIEJAR, $dir.'Cookie.txt');
			//В какой файл записывать

			// Указываем, что будет POST запрос
			curl_setopt($ch, CURLOPT_POST, true);

			//	Передаем значения переменных
			curl_setopt($ch, CURLOPT_POSTFIELDS, "client={$this->updater->client}&login={$this->updater->login}&password={$this->updater->pass}&Ok=%C2%EE%E9%F2%E8");

			// Указываем максимальное время ожидания в секундах
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);

			// Устанавливаем значение поля User-agent
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1');

			// Выполняем запрос
			curl_exec($ch);

			// Освобождаем ресурс
			curl_close($ch);
			fclose($fp);
			unset($ch);
			JFile::delete($dir.'Page.html');
		} else {
			return false;
		}

		// Инициализируем cURL и загружаем прайс
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, 'https://iz.merlion.ru/?action=Y3F86565&action1=YD56AF97&lol=f3725fa395c62ca96b1c665791c371d6&type=xml');

			// Пробуем получить вывод в файл
			$fp = fopen($dir.'Data.xml.zip', "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);

			// Отключаем проверку сертификатов
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			// Выключаем вывод заголовков
			curl_setopt($ch, CURLOPT_HEADER, false);

			// Включаем куки
			curl_setopt($ch, CURLOPT_COOKIEFILE, $dir.'Cookie.txt'); //Из какого файла читать
			curl_setopt($ch, CURLOPT_COOKIEJAR, $dir.'Cookie.txt'); //В какой файл записывать

			// Указываем максимальное время ожидания в секундах
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);

			// Устанавливаем значение поля User-agent
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1');

			// Выполняем запрос
			curl_exec($ch);

			// Освобождаем ресурс
			curl_close($ch);
			fclose($fp);
			unset($ch);
			JFile::delete($dir.'Cookie.txt');
		} else {
			return false;
		}

		// Распаковываем полученый архив
		if (JArchive::extract($dir.'Data.xml.zip',  $dir)) {
			JFile::delete($dir.'Data.xml.zip');
			return true;
		} else {
			return false;
		}
	}

	// Возвращает имя файла с данными
	protected function getFile($dir) {

		$file = $dir.'data.xml';

		// Ищем все файлы, содержащие в имени ".xml"
		$files = JFolder::files($dir, $filter='.xml', false, true, false);
		if (!isset($files[0])) {
			$this->addMsg('Error #'.__LINE__." - Не найден распакованный файл.");
		}

		if (!JFile::exists($files[0])) {
			$this->addMsg('Error #'.__LINE__." - Не найден распакованный файл.");
			return false;
		}

		// Переименовываем файл в удобоваримый вид
		JFile::move($files[0], $file);
		if (!JFile::exists($file)) {
			$this->addMsg('Error #'.__LINE__." - Ошибка приведения имени файла в читаемый вид.");
			return false;
		}

		// Возвращаем имя первого (он же и должен быть единственным)
		return $file;
	}

	// Парсит файл
	protected function getData($file) {

		// Получаемые данные

//			<pricelist>
//			<Date>26.7.2013</Date>
//			<Time>18:52</Time>
//			<Currency>32,5376</Currency>
//			<G1>
//				<MainGroup id="А1">КОМПЛЕКТУЮЩИЕ ДЛЯ КОМПЬЮТЕРОВ</MainGroup>
//				<G2>
//					<Group id="А101">Аксессуары</Group>
//					<G3>
//						<SubGroup id="А10107">Mobile Rack</SubGroup>
//						<Item>
//							<No>729828</No>
//							<Name>Внешний корпус AgeStar 3CM2A 2.5"</Name>
//							<Brand>AGESTAR</Brand>
//							<PartNo>3CM2A</PartNo>
//							<Price>18</Price>
//							<PriceR>586</PriceR>
//							<Price3>18,5</Price3>
//							<PriceR3>602</PriceR3>
//							<Price2>19</Price2>
//							<PriceR2>618</PriceR2>
//							<Price1>19,5</Price1>
//							<PriceR1>634</PriceR1>
//							<Price0>20</Price0>
//							<PriceR0>651</PriceR0>
//							<Avail>+++ </Avail>
//							<Avail_Expect>call</Avail_Expect>
//							<Avail_ExpectNext>call</Avail_ExpectNext>
//							<Date_ExpectNext/>
//							<Min_Pack/>
//							<Pack>50</Pack>
//							<Vol>0,00158</Vol>
//							<WT>0,418</WT>
//							<Warranty>12</Warranty>
//							<Status/>
//							<MAction/>
//						</Item>

		// Инициализируем переменные
		$dom = new DomDocument();
		$dom->load($file);
		$xpath = new DOMXPath($dom);

		// Цикл по G1
		foreach ($xpath->query('.//G1') as $g1) {

			// Цикл по дочкам G1
			foreach ($g1->childNodes as $g2) {
				if ('MainGroup' == $g2->nodeName) { // Это имя главной категории
					// Фиксируем имя главной категории
					$MainGroup = $g2->nodeValue;
				} elseif ('G2' == $g2->nodeName) { // Это категория

					// Цикл по дочкам G2
					foreach ($g2->childNodes as $g3) {
						if ('Group' == $g3->nodeName) { // Это имя категории
							$Group = $g3->nodeValue;
						} elseif ('G3' == $g3->nodeName) { // А это подкатегория

							// Цикл по дочкам G3
							foreach ($g3->childNodes as $item) {
								if ('SubGroup' == $item->nodeName) { // Это имя подкатегории
									$SubGroup = $item->nodeValue;
								} elseif ('Item' == $item->nodeName) { // Продукт

									// Обнуляем свойства продукта
									$product = array();
									$product['categorySynonym'] = "{$MainGroup} | {$Group} | {$SubGroup}";

									// Пробегаем по свойствам
									foreach ($item->childNodes as $property) {
										$product[$property->nodeName] = $property->nodeValue;
									}

									// Заносим данные в массив
									$this->toSQL($product);
								}
							}
						}
					}
				}
			}
		}
 		JFile::delete($file);
		return true;
	}

	// Заносит информацию в базу
	protected function toSQL($product) {

		// Обнуляем переменные
		$productId = 0;
		$categoryId = 0;
		$vendorId = 0;
		$price = 0;

		// Получаем id производителя, если указан в базе
		$synonym = Vendor::getSynonym($product['Brand'], $this->partner->id);
		if (isset($synonym->vendor_id)) {
			$vendorId = $synonym->vendor_id;
		}

		// Добавляем синоним производителя, если его нет в базе
		if (!isset($synonym->id)) {
			$synonym = Vendor::addSynonym($product['Brand'], $this->partner->id);
			if (!isset($synonym->id)) {
				$this->addMsg("Не удалось добавить синоним производителя: {$product['Brand']}");
			} else {
				$this->addMsg("Добавлен синоним производителя: {$product['Brand']}");
			}
		}

		// Получаем id категории, если указан в базе
		$synonym = Category::getSynonym($product['categorySynonym'], $this->partner->id);
		if (isset($synonym->category_id)) {
			$categoryId = $synonym->category_id;
		}

		// Добавляем синоним категории, если его нет в базе
		if (!isset($synonym->id)) {
			$synonym = Category::addSynonym($product['categorySynonym'], $this->partner->id);
			if (!isset($synonym->id)) { // Нет синонима в базе
				$this->addMsg("Не удалось добавить синоним категории: {$product['categorySynonym']}");
			} else {
				$this->addMsg("Добавлен синоним категории : {$synonym->name}");
			}
		}

		// Проверяем, все ли есть для добавления продукта
		if ((true != $vendorId)
		or (true != $categoryId)
		or (true != $product['PartNo'])
		or (true != $product['Name'])) {
			return false;
		}

		// Получаем id продукта
		$productFromDB = Product::getProductFromArticle($product['PartNo'], $vendorId);
		if (isset($productFromDB->id)) {
			$productId = $productFromDB->id;
		} else {
			$productFromDB = Product::addProduct($product['Name'], $product['Name'], $categoryId, $vendorId, $product['PartNo']);
			if (isset($productFromDB->id)) {
				$this->addMsg("Добавлен продукт: [{$product['PartNo']}] {$product['Name']}.");
				$productId = $productFromDB->id;
			} else {
				$this->addMsg("Не удалось добавить продукт: [{$product['PartNo']}] {$product['Name']}.");
				return false;
			}
		}

		// Добавляем цену и количество
		$price = $this->getCorrectedPrice($product['Price']);
		$quantity = $this->getCorrectedQuantity($product['Avail']);

		if (0 < $quantity) {
			Stock::addQuantity(
				$this->stock['merlion-msk-stock']->id,
				$productId,
				$quantity,
				3,
				0
			);
		} else {
			return true;
		}

		if (0 < $price) {
			Price::addPrice(
				$this->stock['merlion-msk-stock']->id,
				$productId,
				$price,
				$this->currency['USD']->id,
				$this->priceType['rdp']->id,
				3,
				0
			);
			return true;
		} else {
			return false;
		}
	}

	// Правим цену (удаляем вредные символы)
	protected function getCorrectedPrice($price) {
		$price = ereg_replace('[,]', '.', $price);
		$price = ereg_replace('[ ]', '', $price);
		return doubleval($price);
	}

	// Правим количество (удаляем вредные символы)
	protected function getCorrectedQuantity($string) {
		$int = ereg_replace('[^0-9]*', '', $string); // убираем из строки все, что не цифра
		if (true == $int) {
			return $int;
		} else {
			$string = utf8_strtolower($string);
			switch ($string) {
				case '0' : return 0; // В противном случае нули выбывают из обработки
				case 'call' : return 1;
				case '+ ' : return 5;
				case '++ ' : return 10;
				case '+++ ' : return 20;
				default : $this->addMsg ("Необходим новый кейс обработки количества: '{$string}'"); return 0;
			}
		}
	}

	protected function postUpdate() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "UPDATE #__anodos_category_synonym SET state = 0 WHERE partner_id = {$this->partner->id} AND LOCATE('ПЛОХАЯ УПАКОВКА', name) = 1;";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос
		$query = "UPDATE #__anodos_category_synonym SET state = 0 WHERE partner_id = {$this->partner->id} AND LOCATE('ТОВАР Б/У', name) = 1;";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос
		$query = "UPDATE #__anodos_category_synonym SET state = 0 WHERE partner_id = {$this->partner->id} AND LOCATE('ПОЗИТРОНИКА', name) = 1;";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}

}
