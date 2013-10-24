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
			<div id="b2b-categories" class="closed">
			<?php
				if (true == $this->categorySelected) {
					echo "<span id=\"category-selected-square\" onClick=\"openCategories()\">&#8862;</span>";
					echo "<span>&nbsp;</span>";
					echo "<span id=\"category-selected-text\" onClick=\"openCategories()\">
						{$this->categorySelected->title}
						</span>";
				} else {
					echo "<span id=\"category-selected-square\" onClick=\"openCategories()\">&#8862;</span>";
					echo "<span>&nbsp;</span>";
					echo "<span id=\"category-selected-text\" onClick=\"openCategories()\">";
					echo JText::_('COM_ANODOS_SELECT_CATEGORY');
					echo "</span>";
				}
				echo $this->categories;
			?>
			</div>
		</td>
	</tr>
	<tr>
		<td><p>Производитель:</p></td>
		<td>
			<div id="b2b-vendors" class="closed">
			<?php
				if (true == $this->vendorSelected) {
					echo "<span id=\"vendor-selected-square\" onClick=\"openVendors()\">&#8862;</span>&nbsp;";
					echo "<span id=\"vendor-selected-text\" onClick=\"openVendors()\">{$this->vendorSelected->name}</span>";
				} else {
					echo "<span id=\"vendor-selected-square\" onClick=\"openVendors()\">&#8862;</span>&nbsp;";
					echo "<span id=\"vendor-selected-text\" onClick=\"openVendors()\">";
					echo JText::_('COM_ANODOS_ALL_VENDORS');
					echo "</span>";
				}
			?>
				<ul>
					<li id="vendor-0" onClick="setVendorSelected(0)"><?php echo JText::_('COM_ANODOS_ALL_VENDORS'); ?></li>
					<?php foreach($this->vendors as $i => $vendor): ?>
					<li id="vendor-<?php echo $vendor->id; ?>" onClick="setVendorSelected(<?php echo $vendor->id; ?>)">
						<?php echo $vendor->name; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<td><p>Параметры</p></td>
		<td>
			<div class="closed">
			<?php echo $this->loadTemplate('parameters'); ?>
			</div>
		</td>
	</tr>
</table>

<div>
<form action="" method="get" name="form">
	<input id="form-category-selected" type="hidden" name="category" value="<?php if (true == $this->categorySelected) {echo $this->categorySelected->id;} else {echo 0;} ?>" />
	<input id="form-vendor-selected" type="hidden" name="vendor" value="<?php if (true == $this->vendorSelected) {echo $this->vendorSelected->id;} else {echo 0;} ?>" />
	<input type="submit" value="<?php echo JText::_('COM_ANODOS_SHOW'); ?>" />
</form>
</div>

<div id="products">
<?php echo $this->loadTemplate('products'); ?>
</div>
<div id="orders">
<?php echo $this->loadTemplate('orders'); ?>
</div>
<div id="order-products">
<?php echo $this->loadTemplate('order_products'); ?>
</div>
