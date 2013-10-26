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

	public function addCategory($name, $parent) {

		// Проверяем право доступа
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('<div class="alert alert-danger">Error #'.__LINE__.' - отказано в доступе.</div>');
			return false;
		} else {
			$this->addMsg('<div class="alert alert-success">Доступ разрешен.</div>');
		}

		// TODO test
//		$this->addMsg('<div class="alert alert-success">$name = '.$name.'</div>');
//		$this->addMsg('<div class="alert alert-success">$alias = '.$alias.'</div>');
//		$this->addMsg('<div class="alert alert-success">$parent = '.$parent.'</div>');

		// TODO Заносим в базу

		// Get the database object
		$db = JFactory::getDbo();

		// JTableCategory is autoloaded in J! 3.0, so...
		if (version_compare(JVERSION, '3.0', 'lt')) {
			JTable::addIncludePath(JPATH_PLATFORM . 'joomla/database/table');
		}

		// Initialize a new category
		$category = JTable::getInstance('Category');
		$category->extension = 'com_anodos.product';
		$category->title = $name;
		$category->description = '';
		$category->published = 1;
		$category->access = 1;
		$category->params = '{}';
		$category->metadata = '{}';
		$category->metadesc = ' ';
		$category->metakey = ' ';
		$category->language = '*';
		$category->parent_id = $parent; 

		// Set the location in the tree
		$category->setLocation($parent, 'last-child');

		// Check to make sure our data is valid
		if (!$category->check()) {
			JError::raiseNotice(500, $category->getError());
			return false;
		} else {
			$this->addMsg('<div class="alert alert-success">Добавлена категория: '.$name.'.</div>');
		}

		// Now store the category
		if (!$category->store(true)) {
			JError::raiseNotice(500, $category->getError());
			return false;
		}

		// Build the path for our category
		$category->rebuildPath($category->id);
	}
}
