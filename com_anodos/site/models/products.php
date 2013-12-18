<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelProducts extends JModelList {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	protected $msg;

	public function getMsg() {
		return $this->msg;
	}

	private function addMsg($msg) {
		$this->msg .= $msg."<br/>";
	}

	protected $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<items>";

	public function getXML() {
		$this->xml .= "\n</items>";
		return $this->xml;
	}

	private function addXML($element) {
		$this->xml .= "\n".$element;
	}

	public function getCategory() {
		return JRequest::getVar('category', '0');
	}

	public function getSubCategories() {
		return JRequest::getVar('subcategories', '0');
	}

	public function getVendor() {
		return JRequest::getVar('vendor', 'all');
	}

	public function getSearch() {
		return JRequest::getVar('search', NULL);
	}

	public function getCategoryName() {

		$id = JRequest::getVar('category', 0);

		if (('all' !== $id) and (0 != $id)) {

			// Подключаемся к базе
			$db = JFactory::getDBO();

			// Исключаем инъекцию
			$id = $db->quote($id);

			// Выполняем запрос
			$query = "SELECT title FROM #__categories WHERE id = {$id} AND extension = 'com_anodos';";
			$db->setQuery($query);
			return $db->loadResult();
		} else {
			return NULL;
		}
	}

	public function getVendorName() {

		$id = JRequest::getVar('vendor', 'all');

		if (('all' !== $id) and (0 != $id)) {

			// Подключаемся к базе
			$db = JFactory::getDBO();

			// Исключаем инъекцию
			$id = $db->quote($id);

			// Выполняем запрос
			$query = "SELECT name FROM #__anodos_partner WHERE id = {$id} AND vendor = 1;";
			$db->setQuery($query);
			return $db->loadResult();
		} else {
			return NULL;
		}
	}

	// Возвращает дерево категорий (в виде вложенных списков)
	public function getCategories() {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$dom = new DOMDocument('1.0');

		// Добавляем div
		$dom->appendChild($div = $dom->createElement('div'));
		$div->setAttribute('id', 'categories-tree');

		// Рекурсивно заполняем вложенные списки категорий
		$this->categorySQLtoDOM($dom, $div);

		// Возвращаем результат
		return $dom->saveHTML();
	}

	// Рекурсивно заполняет вложенные списки категорий
	private function categorySQLtoDOM(&$dom, &$element) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

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
				extension = 'com_anodos'
			ORDER BY lft;
		";

		$db->setQuery($query);
		$category = $db->loadObjectList();

		// Если выборка не пустая - обрабатываем каждый элемент
		if ((true == sizeof($category)) and (true == $category[0]->id)) {
			$newUL = $dom->createElement('ul');
			$element->appendChild($newUL);
			if (1 == $parentId) {
				$newUL->appendChild($newLI = $dom->createElement('li', ''));
					$newLI->appendChild($span = $dom->createElement('span', '&nbsp;'));
					$newLI->appendChild($span = $dom->createElement('span', JText::_('COM_ANODOS_ALL_CATEGORIES')));
						$span->setAttribute('id', 'category-text-all');
						$span->setAttribute('class', 'category-name uk-modal-close');
						$span->setAttribute('data-category-id', 'all');
			}
			for ($i=0; $i<sizeof($category); $i++) {
				$newUL->appendChild($newLI = $dom->createElement('li', ''));
					// Плюсик добавляем только если есть дочки
					$query = "
						SELECT *
						FROM #__categories
						WHERE parent_id = {$category[$i]->id}
						AND extension = 'com_anodos'
						ORDER BY lft;
					";
					$db->setQuery($query);
					$subCategory = $db->loadObjectList();
					if ((true == sizeof($subCategory)) and (true == $subCategory[0]->id)) {
						$newLI->appendChild($span = $dom->createElement('span', '&#8862;'));
							$span->setAttribute('id', "category-square-{$category[$i]->id}");
							$span->setAttribute('class', 'category-square');
							$span->setAttribute('data-category-id', $category[$i]->id);
						$newLI->appendChild($span = $dom->createElement('span', '&nbsp;'));
						$newLI->setAttribute('class', 'closed');
					} else {
						$newLI->appendChild($span = $dom->createElement('span', '&nbsp;'));
					}
					$newLI->appendChild($span = $dom->createElement('span', $category[$i]->title));
						$span->setAttribute('id', "category-text-{$category[$i]->id}");
						$span->setAttribute('class', 'category-name uk-modal-close');
						$span->setAttribute('data-category-id', $category[$i]->id);
				$newLI->setAttribute('id', "category-{$category[$i]->id}");
				$newLI->setAttribute('data-category-id', $category[$i]->id);
				$this->categorySQLtoDOM($dom, $newLI);
			}
		}
	}

	public function getVendors() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__anodos_partner WHERE vendor = 1 AND state = 1 ORDER BY name ASC;";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getProducts() {

		// Инициализируем переменные
		$componentParams = JComponentHelper::getParams('com_anodos');
		$categories = array();
		$category = $this->getCategory();
		$subCategories = $this->getSubCategories();
		$vendor = $this->getVendor();
		$search = $this->getSearch();

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Если категория не выбрана - возвращаем NULL
		if (('0' == $category) and (!isset($search))) {
			return NULL;
		}

		// Готовим запрос выборки продуктов
		$query = '';

		$query .="
			SELECT
				product.id AS product_id,
				product.name AS product_name,
				product.article AS product_article,
				category.id AS category_id,
				category.title AS category_name,
				category.lft AS category_lft,
				vendor.id AS vendor_id,
				vendor.name AS vendor_name,
				price.price AS price,
				MIN(price.price*rate.rate/rate.quantity) AS price_rub,
				currency.id AS currency_id,
				currency.name_html AS currency_name,
				quantity.quantity AS quantity,
				stock.id AS stock_id,
				stock.name AS stock_name,
				stock.delivery_time_min AS delivery_time_min,
				stock.delivery_time_max AS delivery_time_max
			FROM #__anodos_product AS product
			INNER JOIN #__categories AS category ON product.category_id = category.id
			INNER JOIN #__anodos_partner AS vendor ON product.vendor_id = vendor.id
			INNER JOIN #__anodos_price AS price ON price.product_id = product.id	
			INNER JOIN #__anodos_currency AS currency ON currency.id = price.currency_id
			INNER JOIN #__anodos_currency_rate AS rate ON rate.currency_id = currency.id
			INNER JOIN #__anodos_product_quantity AS quantity ON quantity.product_id = product.id
			INNER JOIN #__anodos_stock AS stock ON stock.id = quantity.stock_id
			WHERE price.stock_id = quantity.stock_id
			AND quantity.quantity != 0
		";

		// Только активные производители и категории
		$prefix = ' AND ';
		$query .= "{$prefix} vendor.state = 1 ";
		$prefix = ' AND ';

		// Только активные категории
		$query .= "{$prefix} category.published = 1 ";
		$prefix = ' AND ';

		// Фильтр по указанному производителю
		if (('all' !== $vendor) or (0 != $vendor)) {
			$vendor = $db->quote($vendor);
			$query .= "{$prefix} vendor.id = {$vendor} ";
			$prefix = ' AND ';
		}

		// Если категория не выбрана (но выбрана строка поиска) или выбраны все категории
		if (('all' !== $category) and ('0' !== $category)) {

			// Исключаем инъекцию
			$category = $db->quote($category);

			// Получаем указанную категорию
			$categoryQuery = "
				SELECT *
				FROM #__categories
				WHERE #__categories.id = {$category} AND extension = 'com_anodos'
				ORDER BY lft;";
			$db->setQuery($categoryQuery);
			$categories = $db->loadObjectList();

			// Если указано "включать подкатегории"
			if ('1' == $subCategories) {

				// Получаем массив категорий (указанная и все потомки)
				$categoryQuery = "
					SELECT *
					FROM #__categories
					WHERE LOCATE('{$categories[0]->path}', #__categories.path) = 1 AND extension = 'com_anodos'
					ORDER BY lft;";
				$db->setQuery($categoryQuery);
				$categories = $db->loadObjectList();
			}

			$prefix .= " (";
			$sufix = ' ';
			// Добавляем условия в запрос
			foreach($categories as $i => $c) {
				$query .= $prefix.'category.id = '.$c->id.' ';
				$prefix = "OR ";
				$sufix = ') ';
			}
			// Закрываем условие
			$query .= $sufix;

			// Правим префикс
			$prefix = " AND ";
		}



		// Если задана строка поиска
		if (isset($search)) {
			// Исключаем инъекцию
			$search = $db->quote($search);
			// Добавляем условие выборки
			$query .= $prefix." ((LOCATE({$search}, product.name) != 0) OR (LOCATE({$search}, product.article) != 0)) ";

			// Правим префикс
			$prefix = " AND ";
		}

		// Групируем по продукту
		$query .= " GROUP BY product_id";

		// Сортируем
		$query .= " ORDER BY category_lft, vendor_name, product_name";

		// Закрываем запрос
		$query .=";";

		// Выполняем запрос
		$db->setQuery($query);
		$products = $db->loadObjectList();

		// Получаем стандартную наценку
		$marginStd = $componentParams->get('margin-std', 8);

		// Обрабатываем результат
		foreach($products as $i => $product) {

			// Входная цена в рублях
			$products[$i]->price_rub_in = number_format (round($product->price_rub, 0, PHP_ROUND_HALF_UP), 2, ',', ' ');
			$products[$i]->price_rub_out = number_format (round($product->price_rub * (100 + $marginStd) / 100, 0, PHP_ROUND_HALF_UP), 2, ',', ' ');
			$products[$i]->price_in = number_format (round($product->price, 2, PHP_ROUND_HALF_UP), 2, ',', ' ');
			$products[$i]->price_out = number_format (round($product->price * (100 + $marginStd) / 100, 2, PHP_ROUND_HALF_UP), 2, ',', ' ');
		}

		return $products;
	}

	// Возвращает список категорий для модального окна добавления
	public function getParentCategoryList() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Загружаем категории из базы
		$query = $db->getQuery(true);
		$query->select('id, lft, level, path, extension, title, alias, published');
		$query->from('#__categories');
		$query->where("extension = 'com_anodos'");
		$query->order('lft');
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		// Проводим изменение имени с учетом уровня вложенности
		for ($i=0; $i<sizeof($categories); $i++) {
			$prefix = '';
			for ($k=1; $k<$categories[$i]->level; $k++) {
				$prefix = '- ' . $prefix;
			}
			$categories[$i]->title = $prefix . $categories[$i]->title;
		}
		return $categories;
	}

	// Выводит список id, name производителей из указанной категории
	public function getVendorsFromCategory($categoryId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';
		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// TODO test
		if (('all' === $categoryId) or (0 === $categoryId)) {
			$query = "
				SELECT
					vendor.id AS vendor_id,
					vendor.name AS vendor_name
				FROM #__anodos_partner AS vendor
				WHERE vendor.vendor = 1 AND vendor.state = 1
				ORDER BY vendor_name ASC;";
			$db->setQuery($query);
			return $db->loadObjectList();

		} else {

			// Получаем указанную категорию
			$query = "
				SELECT *
				FROM #__categories
				WHERE #__categories.id = {$categoryId} AND extension = 'com_anodos'
				ORDER BY lft;";
			$db->setQuery($query);
			$category = $db->loadObject();

			// Получаем массив категорий (указанная и все потомки)
			$query = "
				SELECT *
				FROM #__categories
				WHERE LOCATE('{$category->path}', #__categories.path) = 1 AND extension = 'com_anodos'
				ORDER BY lft;";
			$db->setQuery($query);
			$categories = $db->loadObjectList();

			// Открываем запрос
			$query = "
				SELECT
					vendor.id AS vendor_id,
					vendor.name AS vendor_name
				FROM #__anodos_partner AS vendor
				INNER JOIN #__anodos_product AS product ON product.vendor_id = vendor.id
				INNER JOIN #__anodos_product_quantity AS quantity ON quantity.product_id = product.id
				WHERE vendor.vendor = 1 AND quantity.quantity != 0 ";
			$prefix = "AND (";
			$sufix = ' ';

			// Добавляем условия в запрос
			foreach($categories as $i => $c) {
				$query .= $prefix.'product.category_id = '.$c->id.' ';
				$prefix = "OR ";
				$sufix = ') ';
			}
			$query .= $sufix.'GROUP BY vendor_id ORDER BY vendor_name ASC;';

			// Выполняем запрос
			$db->setQuery($query);
			return $db->loadObjectList();
		}
	}

	public function getClients() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_partner
			WHERE client = 1
			ORDER BY name ASC;";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getContractors() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_contractor
			ORDER BY name ASC;";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getOrders() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_order
			WHERE state = 1
			ORDER BY name ASC;";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
