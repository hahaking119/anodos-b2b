<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

class AnodosControllerCategories extends AnodosController {

	public function &getModel($name = 'Categories', $prefix = 'AnodosModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
