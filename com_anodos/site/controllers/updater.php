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
		$model->reportToMail();
//		echo "<!DOCTYPE html><html><head><meta charset=\"utf-8\"><title>Updater</title><link rel=\"stylesheet\" href=\"/components/com_anodos/css/uikit.min.css\" /><script src=\"//code.jquery.com/jquery-1.10.2.min.js\"></script><script src=\"/components/com_anodos/js/uikit.min.js\"></script></head><body>$msg</body></html>";

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
