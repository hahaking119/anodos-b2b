<?php
/**
 * @version     0.0.1
 * @package     com_anodosupdater
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Andrey J Bezpalov <abezpalov@ya.ru> - http://anodos.ru
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Product helper.
 */
class Product {

	// Возвращает id продукта по артикулу и id производителя
	public function getProductFromArticle($article, $vendorId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM `#__anodos_product`
			WHERE '{$article}' = `article`
			AND '{$vendorId}' = `vendor_id`;";
		$db->setQuery($query);
		$product = $db->loadObject();


		//TODO Проверяем, является ли продукт дублем другого, если да, повторяем запрос по идентификатору, если нет - просто возвращаем результат
		if (true == $product->duble_of) {

			// Выполняем запрос
			$query = "
				SELECT *
				FROM `#__anodos_product`
				WHERE '{$id}' = `{$product->duble_of}`;";
			$db->setQuery($query);
			$product = $db->loadObject();
		}

		// Возвращаем результат
		return $product;
	}

	// Добавляет продукт
	public function addProduct($name, $alias, $categoryId, $vendorId, $article, $state = 0, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// TODO перед добавлением провести проверку на уникальность

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_product (
				`name`,
				`alias`,
				`full_name`,
				`category_id`,
				`vendor_id`,
				`article`,
				`created`,
				`created_by`,
				`state`)
			VALUES (
				'{$name}',
				'{$alias}',
				'{$name} [{$article}]',
				'{$categoryId}',
				'{$vendorId}',
				'{$article}',
				NOW(),
				'{$createdBy}',
				'{$state}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM `#__anodos_product`
			WHERE '{$article}' = `article`
			AND '{$vendorId}' = `vendor_id`;";
		$db->setQuery($query);
		$product = $db->loadObject();

		// Возвращаем результат
		return $product;
	}
}
