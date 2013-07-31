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

class UpdaterTreolan {

	// Объявляем переменные
	protected $partnerAlias = 'treolan';
	protected $partnerName = 'Treolan';
	protected $updater;
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

		// Получаем объект загрузчика
		$this->updater = Updater::getUpdater($id);

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
		$alias = 'treolan-msk-stock';
		$name = 'Московский склад Treolan';
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

		// Получаем объект транзитного склада
		// TODO скорректировать срок поставки
		$alias = 'treolan-transit-stock';
		$name = 'Транзитный склад Treolan';
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

		// Получаем id валюты RUB
		$alias = 'RUB';
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

		// Загружаем файл
		$file = $this->getFile($dir);
		if (!$file) {
			$this->addMsg('Error #'.__LINE__.' - Не найден загруженный файл.');
			return false;
		}

		// Помечаем неактуальными устаревшие данные в базе
		Price::clearSQL($this->partner->id);
		Stock::clearSQL($this->stock['treolan-msk-stock']->id);
		Stock::clearSQL($this->stock['treolan-transit-stock']->id);

		// Загружаем данные в массив
		if (true != $this->getData($file)) { return false; }

		// Отмечаем время обновления
		Updater::setUpdated($this->updater->id);

		// Выводим сообщение о завершении обработки
		$this->addMsg("Обработка завершена.");
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

