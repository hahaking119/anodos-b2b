<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.archive.zip');
require_once JPATH_COMPONENT.'/models/helpers/updater.php';
require_once JPATH_COMPONENT.'/models/helpers/contractor.php';
require_once JPATH_COMPONENT.'/models/helpers/stock.php';
require_once JPATH_COMPONENT.'/models/helpers/vendor.php';
require_once JPATH_COMPONENT.'/models/helpers/category.php';
require_once JPATH_COMPONENT.'/models/helpers/product.php';
require_once JPATH_COMPONENT.'/models/helpers/price.php';
require_once JPATH_COMPONENT.'/models/helpers/currency.php';

class UpdaterTreolan {

	// Объявляем переменные
	public $ok = true;
	protected $updaterAlias = 'treolan-stock-and-price-updater';
	protected $contractorAlias = 'treolan';
	protected $contractorName = 'Treolan';

	protected $updater;
	protected $contractor;
	protected $stock = array();
	protected $priceType = array();
	protected $currency = array();

	protected $data = array();

	protected $msg;

	// Точка входа
	public function update() {

		// Получаем объект загрузчика
		$this->updater = Updater::getUpdaterFromAlias($this->updaterAlias);

		// Получаем объект контрагента
		$this->contractor = Contractor::getContractorFromAlias($this->contractorAlias);
		if (true != $this->contractor->id) { // Если контрагента нет
			// Добавляем контрагента
			$this->contractor = Contractor::addContractor($this->contractorName, $this->contractorAlias, 0);
			if (true != $this->contractor->id) { // Если контрагента нет
				// Выводим ошибку добавления контрагента
				$this->addMsg("Не возможно добавить контрагента: {$this->contractorName}.");
			} else { // Если контрагент есть
				$this->addMsg("Добавлен контрагент: {$this->contractorName}.");
			}
		}

		// Проверяем привязку загрузчика к контрагенту
		if ($this->updater->contractor_id != $this->contractor->id) { // Если не привязан
			Updater::linkToContractor($this->updater->id, $this->contractor->id);
		}

		// Получаем объект склада
		$stockAlias = 'treolan-russia-stock';
		$stockName = 'Российский склад Treolan';
		$this->stock[$stockAlias] = Stock::getStockFromAlias($stockAlias);
		if (true != $this->stock[$stockAlias]->id) { // Если склада нет
			// Добавляем склад
			$this->stock[$stockAlias] = Stock::addStock($stockName, $stockAlias, $this->contractor->id, 0);
			if (true != $this->stock[$stockAlias]->id) { // Если склада нет
				// Выводим ошибку добавления контрагента
				$this->addMsg("Не возможно добавить склад: {$stockName}.");
				return false;
			} else { // Если склад есть
				$this->addMsg("Добавлен склад: {$this->stock[$stockAlias]->name}.");
			}
		}

		// Получаем объект транзитного склада
		// TODO скорректировать срок поставки
		$stockAlias = 'treolan-russia-transit';
		$stockName = 'Российский транзитный склад Treolan';
		$this->stock[$stockAlias] = Stock::getStockFromAlias($stockAlias);
		if (true != $this->stock[$stockAlias]->id) { // Если склада нет
			// Добавляем склад
			$this->stock[$stockAlias] = Stock::addStock($stockName, $stockAlias, $this->contractor->id, 0);
			if (true != $this->stock[$stockAlias]->id) { // Если склада нет
				// Выводим ошибку добавления контрагента
				$this->addMsg("Не возможно добавить склад: {$stockName}.");
				return false;
			} else { // Если склад есть
				$this->addMsg("Добавлен склад: {$this->stock[$stockAlias]->name}.");
			}
		}

		// Получаем объект типа цены
		$this->priceType['rdp'] = Price::getPriceTypeFromAlias('rdp');
		if (true != $this->priceType['rdp']->id) { // Если типа цены нет
			// Добавляем тип цены
			$this->priceType['rdp'] = Price::addPriceType('Рекомендованная диллерская цена (RDP, вход)', 'rdp', 0, 0, 0);
			if (true != $this->priceType['rdp']->id) { // Если типа цены нет
				// Выводим ошибку добавления типа цены
				$this->addMsg("Не возможно добавить тип цены: Рекомендованная диллерская цена (RDP, вход).");
				return false;
			} else { // Если тип цены есть
				$this->addMsg("Добавлен тип цены: {$this->priceType['rdp']->name}.");
			}
		}

		// Получаем id валюты USD
		if (true != $this->currency['USD'] = Currency::getCurrencyFromAlias('USD')) {
			$this->addMsg('Отсутствует валюта: USD.');
			$this->addMsg('Дальнейшее выполнение обработчика невозможно.');
			return false;
		}

		// Получаем id валюты RUB
		if (true != $this->currency['RUB'] = Currency::getCurrencyFromAlias('RUB')) {
			$this->addMsg('Отсутствует валюта: RUB.');
			$this->addMsg('Дальнейшее выполнение обработчика невозможно.');
			return false;
		}

		// Получаем имя папки для загрузки
		if (true != $dir = $this->getDir()) {
			return false;
		}

		// Загружаем прайс во временную папку
		if (true != $file = $this->getFile($dir)) { return false; }

		// Загружаем данные в массив
		if (true != $this->getData($file)) { return false; }

		// Идентифицируем колонки
		if (true != $numbers = $this->getNumbers($this->data[0])) { return false; }

		// Помечаем неактуальными устаревшие данные в базе
		Price::clearSQL($this->contractor->id);
		Stock::clearSQL($this->stock['treolan-russia-stock']->id);
		Stock::clearSQL($this->stock['treolan-russia-transit']->id);

		// Загружаем данные в базу
		if (true != $this->toSQL()) { return false; }

		// Отмечаем время обновления
		Updater::setUpdated($this->updater->id);

		// Выводим сообщение о завершении обработки
		$this->addMsg("{$this->updater->name} завершено.");

	}


