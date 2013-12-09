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
			$this->addMsg('<div class="uk-alert-success">'.__LINE__.' - Отчет отправлен на почту.</div>');
		} else {
			$this->addMsg('<div class="uk-alert-danger">'.__LINE__.' - Не удалось отправить отчет на почту.</div>');
		}
	}

	public function update($id, $key) {

		// Получаем данные загрузчика
		$updater = $this->getUpdater($id);
		if (!isset($updater->id)) {
			$this->addMsg('<div class="uk-alert-danger">Error #'.__LINE__.' - Обращение к несуществующему загрузчику.</div>');
			return false;
		}

		if (md5($key) != $updater->key) {
			$this->addMsg('<div class="uk-alert-danger">'.__LINE__.' - Ключ загрузчика не подошел.</div>');
			$this->addMsg("\$key = $key");
			$this->addMsg("md5(\$key) = ".md5($key));
			$this->addMsg("\$updater->key = {$updater->key}");
			return false;
		}

		// Определяем имя файла загрузчика
		$file = JPATH_COMPONENT.'/models/updaters/updater.'.strtolower($updater->alias).'.php';
		if (true == JFile::exists($file)) {
			require_once $file;
		} else {
			$this->addMsg('<div class="uk-alert-danger">Error #'.__LINE__." - Файл загрузчика $file не существует.</div>");
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

	public function addVendor($name) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.');
			return false;
		}

		// Вносим необходимые правки
		$vendor->asset_id = 0;
		$vendor->name = $name;
		$vendor->alias = JFilterOutput::stringURLSafe($name);
		$vendor->published = 1;
		$vendor->created_by = $user->id;

		// Проверяем наличие такого производителя в базе
		if (isset(Vendor::getVendorFromAlias($vendor->alias)->id)) {
			$this->addMsg('Error #'.__LINE__." - Производитель {$vendor->name} [{$vendor->alias}] уже есть в базе.");
			return false;
		}

		// Добавляем производителя в базу
		Vendor::addVendor($vendor);
		$this->addMsg("Добавлен производитель: {$name}.");
	 	return true;
	}

	public function linkSynonymToVendor($synonymId, $vendorId) {

		// Подключаем библиотеки
		require_once JPATH_COMPONENT.'/helpers/anodos.php';
		require_once JPATH_COMPONENT.'/models/helpers/vendor.php';

		// Получаем объект текущего пользователя
		$user = JFactory::getUser();

		// Проверяем право доступа
		$canDo = AnodosHelper::getActions();
		if (!$canDo->get('core.admin')) {
			$this->addMsg('Error #'.__LINE__.' - отказано в доступе.</div>');
			return false;
		}

		// Привязываем синоним к производителю
		Vendor::linkSynonymToVendor($synonymId, $vendorId);
		$this->addMsg('ok');
	 	return true;
	}
}
