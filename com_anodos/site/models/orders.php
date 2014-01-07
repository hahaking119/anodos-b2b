<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelOrders extends JModelList {

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

	public function getClient() {
		return JRequest::getVar('client', 0);
	}

	public function getContractor() {
		return JRequest::getVar('contractor', 0);
	}

	public function getStage() {
		return JRequest::getVar('stage', 0);
	}

	public function getName() {
		return JRequest::getVar('name', '');
	}

	public function getClients() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "SELECT * FROM #__anodos_partner WHERE client = 1 AND state = 1 ORDER BY name ASC;";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Возвращаем результат
		return $result;
	}

	public function getContractors() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "SELECT * FROM #__anodos_contractor WHERE state = 1 ORDER BY name ASC;";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Возвращаем результат
		return $result;
	}

	public function getStages() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "SELECT * FROM #__anodos_order_stage ORDER BY id ASC;";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Возвращаем результат
		return $result;
	}

	public function getOrders() {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// TODO Добавить преобразование суммы в рубли
		// TODO Добавить фильтры
		// TODO Добавить работу с уровнями доступа
		// Выполняем запрос
		$query = "
			SELECT
				orders.id AS order_id,
				orders.name AS order_name,
				order_stage.name AS order_stage,
				clients.name AS client_name,
				orders.partner_name AS order_client_name_draft,
				contractor.name AS contractor_name,
				orders.partner_name AS order_contractor_name_draft,
				SUM(order_line.quantity*order_line.price_out) AS order_sum
			FROM #__anodos_order AS orders
			INNER JOIN #__anodos_order_stage AS order_stage ON order_stage.id = orders.stage_id
			INNER JOIN #__anodos_order_line AS order_line ON order_line.order_id = orders.id
			INNER JOIN #__users AS author ON orders.created_by = author.id
			LEFT JOIN #__anodos_partner AS clients ON orders.partner_id = clients.id
			LEFT JOIN #__anodos_contractor AS contractor ON orders.contractor_id = contractor.id
			WHERE orders.state = 1
			GROUP BY order_id
			ORDER BY order_id ASC;";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Обрабатываем результат
		foreach($result as $i => $order) {

			// Форматируем сумму
			$order->order_sum = number_format (round($order->order_sum, 2, PHP_ROUND_HALF_UP), 2, ',', ' ');
		}

		// Возвращаем результат
		return $result;
	}
}
