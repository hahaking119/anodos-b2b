<?php
defined('_JEXEC') or die('Restricted access');

// Определяем уровень доступа
require_once JPATH_COMPONENT.'/helpers/anodos.php';
$canDo = AnodosHelper::getActions();
if (!$canDo->get('core.sale.manager')) { die('Restricted access'); }

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/vendorsynonyms.js', 'text/javascript', true);
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');

?>
<article class="uk-article">
	<h1>Синонимы производителей</h1>
	<hr class="uk-article-divider">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<form class="uk-form" action="" method="get">
				<div class="uk-form-row">
					<select name="partner">
					<option<?php if (0 === $this->partner) {echo ' selected';} ?> value="0"> - Укажите партнера - </option>
					<option<?php if ('all' == $this->partner) {echo ' selected';} ?> value="all">Все партнеры</option>
						<?php foreach($this->partners as $j => $partner):
							$selected = $partner->partner_id == $this->partner ? 'selected' : '';
							echo "<option value=\"{$partner->partner_id}\" $selected>{$partner->partner_name}</option>";
							endforeach; ?>
					</select>
					<button type="submit" class="uk-button">
						<i class="uk-icon-list"></i>&nbsp;Показать
					</button>
				</div>
				<div class="uk-form-row">
					<label>
						<input name="onlynull" type="checkbox"<?php if ('on' == $this->onlyNull) echo ' checked="checked"'; ?>>
						 Только непривязанные
					</label>
				</div>
			</form>
		</div>
	</div>

	<div class="uk-grid">
		<div class="uk-width-1-1">
			<button class="uk-button" data-uk-modal="{target:'#createVendorModal'}">
				<i class="uk-icon-building"></i>&nbsp;Добавить производителя
			</button>
		</div>
	</div>
	<?php
		$col = 0;
		if (0 < sizeof($this->synonyms)) : ?>
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<table id="b2b-synonyms" class="uk-table">
			<thead>
				<tr>
					<th class="uk-text-center"><?php echo JText::_('COM_ANODOS_N'); $col++; ?></th>
					<th class="uk-text-left"><?php echo JText::_('COM_ANODOS_PARTNER'); $col++; ?></th>
					<th class="uk-text-left"><?php echo JText::_('COM_ANODOS_NAME'); $col++; ?></th>
					<th class="uk-text-left"><?php echo JText::_('COM_ANODOS_VENDOR'); $col++; ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->synonyms as $i => $synonym): ?>
				<tr data-synonym-id="<?php echo $synonym->synonym_id; ?>">
					<td class="uk-text-center"><?php echo $i+1; ?></td>
					<td class="uk-text-left"><?php echo $synonym->partner_name; ?></td>
					<td class="uk-text-left"><?php echo $synonym->synonym_name; ?></td>
					<td class="uk-text-left uk-form">
						<select
							class="select-vendor"
							data-synonym-id="<?php echo $synonym->synonym_id; ?>"
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
</article>
<div id="createVendorModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Добавить производителя?</h1>
		<div class="uk-form">
			<fieldset>
				<input id="create-vendor-name" class="uk-width-1-1" type="text" name="name" placeholder="Имя производителя">
			</fieldset>
			<fieldset>
				<button id="create-vendor-button" class="uk-button uk-button-primary">Добавить</button>
				<button class="uk-button uk-modal-close">Отменить</button>
			</fieldset>
		</div>
		<hr />
		<div id="create-vendor-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a><p>AJAX готов.</p></div>
		</div>
	</div>
</div>
