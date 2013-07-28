<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewProductQuantities extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null) {

		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		AnodosHelper::addSubmenu('productquantities');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/anodos.php';

		$state	= $this->get('State');
		$canDo	= AnodosHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_ANODOS_TITLE_PRODUCT_QUANTITIES'), 'productquantities.png');

		if (isset($this->items[0]->state)) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('productquantities.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('productquantities.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		} else if (isset($this->items[0])) {
			//If this component does not use state then show a direct delete button as we can not trash
			JToolBarHelper::deleteList('', 'productquantities.delete','JTOOLBAR_DELETE');
		}

		if (isset($this->items[0]->state)) {
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('productquantities.archive','JTOOLBAR_ARCHIVE');
		}
		if (isset($this->items[0]->checked_out)) {
			JToolBarHelper::custom('productquantities.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}

		//Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state)) {
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
				JToolBarHelper::deleteList('', 'productquantities.delete','JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			} else if ($canDo->get('core.edit.state')) {
				JToolBarHelper::trash('productquantities.trash','JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_anodos');
		}

		//Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_anodos&view=productquantities');

		$this->extra_sidebar = '';

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
		);
	}

	protected function getSortFields() {
		return array(
			'a.state' => JText::_('COM_ANODOS_STATE'),
			'product_name' => JText::_('COM_ANODOS_PRODUCT'),
			'stock_name' => JText::_('COM_ANODOS_STOCK'),
			'a.quantity' => JText::_('COM_ANODOS_QUANTITY'),
			'a.created' => JText::_('COM_ANODOS_CREATED'),
			'author_name' => JText::_('COM_ANODOS_AUTHOR'),
		);
	}
}
