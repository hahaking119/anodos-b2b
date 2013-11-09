<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelProducts extends JModelList {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	protected $msg;

	public function getCategory() {
		return JRequest::getVar('category', 0);
	}

	public function getSubCategories() {
		return JRequest::getVar('subcategories', '');
	}

	public function getVendor() {
		return JRequest::getVar('vendor', 'all');
	}

	public function getCategoryName() {

		$id = JRequest::getVar('category', 0);

		if (('all' !== $id) and (0 != $id)) {

			// Подключаемся к базе
			$db = JFactory::getDBO();

			// Исключаем инъекцию
			$id = $db->quote($id);

			// Выполняем запрос
			$query = "SELECT title FROM #__categories WHERE id = {$id} AND extension = 'com_anodos.product';";
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
						$span->setAttribute('id', "category-text-all");
						$span->setAttribute('onClick', "setCategorySelected(\"all\")");
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
						$newLI->setAttribute('class', "closed");
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

	public function getVendors() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__anodos_partner WHERE vendor = 1 ORDER BY name ASC;";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getProducts() {

		// Инициализируем переменные
		$categories = array();
		$category = $this->getCategory();
		$subCategories = $this->getSubCategories();
		$vendor = $this->getVendor();
// TODO	$sortBy = JRequest::getInt('sort');

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Если категория не выбрана - возвращаем NULL
		if ('0' == $category) {
			return NULL;
		}

		// Если категория выбрана
		if ('all' !== $category) {

			$query = "
				SELECT *
				FROM #__categories
				WHERE #__categories.id = {$category} AND extension = 'com_anodos.product'
				ORDER BY lft;";
			$db->setQuery($query);
			$categories = $db->loadObjectList();

			// Если закано "включать подкатегории"
			if ('on' == $subCategories) {
				$catid = $category;

				// Уходим в рекурсивную функцию
				$this->getCategoriesArray($categories, $catid);
			}
		}

		// Готовим запрос выборки продуктов
		$query = '';

		/***********************************
		// Пример работающего запроса
		
		SELECT
	product.id AS product_id,
	product.name AS product_name,
	product.article AS product_article,
	category.id AS category_id,
	category.title AS category_name,
	category.lft AS category_lft,
	vendor.id AS vendor_id,
	vendor.name AS vendor_name,
	state.price_rub AS price_rub,
	state.price AS price,
	state.currency_id AS currency_id,
	state.currency_name AS currency_name,
	state.stock_id AS stock_id,
	state.stock_name AS stock_name,
	state.quantity AS quantity,
	state.delivery_time_min AS delivery_time_min,
	state.delivery_time_max AS delivery_time_max
FROM #__anodos_product AS product
INNER JOIN #__categories AS category ON product.category_id = category.id
INNER JOIN #__anodos_partner AS vendor ON product.vendor_id = vendor.id
INNER JOIN ( -- state DISTINCT ON (product_id)
	SELECT DISTINCT ON (product_id)
		product.id AS product_id,
		price.price_rub AS price_rub,
		price.price AS price,
		price.currency_id AS currency_id,
		price.currency_name AS currency_name,
		quantity.stock_id AS stock_id,
		quantity.stock_name AS stock_name,
		quantity.quantity AS quantity,
		quantity.delivery_time_min AS delivery_time_min,
		quantity.delivery_time_max AS delivery_time_max
	FROM #__anodos_product AS product
	INNER JOIN ( -- price DISTINCT ON (product_id, stock_id)
		SELECT DISTINCT ON (product_id, stock_id)
			product.id AS product_id,
			price.stock_id AS stock_id,
			price.price*rate.rate AS price_rub,
			price.price AS price,
			price.created AS price_created,
			currency.id AS currency_id,
			currency.name_html AS currency_name,
			rate.rate AS rate
		FROM #__anodos_product AS product
		INNER JOIN #__anodos_price AS price ON price.product_id = product.id
		INNER JOIN #__anodos_currency AS currency ON currency.id = price.currency_id
		INNER JOIN ( -- rate DISTINCT ON (currency_id)
			SELECT DISTINCT ON (currency_id)
				currency.id AS currency_id,
				rate.rate::real/rate.quantity::real AS rate,
				rate.created AS rate_created
			FROM #__anodos_currency AS currency
			INNER JOIN #__anodos_currency_rate AS rate ON rate.currency_id = currency.id
			WHERE rate.state = 1
			ORDER BY currency_id ASC, rate_created DESC
			) AS rate ON rate.currency_id = currency.id
		WHERE price.state = 1
		ORDER BY product_id ASC, stock_id ASC, price_created DESC
	) AS price ON price.product_id = product.id
	INNER JOIN ( -- quantity DISTINCT ON (product_id, stock_id)
		SELECT DISTINCT ON (product_id, stock_id)
			product.id AS product_id,
			quantity.stock_id AS stock_id,
			quantity.quantity AS quantity,
			quantity.created AS quantity_created,
			stock.name AS stock_name,
			stock.delivery_time_min AS delivery_time_min,
			stock.delivery_time_max AS delivery_time_max
		FROM #__anodos_product AS product
		INNER JOIN #__anodos_product_quantity AS quantity ON quantity.product_id = product.id
		INNER JOIN #__anodos_stock AS stock ON stock.id = quantity.stock_id
		WHERE quantity.state = 1 AND quantity > 0
		ORDER BY product_id ASC, stock_id ASC, quantity_created DESC
	) AS quantity ON quantity.product_id = product.id
	WHERE price.stock_id = quantity.stock_id
	ORDER BY product_id ASC, price_rub DESC, quantity.delivery_time_min ASC
) AS state ON state.product_id = product.id
WHERE category.id = 138 AND vendor.id = 1
ORDER BY category_lft ASC, vendor_name ASC;
		
		************************************/


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
			state.price_rub AS price_rub,
			state.price AS price,
			state.currency_id AS currency_id,
			state.currency_name AS currency_name,
			state.stock_id AS stock_id,
			state.stock_name AS stock_name,
			state.quantity AS quantity,
			state.delivery_time_min AS delivery_time_min,
			state.delivery_time_max AS delivery_time_max
		FROM #__anodos_product AS product
		INNER JOIN #__categories AS category ON product.category_id = category.id
		INNER JOIN #__anodos_partner AS vendor ON product.vendor_id = vendor.id
		INNER JOIN ( -- state DISTINCT ON (product_id)
			SELECT DISTINCT ON (product_id)
				product.id AS product_id,
				price.price_rub AS price_rub,
				price.price AS price,
				price.currency_id AS currency_id,
				price.currency_name AS currency_name,
				quantity.stock_id AS stock_id,
				quantity.stock_name AS stock_name,
				quantity.quantity AS quantity,
				quantity.delivery_time_min AS delivery_time_min,
				quantity.delivery_time_max AS delivery_time_max
			FROM #__anodos_product AS product
			INNER JOIN ( -- price DISTINCT ON (product_id, stock_id)
				SELECT DISTINCT ON (product_id, stock_id)
					product.id AS product_id,
					price.stock_id AS stock_id,
					price.price*rate.rate AS price_rub,
					price.price AS price,
					price.created AS price_created,
					currency.id AS currency_id,
					currency.name_html AS currency_name,
					rate.rate AS rate
				FROM #__anodos_product AS product
				INNER JOIN #__anodos_price AS price ON price.product_id = product.id
				INNER JOIN #__anodos_currency AS currency ON currency.id = price.currency_id
				INNER JOIN ( -- rate DISTINCT ON (currency_id)
					SELECT DISTINCT ON (currency_id)
						currency.id AS currency_id,
						rate.rate::real/rate.quantity::real AS rate,
						rate.created AS rate_created
					FROM #__anodos_currency AS currency
					INNER JOIN #__anodos_currency_rate AS rate ON rate.currency_id = currency.id
					WHERE rate.state = 1
					ORDER BY currency_id ASC, rate_created DESC
					) AS rate ON rate.currency_id = currency.id
				WHERE price.state = 1
				ORDER BY product_id ASC, stock_id ASC, price_created DESC
			) AS price ON price.product_id = product.id
			INNER JOIN ( -- quantity DISTINCT ON (product_id, stock_id)
				SELECT DISTINCT ON (product_id, stock_id)
					product.id AS product_id,
					quantity.stock_id AS stock_id,
					quantity.quantity AS quantity,
					quantity.created AS quantity_created,
					stock.name AS stock_name,
					stock.delivery_time_min AS delivery_time_min,
					stock.delivery_time_max AS delivery_time_max
				FROM #__anodos_product AS product
				INNER JOIN #__anodos_product_quantity AS quantity ON quantity.product_id = product.id
				INNER JOIN #__anodos_stock AS stock ON stock.id = quantity.stock_id
				WHERE quantity.state = 1 AND quantity > 0
				ORDER BY product_id ASC, stock_id ASC, quantity_created DESC
			) AS quantity ON quantity.product_id = product.id
			WHERE price.stock_id = quantity.stock_id
			ORDER BY product_id ASC, price_rub DESC, quantity.delivery_time_min ASC
		) AS state ON state.product_id = product.id
		";

		// Условия выборки
		$prefix = ' WHERE';
		if (('all' !== $vendor) or (0 != $vendor)) {
			$vendor = $db->quote($vendor);
			$query .= "{$prefix} vendor.id = {$vendor} ";
			$prefix = ' AND';
		}

		// Если категория выбрана
		if ('all' !== $category) {
			if (sizeof($categories) > 1) {
				$query .= "{$prefix} ( ";
				$prefix = ' ';
				for ($j=0; $j<sizeof($categories); $j++) {
					if (0 < $j) {
						$prefix = ' OR';
					}
					$query .= "{$prefix} category.id = {$categories[$j]->id} ";
				}
				$query .= ") ";
			} else {
				$query .= "{$prefix} category.id = {$category} ";
				$prefix = ' AND';
			}
		}

		// Сортируем
//		if (true != $sortBy) {
			$query .= " ORDER BY category_lft, vendor_name, product_name";
//		} else {
			// TODO: В зависимости от условий сортировки
//		}

		// Закрываем запрос
		$query .=";";

		// Выполняем запрос и возвращаем результат
		$db->setQuery($query);
		$products = $db->loadObjectList();
		
		foreach($products as $i => $product) {
			$products[$i]->price_rub = number_format (round($product->price_rub, 0, PHP_ROUND_HALF_UP), 2, ',', ' ');
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

	// Возвращает список категорий для модального окна добавления
	public function getParentCategoryList() {

		// Инициализируем переменные
		$db =& JFactory::getDBO();

		// Загружаем категории из базы
		$query = $db->getQuery(true);
		$query->select('id, lft, level, path, extension, title, alias, published');
		$query->from('#__categories');
		$query->where("extension = 'com_anodos.product'");
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
