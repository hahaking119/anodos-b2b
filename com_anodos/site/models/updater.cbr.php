<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
require_once JPATH_COMPONENT.'/models/helpers/partner.php';
require_once JPATH_COMPONENT.'/models/helpers/currency.php';

class UpdaterCBR {

	// Объявляем переменные
	protected $partnerAlias = 'cbr';
	protected $partnerName = "Центральный банк России";

	protected $updater;
	protected $partner;

	protected $date;
	protected $url;

	protected $data = array();
	protected $numbers = array();

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

		// Проверяем привязку загрузчика к контрагенту
		if ($this->updater->partner_id != $this->partner->id) {
			$this->linkToPartner($this->updater->id, $this->partner->id);		
		}

		// Определяем дату
		$this->date = $this->getDate();

		// Определяем URL
		$this->url = $this->getURL();

		// Загружаем данные в массив
		if (true != sizeof($this->data = $this->getData())) {
			$this->addMsg("Не удается загрузить c {$this->url}.");
			return false;
		}

		// Идентифицируем колонки в массиве с данными
		if (true != $this->numbers = $this->getNumbers()) {
			$this->addMsg('Формат выгрузки курсов валют изменился.');
			$this->addMsg('Необходима доработка парсера.');
			return false;
		}

		// Проходим по столбцам, загружаем в базу
		if (true != $this->onParsing()) {
			return false;
		}

		// Отмечаем время обновления
		$this->setUpdated($this->updater->id);

		// Выводим сообщение о завершении обработки
		$this->addMsg("Завершено.");
		return true;
	}

	// Возвращает текущую дату в формате 2012-12-21
	protected function getDate() {

		// Определяем дату
		$date = getdate();

		// Правим день и месяц
		if ($date['mon'] < 10) {
			$date['mon'] = '0'.$date['mon'];
		}
		if ($date['mday'] < 10) {
			$date['mday'] = '0'.$date['mday'];
		}

		// Формируем дату и возвращаем результат
		$date = "{$date['year']}-{$date['mon']}-{$date['mday']}";
		return $date;
	}

	// Возвращает адрес, по которому доступно обновление
	protected function getURL() {

		// Определяем дату
		$date = getdate();

		// Правим день и месяц
		if ($date['mon'] < 10)  $date['mon']  = '0'.$date['mon'];
		if ($date['mday'] < 10) $date['mday'] = '0'.$date['mday'];

		// Формируем URL и возвращаем результат
		$url = "http://cbr.ru/eng/currency_base/D_print.aspx?date_req={$date['mday']}.{$date['mon']}.{$date['year']}";
		return $url;
	}

	// Возвращает массив данных
	protected function getData() {
		$dom = new DomDocument();
		$dom->loadHtmlFile($this->url);
		$row = 0;
		$xpath = new DOMXPath($dom);
		foreach ($xpath->query('//table[@class = "CBRTBL"]/tr') as $tr) {
			$col = 0;
			foreach ($tr->childNodes as $td) {
				$data[$row][$col] = $td->nodeValue;
				$col++;
			}
			$row++;
		}
		return $data;
	}

	// Возвращает массив номеров колонок
	protected function getNumbers() {
		for ($col=0; $col<sizeof($this->data[0]); $col++) {
			switch ($this->data[0][$col]) {
				case 'Num Ñode'  : $numbers['code']	  = $col+0; break;
				case 'Char Ñode' : $numbers['alias']    = $col+1; break;
				case 'Unit'        : $numbers['quantity'] = $col+2; break;
				case 'Currency'    : $numbers['name']     = $col+3; break;
				case 'Rate'        : $numbers['rate']     = $col+4; break;
			}
		}
		if ((0 === $numbers['code'])
			and (2 == $numbers['alias'])
			and (4 == $numbers['quantity'])
			and (6 == $numbers['name'])
			and (8 == $numbers['rate'])) {
			return $numbers;
		} else {
			return false;
		}
	}

	// Загружает информацию в базу банных
	protected function onParsing() {

		// Цикл по всем строкам
		for ($row=1; $row<sizeof($this->data); $row++) {

			// Получаем даные о валюте
			$code  = $this->data[$row][$this->numbers['code']];
			$name  = $this->data[$row][$this->numbers['name']];
			$alias = $this->data[$row][$this->numbers['alias']];
			$alias = $this->clearAlias($alias);

			// Получаем объект валюты
			$currency = Currency::getCurrencyFromAlias($alias);
			// Проверяем, есть ли валюта в базе
			if (!isset($currency->id)) { // Нет такой валюты

				// Добавляем валюту
				$currency = Currency::addCurrency($name, $alias);

				// Проверяем добавлена ли валюта
				if (true != $currency->id) { // Валюта не добавлена

					// Выводим сообщение о невозможности добавления валюты
					$this->addMsg("Не удалось добавить валюту: {$name}.");

					// Уходим в следующий цикл
					continue;

				} else { // Валюта добавлена

					// Выводим сообщение о добавлении валюты
					$this->addMsg("Добавлена валюта: {$name}.");

					// Забираем id валюты
					$id = $currency->id;
				}
			} else { // Есть валюта

				// Забираем id валюты
				$id = $currency->id;
			}

			// Проверяем, есть ли курс на текущую дату
			if (true == Currency::getRate($id, $this->date)) { // Курс есть
				$this->addMsg("Курс валюты {$alias} на текущую дату есть: ".Currency::getRate($id, $this->date).'.');
			} else { // Курса нет

				// Получаем курс
				$quantity = $this->data[$row][$this->numbers['quantity']];
				$quantity = $this->clearQuantity($quantity);
				$rate = $this->data[$row][$this->numbers['rate']];
				$rate = $this->clearRate($rate);

				// Помечаем устаревшие курсы
				Currency::setZeroState($id);

				// Добавлем курс
				Currency::addRate($id, $this->date, $rate, $quantity);
			}
		}
		return true;
	}

	// Возвращает id валюты
	protected function getId($row, $name = '') {

		// Получаем значение и чистим его от мусора
		$alias = $this->data[$row][$this->numbers['alias']];
		$alias = $this->clearAlias($alias);

		// Получаем объект валюты
		$currency = Currency::getCurrencyFromAlias($alias);
	}

	// Чистит буквенный код валюты
	protected function clearAlias($alias) {
		$alias = ereg_replace('[^A-Z]*', '', $alias);
		if (3 == strlen($alias)) {
			return $alias;
		} else {
			return false;
		}
	}

	// Преобразует курс в удобоваримый для базы вид
	protected function clearRate($string) {
		$string = ereg_replace('[,]', '.', $string);
		$string = ereg_replace('[ ]', '', $string);
		return doubleval($string);
	}

	// Преобразует количество из строки в число
	protected function clearQuantity($string) {
		$int = ereg_replace('[^0-9]*', '', $string); // убираем из строки все, что не цифра
		if (true == $int) {
			return $int;
		} else {
			$string = utf8_strtolower($string);
			switch ($string) {
				case '0': return 0; // В противном случае нули выбывают из обработки
				default : $this->addMsg("Необходим новый кейс обработки количества: {$string}."); return 0;
			}
		}
	}
}
