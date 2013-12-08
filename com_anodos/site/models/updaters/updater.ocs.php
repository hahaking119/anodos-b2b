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

class UpdaterOCS {

	// Объявляем переменные
	protected $partnerAlias = 'ocs';
	protected $partnerName = 'OCS';
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
		$alias = 'ocs-ss-stock';
		$name = 'Самарский склад OCS';
		$this->stock[$alias] = Stock::getStockFromAlias($alias);
		if (!isset($this->stock[$alias]->id)) {
			$this->stock[$alias] = Stock::addStock($name, $alias, $this->partner->id, 0);
			if (!isset($this->stock[$alias]->id)) {
				$this->addMsg('<div class="uk-alert uk-alert-danger">'.__LINE__." - Не возможно добавить склад: {$name}.</div>");
				return false;
			} else {
				$this->addMsg("<div class=\"uk-alert uk-alert-succes\">Добавлен склад: {$this->stock[$alias]->name}.</div>");
			}
		}

		// Получаем объект склада
		$alias = 'ocs-tss-stock';
		$name = 'В пути на самарский склад OCS';
		$this->stock[$alias] = Stock::getStockFromAlias($alias);
		if (!isset($this->stock[$alias]->id)) {
			$this->stock[$alias] = Stock::addStock($name, $alias, $this->partner->id, 0);
			if (!isset($this->stock[$alias]->id)) {
				$this->addMsg('<div class="uk-alert uk-alert-danger">'.__LINE__." - Не возможно добавить склад: {$name}.</div>");
				return false;
			} else {
				$this->addMsg("<div class=\"uk-alert uk-alert-succes\">Добавлен склад: {$this->stock[$alias]->name}.</div>");
			}
		}

		// Получаем объект склада
		$alias = 'ocs-co-stock';
		$name = 'Центральный склад OCS';
		$this->stock[$alias] = Stock::getStockFromAlias($alias);
		if (!isset($this->stock[$alias]->id)) {
			$this->stock[$alias] = Stock::addStock($name, $alias, $this->partner->id, 0);
			if (!isset($this->stock[$alias]->id)) {
				$this->addMsg('<div class="uk-alert uk-alert-danger">'.__LINE__." - Не возможно добавить склад: {$name}.</div>");
				return false;
			} else {
				$this->addMsg("<div class=\"uk-alert uk-alert-succes\">Добавлен склад: {$this->stock[$alias]->name}.</div>");
			}
		}

		// Получаем объект склада
		$alias = 'ocs-bt-stock';
		$name = 'Ближний транзит OCS';
		$this->stock[$alias] = Stock::getStockFromAlias($alias);
		if (!isset($this->stock[$alias]->id)) {
			$this->stock[$alias] = Stock::addStock($name, $alias, $this->partner->id, 0);
			if (!isset($this->stock[$alias]->id)) {
				$this->addMsg('<div class="uk-alert uk-alert-danger">'.__LINE__." - Не возможно добавить склад: {$name}.</div>");
				return false;
			} else {
				$this->addMsg("<div class=\"uk-alert uk-alert-succes\">Добавлен склад: {$this->stock[$alias]->name}.</div>");
			}
		}

		// Получаем объект склада
		$alias = 'ocs-dt-stock';
		$name = 'Дальний транзит OCS';
		$this->stock[$alias] = Stock::getStockFromAlias($alias);
		if (!isset($this->stock[$alias]->id)) {
			$this->stock[$alias] = Stock::addStock($name, $alias, $this->partner->id, 0);
			if (!isset($this->stock[$alias]->id)) {
				$this->addMsg('<div class="uk-alert uk-alert-danger">'.__LINE__." - Не возможно добавить склад: {$name}.</div>");
				return false;
			} else {
				$this->addMsg("<div class=\"uk-alert uk-alert-succes\">Добавлен склад: {$this->stock[$alias]->name}.</div>");
			}
		}

