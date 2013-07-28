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
}
