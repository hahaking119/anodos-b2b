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
		$user = JFactory::getUser();
		$canDo = AnodosHelper::getActions();

		// Получаем объект контактного лица
		if ($user->guest) {
			// Возвращать нечего
			return false;
		} elseif (true == $user->id) {
			$person = Person::getPersonFromUser($user->id);
			if (!isset($person->id)) { // Если контактного лица нет в базе - создаем его
				$person = Person::createPersonToUser($user->id);
				if (!isset($person->id)) { // Если контактного лица все еще нет в базе - возвращаем пустоту
					return false;
				}
			}
		}

		// Получаем имеющуюся информацию о контактном лице
		$person->info = Person::getPersonInfo($person->id);
		if (0 == sizeof($person->info)) {

			// TODO Имя, отчество, фамилия
			$info = new JObject;
			$info->personId = $person->id;
			$info->alias = 'name';
			$info->content = $user->name;
			$info->canRemove = 0;
			$info->ordering = 1;
			$info->state = 1;
			Person::createPersonInfo($info);

			// TODO Организация
			$info = new JObject;
			$info->personId = $person->id;
			$info->alias = 'company';
			$info->content = '';
			$info->canRemove = 0;
			$info->ordering = 2;
			$info->state = 1;
			Person::createPersonInfo($info);

			// TODO Должность
			$info = new JObject;
			$info->personId = $person->id;
			$info->alias = 'position';
			$info->content = '';
			$info->canRemove = 0;
			$info->ordering = 3;
			$info->state = 1;
			Person::createPersonInfo($info);

			// TODO Электронная почта
			$info = new JObject;
			$info->personId = $person->id;
			$info->alias = 'email';
			$info->content = $user->email;
			$info->canRemove = 0;
			$info->ordering = 4;
			$info->state = 1;
			Person::createPersonInfo($info);

			// TODO Телефон
			$info = new JObject;
			$info->personId = $person->id;
			$info->alias = 'phone';
			$info->content = '';
			$info->canRemove = 0;
			$info->ordering = 5;
			$info->state = 1;
			Person::createPersonInfo($info);

			// TODO Мобильный телефон
			$info = new JObject;
			$info->personId = $person->id;
			$info->alias = 'mobile';
			$info->content = '';
			$info->canRemove = 0;
			$info->ordering = 6;
			$info->state = 1;
			Person::createPersonInfo($info);

			// Получаем имеющуюся информацию о контактном лице
			$person->info = Person::getPersonInfo($person->id);
		}

		// Возвращаем результат
		return $person;
	}
}
