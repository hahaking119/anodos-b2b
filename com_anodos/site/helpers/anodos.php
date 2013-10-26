<?php

defined('_JEXEC') or die;

abstract class AnodosHelper {

	public static $extension = 'com_anodos';

	public static function getActions() {

		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_anodos';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
