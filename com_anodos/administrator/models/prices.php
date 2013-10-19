<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelPrices extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'stock_name', 'stock.name',
				'product_name', 'product.name',
				'created', 'a.created',
				'price', 'a.price',
				'currency_name', 'currency.name_html',
				'price_type_name', 'price_type.name',
				'state', 'a.state',
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
		parent::populateState('a.created', 'desc');
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
		$query->from('#__anodos_price AS a');

		// Join contractor
		$query->select('stock.name AS stock_name');
		$query->join('LEFT', '#__anodos_stock AS stock ON stock.id = a.stock_id');

		// Join product
		$query->select('product.name AS product_name');
		$query->join('LEFT', '#__anodos_product AS product ON product.id = a.product_id');

		// Join created_by
		$query->select('author.name AS author_name');
		$query->join('LEFT', '#__users AS author ON author.id = a.created_by');

		// Join currency
		$query->select('currency.name_html AS currency_name');
		$query->join('LEFT', '#__anodos_currency AS currency ON currency.id = a.currency_id');

		// Join price_type
		$query->select('price_type.name AS price_type_name');
		$query->join('LEFT', '#__anodos_price_type AS price_type ON price_type.id = a.price_type_id');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
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
