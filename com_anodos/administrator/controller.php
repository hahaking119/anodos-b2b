<?php

defined('_JEXEC') or die;

class AnodosController extends JControllerLegacy {

	public function display($cachable = false, $urlparams = false) {

		require_once JPATH_COMPONENT.'/helpers/anodos.php';

		$view = JFactory::getApplication()->input->getCmd('view', 'partners');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
