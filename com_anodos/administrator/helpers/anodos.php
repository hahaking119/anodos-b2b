<?php

defined('_JEXEC') or die;

class AnodosHelper {

	public static $extension = 'com_anodos';

	public static function addSubmenu($vName = '') {

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_PRODUCTS'),
			'index.php?option=com_anodos&view=products',
			$vName == 'products'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_PRODUCT_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_anodos',
			$vName == 'categories'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_PARTNERS'),
			'index.php?option=com_anodos&view=partners',
			$vName == 'partners'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_PARTNER_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_anodos.partner',
			$vName == 'categories.partner'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_UPDATERS'),
			'index.php?option=com_anodos&view=updaters',
			$vName == 'updaters'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_UPDATER_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_anodos.updater',
			$vName == 'categories.updater'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_CATEGORY_SYNONYMS'),
			'index.php?option=com_anodos&view=categorysynonyms',
			$vName == 'categorysynonyms'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_VENDOR_SYNONYMS'),
			'index.php?option=com_anodos&view=vendorsynonyms',
			$vName == 'vendorsynonyms'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_PRICE_TYPES'),
			'index.php?option=com_anodos&view=pricetypes',
			$vName == 'pricetypes'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_CURRENCIES'),
			'index.php?option=com_anodos&view=currencies',
			$vName == 'currencies'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_PRICES'),
			'index.php?option=com_anodos&view=prices',
			$vName == 'prices'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_CURRENCY_RATES'),
			'index.php?option=com_anodos&view=currencyrates',
			$vName == 'currencyrates'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_STOCKS'),
			'index.php?option=com_anodos&view=stocks',
			$vName == 'stocks'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_STOCK_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_anodos.stock',
			$vName == 'categories.stock'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ANODOS_PRODUCT_QUANTITIES'),
			'index.php?option=com_anodos&view=productquantities',
			$vName == 'productquantities'
		);
	}

	// TODO test here
/*	public static function getActions($type = NULL, $id = NULL) {

		$user = JFactory::getUser();
		$result = new JObject;

		if (isset($id) && isset($type)) {
			$assetName = 'com_anodos.'.$type.'.'.(int) $id;
		} else {
			$assetName = 'com_anodos';
		}

		$actions = JAccess::getActions('com_anodos', 'component');

		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}*/

	public static function getActions($id = 0) {       

		jimport('joomla.access.access');
		$user = JFactory::getUser();
		$result = new JObject;

		if (empty($id)) {
			$assetName = 'com_anodos';
		}
		else {
			$assetName = 'com_anodos.product.'.(int) $id;
		}

		$actions = JAccess::getActions('com_anodos', 'component');

		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

}
