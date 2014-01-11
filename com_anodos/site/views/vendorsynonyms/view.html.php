<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewVendorsynonyms extends JViewLegacy {

	protected $params;

	protected $partner;
	protected $onlyNull;

	protected $vendors;
	protected $partners;
	protected $synonyms;

	public function display($tpl = null) {

		$app = JFactory::getApplication();

		$this->params  = $app->getParams('com_anodos');

		$this->vendors = $this->get('Vendors');
		$this->partner = $this->get('Partner');
		$this->partners = $this->get('Partners');
		$this->synonyms = $this->get('VendorSynonyms');
		$this->onlyNull = $this->get('OnlyNull');

		if (count($errors = $this->get('Errors'))) {;
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
			$this->params->def('page_heading', JText::_('com_anodos_DEFAULT_PAGE_TITLE'));
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
