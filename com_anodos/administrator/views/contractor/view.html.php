<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewContractor extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;

	public function display($tpl = null) {

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

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
		$isNew = ($this->item->id == 0);
		if (isset($this->item->checked_out)) {
			$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		} else {
			$checkedOut = false;
		}
		$canDo = AnodosHelper::getActions();

		JToolBarHelper::title(JText::_('COM_ANODOS_TITLE_CONTRACTOR'), 'contractor.png');

		// If not checked out, can save the item
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))) {
			JToolBarHelper::apply('contractor.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('contractor.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))) {
			JToolBarHelper::custom('contractor.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('contractor.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('contractor.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('contractor.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
