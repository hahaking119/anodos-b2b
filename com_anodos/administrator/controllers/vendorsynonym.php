<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerVendorSynonym extends JControllerForm {

	function __construct() {
		$this->view_list = 'vendorsynonyms';
		parent::__construct();
	}
}
