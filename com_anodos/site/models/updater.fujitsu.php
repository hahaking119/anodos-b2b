<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.archive.zip');
require_once JPATH_COMPONENT.'/models/helpers/updater.php';
require_once JPATH_COMPONENT.'/models/helpers/contractor.php';
require_once JPATH_COMPONENT.'/models/helpers/stock.php';
require_once JPATH_COMPONENT.'/models/helpers/vendor.php';
require_once JPATH_COMPONENT.'/models/helpers/category.php';
require_once JPATH_COMPONENT.'/models/helpers/product.php';
require_once JPATH_COMPONENT.'/models/helpers/price.php';
require_once JPATH_COMPONENT.'/models/helpers/currency.php';

class UpdaterFujitsu {

	// Объявляем переменные
	public $ok = true;
	protected $updaterAlias = 'fujitsu-price-updater';
	protected $contractorAlias = 'fujitsu';
	protected $contractorName = 'Fujitsu';

	protected $updater;
	protected $contractor;
	protected $vendor;
	protected $stock = array();
	protected $priceType = array();
	protected $currency = array();

	protected $data = array();

	protected $msg;

	// Точка входа
	public function update() {

		// Получаем объект загрузчика
		$this->updater = Updater::getUpdaterFromAlias($this->updaterAlias);

		// Получаем объект контрагента
		$this->contractor = Contractor::getContractorFromAlias($this->contractorAlias);
		if (false == $this->contractor->id) { // Если контрагента нет
			// Добавляем контрагента
			$this->contractor = Contractor::addContractor($this->contractorName, $this->contractorAlias, 0);
			if (false == $this->contractor->id) { // Если контрагента нет
				// Выводим ошибку добавления контрагента
				$this->addMsg("Не возможно добавить контрагента: {$this->contractorName}.");
			} else { // Если контрагент есть
				$this->addMsg("Добавлен контрагент: {$this->contractorName}.");
			}
		}

		// Проверяем привязку загрузчика к контрагенту
		if ($this->updater->contractor_id != $this->contractor->id) { // Если не привязан
			Updater::linkToContractor($this->updater->id, $this->contractor->id);
		}

		// TODO TEST Получаем объект производителя
		$this->vendor = Vendor::getVendorFromAlias($this->contractorAlias);
		if (false === $this->vendor->id) { // Если производителя нет
			// Добавляем производителя
			$this->vendor = Vendor::addVendor($this->contractorName, $this->contractorAlias, 0);
			if (false === $this->vendor->id) { // Если производителя нет
				// Выводим ошибку добавления производителя
				$this->addMsg("Не возможно добавить производителя: {$this->contractorName}.");
			} else { // Если производитель есть
				$this->addMsg("Добавлен производитель: {$this->contractorName}.");
			}
		}

		// Получаем объект склада
		$stockAlias = 'fujitsu-germany-stock';
		$stockName = 'Германский склад Fujitsu';
		$this->stock[$stockAlias] = Stock::getStockFromAlias($stockAlias);
		if (false === $this->stock[$stockAlias]->id) { // Если склада нет
			// Добавляем склад
			$this->stock[$stockAlias] = Stock::addStock($stockName, $stockAlias, $this->contractor->id, 0);
			if (false === $this->stock[$stockAlias]->id) { // Если склада нет
				// Выводим ошибку добавления контрагента
				$this->addMsg("Не возможно добавить склад: {$stockName}.");
				return false;
			} else { // Если склад есть
				$this->addMsg("Добавлен склад: {$this->stock[$stockAlias]->name}.");
			}
		}

		// Получаем объект типа цены
		$this->priceType['rdp'] = Price::getPriceTypeFromAlias('rdp');
		if (false === $this->priceType['rdp']->id) { // Если типа цены нет
			// Добавляем тип цены
			$this->priceType['rdp'] = Price::addPriceType('Рекомендованная диллерская цена (RDP, вход)', 'rdp', 0, 0, 0);
			if (false === $this->priceType['rdp']->id) { // Если типа цены нет
				// Выводим ошибку добавления типа цены
				$this->addMsg("Не возможно добавить тип цены: Рекомендованная диллерская цена (RDP, вход).");
				return false;
			} else { // Если тип цены есть
				$this->addMsg("Добавлен тип цены: {$this->priceType['rdp']->name}.");
			}
		}

		// Получаем id валюты USD
		if (false === $this->currency['USD'] = Currency::getCurrencyFromAlias('USD')) {
			$this->addMsg('Отсутствует валюта: USD.');
			$this->addMsg('Дальнейшее выполнение обработчика невозможно.');
			return false;
		}

		// Получаем имя папки для загрузки
		if (false === $dir = $this->getDir()) {
			return false;
		}

		// Загружаем прайс во временную папку
		if (false === $files = $this->getFile($dir)) { return false; }

		// Помечаем неактуальными устаревшие данные в базе
		Price::clearSQL($this->contractor->id);
		Stock::clearSQL($this->stock['fujitsu-germany-stock']->id);

		// Загружаем данные в базу
		if (false === $this->toSQL($files)) { return false; }

		// Отмечаем время обновления
		Updater::setUpdated($this->updater->id);

		// Выводим сообщение о завершении обработки
		$this->addMsg("{$this->updater->name} завершено.");
	}


	// Создает папку для загрузки, возвращает ее имя
	protected function getDir() {
		$dir = JFactory::getApplication()->getCfg('tmp_path').'/'.$this->updaterAlias.'/';
		if (false === JFolder::exists($dir)) {
			$this->addMsg('true != JFolder::exists($dir)');
			JFolder::create($dir);
		} else {
			// TODO удалить все содержимое
		}
		return $dir;
	}

