<?php

defined('_JEXEC') or die;

class Product {

	// Возвращает id продукта по артикулу и id производителя
	public function getProductFromArticle($article, $vendorId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$article = $db->quote($article);
		$vendorId = $db->quote($vendorId);

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_product
			WHERE {$article} = article
			AND {$vendorId} = vendor_id;";
		$db->setQuery($query);
		$product = $db->loadObject();

		//TODO Проверяем, является ли продукт дублем другого, если да, повторяем запрос по идентификатору, если нет - просто возвращаем результат
//		if (isset($product->duble_of)) {

			// Выполняем запрос
//			$query = "
//				SELECT *
//				FROM #__anodos_product
//				WHERE id = {$product->duble_of};";
//			$db->setQuery($query);
//			$product = $db->loadObject();
//		}

		// Возвращаем результат
		if (isset($product->id)) {
			return $product;
		} else {
			return false;
		}
	}

	// Добавляет продукт
	public function addProduct($name, $alias, $categoryId, $vendorId, $article, $state = 1, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$fullName = $db->quote("{$name} [{$article}]");
		$name = $db->quote($name);
		$alias = $db->quote($alias);
		$categoryId = $db->quote($categoryId);
		$vendorId = $db->quote($vendorId);
		$article = $db->quote($article);
		$state = $db->quote($state);
		$createdBy = $db->quote($createdBy);

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_product (
				name,
				alias,
				full_name,
				category_id,
				vendor_id,
				article,
				created,
				created_by,
				state)
			VALUES (
				{$name},
				{$alias},
				{$fullName},
				{$categoryId},
				{$vendorId},
				{$article},
				NOW(),
				{$createdBy},
				{$state});";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_product
			WHERE {$article} = article
			AND {$vendorId} = vendor_id;";
		$db->setQuery($query);
		$product = $db->loadObject();

		// Возвращаем результат
		return $product;
	}
}
