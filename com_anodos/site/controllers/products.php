<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

class AnodosControllerProducts extends AnodosController {

	public function &getModel($name = 'Products', $prefix = 'AnodosModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
