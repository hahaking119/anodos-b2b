<?php

defined('_JEXEC') or die;

class AnodosHelper {

	public static function addSubmenu($vName = '') {

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_CONTRACTORS'),
			'index.php?option=com_anodos&view=contractors',
			$vName == 'contractors'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_CONTRACTOR_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_anodos.contractor',
			$vName == 'categories'
		);
	}

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
