<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewPrices extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null) {

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		AnodosHelper::addSubmenu('prices');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/anodos.php';

		$state	= $this->get('State');
		$canDo	= AnodosHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_ANODOS_TITLE_PRICES'), 'prices.png');

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_anodos');
		}

        //Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_anodos&view=prices');

		$this->extra_sidebar = '';

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
		);

	}

	protected function getSortFields() {
		return array(
			'product_name' => JText::_('COM_ANODOS_PRODUCT'),
			'stock_name' => JText::_('COM_ANODOS_STOCK'),
			'price' => JText::_('COM_ANODOS_PRICE'),
			'a.state' => JText::_('COM_ANODOS_STATE'),
			'a.created' => JText::_('COM_ANODOS_CREATED'),
			'author_name' => JText::_('COM_ANODOS_AUTHOR'),
		);
	}
}