	// Создает папку для загрузки, возвращает ее имя
	protected function getDir() {
		$dir = JFactory::getApplication()->getCfg('tmp_path').'/'.$this->updaterAlias.'/';
		if (true != JFolder::exists($dir)) {
			$this->addMsg('true != JFolder::exists($dir)');
			JFolder::create($dir);
		} else {
			// TODO удалить все содержимое
		}
		return $dir;
	}

	// Загружает прайс во временную папку и возвращает ее имя
	protected function getFile($dir) {

		// Инициализируем переменные
		$file = $dir.'Data.xml';
		$this->addMsg("\$file = {$file}");

		// Инициализируем cURL и логинимся
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, 'https://b2b.treolan.ru/processlogin.asp');

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

			// Указываем, что будет POST запрос
			curl_setopt($ch, CURLOPT_POST, true);

			//	Передаем значения переменных
			curl_setopt($ch, CURLOPT_POSTFIELDS, "client={$this->updater->login}&pass={$this->updater->pass}&remember=on&x=7&y=5");

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
			curl_setopt($ch, CURLOPT_URL, 'https://b2b.treolan.ru/catalog.excel.asp?category=04030AB1-678B-457D-8976-AC7297C65CE6&vendor=0&ncfltr=1&daysback=0&reporttype=stock&price_min=&price_max=&hdn_extParams=&tvh=0&srh=&sart=on');
			

			// Пробуем получить вывод в файл
			$fp = fopen($file, "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);

			// Отключаем проверку сертификатов
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			// Выключаем вывод заголовков
			curl_setopt($ch, CURLOPT_HEADER, false);

			// Включаем куки
			curl_setopt($ch, CURLOPT_COOKIEFILE, $dir.'Cookie.txt');
			curl_setopt($ch, CURLOPT_COOKIEJAR, $dir.'Cookie.txt');

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
		return $file;
	}

	// Возвращает массив данных
	protected function getData($file) {

		// Загружаем данные из файла в DOM
		$dom = new DomDocument();
		$dom->loadHtmlFile($file);
		$xpath = new DOMXPath($dom);

		// Парсим и загружаем в массив
		$row = 0;
		foreach ($xpath->query('.//tr') as $tr) {
			$col = 0;
			foreach ($tr->childNodes as $td) {
				$this->data[$row][$col] = $td->nodeValue;
				$this->addMsg("\$this->data[{$row}][{$col}] = {$td->nodeValue};");
				$col++;
			}
			$row++;
		}
		// JFile::delete($file);
		return true;
	}

	// Идентифицирует колонки
	protected function getNumbers($tr) {
	
		for ($col=0; $col<sizeof($tr); $col++) {
			switch ($tr[$col]) {
				case 'Артикул'       : $numbers['article']       = $col; break;
				case 'Производитель' : $numbers['vendor']        = $col; break;
				case 'Наименование'  : $numbers['name']          = $col; break;
				case 'Цена*'         : $numbers['price_USD']     = $col; break;
				case 'Цена руб.**'   : $numbers['price_RUB']     = $col; break;
				case 'Св.'           : $numbers['stock_russia']  = $col; break;
				case 'Св.+Тр.'       : $numbers['stock_transit'] = $col; break;
				case 'Доп.'          : $numbers['comment']       = $col; break;
				case 'Гар.'          : $numbers['warranty']      = $col; break;
			}
		}
		if ((0 === $numbers['article'])
		and (true == $numbers['vendor'])
		and (true == $numbers['name'])
		and (true == $numbers['price_USD'])
		and (true == $numbers['price_RUB'])
		and (true == $numbers['stock_russia'])
		and (true == $numbers['stock_transit'])
//		and (true == $numbers['comment'])
		and (true == $numbers['warranty'])) { // Все колонки на месте
			return $numbers;
		} else { // Не хватает обязательных колонок
			$this->addMsg('Формат прайс-листа поменялся - необходима доработка обработчика.');
			return false;
		}
	}

