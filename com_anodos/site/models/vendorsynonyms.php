<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelVendorsynonyms extends JModelList {

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
	public function getVendors() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		$query = "
			SELECT *
			FROM #__anodos_partner
			WHERE vendor = 1
			ORDER BY name ASC;
		";

		$db->setQuery($query);
		return $db->loadObjectList();
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

	public function getVendorSynonyms() {

		// Инициализируем переменные
		$vendors = array();
		$onlyNull = JRequest::getVar('onlynull', 'off');
		$partnerId = JRequest::getVar('partner', 0);
		$sortBy = JRequest::getInt('sort');

		// Запрашиваем список товара
		$db = JFactory::getDBO();

		$query = "
			SELECT
				synonym.id AS synonym_id,
				synonym.name AS synonym_name,
				partner.id AS partner_id,
				partner.name AS partner_name,
				vendor.id AS vendor_id,
				vendor.name AS vendor_name
			FROM #__anodos_vendor_synonym AS synonym
			LEFT JOIN #__anodos_partner AS partner
				ON synonym.partner_id = partner.id
			LEFT JOIN #__anodos_partner AS vendor
				ON synonym.vendor_id = vendor.id
		";

		// Условия выборки
		$prefix = 'WHERE';
		if ('on' === $onlyNull) {
			$query .= "{$prefix} synonym.vendor_id IS NULL ";
			$prefix = 'AND';
		}
		if ('all' !== $partnerId) {
			$query .= "{$prefix} synonym.partner_id = {$partnerId} ";
			$prefix = 'AND';
		}

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
}
