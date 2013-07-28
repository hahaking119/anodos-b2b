<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerUpdater extends JControllerForm {

	function __construct() {
		$this->view_list = 'updaters';
		parent::__construct();
	}
}
