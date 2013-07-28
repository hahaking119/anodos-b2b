<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class AnodosControllerCategorySynonyms extends JControllerAdmin {

	public function getModel($name = 'categorysynonym', $prefix = 'AnodosModel') {
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

	public function save() {

		// Проверяем подмену запроса
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Инициализируем переменные
		$user = JFactory::getUser();
		$synonyms = JRequest::getVar('synonyms', array(), 'post');
		$categories = JRequest::getVar('categories', array(), 'post');

		// Подключаем модель
		$model = $this->getModel('CategorySynonyms', 'AnodosModel');
		$return = $model->save($synonyms, $categories);

		// Отображаем результат
		if ($return === false) { // Загрузка прошла неудачно
			$message = JText::sprintf('COM_ANODOS_ERROR_SAVE_FAILED'.' '.$model->getError(), $model->getError());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message, 'error');
			return false;
		} else { // Загрузка прошла удачно
			$message =  JText::plural($model->getMsg(), $model->getMsg());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message);
			return true;
		}
	}
}
