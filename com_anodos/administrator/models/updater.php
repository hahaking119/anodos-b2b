<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class AnodosModelUpdater extends JModelAdmin {

	protected $text_prefix = 'COM_ANODOS';

	public function getTable($type = 'Updater', $prefix = 'AnodosTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		// Initialise variables
		$app = JFactory::getApplication();

		// Get the form
		$form = $this->loadForm('com_anodos.updater', 'updater', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	protected function loadFormData() {

		// Check the session for previously entered form data
		$data = JFactory::getApplication()->getUserState('com_anodos.edit.updater.data', array());

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
				$db->setQuery('SELECT MAX(ordering) FROM #__anodos_updater');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}

	// Проверка на право удаления записи core.delete
	protected function canDelete($record) {

		if( !empty( $record->id ) ){
			$user = JFactory::getUser();
			return $user->authorise( "core.delete", "com_anodos.updater." . $record->id );
		}
	}

}
