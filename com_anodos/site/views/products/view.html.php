<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewProducts extends JViewLegacy {

	protected $msg;

	protected $categorySelected;
	protected $categories;
	protected $vendorSelected;
	protected $vendors;

	protected $products;
	protected $orders;
	protected $orderProducts;

	protected $params;

	public function display($tpl = null) {

		$app = JFactory::getApplication();

		$this->params  = $app->getParams('com_anodos');

		$this->categorySelected = $this->get('CategorySelected');
		$this->categories = $this->get('Categories');
		$this->vendorSelected = $this->get('VendorSelected');
		$this->vendors = $this->get('Vendors');
		$this->products = $this->get('Products');
//		$this->orders = $this->get('Orders');
//		$this->orderProducts = $this->get('OrderProducts');
//		$this->parameters = $this->get('Parameters');
//		$this->msg = $this->get('Msg');

		$this->parentCategoryList = $this->get('ParentCategoryList');

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

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
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

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

	// TODO исправить формат
	protected function getDeliveryTime($dtime) {
		switch($dtime) {
			case '0000-00-10 00:00:00' : return "10 дней";
			case '0000-02-00 00:00:00' : return "2 месяца";
			default : return $dtime;
		}
	}
}