		// Получаем объект склада
		$alias = 'ocs-cbr-stock';
		$name = 'Транзит OCS с неизвестным сроком';
		$this->stock[$alias] = Stock::getStockFromAlias($alias);
		if (!isset($this->stock[$alias]->id)) {
			$this->stock[$alias] = Stock::addStock($name, $alias, $this->partner->id, 0);
			if (!isset($this->stock[$alias]->id)) {
				$this->addMsg('<div class="uk-alert uk-alert-danger">'.__LINE__." - Не возможно добавить склад: {$name}.</div>");
				return false;
			} else {
				$this->addMsg("<div class=\"uk-alert uk-alert-succes\">Добавлен склад: {$this->stock[$alias]->name}.</div>");
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

		// Получаем объект типа цены
		$alias = 'gpl';
		$name = 'Глобальный уровень цены (GPL, выход)';
		$this->priceType[$alias] = Price::getPriceTypeFromAlias('gpl');
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
			$this->addMsg('<div class="uk-alert uk-alert-danger">'.__LINE__." - Нет валюты: {$alias}.</div>");
			return false;
		}

		// Получаем id валюты RUB
		$alias = 'RUB';
		$this->currency[$alias] = Currency::getCurrencyFromAlias($alias);
		if (!isset($this->currency[$alias])) {
			$this->addMsg('<div class="uk-alert uk-alert-danger">'.__LINE__." - Нет валюты: {$alias}.</div>");
			return false;
		}

		// Загружаем каталог
		if (!$this->getCatalog()) {
			$this->addMsg('Error #'.__LINE__.' - Не удалось получить каталог.');
			return false;
		}

		// Загружаем продукты
		if (!$this->getProducts()) {
			$this->addMsg('Error #'.__LINE__.' - Не удалось загрузить продукты.');
			return false;
		}

		// Помечаем неактуальными устаревшие данные в базе
		Price::clearSQL($this->stock['ocs-ss-stock']->id, $this->startTime);
		Price::clearSQL($this->stock['ocs-co-stock']->id, $this->startTime);
		Price::clearSQL($this->stock['ocs-bt-stock']->id, $this->startTime);
		Price::clearSQL($this->stock['ocs-dt-stock']->id, $this->startTime);
		Price::clearSQL($this->stock['ocs-cbr-stock']->id, $this->startTime);
		Stock::clearSQL($this->stock['ocs-ss-stock']->id, $this->startTime);
		Stock::clearSQL($this->stock['ocs-co-stock']->id, $this->startTime);
		Stock::clearSQL($this->stock['ocs-bt-stock']->id, $this->startTime);
		Stock::clearSQL($this->stock['ocs-dt-stock']->id, $this->startTime);
		Stock::clearSQL($this->stock['ocs-cbr-stock']->id, $this->startTime);

		// Отмечаем время обновления
		Updater::setUpdated($this->updater->id);

		// Выводим сообщение о завершении обработки
		$this->addMsg('<div class="uk-alert uk-alert-success">Обработка завершена.</div>');
		return true;
	}

	// Загружаем каталог в #__anodos_category_synonym
	protected function getCatalog() {

		// Инициализируем cURL и логинимся
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, "{$this->updater->client}/GetCatalog");
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);

			// Отключаем проверку сертификатов
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			// Добавляем заголовки
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'Content-Type: application/json; charset=utf-8',
			    'Accept: application/json; charset=utf-8'
			));

			// Формируем содержимое POST
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"Login\":\"{$this->updater->login}\",\"Token\":\"{$this->updater->pass}\"}");

			// Выполняем запрос
			$return = curl_exec($ch);
