<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelCategorySynonyms extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'state', 'a.state',
				'partner_name', 'partner.name',
				'category_name', 'category.title',
				'author_name', 'author.name',
			);
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null) {

		// Initialise variables
		$app = JFactory::getApplication('administrator');

		// Load the filter state
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters
		$params = JComponentHelper::getParams('com_anodos');
		$this->setState('params', $params);

		// List state information
		parent::populateState('a.id', 'asc');
	}

	protected function getStoreId($id = '') {

		// Compile the store id
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery() {

		// Create a new query object
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__anodos_category_synonym AS a');

		// Join vendor
		$query->select('partner.name AS partner_name');
		$query->join('LEFT', '#__anodos_partner AS partner ON partner.id = a.partner_id');

		// Join category
		$query->select('category.title AS category_name');
		$query->join('LEFT', '#__categories AS category ON category.id = a.category_id');

		// Join over the user field 'created_by'
		$query->select('author.name AS author_name');
		$query->join('LEFT', '#__users AS author ON author.id = a.created_by');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
			}
		}

		// Add the list ordering clause
		$orderCol = $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}

		return $query;
	}

	// Возвращает список категорий
	public function getCategories() {

		// Инициализируем переменные
		$db =& JFactory::getDBO();

		// Загружаем категории из базы
		$query = $db->getQuery(true);
		$query->select('id, lft, level, path, extension, title, alias, published');
		$query->from('#__categories');
		$query->where("extension = 'com_anodos'");
		$query->order('lft');
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		// Проводим изменение имени с учетом уровня вложенности
		for ($i=0; $i<sizeof($categories); $i++) {
			$prefix = '';
			for ($k=1; $k<$categories[$i]->level; $k++) {
				$prefix = '- ' . $prefix;
			}
			$categories[$i]->title = $prefix . $categories[$i]->title;
		}
		return $categories;
	}

	// Сохраняет изменения в синонимах категорий
	public function save($synonyms, $categories) {

		// Инициализируем переменные
		$db =& JFactory::getDBO();

		// Применяем изменения по циклу
		for($i = 0; $i<sizeof($synonyms); $i++) {
			$query = $db->getQuery(true);
			$query->update('#__anodos_category_synonym');
			$query->set("category_id=$categories[$i]");
			$query->where("id=$synonyms[$i]");
			$db->setQuery($query);
			$db->query();
		}

		// Возвращаем результат
		return true;
	}

	// Возвращает строку сообщений
	public function getMsg() {
		return $this->msg;
	}

	// Добавляет сообщение
	protected function addMsg($msg) {
		$this->msg .= "{$msg}<br/>\n";
	}
}
