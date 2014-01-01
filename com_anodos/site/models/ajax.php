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

	// Создает новую категорию продуктов
	// TODO test
	public function createProductCategory($categoryName, $parentId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';

		// Инициализируем переменные
		$result = new JObject;

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - отказано в доступе.';
			return $result;
		}

		// Добавляем категорию в базу
		$result->category = Category::createProductCategory($categoryName, $parentId);

		// Возвращаем результат
		if(isset($result->category->id)) {
			$result->status = 'success';
			$result->text = "Добавлена категория: {$result->category->title}.";
			return $result;
		} else {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - не удалось создать категорию.';
			return $result;
		}
	}

	// Переименовывает категорию
	// TODO test
	public function renameCategory($categoryId, $categoryName) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';

		// Инициализируем переменные
		$result = new JObject;

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - отказано в доступе.';
			return $result;
		}

		// Переименовываем категорию
		$result->category = Category::renameCategory($categoryId, $categoryName);

		// Возвращаем результат
		if(isset($result->category->id)) {
			$result->status = 'success';
			$result->text = "Переименована категория: {$result->category->title}.";
			return $result;
		} else {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - не удалось переименовать категорию.';
			return $result;
		}
	}

	// Удаляет категорию продуктов
	// TODO test
	public function removeProductCategory($categoryId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';
		require_once JPATH_COMPONENT.'/models/helpers/product.php';
		require_once JPATH_COMPONENT.'/models/helpers/price.php';
		require_once JPATH_COMPONENT.'/models/helpers/stock.php';

		// Инициализируем переменные
		$result = new JObject;

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - отказано в доступе.';
			return $result;
		}

		// Получаем список категорий (указанная и все дочерние)
		$categories = Category::getTreeFromCategory($categoryId);
		if (false == $categories) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - не удалось выстроить дерево категорий.';
			return $result;
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

		// Возвращаем результат
		$result->status = 'success';
		$result->text = "Удаление категории завершено.";
		$result->categories = $categories;
		return $result;
	}

	// Привязывает синоним к категории
	// TODO test
	public function linkSynonymToCategory($synonymId, $categoryId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			return false;
		}

		// Привязываем синоним к категории
		Category::linkSynonymToCategory($synonymId, $categoryId);
	 	return true;
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

	// Переименовывает продукт
	// TODO test
	public function renameProduct($productId, $productName) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/product.php';

		// Инициализируем переменные
		$result = new JObject;

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - отказано в доступе.';
			return $result;
		}

		// Переименовываем продукт
		$result->product = Product::renameProduct($productId, $productName);

		// Возвращаем результат
		if(isset($result->product->id)) {
			$result->status = 'success';
			$result->text = "Переименован продукт: {$result->product->name}.";
			return $result;
		} else {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - не удалось переименовать продукт.';
			return $result;
		}
	}

	// Перемещает продукт
	// TODO test
	public function moveProduct($productId, $categoryId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/product.php';

		// Инициализируем переменные
		$result = new JObject;

		// Проверяем право доступа
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - отказано в доступе.';
			return $result;
		}

		// Переименовываем продукт
		$result->product = Product::moveProduct($productId, $categoryId);

		// Возвращаем результат
		if(isset($result->product->id)) {
			$result->status = 'success';
			$result->text = "Перемещен продукт: {$result->product->name}.";
			return $result;
		} else {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - не удалось переместить продукт.';
			return $result;
		}
	}

	public function addToOrder($productId, $clientId, $clientName, $contractorId, $contractorName, $orderId, $orderName, $quantity) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/order.php';
		require_once JPATH_COMPONENT.'/models/helpers/price.php';

		// Инициализируем переменные
		$result = new JObject;

		// Разрешаем только зарегистрированным пользователям
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();
		if ($user->guest) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - отказано в доступе. Авторизуйтесь или зарегистрируйтесь.';
			return $result;
		}

		// Получаем объект заказа
		if (0 === $orderId) { // Новый заказ
			$order = Order::createOrder($clientId, $clientName, $contractorId, $contractorName, $orderName);
		} else { // Существующий заказ
			$order = Order::getOrderById($orderId);
			// TODO проверяем есть ли заказ в базе и права на его редактирование
		}

		// Проверяем объект заказа
		if (isset($order->id)) {
			$orderId = $order->id;
		} else {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - не удалось получить объект заказа.';
			return $result;
		}

		// Получаем объект цены продукта
		$price = Price::getPrice($productId);

		// Проверяем объект цены продукта
		if (!isset($price->price_rub_out)) {
			$result->status = 'danger';
			$result->text = 'Error #'.__LINE__.' - не удалось получить объект цены.';
			return $result;
		}

		// Добавляем строку в заказ
		Order::addOrderLine($order, $price, $quantity);

		// Получаем список строк заказа
		$orderLines = Order::getLinesFromOrder($order->id);

		// Готовим ответ
		$result->status = 'success';
		$result->text = 'Добавлено';
		$result->order = $order;
		$result->lines = $orderLines;

		// Возвращаем результат
		return $result;
	}

	public function setCategorySynonymState($synonymId, $state) {
		
	}



}