//			echo $return."<br/>\n";
			$return = json_decode($return, true);

			// TODO отработать код и сообщение ответа сервера

			if (0 == $return["d"]["OperationStatus"]) {
				$return = $return["d"]["Categories"];
			} else {
				$this->addMsg('<div class="uk-alert uk-alert-success">'.$return["d"]["OperationStatus"].' - '.$return["d"]["ErrorText"].'</div>');
				return false;
			}

			// Освобождаем ресурс
			curl_close($ch);
			unset($ch);
		} else {
			return false;
		}

		$categories = array();
		foreach ($return as $r) {
			if ($r["CategoryID"] === "") { // Корень - не интересует
				continue;
			} elseif ($r["ParentCategoryID"] === "" ) { // Верхний уковень категорий

				// Инициализируем переменные
				$category = array();

				// Получаем данные о категории
				$category["name"] = $r["CategoryName"];
				$category["parent_id"] = false;
				$categories[$r["CategoryID"]] = $category;

				// Добавляем синоним категории, если его нет в базе
				$synonym = Category::getSynonym($category["name"], $this->partner->id);
				if (!isset($synonym->id)) {
					$synonym = Category::addSynonym($category["name"], $this->partner->id, $r["CategoryID"]);
					if (!isset($synonym->id)) { // Нет синонима в базе
						$this->addMsg("Не удалось добавить синоним категории: {$category["name"]}");
					} else {
						$this->addMsg("Добавлен синоним категории : {$synonym->name}");
					}
				} else {
					// Обновляем id синонима категории
					$synonymOriginalId = Category::setOriginalIdToSynonym($synonym->id, $r["CategoryID"]);
					// TODO проверить
				}

				// Обнуляем переменные
				unset($category);

			} elseif (2 < $r["NestingLevel"]) { // Вложенные категории

				// Инициализируем переменные
				$category = array();

				// Получаем данные о категории
				$category["name"] = $r["CategoryName"];
				$category["parent_id"] = $r["ParentCategoryID"];

				// В цикле добавляем имена родительских категорий
				$parent = $r["ParentCategoryID"];
				while (false !== $parent) {
					$category["name"] = $categories[$parent]["name"].' | '.$category["name"];
					$parent = $categories[$parent]["parent_id"];
				}
				$categories[$r["CategoryID"]] = $category;

				// Добавляем синоним категории, если его нет в базе
				$synonym = Category::getSynonym($category["name"], $this->partner->id);
				if (!isset($synonym->id)) {
					$synonym = Category::addSynonym($category["name"], $this->partner->id, $r["CategoryID"]);
					if (!isset($synonym->id)) { // Нет синонима в базе
						$this->addMsg("Не удалось добавить синоним категории: {$category["name"]}");
					} else {
						$this->addMsg("Добавлен синоним категории : {$synonym->name}");
					}
				} else {
					// Обновляем id синонима катего
					$synonymOriginalId = Category::setOriginalIdToSynonym($synonym->id, $r["CategoryID"]);
					// TODO проверить
				}

				// Обнуляем переменные
				unset($category);
			}
		}

		return true;
	}

	// Загружаем товары, сток и цены
	protected function getProducts() {

		// Инициализируем cURL и логинимся
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, "{$this->updater->client}/GetProductAvailability");
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);

			// Отключаем проверку сертификатов
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			// Добавляем заголовки
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'Content-Type: application/json; charset=utf-8',
			    'Accept: application/json; charset=utf-8'
			));

			// Формируем содержимое POST
			curl_setopt($ch, CURLOPT_POST, true);
//			$data = '
//				{
//					"Login":"'.$this->updater->login.'",
//					"Token":"'.$this->updater->pass.'",
//					"Availability":1,
//					"ShipmentCity":"Москва",
//					"CategoryIDList":[
//						{"CategoryID": "01"}
//					],
//					"LocationList":[
//						{"Location":"CBR"}
//						{"DisplayMissing":1}
//					]
//				}';
			$data = '{"Login":"'.$this->updater->login.'","Token":"'.$this->updater->pass.'","Availability":1, "ShipmentCity":"Самара","CategoryIDList":null,"ItemIDList":null,"LocationList":["Самара","В пути","БТ","ДТ","CBR"],"DisplayMissing":1}';

//			echo $data."<br/><br/>\n>";
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

			// Выполняем запрос
			$return = curl_exec($ch);
//			echo $return."<br/>\n";
			$return = json_decode($return, true);

			// Отработать код и сообщение ответа сервера
			if (0 == $return["d"]["OperationStatus"]) {
				$products = $return["d"]["Products"];
			} else {
				$this->addMsg('<div class="uk-alert uk-alert-success">'.$return["d"]["OperationStatus"].' - '.$return["d"]["ErrorText"].'</div>');
				return false;
			}

