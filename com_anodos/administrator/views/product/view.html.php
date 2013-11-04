<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AnodosViewProduct extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;
	protected $canDo;

	public function display($tpl = null) {

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$this->canDo = AnodosHelper::getActions('product', $this->item->id);

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
		JToolBarHelper::title($isNew ? JText::_('COM_ANODOS_PRODUCT_NEW') : JText::_('COM_ANODOS_PRODUCT_EDIT'), 'product.png');

		if ($this->canDo->get('core.create')) {
			JToolBarHelper::apply('product.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('product.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('product.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::custom('product.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if ($isNew) {
			JToolBarHelper::cancel('product.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('product.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
