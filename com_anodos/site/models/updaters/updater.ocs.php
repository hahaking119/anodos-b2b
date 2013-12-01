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

		// Получаем имя папки для загрузки
		if (!$this->getCatalog()) {
			$this->addMsg('Error #'.__LINE__.' - Не удалось получить каталог.');
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

	// Получаем каталог
	/* Ожидаемый формат данных
	{"d":
		{"__type":"OCS.CatalogResult",
		"OperationStatus":0,
		"ErrorText":null,
		"Categories":
		[
			{"CategoryID":"",
			"CategoryName":"Виды оборудования",
			"ParentCategoryID":null,
			"NestingLevel":1},
			{"CategoryID":"01",
			"CategoryName":"Активное сетевое борудование",
			"ParentCategoryID":"",
			"NestingLevel":2},
			{"CategoryID":"02",
			"CategoryName":"Пассивное сетевое оборудование",
			"ParentCategoryID":"",		
			"NestingLevel":2},
			…
		]}
	}
	********************************/
	protected function getCatalog() {

		// Инициализируем cURL и логинимся
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, "https://b2btestservice.ocs.ru/b2bjson.asmx/GetCatalog");
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
			// TODO убрать тестовый вывод на экран в рабочей версии
			curl_setopt($ch, CURLOPT_POST, true);
			echo $data = "{\"Login\":\"{$this->updater->login}\",\"Token\":\"{$this->updater->pass}\"}";
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

			// Выполняем запрос
			// TODO убрать тестовый вывод на экран в рабочей версии
			echo $result = curl_exec($ch);

			// Освобождаем ресурс
			curl_close($ch);
			unset($ch);
		} else {
			return false;
		}

		return $result;
	}

	// Заносит информацию в базу
	protected function toSQL($product) {

	}

}
