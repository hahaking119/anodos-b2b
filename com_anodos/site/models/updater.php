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

	public function reportToMail() { // TODO

		if (mail("abezpalov@ya.ru", $this->subject, $this->msg, "From: bot@anodos.ru \r\n")) {
			echo "Messege acepted for delivery.";
		} else {
			echo "Some error happen.";
		}
	}

	public function update($id, $key) {

		// Получаем данные загрузчика
		$updater = $this->getUpdater($id);
		if (md5($key) == $updater->key) {
			$this->addMsg('Ключ загрузчика подошел.');
		} else {
			$this->addMsg('Ключ загрузчика не подошел.');
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
		$this->addMsg("\$updater = $updater");
		$updater = new $updater;
		if ($updater->update($id)) {
			$this->subject .= 'complite';
			$this->msg .= $updater->getMsg();
			return true;
		} else {
			$this->subject .= 'error';
			return false;
		}
	}

	protected function getUpdater($id) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM `#__anodos_updater`
			WHERE `id` = '{$id}';";
		$db->setQuery($query);
		$updater = $db->loadObject();

		// Возвращаем результат
		return $updater;
	}
}
