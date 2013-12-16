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

	public function createProductCategory($categoryName, $parentId) {

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

		// Добавляем категорию в базу
		$result = Category::createProductCategory($categoryName, $parentId);

		return $result;
	}

	public function createVendor($vendorName) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.');
			return false;
		}

		// Вносим необходимые правки
		$vendor->asset_id = 0;
		$vendor->name = $vendorName;
		$vendor->alias = JFilterOutput::stringURLSafe($vendorName);
		$vendor->published = 1;
		$vendor->created_by = $user->id;

		// Проверяем наличие такого производителя в базе
		if (isset(Vendor::getVendorFromAlias($vendor->alias)->id)) {
			return false;
		}

		// Добавляем производителя в базу
		$result = Vendor::createVendor($vendor);
		return $result;
	}

	public function linkSynonymToCategory($synonymId, $categoryId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.</div>');
			return false;
		}

		// Привязываем синоним к категории
		Category::linkSynonymToCategory($synonymId, $categoryId);
	 	return true;
	}

	public function linkSynonymToVendor($synonymId, $vendorId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.</div>');
			return false;
		}

		// Привязываем синоним к производителю
		Vendor::linkSynonymToVendor($synonymId, $vendorId);
	 	return true;
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

	public function moveProduct($productId, $categoryId) {

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
		$result = Product::moveProduct($productId, $categoryId);

		// Возвращаем объект перемещенного продукта
		return $result;
	}

	public function addToOrder($productId, $orderId, $orderName, $clientId, $clientName, $quantity) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/order.php';

		$result = new JObject;

		// Разрешаем только зарегистрированным пользователям
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if ($user->guest) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - отказано в доступе. Авторизуйтесь или зарегистрируйтесь.';
			return $result;
		}

		if (0 === $orderId) { // Новый заказ
			$order = Order::createOrder($orderName, $clientId, $clientName);
			if (isset($order->id)) {
				$orderId = $order->id;
			} else {
				$result->status = 'danger';
				$result->text = 'Error #'.__LINE__.' - не удалось создать новый заказ.';
				return $result;
			}
		} else { // Существующий заказ
			// TODO проверяем есть ли заказ в базе и права на его редактирование

		}




		// Готовим ответ
		$result->status = 'success';
		$result->text = "Тестовый вывод: \$productId = $productId, \$orderId = $orderId, \$orderName = $orderName, \$clientId = $clientId, \$clientName = $clientName, \$quantity = $quantity";

		// Возвращаем результат
		return $result;
	}
}
