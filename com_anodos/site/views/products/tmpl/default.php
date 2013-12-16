<?php
defined('_JEXEC') or die('Restricted access');

// Определяем уровень доступа
require_once JPATH_COMPONENT.'/helpers/anodos.php';
$canDo = AnodosHelper::getActions();

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/products.js', 'text/javascript', true);
if ($canDo->get('core.admin')) {
	$doc->addScript($this->baseurl.'/components/com_anodos/js/products-admin.js', 'text/javascript', true);
}
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');


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
					<div class="uk-button-group">
					<button
						class="uk-button uk-button-mini rename-product"
						data-uk-modal="{target:'#renameProductModal'}"
						data-product-id="<?php echo $product->product_id; ?>">
						<i class="uk-icon-edit"></i></button>
					<button
						class="uk-button uk-button-mini move-product"
						data-uk-modal="{target:'#moveProductModal'}"
						data-product-id="<?php echo $product->product_id; ?>"
						data-category-id="<?php echo $product->category_id; ?>">
						<i class="uk-icon-folder-open-alt"></i></button>
					</div>
				</td>
				<?php endif; ?>
				<td class="text-center">
					<button
						class="uk-button uk-button-mini add-to-order"
						data-uk-modal="{target:'#addToOrderModal'}"
						data-product-id="<?php echo $product->product_id; ?>">
						<i class="uk-icon-shopping-cart"></i>
					</button>
				</td>
				<td class="text-left name"><span id="product-name-<?php echo $product->product_id; ?>"><?php echo $product->product_name; ?></span><span><?php echo ' ['.$product->product_article.']'; ?></span></td>
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

<!-- TODO Добавление товара в заказ -->
<div id="addToOrderModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Добавить в заказ?</h1>
		<div id="add-to-order-product-desc" class="uk-panel uk-panel-box"></div>
		<hr/>
		<div class="uk-form">
			<?php if ($canDo->get('core.manage')) : ?>
			<fieldset>
				<select id="add-to-oder-client" class="uk-width-1-2">
					<option value="0">Новый заказчик</option>
					<?php foreach($this->clients as $j => $client):
					echo "<option value=\"{$client->id}\">{$client->name}</option>";
					endforeach; ?>
				</select>
				<input id="add-to-oder-client-name" type="text" placeholder="Имя заказчика" value="">
			</fieldset>
			<?php else : ?>
				<input id="add-to-oder-client" type="hidden" value="0">
				<input id="add-to-oder-client-name" type="hidden" value="">
			<?php endif; ?>
			<fieldset>
				<select id="add-to-oder-order" class="uk-width-1-2">
					<option value="0">Новый заказ</option>
					<?php foreach($this->orders as $j => $order):
					echo "<option value=\"{$order->id}\">{$order->name}</option>";
					endforeach; ?>
				</select>
				<input id="add-to-oder-order-name" type="text" placeholder="Имя заказа">
			</fieldset>
			<fieldset>
				<label class="uk-form-label" for="add-to-oder-quantity">Количество:</label>
				<input id="add-to-oder-quantity" class="uk-form-width-small" type="number" min="1" value="1">
			</fieldset>
			<fieldset>
				<button id="add-to-order-button" class="uk-button uk-button-primary">Добавить</button>
				<button class="uk-button uk-modal-close">Отменить</button>
			</fieldset>
		</div>
		<hr />
		<div id="add-to-order-products-from-order" class="hidden"></div>
	</div>
</div>

<?php if ($canDo->get('core.admin')) : ?>
<!-- Переименование продукта TODO test -->
<div id="renameProductModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Переименовать продукт?</h1>
		<div class="uk-form">
			<fieldset>
				<textarea id="rename-product-name" class="uk-width-1-1" rows="8" name="name" placeholder="Имя продукта"></textarea>
			</fieldset>
			<fieldset>
				<button id="rename-product-button" class="uk-button uk-button-primary">Переименовать</button>
				<button class="uk-button uk-modal-close">Отменить</button>
			</fieldset>
		</div>
		<hr />
		<div id="rename-product-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a><p>AJAX готов.</p></div>
		</div>
	</div>
</div>

<!-- Перемещение продукта TODO test -->
<div id="moveProductModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Перенести продукт?</h1>
		<div class="uk-form">
			<fieldset>
				<select id="move-product-category" name="category" class="uk-width-1-1 inputbox">
					<?php foreach($this->parentCategoryList as $j => $category):
					echo "<option value=\"{$category->id}\">{$category->title}</option>";
					endforeach; ?>
				</select>
			<fieldset>
			</fieldset>
				<button id="move-product-button" class="uk-button uk-button-primary">Переместить</button>
				<button class="uk-button uk-modal-close">Отменить</button>
			</fieldset>
		</div>
		<hr />
		<div id="move-product-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a><p>AJAX готов.</p></div>
		</div>
	</div>
</div>
<?php endif; ?>
