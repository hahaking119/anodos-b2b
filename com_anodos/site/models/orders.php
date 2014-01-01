<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelOrders extends JModelList {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	protected $msg;

	public function getMsg() {
		return $this->msg;
	}

	private function addMsg($msg) {
		$this->msg .= $msg."<br/>";
	}

}
