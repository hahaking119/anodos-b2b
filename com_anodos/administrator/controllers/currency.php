<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerCurrency extends JControllerForm {

	function __construct() {
		$this->view_list = 'Currencies';
		parent::__construct();
	}
}
