<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerCategorySynonym extends JControllerForm {

	function __construct() {
		$this->view_list = 'categorysynonyms';
		parent::__construct();
	}
}
