<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerStock extends JControllerForm {

	function __construct() {
		$this->view_list = 'stocks';
		parent::__construct();
	}

	// Проверяет право создания записи "core.add"
	protected function allowAdd($data = array()) {
		return parent::allowAdd($data);
	}
 
	// Проверяет право редактирования записи "core.edit"
	protected function allowEdit($data = array(), $key = 'id') {
		$id = isset( $data[ $key ] ) ? $data[ $key ] : 0;
		if( !empty( $id ) ) {
			$user = JFactory::getUser();
			return $user->authorise('core.edit', 'com_anodos.stock.'.$id);
		}
	}
}
