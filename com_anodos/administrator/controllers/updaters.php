<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class AnodosControllerUpdaters extends JControllerAdmin {

	public function getModel($name = 'updater', $prefix = 'AnodosModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function saveOrderAjax() {

		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return) {
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	public function update() {

		// Проверяем подмену запроса
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Инициализируем переменные
		$user = JFactory::getUser();
		$ids = JRequest::getVar('cid', array(), 'post');

		// Подключаем модель
		$model = $this->getModel('Updaters', 'AnodosModel');
		$return = $model->update($ids);
		if ($return === false) { // Загрузка прошла неудачно
			$message = JText::sprintf('COM_PRICER_ERROR_LOADING_FAILED'.' '.$model->getError(), $model->getError());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message, 'error');
			return false;
		} else { // Загрузка прошла удачно
			$message =  JText::plural($model->getMsg(), $model->getMsg());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message);
			return true;
		}
	}
}
