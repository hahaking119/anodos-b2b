<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class AnodosModelUpdater extends JModelList {

	protected $msg;
	protected $subject;

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function getMsg() {
		return $this->msg;
	}

	protected function addMsg($msg) {
		$this->msg .= $msg."\n";
	}

	public function reportToMail() {
		if (mail("abezpalov@ya.ru", $this->subject, $this->msg, "From: bot@anodos.ru \r\n")) {
			echo "Message acepted for delivery.";
		} else {
			echo "Some error happen.";
		}
	}

	public function update($id, $key) {

		// Получаем данные загрузчика
		$updater = $this->getUpdater($id);
		if (!isset($updater->id)) {
			$this->addMsg('<div class="alert alert-danger">Error #'.__LINE__.' - обращение к несуществующему загрузчику.</div>');
			return false;
		}

		if (md5($key) != $updater->key) {
			$this->addMsg('<div class="alert alert-danger">Ключ загрузчика не подошел.</div>');
			$this->addMsg("\$key = $key");
			$this->addMsg("md5(\$key) = ".md5($key));
			$this->addMsg("\$updater->key = {$updater->key}");
			return false;
		}

		// Определяем имя файла загрузчика
		$file = JPATH_COMPONENT.'/models/updater.'.strtolower($updater->alias).'.php';
		if (true == JFile::exists($file)) {
			require_once $file;
		} else {
			$this->addMsg('Файл загрузчика не существует.');
			$this->addMsg('$file = '.$file);
			return false;
		}

		// Запускаем загрузчик
		$this->subject = "Anodos {$updater->alias} update ";
		$updater = 'Updater'.$updater->alias;
		$updater = new $updater;
		if ($updater->update($id)) {
			$this->subject .= 'complite';
			$this->msg .= $updater->getMsg();
			return true;
		} else {
			$this->subject .= 'error';
			$this->msg .= $updater->getMsg();
			return false;
		}
	}

	protected function getUpdater($id) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_updater
			WHERE id = '{$id}';";
		$db->setQuery($query);
		$updater = $db->loadObject();

		// Возвращаем результат
		return $updater;
	}

	public function addProductCategory($categoryName, $parentId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('<div class="alert alert-danger">Error #'.__LINE__.' - отказано в доступе.</div>');
			return false;
		} else {
			$this->addMsg('<div class="alert alert-success">Доступ разрешен.</div>');
		}

		// TODO test
		$this->addMsg('<div class="alert alert-success">$categoryName = '.$categoryName.'</div>');
		$this->addMsg('<div class="alert alert-success">$parentId = '.$parentId.'</div>');

		// TODO Получаем объект родительской категории
		$parent = Category::getCategory($parentId);
		$this->addMsg('<div class="alert alert-success">$parent->title = '.$parent->title.'</div>');

		// Копируем объект родительской категории
		$category = $parent;

		// Вносим необходимые правки
		$category->asset_id = 0;
		$category->parent_id = $parentId;
		$category->lft = Category::getNextLFT($parent);
		$category->rgt = 0;
		$category->level++;
		$category->title = $categoryName;
		$category->alias = JFilterOutput::stringURLSafe($category->title);
		if (true == $category->path) {
			$category->path .= '/'.$category->alias;
		} else {
			$category->path = $category->alias;
		}
		$category->extension = 'com_anodos.product';
		$category->note = '';
		$category->description = ' ';
		$category->published = 1;
		$category->params = '{}';
		$category->metadesc = ' ';
		$category->metakey = ' ';
		$category->metadata = '{}';
		$category->created_user_id = $user->id;
		$category->language = '*';
		$category->version = 1;

		// Добавляем категорию в базу
		$result = Category::addCategory($category);

		// Перестраиваем дерево категорий
		$id = $category->id;
		$category = JTable::getInstance('Category');
		$category->rebuild();
	}

	public function addVendor($name) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('<div class="alert alert-danger">Error #'.__LINE__.' - отказано в доступе.</div>');
			return false;
		} else {
			$this->addMsg('<div class="alert alert-success">Доступ разрешен.</div>');
		}

		// TODO test
		$this->addMsg('<div class="alert alert-success">$name = '.$name.'</div>');

		// Вносим необходимые правки
		$vendor->asset_id = 0;
		$vendor->name = $name;
		$vendor->alias = JFilterOutput::stringURLSafe($name);
		$vendor->published = 1;
		$vendor->created_by = $user->id;

		// Добавляем категорию в базу
		$result = Vendor::addVendor($vendor);
	}

	public function linkSynonymToCategory($synonymId, $categoryId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/category.php';
		$this->addMsg('<?xml version="1.0" encoding="UTF-8"?>');
		$this->addMsg('<body>');

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('<div class="alert alert-danger">Error #'.__LINE__.' - отказано в доступе.</div>');
			$this->addMsg('</body>');
			return false;
		} else {
			$this->addMsg('<div class="alert alert-success">Доступ разрешен.</div>');
		}

		// Добавляем категорию в базу
		Category::linkSynonymToCategory($synonymId, $categoryId);

		// Закрываем XML
		$this->addMsg('</body>');
	}

	public function linkSynonymToVendor($synonymId, $vendorId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';
		$this->addMsg('<?xml version="1.0" encoding="UTF-8"?>');
		$this->addMsg('<body>');

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('<div class="alert alert-danger">Error #'.__LINE__.' - отказано в доступе.</div>');
			$this->addMsg('</body>');
			return false;
		} else {
			$this->addMsg('<div class="alert alert-success">Доступ разрешен.</div>');
		}

		// Добавляем категорию в базу
		Vendor::linkSynonymToVendor($synonymId, $vendorId);

		// Закрываем XML
		$this->addMsg('</body>');
	}
}
