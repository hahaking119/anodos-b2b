<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewOrders extends JViewLegacy {

	protected $client;
	protected $contractor;
	protected $stage;
	protected $name;

	protected $clients;
	protected $contractors;
	protected $stages;

	protected $orders;

    protected $params;

	public function display($tpl = null) {

		$app = JFactory::getApplication();

		$this->client = $this->get('Client');
		$this->contractor = $this->get('Contractor');
		$this->stage = $this->get('Stage');
		$this->name = $this->get('Name');

		$this->clients = $this->get('Clients');
		$this->contractors = $this->get('Contractors');
		$this->stages = $this->get('Stages');

		$this->orders = $this->get('Orders');

		$this->params = $app->getParams('com_anodos');

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
