<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelAjax extends JModelList {

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

	// Выводит список id, name производителей из указанной категории
	public function getVendorsFromCategory($categoryId) {

		// Подключаем библиотеки
//		require_once JPATH_COMPONENT.'/helpers/anodos.php';
//		require_once JPATH_COMPONENT.'/models/helpers/category.php';
//		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';

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

	// Выводит список id, name производителей из указанной категории
	public function renameCategory($categoryId, $categoryName) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.');
			return false;
		}

		// Переименовываем категорию
		$result = Category::renameCategory($categoryId, $categoryName);

		// Возвращаем список удаленных категорий
		return $result;
	}

	public function removeProductCategory($categoryId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';
		require_once JPATH_COMPONENT.'/models/helpers/product.php';
		require_once JPATH_COMPONENT.'/models/helpers/price.php';
		require_once JPATH_COMPONENT.'/models/helpers/stock.php';

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.');
			return false;
		}

		// Получаем список категорий (указанная и все дочерние)
		$categories = Category::getTreeFromCategory($categoryId);
		if (false == $categories) {
			return false;
		}

		// Проходим по каждой категории
		foreach($categories as $i => $c) {

			// Отвязываем все синонимы
			Category::unlinkSynonymOfCategory($c->id);

			// Получаем полный список продуктов в категории
			$products = Product::getProductsFromCategory($c->id);

			// Проходим по каждому продукту
			foreach($products as $j => $p) {

				// Удаляем цены
				Price::removePriceOfProduct($p->id);

				// Удаляем состояние складов
				Stock::removeQuantityOfProduct($p->id);

				// Удаляем продукт
				Product::removeProduct($p->id);
			}

			// Удаляем категорию
			Category::removeCategory($c->id);
		}

		// Перестраиваем дерево категорий
		$category = JTable::getInstance('Category');
		$category->rebuildPath(1);

		// Возвращаем список удаленных категорий
		return $categories;
	}

	public function renameProduct($productId, $productName) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/product.php';

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.');
			return false;
		}

		// Переименовываем продукт
		$result = Product::renameProduct($productId, $productName);

		// Возвращаем объект переименованного продукта
		return $result;
	}
}
