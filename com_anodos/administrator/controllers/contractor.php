<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerContractor extends JControllerForm {

	function __construct() {
		$this->view_list = 'contractors';
		parent::__construct();
	}
}