//			"ItemID":"1000017421",
//			"PartNumber":"DUB-E100, DUB-E100/B/C1A",
//			"Producer":"D-Link",
//			"ItemName":"10/100Base-TX Fast Ethernet USB 2.0 NIC (RJ-45 connector) (USB 2.0)",
//			"ItemNameRus":"Сетевой адаптер",
//			"CategoryID":"0101",
//			"Price":22.590000000000,
//			"Currency":"USD",
//			"PercentConv":3.000000000000,
//			"Availability":1,
//			"Multiplicity":1,
//			"UOM":"шт",
//			"Weight":0.125000000000,
//			"Volume":0.000014283000,
//			"EAN128":null,
//			"UPC":"790069272707, 790069369056",
//			"EquipmentGroup":"Активное сетевое оборудование",
//			"EquipmentType":"Сетевые адаптеры Ethernet",
//			"Comment":"",
//			"Locations":[{
//				"Location":"Самара",
//				"Quantity":2,
//				"GreaterThanQuantity":0
//			}]


			// Освобождаем ресурс
			curl_close($ch);
			unset($ch);
		} else {
			return false;
		}

		// Заносим данные о продукте в базу
		foreach ($products as $product) {
			$this->toSQL($product);
		}

		return true;
	}

	// Заносит информацию в базу
	protected function toSQL($product) {

		// Обнуляем переменные
		$productId = 0;
		$categoryId = 0;
		$vendorId = 0;
		$price = 0;
		$currencyId = 0;

		// Получаем id производителя, если указан в базе
		$synonym = Vendor::getSynonym($product['Producer'], $this->partner->id);
		if (isset($synonym->vendor_id)) {
			$vendorId = $synonym->vendor_id;
		}

		// Добавляем синоним производителя если его нет
		if (!isset($synonym->id)) {
			$synonym = Vendor::addSynonym($product['Producer'], $this->partner->id);
			if (!isset($synonym->id)) {
				$this->addMsg("<div class=\"uk-alert uk-alert-danger\">Не удалось добавить синоним производителя: {$product['Producer']}.</div>");
			} else {
				$this->addMsg("<div class=\"uk-alert uk-alert-succes\">Добавлен синоним производителя: {$product['Producer']}.</div>");
			}
		}

		// Получаем id категории если она указана
		$synonym = Category::getSynonymFromOriginalId($product['CategoryID'], $this->partner->id);
// TODO DEL
//		echo "\$synonym = Category::getSynonymFromOriginalId({$product['CategoryID']}, {$this->partner->id})<br/>\n";
//		return true;

		if (isset($synonym->category_id)) {
			$categoryId = $synonym->category_id;
		}

		// Проверяем, все ли есть для добавления продукта
		if ((true != $vendorId)
		or (true != $categoryId)
		or (true != $product['PartNumber'])
		or (true != $product['ItemName'])) {
			return false;
		}

		// Получаем id продукта
		$productFromDB = Product::getProductFromArticle($product['PartNumber'], $vendorId);
		if (isset($productFromDB->id)) {
			$productId = $productFromDB->id;
		} else {
			$productFromDB = Product::addProduct($product['ItemNameRus'].' '.$product['ItemName'], $product['ItemNameRus'].' '.$product['ItemName'], $categoryId, $vendorId, $product['PartNumber']);
			if (isset($productFromDB->id)) {
				$this->addMsg("<div class=\"uk-alert uk-alert-succes\">Добавлен продукт: [{$product['PartNumber']}] {$product['ItemName']}.</div>");
				$productId = $productFromDB->id;
			} else {
				$this->addMsg("<div class=\"uk-alert uk-alert-danger\">Не удалось добавить продукт: [{$product['PartNumber']}] {$product['ItemName']}.</div>");
				return false;
			}
		}

		// Получаем id валюты
		if ('RUR' == $product['Currency']) {
			$currencyId = $this->currency['RUB']->id;
		} elseif ('USD' == $product['Currency']) {
			$currencyId = $this->currency['USD']->id;
		} else {
			$this->addMsg("<div class=\"uk-alert uk-alert-danger\">Не знаю, что делать с {$product['Currency']}.</div>");
			return false;
		}

		// Готовим цену
		$price = $this->getCorrectedPrice($product['Price']);

		// Циклично пробегаем по наличию на складах
		foreach ($product['Locations'] as $location) {

			// Обнуляем переменные
			$stockId = 0;
			$quantity = 0;

			$stockId = $this->getStockId($location['Location']);
			$quantity = $this->getCorrectedQuantity($location['Quantity']);
			if (0 !== $stockId) {

				// Заносим информацию о наличии в базу
				Stock::addQuantity(
					$stockId,
					$productId,
					$location['Quantity'],
					3,
					0
				);

				// Заносим цену в базу
				Price::addPrice(
					$stockId,
					$productId,
					$price,
					$currencyId,
					$this->priceType['rdp']->id,
					3,
					0
				);
			}
		}
		unset($stockId);
		unset($quantity);
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
				default : $this->addMsg("<div class=\"uk-alert uk-alert-danger\">Необходим новый кейс обработки количества: '$string'.</div>"); return 0;
			}
		}
	}

	// Выясняем id склада по его имени
	protected function getStockId($name) {

		switch ($name) {
			case 'Самара' : return $this->stock['ocs-ss-stock']->id;
			case 'В пути' : return $this->stock['ocs-tss-stock']->id;
			case 'ЦО' : return $this->stock['ocs-co-stock']->id;
			case 'БТ' : return $this->stock['ocs-bt-stock']->id;
			case 'ДТ' : return $this->stock['ocs-dt-stock']->id;
			case 'CBR' : return $this->stock['ocs-cbr-stock']->id;
			default : $this->addMsg("<div class=\"uk-alert uk-alert-danger\">Необходим новый кейс обработки склада: '$string'.</div>"); return 0;
		}
	}
}
