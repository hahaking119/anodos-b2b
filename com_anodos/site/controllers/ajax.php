<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

class AnodosControllerAjax extends AnodosController {

	public function createProductCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$name = JRequest::getVar('name', '');
		$parent = JRequest::getVar('parent', 1);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->createProductCategory($name, $parent);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	public function linkSynonymToCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$synonym = JRequest::getVar('synonym');
		$category = JRequest::getVar('category', NULL);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->linkSynonymToCategory($synonym, $category);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	public function renameCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$id = JRequest::getVar('id', false);
		$name = JRequest::getVar('name', false);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->renameCategory($id, $name);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	public function removeProductCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$id = JRequest::getVar('id', 0);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->removeProductCategory($id);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	public function renameProduct() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$id = JRequest::getVar('id', false);
		$name = JRequest::getVar('name', false);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->renameProduct($id, $name);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	public function moveProduct() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$id = JRequest::getVar('id', false);
		$category = JRequest::getVar('category', false);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->moveProduct($id, $category);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	public function createVendor() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$name = JRequest::getVar('name', '');

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->createVendor($name);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	public function linkSynonymToVendor() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$synonymId = JRequest::getVar('synonym');
		$vendorId = JRequest::getVar('vendor', NULL);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->linkSynonymToVendor($synonymId, $vendorId);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	public function getVendorsFromCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// TODO ??
		JFactory::getDocument()->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');

		// Получаем данные
		$category = JRequest::getVar('category', 0);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->getVendorsFromCategory($category);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	// TODO функция не представлена в модели
	public function getOrdersFromClient() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// TODO ??
		JFactory::getDocument()->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');

		// Получаем данные
		$client = JRequest::getVar('client', 0);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->getOrdersFromClient($client);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	// TODO функция не представлена в модели
	public function getFromOrder() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// TODO ??
		JFactory::getDocument()->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');

		// Получаем данные
		$order = JRequest::getVar('order', 0);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->getProductFromOrder($order);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}

	// TODO функция не представлена в модели
	public function addToOrder() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// TODO ??
		JFactory::getDocument()->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');

		// Получаем данные
		$productId = JRequest::getInt('productId', 0);
		$orderId = JRequest::getInt('orderId', 0);
		$orderName = JRequest::getVar('orderName', '');
		$clientId = JRequest::getInt('clientId', 0);
		$clientName = JRequest::getVar('orderName', '');
		$quantity = JRequest::getInt('quantity', 1);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$result = $model->addToOrder($productId, $orderId, $orderName, $clientId, $clientName, $quantity);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);
		JFactory::getApplication()->close();
	}
}
