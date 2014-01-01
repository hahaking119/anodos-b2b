<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelCategorysynonyms extends JModelList {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	protected $msg;

	public function getMsg() {
		return $this->msg;
	}

	private function addMsg($msg) {
		$this->msg .= $msg."<br/>";
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

	public function getPartner() {
		return JRequest::getVar('partner', 0);
	}

	// TODO test
	public function getPartners() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		$query = "
			SELECT 
				partner.id AS partner_id,
				partner.name AS partner_name
			FROM #__anodos_partner AS partner 
			LEFT JOIN #__anodos_category_synonym AS synonym 
				ON partner.id = synonym.partner_id
			WHERE synonym.id != 0
			GROUP BY partner.id 
			ORDER BY partner_name ASC;
		";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCategorySynonyms() {

		// Инициализируем переменные
		$categories = array();
		$onlyNull = JRequest::getVar('onlynull', 'off');
		$partnerId = JRequest::getVar('partner', 0);
		$sortBy = JRequest::getInt('sort');

		// Запрашиваем список товара
		$db = JFactory::getDBO();

		$query = "
			SELECT
				synonym.id AS synonym_id,
				synonym.name AS synonym_name,
				synonym.state AS synonym_state,
				partner.id AS partner_id,
				partner.name AS partner_name,
				category.id AS category_id,
				category.title AS category_name
			FROM #__anodos_category_synonym AS synonym
			LEFT JOIN #__anodos_partner AS partner
				ON synonym.partner_id = partner.id
			LEFT JOIN #__categories AS category
				ON synonym.category_id = category.id
		";

		// Условия выборки
		$prefix = 'WHERE';
		if ('on' === $onlyNull) {
			$query .= "{$prefix} synonym.category_id IS NULL ";
			$prefix = 'AND';
		}
		if ('all' !== $partnerId) {
			$query .= "{$prefix} synonym.partner_id = {$partnerId} ";
			$prefix = 'AND';
		}
		$query .= "{$prefix} synonym.state = 1 ";

		// Сортируем
		if (true != $sortBy) {
			$query .= "ORDER BY partner_name, synonym_name ";
		} else {
			// TODO: В зависимости от условий сортировки
		}

		// Закрываем запрос
		$query .=";";

		// Выполняем запрос и возвращаем результат
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getOnlyNull() {
		return JRequest::getVar('onlynull');
	}

	// Возвращает список категорий для модального окна добавления
	public function getParentCategoryList() {

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
}
