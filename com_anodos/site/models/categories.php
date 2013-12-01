<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AnodosModelCategories extends JModelList {

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
		$query->select('id, lft, level, path, extension, title, alias, published, parent_id');
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
			$categories[$i]->name = $prefix . $categories[$i]->title;
		}
		return $categories;
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
			$categories[$i]->name = $prefix . $categories[$i]->title;
		}
		return $categories;
	}
}
