<?php

defined('_JEXEC') or die;

abstract class AnodosHelper {

	public static $extension = 'com_anodos';

	public static function getActions() {

		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_anodos';

		$actions = array('core.admin', 'core.sale', 'core.sale.manager', 'core.vendor', 'core.distributor', 'core.client');

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
