<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.archive.zip');
require_once JPATH_COMPONENT.'/models/helpers/partner.php';
require_once JPATH_COMPONENT.'/models/helpers/stock.php';
require_once JPATH_COMPONENT.'/models/helpers/vendor.php';
require_once JPATH_COMPONENT.'/models/helpers/category.php';
require_once JPATH_COMPONENT.'/models/helpers/product.php';
require_once JPATH_COMPONENT.'/models/helpers/price.php';
require_once JPATH_COMPONENT.'/models/helpers/currency.php';

class UpdaterMerlion {

	// Объявляем переменные
	protected $partnerAlias = 'merlion';
	protected $partnerName = 'Merlion';
	
	protected $updater;
	protected $partner;

	protected $stock = array();
	protected $priceType = array();
	protected $currency = array();
	protected $dir;
	protected $file;
	protected $data = array();

	protected $msg;

	public function getMsg() {
		return $this->msg;
	}

	protected function addMsg($msg) {
		$this->msg .= $msg."\n";
	}

	protected function getUpdater($id) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM `#__anodos_updater`
			WHERE `id` = '{$id}';";
		$db->setQuery($query);
		$updater = $db->loadObject();

		// Возвращаем результат
		return $updater;
	}

	// Привязывает загрузчика к контрагенту
	protected function linkToPartner ($updaterId, $partnerId) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE `#__anodos_updater`
			SET `partner_id` = {$partnerId}
			WHERE `id` = '{$partnerId}';";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}

	// Устанавливает время обновления
	protected function setUpdated ($id) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE `#__anodos_updater`
			SET `updated` = NOW()
			WHERE `id` = '{$id}';";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}

	// Точка входа
	public function update($id) {

		// Получаем объект загрузчика
		$this->updater = $this->getUpdater($id);

		// Получаем объект партнера
		$this->partner = Partner::getPartnerFromAlias($this->partnerAlias);
		if (!isset($this->partner->id)) {
			$this->partner = Partner::addPartner($this->partnerName, $this->partnerAlias, 0);
			if (!isset($this->partner->id)) {
				$this->addMsg("Не возможно добавить партнера.");
				return false;
			} else {
				$this->addMsg("Добавлен партнер: {$this->partnerName}.");
			}
		}

		// Получаем объект склада
		$this->stock['merlion-russia-stock'] = Stock::getStockFromAlias('merlion-russia-stock');
		if (!isset($this->stock['merlion-russia-stock']->id)) {
			$this->stock['merlion-russia-stock'] = Stock::addStock('Российский склад Merlion', 'merlion-russia-stock', $this->partner->id, 0);
			if (!isset($this->stock['merlion-russia-stock']->id)) {
				$this->addMsg("Не возможно добавить склад: Российский склад Merlion.");
				return false;
			} else {
				$this->addMsg("Добавлен склад: {$this->stock['merlion-russia-stock']->name}.");
			}
		}

		// Получаем объект типа цены
		$this->priceType['rdp'] = Price::getPriceTypeFromAlias('rdp');
		if (!isset($this->priceType['rdp']->id)) {
			$this->priceType['rdp'] = Price::addPriceType('Рекомендованная диллерская цена (RDP, вход)', 'rdp', 0, 0, 0);
			if (!isset($this->priceType['rdp']->id)) {
				$this->addMsg("Не возможно добавить тип цены: Рекомендованная диллерская цена (RDP, вход).");
				return false;
			} else {
				$this->addMsg("Добавлен тип цены: {$this->priceType['rdp']->name}.");
			}
		}

		// Получаем id валюты USD
		$this->currency['USD'] = Currency::getCurrencyFromAlias('USD');
		if (!isset($this->currency['USD'])) {
			$this->addMsg('Нет валюты: USD.');
			return false;
		}

		// Получаем имя папки для загрузки
		if (true != $this->dir = $this->getDir()) {
			$this->addMsg('Не задана папка загрузки.');
			return false;
		}

		// Загружаем прайс во временную папку
		if (true != $this->loadToDir($this->dir)) {
			$this->addMsg('true != $this->loadToDir($this->dir)');
			return false;
		}

		// Находим загруженный прайс
		if (true != $this->file = $this->getFile($this->dir)) {
			$this->addMsg('true != $this->file = $this->getFile($this->dir)');
			return false;
		}

		// Загружаем данные в массив
		if (true != $this->getData($this->file)) {
			$this->addMsg('true != $this->getData($this->file)');
			return false;
		}

//		$this->addMsg('stop'); return false;

		// Помечаем неактуальными устаревшие данные в базе
		Price::clearSQL($this->contractor->id);
		Stock::clearSQL($this->stock['merlion-russia-stock']->id);

		// Загружаем данные в базу
		if (true != $this->toSQL()) { return false; }

		// Отмечаем время обновления
		$this->setUpdated($this->updater->id);

		// Выводим сообщение о завершении обработки
		$this->addMsg("The End.");
		return true;
	}

	// Создает папку для загрузки, возвращает ее имя
	protected function getDir() {
		$dir = JFactory::getApplication()->getCfg('tmp_path').'/'.$this->updaterAlias.'/';
		if (true != JFolder::exists($dir)) {
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

		// Ищем все файлы, содержащие в имени ".xml"
		$files = JFolder::files($dir, $filter='.xml', false, true, false);

		// Возвращаем имя первого (он же и должен быть единственным)
		return $files[0];
	}

	// Возвращает массив данных
	protected function getData($file) {

		// Получаемые данные
		//<No>538029</No>
		//<Name>Чехол для iPod Nano Vibes iSkin VBSN4G-CK</Name>
		//<Brand>VIBES</Brand>
		//<PartNo>VBSN4G-CK</PartNo>
		//<Price>19,5</Price>
		//<Avail>call</Avail>
		//<Avail_Expect>call</Avail_Expect>
		//<Avail_ExpectNext>call</Avail_ExpectNext>
		//<Date_ExpectNext/>
		//<Min_Pack/>
		//<Pack>1</Pack>
		//<Vol>0,001</Vol>
		//<WT>0,001</WT>
		//<Warranty/>
		//<Status/>
		//<MAction/>

		// Инициализируем переменные
		$dom = new DomDocument();
		$dom->load($file);
		$xpath = new DOMXPath($dom);

		// Цикл по G1
		$g1Number = 0;
		foreach ($xpath->query('.//G1') as $g1) {
			$g2Number = 0;

			// Цикл по дочкам G1
			foreach ($g1->childNodes as $g2) {
				if ('MainGroup' == $g2->nodeName) { // Это имя главной категории
					// Фиксируем имя главной категории
					$MainGroup = $g2->nodeValue;
				} elseif ('G2' == $g2->nodeName) { // Это категория
					$g3Number = 0;

					// Цикл по дочкам G2
					foreach ($g2->childNodes as $g3) {
						if ('Group' == $g3->nodeName) { // Это имя категории
							$Group = $g3->nodeValue;
						} elseif ('G3' == $g3->nodeName) { // А это подкатегория
							$itemNumber = 0;

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
									$this->data[] = $product;
									$itemNumber++;
								}
							}
							$g3Number++;
						}
					}
					$g2Number++;
				}
			}
			$g1Number++;
		}
		JFile::delete($file);
		return true;
	}

	// Заносит информацию в базу
	protected function toSQL() {

		// Пробегаем по каждому продукту
		foreach ($this->data as $product) {

			// Обнуляем переменные
			$productId = 0;
			$categoryId = 0;
			$price = 0;


			// Получаем id производителя
			$synonym = Vendor::getSynonym($product['Brand']);
			if (true == $synonym->vendor_id) { // Есть id производителя
				$vendorId = $synonym->vendor_id;
			} else { // Нет id производителя
				$vendorId = 0;
			}

			// Проверяем есть ли синоним производителя
			if (true != $synonym->id) { // Нет синонима в базе
				$synonym = Vendor::addSynonym($product['Brand']);
				if (true != $synonym->id) { // Нет синонима в базе
					$this->addMsg("Не удалось добавить синоним производителя: {$product['Brand']}");
				} else {
					$this->addMsg("Синоним производителя добавлен: {$product['Brand']}");
				}
			}

			// Получаем id категории
			$synonym = Category::getSynonym($product['categorySynonym'], $this->contractor->id);
			if (true == $synonym->category_id) { // Синоним есть и привязан к категории
				$categoryId = $synonym->category_id;
			} else {
				$categoryId = 0;
			}

			// Проверяем есть ли синоним категории
			if (true != $synonym->id) { // Нет синонима в базе
				$synonym = Category::addSynonym($product['categorySynonym'], $this->contractor->id);
				if (true != $synonym->id) { // Нет синонима в базе
					$this->addMsg("Не удалось добавить синоним категории: {$product['categorySynonym']}");
				} else {
					$this->addMsg("Синоним категории добавлен: {$synonym->name}");
				}
			}

			// Проверяем, все ли есть для добавления продукта
			if ((true != $vendorId)
			or (true != $categoryId)
			or (true != $product['PartNo'])) {
				continue;
			}

			// Проверяем есть ли продукт
			$productId = Product::getProductFromArticle($product['PartNo'], $vendorId)->id;
			if (true != $productId) { // Нет продукта в базе
				$productId = Product::addProduct($product['Name'], $product['Name'], $categoryId, $vendorId, $product['PartNo'], 1)->id;
				if (true != $productId) { // Нет синонима в базе
					$this->addMsg("Не удалось добавить продукт: {$product['Name']} [{$product['PartNo']}].");
					continue;
				} else {
					$this->addMsg("Продукт добавлен: {$product['Name']} [{$product['PartNo']}].");
				}
			}

			// Добавляем цену и количество (только если есть в наличии)
			$price = $this->getFixedPrice($product['Price']);
			$quantity = $this->getFixedQuantity($product['Avail']);
			if ((true == $price) and (true == $quantity)) {
				Price::addPrice($this->contractor->id, $productId, $price, $this->currency['USD']->id, $this->priceType['rdp']->id, 3, 0);
				Stock::addQuantity($this->stock['merlion-russia-stock']->id, $productId, $quantity, 3, 0);
			} else {
				continue;
			}
		}
	}

	// Правим цену (удаляем вредные символы)
	protected function getFixedPrice($price) {
		$price = ereg_replace('[,]', '.', $price);
		$price = ereg_replace('[ ]', '', $price);
		return doubleval($price);
	}

	// Правим количество (удаляем вредные символы)
	protected function getFixedQuantity($string) {
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
}