	// Загружает прайс во временную папку и возвращает ее имя
	protected function getFile($dir) {

		// Инициализируем переменные
		$file = $dir.'data.xml';

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
			curl_setopt($ch, CURLOPT_URL, 'https://b2b.treolan.ru/catalog.excel.asp?category=04030AB1-678B-457D-8976-AC7297C65CE6&vendor=0&ncfltr=1&daysback=0&reporttype=stock&price_min=&price_max=&hdn_extParams=&tvh=0&srh=&sart=on&podbor=1');

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

		// Получаемые данные

//		<html
//			xmlns:x="urn:schemas-microsoft-com:office:excel"
//			xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
//			xmlns="http://www.w3.org/TR/REC-html40"
//			xmlns:ms="urn:schemas-microsoft-com:xslt">
//			<head>
//				<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
//				<style type="text/css"></style>
//			</head>
//			<body>
//				<h1>Áàçîâûé ïðàéñ-ëèñò</h1>
//				<p>Èíäèâèäóàëüíûå öåíû ñìîòðèòå íà <a href="https://b2b.treolan.ru/catalog.asp">B2B</a></p>
//				<table>
//					<tr class="sHead">
//						<td nowrap x:autofilter="all" xmlns="">Àðòèêóë</td>
//						<td nowrap x:autofilter="all" xmlns="">Íàèìåíîâàíèå</td>
//						<td nowrap x:autofilter="all" xmlns="">Ïðîèçâîäèòåëü</td>
//						<td nowrap x:autofilter="all" xmlns="">Ñâ.</td>
//						<td nowrap x:autofilter="all" xmlns="">Ñâ.+Òð.</td>
//						<td nowrap x:autofilter="all" xmlns="">Á. Òð.</td>
//						<td nowrap x:autofilter="all" xmlns="">Öåíà*</td>
//						<td nowrap x:autofilter="all" xmlns="">Öåíà ðóá.**</td>
//						<td nowrap x:autofilter="all" xmlns="">Äîï.</td>
//						<td nowrap x:autofilter="all" xmlns="">Ãàð.</td>
//					</tr>
//					<tr class="sGroup">
//						<td colspan="12">Êàòåãîðèÿ - 01. Ñåðâåðû -&gt;  1-ïðîöåññîðíûå ñåðâåðû -&gt; ASUS RS100</td>
//					</tr>
//					<tr class="sRow">
//						<td class="s1" xmlns=""><ss:Data ss:Type="String">RS100-X7</ss:Data></td>
//						<td width="400" xmlns="">Ñåðâåðíàÿ ïëàòôîðìà ASUS RS100-X7/WOCPU/WOMEM/WOHDD//CEE/WOD/EN</td>
//						<td xmlns="">ASUS</td><td xmlns="">1</td>
//						<td class="p" xmlns="">ìíîãî</td>
//						<td class="d" xmlns="">19.08.13 (ÎÏ)</td>
//						<td class="num" xmlns="">460,00</td>
//						<td class="num" xmlns=""></td>
//						<td xmlns=""><nobr> </nobr></td>
//						<td xmlns=""><ss:Data ss:Type="String">3 ãîäà</ss:Data></td>
//					</tr>
//					<tr class="sGroup">
//						<td colspan="12">Êàòåãîðèÿ - 01. Ñåðâåðû -&gt;  1-ïðîöåññîðíûå ñåðâåðû -&gt; ASUS RS300</td>
//					</tr>
//					<tr class="sRow">
//						<td class="s1" xmlns=""><ss:Data ss:Type="String">RS300-E7/RS4</ss:Data></td>
//						<td width="400" xmlns="">Ñåðâåðíàÿ ïëàòôîðìà ASUS RS300-E7-RS4/WOCPU/WOMEM/WOHDD//2CEE/DVR/ENG</td>
//						<td xmlns="">ASUS</td>
//						<td xmlns="">ìíîãî</td>
//						<td class="p" xmlns="">ìíîãî</td>
//						<td class="d" xmlns=""> </td>
//						<td class="num" xmlns="">1 080,00</td>
//						<td class="num" xmlns=""></td>
//						<td xmlns=""><nobr> </nobr></td>
//						<td xmlns=""><ss:Data ss:Type="String">3 ãîäà</ss:Data></td>
//					</tr>

		// Загружаем данные из файла в DOM
		$dom = new DomDocument();
		$dom->loadHtmlFile($file);
		$xpath = new DOMXPath($dom);

		// TODO Парсим и загружаем в массив
		$row = 0;
		foreach ($xpath->query('.//tr') as $tr) {

			// Обнуляем
			$data = array();
			$col = 0;

			// Пробегаем по дочкам
			foreach ($tr->childNodes as $td) {
				$data[$col] = $td->nodeValue;
				$col++;
			}

			if (0 == $row) { // Первая страка таблицы

				// Получаем значения столбцов
				$numbers = $this->getNumbers($data);
				if (true != $numbers) {
					$this->addMsg('Error #'.__LINE__." - Необходима доработка загрузчика.");
					return false;
				}

			} elseif (1 == sizeof($data)) { // Строка с именем категории

				// Получаем имя синонима категории
				$category = $td->nodeValue;

			} else { // Строка с данными о товаре

				// Переопределяем свойства
				$product = array();
				$product['category'] = $category;
				$product['article'] = $data[$numbers['article']];
				$product['vendor'] = $data[$numbers['vendor']];
				$product['name'] = $data[$numbers['name']];
				$product['price_USD'] = $data[$numbers['price_USD']];
				$product['price_RUB'] = $data[$numbers['price_RUB']];
				$product['stock_msk'] = $data[$numbers['stock_msk']];
				$product['stock_transit'] = $data[$numbers['stock_transit']];
				$product['comment'] = $data[$numbers['comment']];
				$product['warranty'] = $data[$numbers['warranty']];

				// Заносим данные в массив
				$this->toSQL($product);
			}
			$row++;
		}
		JFile::delete($file);
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
				case 'Св.'           : $numbers['stock_msk']     = $col; break;
				case 'Св.+Тр.'       : $numbers['stock_transit'] = $col; break;
				case 'Доп.'          : $numbers['comment']       = $col; break;
				case 'Гар.'          : $numbers['warranty']      = $col; break;
			}
		}
		if ((isset($numbers['article']))
		and (isset($numbers['vendor']))
		and (isset($numbers['name']))
		and (isset($numbers['price_USD']))
		and (isset($numbers['price_RUB']))
		and (isset($numbers['stock_msk']))
		and (isset($numbers['stock_transit']))
		and (isset($numbers['warranty']))) { // Все колонки на месте
			return $numbers;
		} else { // Не хватает обязательных колонок
			return false;
		}
	}

	// Заносит информацию в базу
	protected function toSQL($product) {

		// Обнуляем переменные
		$productId = 0;
		$categoryId = 0;
		$price = 0;

		// Получаем id производителя
		$synonym = Vendor::getSynonym($product['vendor']);
		if (isset($synonym->vendor_id)) { // Есть id производителя
			$vendorId = $synonym->vendor_id;
		} else { // Нет id производителя
			$vendorId = 0;
		}

		// Проверяем есть ли синоним производителя
		if (!isset($synonym->id)) {
			$synonym = Vendor::addSynonym($product['vendor']);
			if (!isset($synonym->id)) {
				$this->addMsg("Не удалось добавить синоним производителя: {$product['vendor']}");
			} else {
				$this->addMsg("Добавлен синоним производителя: {$product['vendor']}");
			}
		}

		// Получаем id категории
		$synonym = Category::getSynonym($product['category'], $this->partner->id);
		if (isset($synonym->category_id)) {
			$categoryId = $synonym->category_id;
		} else {
			$categoryId = 0;
		}

		// Проверяем есть ли синоним категории
		if (!isset($synonym->id)) {
			$synonym = Category::addSynonym($product['category'], $this->partner->id);
			if (!isset($synonym->id)) { // Нет синонима в базе
				$this->addMsg("Не удалось добавить синоним категории: {$product['category']}");
			} else {
				$this->addMsg("Добавлен синоним категории: {$synonym->name}");
			}
		}

		// Проверяем, все ли есть для добавления продукта
		if ((true != $vendorId)
		or (true != $categoryId)
		or (true != $product['article'])
		or (true != $product['name'])) {
			return false;
		}

		// Получаем id продукта
		$productFromDB = Product::getProductFromArticle($product['article'], $vendorId);
		if (isset($productFromDB->id)) {
			$productId = $productFromDB->id;
		} else {
			$productFromDB = Product::addProduct($product['name'], $product['name'], $categoryId, $vendorId, $product['article']);
			if (isset($productFromDB->id)) {
				$this->addMsg("Добавлен продукт: [{$product['article']}] {$product['name']}.");
				$productId = $productFromDB->id;
			} else {
				$this->addMsg("Не удалось добавить продукт: [{$product['article']}] {$product['name']}.");
				return false;
			}
		}

		// Добавляем цену и количество (только если есть в наличии)
		$priceUSD = $this->getCorrectedPrice($product['price_USD']);
		$priceRUB = $this->getCorrectedPrice($product['price_RUB']);
		$quantityStock = $this->getCorrectedQuantity($product['stock_msk']);
		$quantityTransit = $this->getCorrectedQuantity($this->getCorrectedQuantity($product['stock_transit']) - $quantityStock);

		// Есть ли наличие хотя бы на одном из складов?
		if ((true == $quantityStock) or (true == $quantityStock)) { // Наличие есть

			// Заносим информацию о наличие в базу
			Stock::addQuantity($this->stock['treolan-russia-stock']->id, $productId, $quantityStock, 3, 0);
			Stock::addQuantity($this->stock['treolan-russia-transit']->id, $productId, $quantityTransit, 3, 0);

			// Есть ли долларовая цена?
			if (true == $priceUSD) {
				Price::addPrice(
					$this->partner->id,
					$productId,
					$priceUSD,
					$this->currency['USD']->id,
					$this->priceType['rdp']->id,
					3,
					0
				);
			}

			// Есть ли рублевая цена?
			if (true == $priceRUB) {
				Price::addPrice(
					$this->partner->id,
					$productId,
					$priceRUB,
					$this->currency['RUB']->id,
					$this->priceType['rdp']->id,
					3,
					0
				);
			}

		} else { // Наличия нет
			return false;
		}
		return true;
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
}
