<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

class AnodosControllerUpdater extends AnodosController {

	public function update() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$id = JRequest::getVar('id', null);
		$key = JRequest::getVar('key', null);

		// Передаем данные в модель
		$model = parent::getModel('Updater', 'AnodosModel', array('ignore_request' => true));
		$model->update($id, $key);

		// Выводим сообщения из модели
		$msg = $model->getMsg();
		echo $msg;
		$model->reportToMail();

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	public function addProductCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$name = JRequest::getVar('name', '');
		$parent = JRequest::getVar('parent', 1);

		// Передаем данные в модель
		$model = parent::getModel('Updater', 'AnodosModel', array('ignore_request' => true));
		$model->addProductCategory($name, $parent);

		// Выводим сообщения из модели
		$msg = $model->getMsg();
		echo $msg;

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	public function addVendor() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$name = JRequest::getVar('name', '');

		// Передаем данные в модель
		$model = parent::getModel('Updater', 'AnodosModel', array('ignore_request' => true));
		$model->addVendor($name);

		// Выводим сообщения из модели
		$msg = $model->getMsg();
		echo $msg;

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	public function linkSynonymToCategory() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$synonymId = JRequest::getVar('synonym');
		$categoryId = JRequest::getVar('category', NULL);

		// Передаем данные в модель
		$model = parent::getModel('Updater', 'AnodosModel', array('ignore_request' => true));
		$model->linkSynonymToCategory($synonymId, $categoryId);

		// Выводим сообщения из модели
		$msg = $model->getMsg();
		echo $msg;

		// Закрываем приложение
		JFactory::getApplication()->close();
	}

	public function linkSynonymToVendor() {

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Получаем данные
		$synonymId = JRequest::getVar('synonym');
		$vendorId = JRequest::getVar('vendor', NULL);

		// Передаем данные в модель
		$model = parent::getModel('Updater', 'AnodosModel', array('ignore_request' => true));
		$model->linkSynonymToVendor($synonymId, $vendorId);

		// Выводим сообщения из модели
		$msg = $model->getMsg();
		echo $msg;

		// Закрываем приложение
		JFactory::getApplication()->close();
	}
}
