<?php

defined('_JEXEC') or die;

// Access check
if (!JFactory::getUser()->authorise('core.manage', 'com_anodos')) {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Anodos');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
