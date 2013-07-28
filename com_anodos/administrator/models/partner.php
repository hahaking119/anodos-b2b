<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class AnodosModelPartner extends JModelAdmin {

	protected $text_prefix = 'COM_ANODOS';

	public function getTable($type = 'Partner', $prefix = 'AnodosTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		// Initialise variables
		$app = JFactory::getApplication();

		// Get the form
		$form = $this->loadForm('com_anodos.partner', 'partner', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	protected function loadFormData() {

		// Check the session for previously entered form data
		$data = JFactory::getApplication()->getUserState('com_anodos.edit.partner.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null) {

		if ($item = parent::getItem($pk)) {
			//Do any procesing on fields here if needed
		}

		return $item;
	}

	protected function prepareTable($table) {

		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__anodos_partner');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}
}
