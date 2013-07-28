<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerProduct extends JControllerForm {

	function __construct() {
		$this->view_list = 'products';
		parent::__construct();
	}
}
