<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelProducts extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
				'article', 'a.article',
				'name', 'a.name',
				'state', 'a.state',
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
		$query->from('#__anodos_product AS a');

		// Join category
		$query->select('category.title AS category_name');
		$query->join('LEFT', '#__categories AS category ON category.id = a.category_id');

		// Join vendor
		$query->select('vendor.name AS vendor_name');
		$query->join('LEFT', '#__anodos_partner AS vendor ON vendor.id = a.vendor_id');

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
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}

		return $query;
	}
}
