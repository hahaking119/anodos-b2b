<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerStock extends JControllerForm {

	function __construct() {
		$this->view_list = 'stocks';
		parent::__construct();
	}
}
