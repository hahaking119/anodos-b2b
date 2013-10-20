<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/products.js', 'text/javascript', true);
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');

echo $this->msg;
?>
<table>
	<tr>
		<td><p>Категория:</p></td>
		<td>
			<div id="pricer-categories" class="close">
			<?php echo $this->loadTemplate('category_selected'); ?>
			<?php echo $this->loadTemplate('categories'); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td><p>Производитель:</p></td>
		<td>
			<div id="pricer-vendors" class="close">
			<?php echo $this->loadTemplate('vendor_selected'); ?>
			<?php echo $this->loadTemplate('vendors'); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td><p>Параметры</p></td>
		<td>
			<div id="pricer-parameters" class="close">
			<?php echo $this->loadTemplate('parameters'); ?>
			</div>
		</td>
	</tr>
</table>

<div id="pricer-buttons">
<form action="" method="get" name="pricer-form">
	<input id="pricer-form-category-selected" type="hidden" name="category" value="<?php if (true == $this->categorySelected) {echo $this->categorySelected->id;} else {echo 0;} ?>" />
	<input id="pricer-form-vendor-selected" type="hidden" name="vendor" value="<?php if (true == $this->vendorSelected) {echo $this->vendorSelected->id;} else {echo 0;} ?>" />
	<input type="submit" value="<?php echo JText::_('COM_ANODOS_SHOW'); ?>" />
</form>
</div>

<div id="pricer-products">
<?php echo $this->loadTemplate('products'); ?>
</div>
<div id="pricer-orders">
<?php echo $this->loadTemplate('orders'); ?>
</div>
<div id="pricer-order-products">
<?php echo $this->loadTemplate('order_products'); ?>
</div>
