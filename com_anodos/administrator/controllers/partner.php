<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerPartner extends JControllerForm {

	function __construct() {
		$this->view_list = 'partners';
		parent::__construct();
	}
}
