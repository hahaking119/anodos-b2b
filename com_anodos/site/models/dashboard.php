<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelDashboard extends JModelList {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function getPerson() {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/person.php';

		// Инициализируем переменные
		$result = new JObject;
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();

		// Если пользователь неавторизован
		if ($user->guest) {
			// Возвращать нечего
			return false;
		} elseif (true == $user->id) {
			$person = Person::getPersonFromUser($user->id);
			if (isset($person->id)) {
				echo "Контактное лицо получено - получить имеющуюся по нему информацию?";
			} else {
				echo "Контактного лица нет - создать?";
			}
		}


	}

}
