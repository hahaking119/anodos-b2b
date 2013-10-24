<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelProducts extends JModelList {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	protected $msg;

	public function getCategorySelected() {

		// Инициализируем переменные
		$categoryId = JRequest::getInt('category'); // Идентификатор категории

		// Запрашиваем имя и псевдоним категории
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__categories WHERE id = {$categoryId} AND extension = 'com_anodos.product';";
		$db->setQuery($query);
		return $db->loadObject();
	}

	// Возвращает дерево категорий (в виде вложенных списков)
	public function getCategories() {

		// Инициализируем переменные
		$db =& JFactory::getDBO();
		$dom = new DOMDocument('1.0');

		// Добавляем div
		$dom->appendChild($div = $dom->createElement('div'));
		$div->setAttribute('id', 'categories-list');

		// Рекурсивно заполняем вложенные списки категорий
		$this->categorySQLtoDOM($dom, $div);

		// Возвращаем результат
		return $dom->saveHTML();
	}

	// Рекурсивно заполняет вложенные списки категорий
	private function categorySQLtoDOM(&$dom, &$element) {

		// Инициализируем переменные
		$db =& JFactory::getDBO();

		// Проверяем наличие параметра category_id - если его нет - мы имеем дело с родительским элементом
		if (true != $parentId = $element->getAttribute('data-category-id')) {
			$parentId = 1;
		}

		// Делаем выборку из базы в соответствие со значением $parentId
		$query = "
			SELECT *
			FROM #__categories
			WHERE
				parent_id = {$parentId} AND
				published = 1 AND
				extension = 'com_anodos.product'
			ORDER BY lft;
		";

		$db->setQuery($query);
		$category = $db->loadObjectList();

		// Если выборка не пустая - обрабатываем каждый элемент
		if ((true == sizeof($category)) and (true == $category[0]->id)) {
			$newUL = $dom->createElement('ul');
			$element->appendChild($newUL);
			if (1 == $parentId) {
				$newUL->appendChild($newLI = $dom->createElement('li', ""));
					$newLI->appendChild($span = $dom->createElement('span', "&nbsp;"));
					$newLI->appendChild($span = $dom->createElement('span', JText::_('COM_ANODOS_ALL_CATEGORIES')));
						$span->setAttribute('id', "category-text--1");
						$span->setAttribute('onClick', "setCategorySelected(-1)");
			}
			for ($i=0; $i<sizeof($category); $i++) {
				$newUL->appendChild($newLI = $dom->createElement('li', ""));
					// Плюсик добавляем только если есть дочки
					$query = "
						SELECT *
						FROM #__categories
						WHERE parent_id = {$category[$i]->id}
						AND extension = 'com_anodos.product'
						ORDER BY lft;
					";
					$db->setQuery($query);
					$subCategory = $db->loadObjectList();
					if ((true == sizeof($subCategory)) and (true == $subCategory[0]->id)) {
						$newLI->appendChild($span = $dom->createElement('span', "&#8862;"));
							$span->setAttribute('id', "category-square-{$category[$i]->id}");
							$span->setAttribute('onClick', "openCategory({$category[$i]->id})");
						$newLI->appendChild($span = $dom->createElement('span', "&nbsp;"));
						$newLI->setAttribute('class', "close");
					} else {
						$newLI->appendChild($span = $dom->createElement('span', "&nbsp;"));
					}
					$newLI->appendChild($span = $dom->createElement('span', "{$category[$i]->title}"));
						$span->setAttribute('id', "category-text-{$category[$i]->id}");
						$span->setAttribute('onClick', "setCategorySelected({$category[$i]->id})");
				$newLI->setAttribute('id', "category-{$category[$i]->id}");
				$newLI->setAttribute('data-category-id', $category[$i]->id);
				$this->categorySQLtoDOM($dom, $newLI);
			}
		}
	}

	public function getVendorSelected() {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$vendorId = JRequest::getInt('vendor'); // Идентификатор вендора

		// Запрашиваем имя и псевдоним вендора
		$query = "SELECT * FROM #__anodos_partner WHERE id = {$vendorId} AND vendor = 1;";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getVendors() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__anodos_partner WHERE vendor = 1;";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getProducts() {

		// Инициализируем переменные
		$categories = array();
		$categoryId = JRequest::getInt('category'); // Идентификатор категории
		$vendorId = JRequest::getInt('vendor'); // Идентификатор вендора
		$sortBy = JRequest::getInt('sort'); // Идентификатор вендора

		// Получаем дерево категорий (к выбранной категории добавляем подкатегории)
		if (0 < $categoryId) { // Актуально, только если выбрана категория
			$db =& JFactory::getDBO();
			$query = "
				SELECT *
				FROM #__categories
				WHERE #__categories.id = {$categoryId} AND extension = 'com_anodos.product'
				ORDER BY lft;";
			$db->setQuery($query);
			$categories = $db->loadObjectList();
			$catid = $categoryId;
			// Уходим в рекурсивную функцию
			$this->getCategoriesArray($categories, $catid);
		}

		// Запрашиваем список товара
		$db = JFactory::getDBO();

		// Лешина версия запроса
//		$query = "
//			SELECT
//				tmp.id AS product_id,
//				tmp.article AS product_article,
//				product.title AS product_title,
//				vendor.title AS vendor_title,
//				tmp.stock_title AS stock_title,
//				tmp.q AS product_quantity,
//				tmp.price_rur AS price_rur,
//				min(tmp.dtime) AS stock_delivery_time,
//				tmp.price AS price,
//				tmp.currency_alias AS currency_alias,
//				currency.title_html AS currency_title,
//				product.catid,
//				category.title AS category_title
//			FROM #__pricer_product AS product
//			INNER JOIN #__pricer_vendor AS vendor ON product.vendor_id = vendor.id
//			INNER JOIN #__categories AS category ON product.catid = category.id
//			INNER JOIN (
//				SELECT
//					product.id,
//					product.article,
//					stock.title AS stock_title,
//					product_quantity.quantity AS q,
//					min(price_table.price*currency_rate.rate) AS price_rur,
//					stock.delivery_time AS dtime,
//					price_table.price,
//					price_table.currency_alias
//				FROM #__pricer_product AS product
//				INNER JOIN #__pricer_product_quantity AS product_quantity ON product_quantity.product_id = product.id
//				INNER JOIN #__pricer_stock AS stock ON product_quantity.stock_id = stock.id
//				INNER JOIN #__pricer_contractor AS contractor ON contractor.id = stock.contractor_id
//				INNER JOIN #__pricer_price AS price_table ON product.id = price_table.product_id
//				INNER JOIN #__pricer_currency_rate AS currency_rate ON price_table.currency_alias = currency_rate.currency_alias
//				WHERE stock.delivery_time <= date('0000-00-10')
//					AND product_quantity.quantity > 0
//					AND currency_rate.state = 1
//					AND stock.state = 1
//					AND product.state = 1
//					AND price_table.state = 1
//				GROUP BY product.id
//
//				UNION
//
//				SELECT
//					product.id,
//					product.article,
//					stock.title AS stock_title,
//					product_quantity.quantity AS q,
//					min(price_table.price*currency_rate.rate) AS price_rur,
//					stock.delivery_time AS dtime,
//					price_table.price,
//					price_table.currency_alias
//				FROM #__pricer_product AS product
//				INNER JOIN #__pricer_product_quantity AS product_quantity ON product_quantity.product_id = product.id
//				INNER JOIN #__pricer_stock AS stock ON product_quantity.stock_id = stock.id
//				INNER JOIN #__pricer_contractor AS contractor ON contractor.id = stock.contractor_id
//				INNER JOIN #__pricer_price AS price_table ON product.id = price_table.product_id
//				INNER JOIN #__pricer_currency_rate AS currency_rate ON price_table.currency_alias = currency_rate.currency_alias
//				WHERE stock.delivery_time > date('0000-00-10')
//					AND product_quantity.quantity > 0
//					AND currency_rate.state = 1
//					AND stock.state = 1
//					AND product.state = 1
//					AND price_table.state = 1
//				GROUP BY product.id
//			) AS tmp
//			ON tmp.id = product.id
//			INNER JOIN #__pricer_currency AS currency ON tmp.currency_alias = currency.alias
//			";

//		$query = "
//			SELECT
//				product.id AS product_id,
//				product.title AS product_title,
//				product.article AS product_article,
//				category.id AS category_id,
//				category.title AS category_title,
//				category.lft AS category_lft,
//				vendor.id AS vendor_id,
//				vendor.title AS vendor_title,
//				distributor.price AS price,
//				distributor.price_rub AS price_rub,
//				distributor.quantity AS quantity,
//				distributor.delivery_time AS delivery_time,
//				distributor.distributor_title AS distributor_title,
//				currency.title AS currency_title,
//				currency.name_html AS currency_html
//			FROM #__pricer_product AS product
//			INNER JOIN #__categories AS category
//				ON product.catid = category.id
//			INNER JOIN #__pricer_vendor AS vendor
//				ON product.vendor_id = vendor.id
//			INNER JOIN (
//				SELECT 
//					product.id,
//					product.article,
//					stock.title AS stock_title,
//					stock.delivery_time AS delivery_time,
//					contractor.title AS distributor_title,
//					product_quantity.quantity AS quantity,
//					min(price_table.price*currency_rate.rate/currency_rate.quantity) AS price_rub,
//					price_table.price,
//					price_table.currency_alias
//				FROM #__pricer_product AS product
//				INNER JOIN #__pricer_product_quantity AS product_quantity ON product_quantity.product_id = product.id
//				INNER JOIN #__pricer_stock AS stock ON product_quantity.stock_id = stock.id
//				INNER JOIN #__pricer_contractor AS contractor ON contractor.id = stock.contractor_id
//				INNER JOIN #__pricer_price AS price_table ON product.id = price_table.product_id
//				INNER JOIN #__pricer_currency_rate AS currency_rate ON price_table.currency_alias = currency_rate.currency_alias
//				WHERE
//					product_quantity.quantity > 0 AND
//					currency_rate.state = 1 AND
//					stock.state = 1 AND
//					product.state = 1 AND
//					price_table.state = 1
//				GROUP BY product.id
//			) AS distributor
//				ON distributor.id = product.id
//			INNER JOIN #__pricer_currency AS currency
//				ON distributor.currency_alias = currency.alias
//		";

		// Если есть хоть какие-то условия выборки
//		if ((-1 != $categoryId) or (true == $vendorId)) {
//			$query .= "WHERE ";
//			$i = 0;
//			if (sizeof($categories) > 1) {
//				$query .= "( ";
//				for ($j=0; $j<sizeof($categories); $j++) {
//					if (0 != $j) {
//						$query .= "OR ";
//					}
//					$query .= "product.catid = {$categories[$j]->id} ";
//				}
//				$query .= ") ";
//				$i++;
//			}
//			elseif ((-1 != $categoryId) and (true == $i)) { // Если указаны не все категории
//				$query .= "AND product.catid = {$categoryId} ";
//				$i++;
//			}
//			elseif ((-1 != $categoryId) and (true != $i)) { // Если указаны не все категории
//				$query .= "product.catid = {$categoryId} ";
//				$i++;
//			}
//			if ((true == $vendorId) and (true == $i)) { // Если указан вендор
//				$query .= "AND vendor.id = {$vendorId} ";
//				$i++;
//			}
//			if ((true == $vendorId) and (true != $i)) { // Если указан вендор
//				$query .= "vendor.id = {$vendorId} ";
//				$i++;
//			}
//		}

		// Группируем
//		$query .= "GROUP BY product_id ";

		// Сортируем
//		if (true != $sortBy) {
//			$query .= "ORDER BY category_lft, vendor_title, product_title ";
//		} else {
//			// TODO: В зависимости от условий сортировки
//		}

//		// Закрываем запрос
//		$query .=";";

	$query = 'SELECT * FROM #__anodos_product;';

		// Выполняем запрос и возвращаем результат
		$db->setQuery($query);
		$products = $db->loadObjectList();
		
		foreach($products as $i => $product) {
//			$products[$i]->price_rub = number_format (round($product->price_rub, 0, PHP_ROUND_HALF_UP), 2, ',', ' ');
			$products[$i]->price = number_format (round($product->price, 2, PHP_ROUND_HALF_UP), 2, ',', ' ');
		}

		return $products;
	}

	private function getCategoriesArray(&$categories, &$catid) {

		// Инициализируем переменные
		$db =& JFactory::getDBO();

		// Получаем список дочерних категорий
		$query = "
			SELECT *
			FROM #__categories
			WHERE
				parent_id = {$catid} AND
				published = 1 AND
				extension = 'com_anodos.product'
			ORDER BY lft;
		";
		$db->setQuery($query);
		$subCategories = $db->loadObjectList();

		// Пробегаем по полученному списку
		if ((true == sizeof($subCategories)) and (true == $subCategories[0]->id)) {
			for ($i=0; $i<sizeof($subCategories); $i++) {

				// Добавляем подкатегорию в общий список
				$categories[] = $subCategories[$i];
				$catid = $subCategories[$i]->id;

				// Уходим в рекурсивку во вложенной категории
				$this->getCategoriesArray($categories, $catid);
			}
		}
	}

	public function getOrders() {
		return 'public function getOrders()';
	}

	public function getOrderProducts() {
		return 'public function getOrderProducts()';
	}

	public function getParameters() {
		return 'public function getParameters()';
	}

	public function getMsg() {
		return $this->msg;
	}

	private function addMsg($msg) {
		$this->msg .= $msg."<br/>";
	}
}
