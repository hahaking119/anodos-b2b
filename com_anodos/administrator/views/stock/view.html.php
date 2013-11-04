<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewStock extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;
	protected $canDo;

	public function display($tpl = null) {

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$rhis->canDo = AnodosHelper::getActions('stock', $this->item->id);

		// Check for errors
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		JToolBarHelper::title($isNew ? JText::_('COM_ANODOS_STOCK_NEW') : JText::_('COM_ANODOS_STOCK_EDIT'), 'stock.png');

		if ($this->canDo->get('core.create')) {
			JToolBarHelper::apply('stock.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('stock.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('stock.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::custom('stock.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if ($isNew) {
			JToolBarHelper::cancel('stock.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('stock.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
