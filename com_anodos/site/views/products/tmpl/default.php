<?php
defined('_JEXEC') or die('Restricted access');

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/products.js', 'text/javascript', true);
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');

// Определяем уровень доступа
require_once JPATH_COMPONENT.'/helpers/anodos.php';
$canDo = AnodosHelper::getActions();

echo $this->msg;
?>
<div class="container">
	<div class="row">
		<div class="col-sm-4 col-md-2">
			<p>Категория:</p>
		</div>
		<div class="col-sm-8 col-md-10 closed" id="b2b-categories" >
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
	</div>
	<div class="row">
		<div class="col-sm-4 col-md-2">
			<p>Производитель:</p>
		</div>
		<div class="col-sm-8 col-md-10 closed" id="b2b-vendors">
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
	</div>
	<div class="row">
		<div class="col-md-12">
			<form  role="form" action="" method="get" name="form">
				<div class="form-group">
					<input
						id="form-category-selected"
						type="hidden"
						name="category"
						value="<?php if (true == $this->categorySelected) {echo $this->categorySelected->id;} else {echo 0;} ?>"
					/>
					<input
						id="form-vendor-selected"
						type="hidden"
						name="vendor"
						value="<?php if (true == $this->vendorSelected) {echo $this->vendorSelected->id;} else {echo 0;} ?>"
					/>
					<button type="submit" class="btn btn-default"><?php echo JText::_('COM_ANODOS_SHOW'); ?></button>
				</div>
			</form>
		</div>
	</div>

	<?php if ($canDo->get('core.admin')) : ?>
	<div class="row">
		<div class="col-md-12">
			<div class="btn-toolbar">
				<div class="btn-group">
					<a data-toggle="modal" href="#addCategoryModal" class="btn btn-default">Добавить категорию</a>
					<a data-toggle="modal" href="#addVendorModal" class="btn btn-default">Добавить производителя</a>
				</div>
				<div class="btn-group">
					<a data-toggle="modal" href="#categorySynonymsModal" class="btn btn-default">Синонимы категорий</a>
					<a data-toggle="modal" href="#vendorSynonymsModal" class="btn btn-default">Синонимы производителей</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Добавить категорию?</h4>
				</div>
				<form role="form" action="" method="post" id="add-category-form" target="addCategoryModalFrame">
					<div class="modal-body">
						<div class="form-group">
							<label for="name">Имя категории</label>
							<input type="text" class="form-control" name="name" placeholder="Имя категории">
						</div>
						<div class="form-group">
							<label for="parent">Родительская категория</label>
							<select name="parent" class="inputbox">
								<option value="1" selected> - В корень - </option>
								<?php foreach($this->parentCategoryList as $j => $category):
								echo "<option value=\"{$category->id}\">{$category->title}</option>";
								endforeach; ?>
							</select>
						</div>
						<iframe class="well well-sm" id="addCategoryModalFrame" style="width:100%;"></iframe>
						<input type="hidden" name="option" value="com_anodos" />
						<input type="hidden" name="task" value="updater.addCategory" />
						<?php echo JHtml::_('form.token'); ?>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Добавить</button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_('JCANCEL') ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<hr />
	<?php endif; ?>
	<?php
		$col = 0;
		$lastCategoryId = 0;
		if (0 < sizeof($this->products)) :
	?>
	<div class="row">
		<div class="table-responsive">
			<table id="b2b-products" class="table table-bordered">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_ANODOS_N'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_ARTICLE'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_NAME'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_VENDOR'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_PRICE_IN'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_PRICE_OUT'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_QUANTITY'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_DISTRIBUTOR'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_DELIVERY_TIME'); $col++; ?></th>
						<?php if ($canDo->get('core.admin')) : ?>
						<th><?php echo JText::_('COM_ANODOS_MANAGEMENT'); $col++; ?></th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($this->products as $i => $product): ?>
					<?php if ($lastCategoryId != $product->category_id) : ?>
					<tr class="active" >
						<td colspan="<?php echo $col; ?>"><b><?php echo $product->category_name; ?></b></td>
					</tr>
					<?php endif; $lastCategoryId = $product->category_id; ?>
					<tr data-product-id="<?php echo $product->product_id; ?>">
						<td class="text-center"><?php echo $i+1; ?></td>
						<td class="text-left"><?php echo $product->product_article; ?></td>
						<td class="text-left"><?php echo $product->product_name; ?></td>
						<td class="text-center"><?php echo $product->vendor_name; ?></td>
						<td class="text-right">TODO&nbsp;TODO</td>
						<td class="text-right">TODO&nbsp;TODO</td>
						<td class="text-right">TODO</td>
						<td class="text-center">TODO</td>
						<td class="text-center">TODO</td>
						<?php if ($canDo->get('core.admin')) : ?>
						<td class="text-center">TODO</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php endif; ?>
</div>
