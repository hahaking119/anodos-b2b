<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

class AnodosControllerAjax extends AnodosController {

	// TODO функция представлена в другой модели
	public function addProductCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$name = JRequest::getVar('name', '');
		$parent = JRequest::getVar('parent', 1);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$model->addProductCategory($name, $parent);

		// Выводим сообщения из модели
		echo json_encode($model->getMsg());

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	// Редактирование категории продуктов
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

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	// Удаление категории продуктов
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

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	// TODO функция представлена в другой модели
	public function addVendor() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$name = JRequest::getVar('name', '');

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$model->addVendor($name);

		// Выводим сообщения из модели
		echo json_encode($model->getMsg());

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	// TODO функция представлена в другой модели
	public function linkSynonymToCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$synonymId = JRequest::getVar('synonym');
		$categoryId = JRequest::getVar('category', NULL);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$model->linkSynonymToCategory($synonymId, $categoryId);

		// Выводим сообщения из модели
		echo json_encode($model->getMsg());

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	// TODO функция представлена в другой модели
	public function linkSynonymToVendor() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$synonymId = JRequest::getVar('synonym');
		$vendorId = JRequest::getVar('vendor', NULL);

		// Передаем данные в модель
		$model = parent::getModel('Ajax', 'AnodosModel', array('ignore_request' => true));
		$model->linkSynonymToVendor($synonymId, $vendorId);

		// Выводим сообщения из модели
		echo json_encode($model->getMsg());

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	// Возвращает список производителей, товар которых есть в указанной категории
	public function getVendorsFromCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');

		// Получаем данные
		$category = JRequest::getVar('category', 0);

		// Передаем данные в модель
		$model = parent::getModel('Products', 'AnodosModel', array('ignore_request' => true));
		$result = $model->getVendorsFromCategory($category);

		// Выводим сообщения из модели
		echo new JResponseJson($result, JText::_('COM_COMPONENT_MY_TASK_ERROR'), true);

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

}