	// Заносит информацию в базу
	protected function toSQL() {

		// Цикл по всем строкам (кроме первой!!)
		for ($row=1; $row<sizeof($this->data); $row++) {

			$product = $this->data[$row];


			if (1 == sizeof($product)) { // Строка с категорией

				// Получаем id категории
				$synonym = Category::getSynonym($product[0], $this->contractor->id);
				if (true == $synonym->category_id) { // Синоним есть и привязан к категории
					$categoryId = $synonym->category_id;
				} else {
					$categoryId = 0;
				}

				// Проверяем есть ли синоним категории
				if (true != $synonym->id) { // Нет синонима в базе
					$synonym = Category::addSynonym($product[0], $this->contractor->id);
					if (true != $synonym->id) { // Нет синонима в базе
						$this->addMsg("Не удалось добавить синоним категории: {$product[0]}.");
					} else {
						$this->addMsg("Синоним категории добавлен: {$synonym->name}.");
					}
					continue;
				}

			} elseif (true == $product[$this->number['article']] and $product[$this->number['vendor']]) { // Строка с товаром

				// Получаем id производителя
				$synonym = Vendor::getSynonym($product[$this->number['vendor']]);
				if (true == $synonym->vendor_id) { // Есть id производителя
					$vendorId = $synonym->vendor_id;
				} else { // Нет id производителя
					$vendorId = 0;
				}

				// Проверяем есть ли синоним производителя
				if (true != $synonym->id) { // Нет синонима в базе
					$synonym = Vendor::addSynonym($product[$this->number['vendor']]);
					if (true != $synonym->id) { // Нет синонима в базе
						$this->addMsg("Не удалось добавить синоним производителя: {$product[$this->number['vendor']]}.");
					} else {
						$this->addMsg("Синоним производителя добавлен: {$synonym->name}.");
					}
				}

				// Все ли есть для идентификации продукта?
				if ((true != $vendorId)
				or (true != $categoryId)
				or (true != $product['PartNo'])) {
					continue;
				}

				// Есть ли продукт в базе?
				$productId = Product::getProductFromArticle($product['PartNo'], $vendorId)->id;
				if (true != $productId) { // Нет продукта в базе
					$productId = Product::addProduct($product['Name'], $product['Name'], $categoryId, $vendorId, $product['PartNo'], 1)->id;
					if (true != $productId) { // Нет продукта в базе
						$this->addMsg("Не удалось добавить продукт: {$product['Name']} [{$product['PartNo']}].");
						continue;
					} else {
						$this->addMsg("Продукт добавлен: {$product['Name']} [{$product['PartNo']}].");
					}
				}

				// Добавляем цену и количество (только если есть в наличии)
				$priceUSD = $this->getFixedPrice($product[$this->number['price_USD']]);
				$priceRUB = $this->getFixedPrice($product[$this->number['price_RUB']]);
				$quantityStock = $this->getFixedQuantity($product[$this->number['stock_russia']]);
				$quantityTransit = $this->getFixedQuantity($product[$this->number['stock_transit']]);

				// Есть ли наличие хотя бы на одном из складов?
				if ((true == $quantityStock) or (true == $quantityStock)) { // Наличие есть

					// Заносим информацию о наличие в базу
					Stock::addQuantity($this->stock['treolan-russia-stock']->id, $productId, $quantityStock, 3, 0);
					Stock::addQuantity($this->stock['treolan-russia-transit']->id, $productId, $quantityTransit, 3, 0);

					// Есть ли долларовая цена?
					if (true == $priceUSD) {
						Price::addPrice($this->contractor->id, $productId, $priceUSD, $this->currency['USD']->id, $this->priceType['rdp']->id, 3, 0);
					}

					// Есть ли рублевая цена?
					if (true == $priceRUB) {
						Price::addPrice($this->contractor->id, $productId, $priceRUB, $this->currency['RUB']->id, $this->priceType['rdp']->id, 3, 0);
					}

				} else { // Наличия нет
					continue;
				}
			}
		}
		return true;
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
				case '0' : return 0;
				case 'склад' : return 1;
				case 'резерв' : return 0;
				case 'транзит' : return 1;
				case 'много' : return 10;
				case 'мало' : return 5;
				default : $this->addMsg("Необходим новый кейс обработки количества: '$string'"); return 0;
			}
		}
	}

	// Возвращает строку сообщений
	public function getMsg() {
		return $this->msg;
	}

	// Добавляет сообщение
	private function addMsg($msg) {
		$this->msg .= "{$msg}<br/>\n";
	}
}