	// Загружает прайс во временную папку и возвращает ее имя
	protected function getFile($dir) {

		// Инициализируем переменные
		$file = $dir.'sa_db.zip';

		// Инициализируем cURL и логинимся
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, 'https://globalpartners.ts.fujitsu.com//CookieAuth.dll?Logon');

			// Пробуем получить вывод в файл
			$fp = fopen($dir.'Page.html', "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);

			// Отключаем проверку сертификатов
//			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			// При значении true CURL включает в вывод заголовки.
			curl_setopt($ch, CURLOPT_HEADER, false);

			// Включаем куки
			curl_setopt($ch, CURLOPT_COOKIEJAR, $dir.'Cookie.txt');

			// Указываем, что будет POST запрос
			curl_setopt($ch, CURLOPT_POST, true);

			//	Передаем значения переменных
			curl_setopt($ch, CURLOPT_POSTFIELDS, "curl=Z2F&flags=0&forcedownlevel=0&formdir=15&username={$this->updater->login}&password={$this->updater->pass}&SubmitCreds=Sign+In&trusted=0");

			// Указываем максимальное время ожидания в секундах
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);

			// Устанавливаем значение поля User-agent
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1');

			// Выполняем запрос
			curl_exec($ch);

			// Освобождаем ресурс
			curl_close($ch);
			fclose($fp);
			unset($ch);
//			JFile::delete($dir.'Page.html');
		} else {
			return false;
		}

		// Инициализируем cURL и загружаем прайс
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, 'https://globalpartners.ts.fujitsu.com/sites/CPP/ru/config-tools/Pages/default.aspx');

			// Пробуем получить вывод в файл
			$fp = fopen($dir.'Page.html', "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);

			// Отключаем проверку сертификатов
//			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			// Выключаем вывод заголовков
			curl_setopt($ch, CURLOPT_HEADER, false);

			// Включаем куки
			curl_setopt($ch, CURLOPT_COOKIEFILE, $dir.'Cookie.txt');
			curl_setopt($ch, CURLOPT_COOKIEJAR, $dir.'Cookie.txt');

			// Указываем максимальное время ожидания в секундах
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);

			// Устанавливаем значение поля User-agent
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1');

			// Выполняем запрос
			curl_exec($ch);

			// Освобождаем ресурс
			curl_close($ch);
			fclose($fp);
			unset($ch);
		} else {
			return false;
		}

		// Получаем ссылку на файл с базой цен
		if (false === $url = $this->getURL($dir.'Page.html')) {
			return false;
		}

		// Инициализируем cURL и загружаем прайс
		if (true == $ch = curl_init()) {

			// Устанавливаем URL запроса
			curl_setopt($ch, CURLOPT_URL, $url);
			

			// Пробуем получить вывод в файл
			$fp = fopen($file, "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);

			// Отключаем проверку сертификатов
//			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			// Выключаем вывод заголовков
			curl_setopt($ch, CURLOPT_HEADER, false);

			// Включаем куки
			curl_setopt($ch, CURLOPT_COOKIEFILE, $dir.'Cookie.txt');
			curl_setopt($ch, CURLOPT_COOKIEJAR, $dir.'Cookie.txt');

			// Указываем максимальное время ожидания в секундах
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);

			// Устанавливаем значение поля User-agent
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1');

			// Выполняем запрос
			curl_exec($ch);

			// Освобождаем ресурс
			curl_close($ch);
			fclose($fp);
			unset($ch);
			JFile::delete($dir.'Cookie.txt'); // Куки больше не нужны
		} else {
			return false;
		}

		// Распаковываем полученый архив
		if (JArchive::extract($file,  $dir)) {
			JFile::delete($file);
		} else {
			return false;
		}

		// Находим файлы и возвращаем ссылки на них
		$files = array();
		$files[0] = $dir.'sys_arc.mdb';
		$files[1] = $dir.'prices.mdb';

		// Проверяем и выводим результат
		if (Jfile::exists($files[0]) and JFile::exists($files[1])) {
			return false;
		}

		return $files;
	}

	// Возвращает ссылку на архив с обновлением цен
	protected function getURL($file) {

		// Инициализируем переменные
		$link = array();

		// Загружаем данные из файла в DOM
		$dom = new DomDocument();
		$dom->loadHtmlFile($file);
		$xpath = new DOMXPath($dom);

		// Парсим и загружаем в массив
		foreach ($xpath->query('.//tr/td/a') as $a) {
			$link = $a->getAttribute('href');
			if (1 == substr_count($link, "_RDP.zip")) {
				return 'https://globalpartners.ts.fujitsu.com'.$link;
			}
		}
		return false;
	}

	// Заносит информацию в базу
	protected function toSQL($files) {

		// TODO заносим продукты
		$mdb = mdb_open($files[0]);
		if (false === $mdb) {
			$this->addMsg("Не удается подключиться к базе {$files[0]}.");
			return false;
		}

		// TODO TEST Отображаем список таблиц
		$tables = mdb_tables($mdb);
		if (sizeof($tables) > 0) {
			foreach ($tables as $table) {
				$this->addMsg("Найдена таблица: {$table}.");
			}
		} else {
			$this->addMsg("Не обнаружено ни одной таблицы в базе {$files[0]}");
			return false;
		}

//		$table = mdb_table_open($mdb, 


		// TODO заносим цены

		// TODO заносим состояние складов

		return true;
	}

	// Возвращает строку сообщений
	public function getMsg() {
		return $this->msg;
	}

	// Добавляет сообщение
	private function addMsg($msg) {
		$this->msg .= "{$msg}<br/>\n";
	}
}
