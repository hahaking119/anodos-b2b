<?php
defined('_JEXEC') or die('Restricted access');

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/products.js', 'text/javascript', true);
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');

// Определяем уровень доступа
require_once JPATH_COMPONENT.'/helpers/anodos.php';
$canDo = AnodosHelper::getActions();

// echo $this->msg;
?>
<div class="uk-grid">
	<div class="uk-width-1-2">
		<div class="uk-button-group">
			<button class="uk-button" data-uk-modal="{target:'#selectCategoryModal'}"><i class="uk-icon-folder-close-alt"></i>&nbsp;<span id="category-selected"><?php
				if ('0' == $this->category) {
					echo JText::_('COM_ANODOS_SELECT_CATEGORY');
				} elseif ('all' === $this->category) {
					echo JText::_('COM_ANODOS_ALL_CATEGORIES');
				} else {
					echo $this->categoryName;
				}
				?></span></button>
			<button class="uk-button" data-uk-modal="{target:'#selectVendorModal'}"><i class="uk-icon-building"></i>&nbsp;<span id="vendor-selected"><?php
				if (('all' === $this->vendor) or ('0' == $this->vendor)) {
					echo JText::_('COM_ANODOS_ALL_VENDORS');
				} else {
					echo $this->vendorName;
				}
				?></span></button>
				<button id="show-product-button" class="uk-button"><i class="uk-icon-list"></i>&nbsp;<?php echo JText::_('COM_ANODOS_SHOW'); ?></button>

		</div>
		<label><input id="subcategories-checkbox" type="checkbox"<?php
				if (('all' === $this->category) or (0 == $this->category)) {
					echo ' disabled';
				}
				if ('1' == $this->subcategories) {
					echo ' checked="checked"';
				}
				?>>&nbsp;Включая подкатегории</label>
	</div>
	<div class="uk-width-1-2 uk-text-right">
		<form id="form-show-product" class="uk-form" action="" method="get">
			<fieldset>
				<input id="form-category-selected" name="category" type="hidden" value="<?php echo $this->category; ?>"/>
				<input id="form-vendor-selected" name="vendor" type="hidden" value="<?php echo $this->vendor; ?>"/>
				<input id="form-subcategories" name="subcategories" type="hidden" value="<?php echo $this->subcategories; ?>"/>
				<input id="form-search" name="search" type="text" placeholder="Поиск по складу" value="<?php echo $this->search; ?>" />
				<button class="uk-button" type="submit"><i class="uk-icon-search"></i>&nbsp;Найти</button>
			</fieldset>
		</form>
	</div>
</div>

<?php
	$col = 0;
	$lastCategoryId = 0;
	if (isset($this->products)) :
?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<table id="products-list" class="uk-table">
		<thead>
			<tr>
				<th><i class="uk-icon-list-ol"></i><?php $col++; ?></th>
				<?php if ($canDo->get('core.admin')) : ?>
				<th><i class="uk-icon-edit"></i><?php $col++; ?></th>
				<?php endif; ?>
				<th><i class="uk-icon-shopping-cart"></i><?php $col++; ?></th>
				<th><?php echo JText::_('COM_ANODOS_NAME').' ['.JText::_('COM_ANODOS_ARTICLE').']'; $col++; ?></th>
				<th><?php echo JText::_('COM_ANODOS_VENDOR'); $col++; ?></th>
				<?php if ($canDo->get('core.manage')) : ?>
				<th colspan="2"><?php echo JText::_('COM_ANODOS_PRICE_IN_OUT'); $col++; $col++; ?></th>
				<?php else : ?>
				<th><?php echo JText::_('COM_ANODOS_PRICE'); $col++; ?></th>
				<?php endif; ?>
				<th><?php echo JText::_('COM_ANODOS_QUANTITY'); $col++; ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->products as $i => $product): ?>
			<?php if ($lastCategoryId != $product->category_id) : ?>
			<tr class="active" >
				<td colspan="<?php echo $col; ?>"><b><?php echo $product->category_name; ?></b></td>
			</tr>
			<?php endif; $lastCategoryId = $product->category_id; ?>
			<tr>
				<td class="text-center"><?php echo $i+1; ?></td>
				<?php if ($canDo->get('core.admin')) : ?>
				<td class="uk-form">
					<button
						class="uk-button uk-button-mini"
						data-uk-modal="{target:'#editProductModal'}"
						data-product-id="<?php echo $product->product_id; ?>">
						<i class="uk-icon-pencil"></i></button></td>
				<?php endif; ?>
				<td class="text-center">
					<button
						class="uk-button uk-button-mini"
						data-uk-modal="{target:'#addToOrderModal'}"
						data-product-id="<?php echo $product->product_id; ?>">
						<i class="uk-icon-plus"></i>
					</button>
				</td>
				<td class="text-left name"><?php echo $product->product_name.' ['.$product->product_article.']'; ?></td>
				<td class="text-center"><?php echo $product->vendor_name; ?></td>
				<?php if ($canDo->get('core.manage')) : ?>
				<td class="text-right td-price" data-uk-tooltip title="<?php echo $product->stock_name; ?>">
					<?php echo $product->price_in; ?> <?php echo $product->currency_name; ?>
				</td>
				<td class="text-right td-price">
					<?php echo $product->price_rub_out; ?> р.
				</td>
				<?php else : ?>
				<td class="text-right td-price">
					<?php echo $product->price_rub_out; ?> р.
				</td>
				<?php endif; ?>
				<td class="uk-form text-right">
					<?php echo $product->quantity; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	</div>
</div>
<?php endif; ?>

<div id="selectCategoryModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Категорию?</h1>
		<?php echo $this->categories; ?>
	</div>
</div>

<div id="selectVendorModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Производителя?</h1>
		<div id="vendors-list" class="modal-body">
			<button id="vendor-all" data-vendor-id="all" class="uk-button vendor-name uk-modal-close"><?php echo JText::_('COM_ANODOS_ALL_VENDORS'); ?></button>
			<hr />
			<?php foreach($this->vendors as $i => $vendor): ?>
			<button id="vendor-<?php echo $vendor->id; ?>" data-vendor-id="<?php echo $vendor->id; ?>" class="uk-button vendor-name uk-modal-close"><?php echo $vendor->name; ?></button>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div id="addToOrderModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Добавить в заказ</h1>
		<p>В разработке.</p>
	</div>
</div>


<?php if ($canDo->get('core.admin')) : ?>
<div id="editProductModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Отредактировать продукт?</h1>
		<div class="uk-form">
			<fieldset>
				<select id="edit-product-category" name="category" class="inputbox">
					<option value="1" selected> - В корень - </option>
					<?php foreach($this->parentCategoryList as $j => $category):
					echo "<option value=\"{$category->id}\">{$category->title}</option>";
					endforeach; ?>
				</select>
			</fieldset>
			<fieldset>
				<input id="edit-product-name" type="text" name="name" placeholder="Имя продукта">
			</fieldset>
			<fieldset>
				<button id="edit-product-button" class="uk-button uk-modal-close">Готово</button>
			</fieldset>
		</div>
		<hr />
		<div id="edit-product-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a><p>AJAX готов.</p></div>
		</div>
	</div>
</div>
<?php endif; ?>
