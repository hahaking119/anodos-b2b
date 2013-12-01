<?php

defined('_JEXEC') or die;

class Currency {

	public function getCurrencyFromAlias($alias) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_currency
			WHERE '{$alias}' = alias;";
		$db->setQuery($query);
		$currency = $db->loadObject();

		// Возвращаем результат
		return $currency;
	}

	// Добавляет валюту, возвращает ее объект
	public function addCurrency($name, $alias, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос вставки
		$query = "
			INSERT
			INTO #__anodos_currency (
				id,
				name,
				alias,
				name_html,
				state,
				created,
				created_by)
			VALUES (
				DEFAULT,
				'{$name}',
				'{$alias}',
				'{$alias}',
				'1',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_currency
			WHERE '{$alias}' = alias;";
		$db->setQuery($query);
		$currency = $db->loadObject();

		// Возвращаем результат
		return $currency;
	}

	// Возвращает курс валюты по ее id на указанную дату
	public function getRate($id, $date) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Проверяем, есть ли курс на текущую дату
		$query = "
			SELECT rate
			FROM #__anodos_currency_rate
			WHERE
				#__anodos_currency_rate.currency_id = '{$id}' AND
				#__anodos_currency_rate.date = '{$date}';";
		$db->setQuery($query);
		$rate = $db->loadResult();

		// Возвращаем результат
		return $rate;
	}

	// Добавляет курс валюты по ее коду на указанную дату 
	public function addRate($id, $date, $rate, $quantity, $addDate = 3) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$id = $db->quote($id);
		$date = $db->quote($date);
		$rate = $db->quote($rate);
		$quantity = $db->quote($quantity);

		// TODO курс есть - изменяем, если курса нет - добавляем
		$query="SELECT rate FROM #__anodos_currency_rate WHERE currency_id = {$id};";
		$db->setQuery($query);
		if (isset($db->loadResult)) {
			$query="
				UPDATE #__anodos_currency_rate (
					currency_id,
					date,
					rate,
					quantity,
					publish_up,
					publish_down)
				VALUES (
					{$id},
					{$date},
					{$rate},
					{$quantity},
					{$currencyId},
					{$priceTypeId},
					NOW(),
					DATE_ADD(NOW(), INTERVAL {$addDate} DAY))
				WHERE currency_id = {$id};
			";
			$db->setQuery($query);
			$db->query();
		} else {
			$query="
				INSERT INTO #__anodos_currency_rate (
					currency_id,
					date,
					rate,
					quantity,
					publish_up,
					publish_down)
				VALUES (
					{$id},
					{$date},
					{$rate},
					{$quantity},
					NOW(),
					DATE_ADD(NOW(), INTERVAL {$addDate} DAY));
			";
			$db->setQuery($query);
			$db->query();
		}
		return true;
	}

	// Снятие с публикации устаревших курсов валют
	public function setZeroState($id) {

		// Подключаемся к базе
//		$db = JFactory::getDBO();

		// Выполняем запрос
//		$query = "
//			UPDATE #__anodos_currency_rate
//			SET state = '0'
//			WHERE currency_id = '{$id}';";
//		$db->setQuery($query);
//		$db->query();

		// Возвращаем результат
		return true;
	}
}
