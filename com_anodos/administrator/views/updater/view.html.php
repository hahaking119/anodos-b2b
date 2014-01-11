<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewUpdater extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;
	protected $canDo;

	public function display($tpl = null) {

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$this->canDo = AnodosHelper::getActions('updater', $this->item->id);

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
		JToolBarHelper::title($isNew ? JText::_('COM_ANODOS_UPDATER_NEW') : JText::_('COM_ANODOS_UPDATER_EDIT'), 'updater.png');

		if ($this->canDo->get('core.sale.manager')) {
			JToolBarHelper::apply('updater.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('updater.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('updater.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::custom('updater.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if ($isNew) {
			JToolBarHelper::cancel('updater.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('updater.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
