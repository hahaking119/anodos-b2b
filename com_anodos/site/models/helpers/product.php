<?php

defined('_JEXEC') or die;

class Product {

	// Возвращает список объектов продуктов из указанной категории
	public function getProductsFromCategory($categoryId) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$categoryId = $db->quote($categoryId);

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_product
			WHERE category_id = {$categoryId};
		";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Возвращяем результат
		return $result;
	}

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

	// Переименовывает продукт
	public function renameProduct($productId, $productName) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$productId = $db->quote($productId);
		$productName = $db->quote($productName);

		// Выполняем запрос
		$query = "UPDATE #__anodos_product SET name = {$productName} WHERE id = {$productId};";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "SELECT * FROM #__anodos_product WHERE {$productId} = id;";
		$db->setQuery($query);
		$product = $db->loadObject();

		// Возвращаем результат
		if(!isset($product->id)) {
			return false;
		} else {
			return $product;
		}
	}

	// Удаляет товар
	public function removeProduct($productId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$productId = $db->quote($productId);

		// Выполняем запрос
		$query = "DELETE FROM #__anodos_product WHERE id = {$productId};";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
