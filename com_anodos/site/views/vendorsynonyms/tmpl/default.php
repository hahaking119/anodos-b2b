<?php
defined('_JEXEC') or die('Restricted access');

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/vendorsynonyms.js', 'text/javascript', true);
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');

// Определяем уровень доступа
require_once JPATH_COMPONENT.'/helpers/anodos.php';
$canDo = AnodosHelper::getActions();
if (!$canDo->get('core.admin')) { die('Restricted access'); }

echo $this->msg;
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="form-horizontal" role="form" action="" method="get" name="form">
				<div class="form-group">
					<label for="partner" class="col-sm-2 control-label"><?php echo JText::_('COM_ANODOS_PARTNER'); ?></label>
					<div class="col-sm-10">
						<select name="partner" class="form-control">
							<option<?php if (0 === $this->partner) {echo ' selected';} ?> value="0"> - Укажите партнера - </option>
							<option<?php if ('all' == $this->partner) {echo ' selected';} ?> value="all">Все партнеры</option>
							<?php foreach($this->partners as $j => $partner):
								$selected = $partner->partner_id == $this->partner ? 'selected' : '';
								echo "<option value=\"{$partner->partner_id}\" $selected>{$partner->partner_name}</option>";
							endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<div class="checkbox">
							<label>
								<input name="onlynull" type="checkbox"<?php if ('on' == $this->onlyNull) echo ' checked="checked"'; ?>> Только непривязанные
							</label>
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

	<?php if ($canDo->get('core.admin')) : ?>
	<div class="row">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="btn-toolbar">
				<div class="btn-group">
					<a data-toggle="modal" href="#addVendorModal" class="btn btn-default">Добавить производителя</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addVendorModal" tabindex="-1" role="dialog" aria-labelledby="addVendorModalLabel" aria-hidden="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Добавить производителя?</h4>
				</div>
				<form role="form" action="" method="post" id="add-vendor-form" target="addVendorModalFrame">
					<div class="modal-body">
						<div class="form-group">
							<label for="name">Имя производителя</label>
							<input type="text" class="form-control" name="name" placeholder="Имя производителя">
						</div>
						<iframe class="well well-sm" id="addVendorModalFrame" style="width:100%;"></iframe>
						<input type="hidden" name="option" value="com_anodos" />
						<input type="hidden" name="task" value="updater.addVendor" />
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
		if (0 < sizeof($this->synonyms)) :
	?>
	<div class="row">
		<div class="table-responsive">
			<table id="b2b-synonyms" class="table table-bordered">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_ANODOS_N'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_PARTNER'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_NAME'); $col++; ?></th>
						<th><?php echo JText::_('COM_ANODOS_VENDOR'); $col++; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($this->synonyms as $i => $synonym): 
					
					
					// TODO написать ajax привязывающий производителя
					?>
					<tr data-synonym-id="<?php echo $synonym->synonym_id; ?>">
						<td class="text-center"><?php echo $i+1; ?></td>
						<td class="text-left"><?php echo $synonym->partner_name; ?></td>
						<td class="text-left"><?php echo $synonym->synonym_name; ?></td>
						<td class="text-left">
							<select
								class="form-control"
								id="select-vendor-<?php echo $synonym->synonym_id; ?>"
								onchange="linkSynonymToVendor('<?php echo $synonym->synonym_id; ?>')"
							>
								<?php $selected = isset($synonym->vendor_id) ? '' : ' selected'; ?>
								<option value="NULL"<?php echo $selected; ?>> - Без привязки - </option>
								<?php foreach($this->vendors as $j => $vendor):
									$selected = $vendor->id == $synonym->vendor_id ? ' selected' : '';
									echo "<option value=\"{$vendor->id}\"{$selected}>{$vendor->name}</option>";
								endforeach; ?>
							</select>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php endif; ?>
</div>
