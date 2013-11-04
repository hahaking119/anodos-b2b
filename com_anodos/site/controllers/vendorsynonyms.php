<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

class AnodosControllerVendorsynonyms extends AnodosController {

	public function &getModel($name = 'Vendorsynonyms', $prefix = 'AnodosModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
