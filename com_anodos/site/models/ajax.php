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
	public function deleteProductCategory($id) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
//		require_once JPATH_COMPONENT.'/models/helpers/category.php';
//		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.');
			return false;
		}

		// Инициализируем переменные
		$db = JFactory::getDBO();

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

		return $categories;

		// TODO Отвязываем все синонимы

		// TODO Получаем полный список товаров в категориях

		// TODO Удаляем цены

		// TODO Удаляем состояние складов

		// TODO Удаляем товары

		// TODO Удаляем категории

	}
}
