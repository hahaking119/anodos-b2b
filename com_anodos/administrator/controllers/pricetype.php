<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class AnodosControllerPriceType extends JControllerForm {

	function __construct() {
		$this->view_list = 'pricetypes';
		parent::__construct();
	}
}
