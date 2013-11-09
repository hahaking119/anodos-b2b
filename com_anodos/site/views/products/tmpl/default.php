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
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="form-horizontal" role="form" action="" method="get" name="form">
				<div class="form-group">
					<label for="category" class="col-sm-2 control-label"><?php echo JText::_('COM_ANODOS_CATEGORY'); ?></label>
					<div class="col-sm-10">
						<input id="form-category-selected" name="category" type="hidden" value="<?php echo $this->category; ?>"/>

						<!-- TODO выбор категории -->
						<div class="btn-group">
							<a data-toggle="modal" href="#selectCategoryModal" class="btn btn-default">
								<span class="glyphicon glyphicon-folder-close"></span>&nbsp;
								<span id="category-selected-text"><?php
								if ('0' == $this->category) {
									echo JText::_('COM_ANODOS_SELECT_CATEGORY');
								} elseif ('all' === $this->category) {
									echo JText::_('COM_ANODOS_ALL_CATEGORIES');
								} else {
									echo $this->categoryName;
								}
								?></span></a>
						</div>
						<div class="modal fade" id="selectCategoryModal" tabindex="-1" role="dialog" aria-labelledby="selectCategoryModalLabel" aria-hidden="false">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title">Какую категорию показать?</h4>
									</div>
									<div class="modal-body">
										<?php echo $this->categories; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<div class="checkbox">
							<label>
								<input id="form-subcategories" name="subcategories" type="checkbox"<?php
									if ('0' == $this->category) {
										echo ' disabled';
									} elseif ('on' == $this->subcategories) {
										echo ' checked="checked"';
									}
									?>> Включая подкатегории
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="vendor" class="col-sm-2 control-label"><?php echo JText::_('COM_ANODOS_VENDOR'); ?></label>
					<div class="col-sm-10">
						<input id="form-vendor-selected" name="vendor" type="hidden" value="<?php echo $this->vendor; ?>"/>

						<!-- TODO выбор производителя -->
						<div class="btn-group">
							<a data-toggle="modal" href="#selectVendorModal" class="btn btn-default">
								<span class="glyphicon glyphicon-wrench"></span>&nbsp;
								<span id="vendor-selected-text"><?php
								if (('all' === $this->vendor) or (0 == $this->vendor)) {
									echo JText::_('COM_ANODOS_ALL_VENDORS');
								} else {
									echo $this->vendorName;
								}
								?></span></a>
						</div>

						<!-- TODO Модальное окно выбора категории -->
						<div class="modal fade" id="selectVendorModal" tabindex="-1" role="dialog" aria-labelledby="selectVendorModalLabel" aria-hidden="false">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title">Какого производителя предпочитаете?</h4>
									</div>
									<div id="vendors-list" class="modal-body">
										<button
											id="vendor-all"
											onclick="setVendorSelected('all')"
											type="button"
											class="btn btn-default">
											<?php echo JText::_('COM_ANODOS_ALL_VENDORS'); ?></button>
										<hr />
										<?php foreach($this->vendors as $i => $vendor): ?>
										<button
											id="vendor-<?php echo $vendor->id; ?>"
											onclick="setVendorSelected('<?php echo $vendor->id; ?>')"
											type="button"
											class="btn btn-default">
											<?php echo $vendor->name; ?></button>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default"><?php echo JText::_('COM_ANODOS_SHOW'); ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?php
		$col = 0;
		$lastCategoryId = 0;
		if (isset($this->products)) :
	?>
	<div class="row">
		<table id="products-list" class="table table-bordered col-xs-12">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_ANODOS_N'); $col++; ?></th>
					<th><?php echo JText::_('COM_ANODOS_NAME').' ['.JText::_('COM_ANODOS_ARTICLE').']'; $col++; ?></th>
					<th><?php echo JText::_('COM_ANODOS_VENDOR'); $col++; ?></th>
					<th><?php echo JText::_('COM_ANODOS_PRICE'); $col++; ?></th>
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
				<tr data-product-id="<?php echo $product->product_id; ?>">
					<td class="text-center"><?php echo $i+1; ?></td>
					<td class="text-left name">
						<?php echo $product->product_name.' ['.$product->product_article.']'; ?>
						<?php if ($canDo->get('core.admin')) : ?>
						<a class="btn btn-default btn-xs">
							<span class="glyphicon glyphicon-edit"></span>
						</a>
						<a class="btn btn-default btn-xs">
							<span class="glyphicon glyphicon-transfer"></span>
						</a>
						<?php endif; ?>
					</td>
					<td class="text-center"><?php echo $product->vendor_name; ?></td>
					<td class="text-right td-price"><?php echo $product->price_rub; ?> р.</td>
					<td class="text-right"><?php echo $product->quantity; ?> <a class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus-sign"></span></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php endif; ?>
</div>
