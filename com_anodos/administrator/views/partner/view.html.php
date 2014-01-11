<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewPartner extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;
	protected $canDo;

	public function display($tpl = null) {

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$this->canDo = AnodosHelper::getActions('partner', $this->item->id);

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
		JToolBarHelper::title($isNew ? JText::_('COM_ANODOS_PARTNER_NEW') : JText::_('COM_ANODOS_PARTNER_EDIT'), 'partner.png');

		if ($this->canDo->get('core.sale')) {
			JToolBarHelper::apply('partner.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('partner.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('partner.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::custom('partner.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if ($isNew) {
			JToolBarHelper::cancel('partner.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('partner.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
