<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewDashboard extends JViewLegacy {

    protected $params;

	protected $person;
	protected $partner;
	protected $manager;

	protected $partners;
	protected $contractors;
	protected $tasks;

	protected $orders;

	public function display($tpl = null) {

		$app = JFactory::getApplication();
		$this->params = $app->getParams('com_anodos');

		$this->person = $this->get('Person');
		$this->partner = $this->get('Partner');
		$this->manager = $this->get('Manager');

		$this->partners = $this->get('Partners');
		$this->contractors = $this->get('Contractors');
		$this->tasks = $this->get('Tasks');

		$this->orders = $this->get('Orders');

		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();
		parent::display($tpl);
	}

	protected function _prepareDocument() {

		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		$menu = $menus->getActive();
		if($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_ANODOS_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
