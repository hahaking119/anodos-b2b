<?php
defined('_JEXEC') or die('Restricted access');

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/categorysynonyms.js', 'text/javascript', true);
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');

// Определяем уровень доступа
require_once JPATH_COMPONENT.'/helpers/anodos.php';
$canDo = AnodosHelper::getActions();
if (!$canDo->get('core.admin')) { die('Restricted access'); }

echo $this->msg;
?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<form class="uk-form" action="" method="get">
			<select name="partner" class="form-control">
				<option<?php if (0 === $this->partner) {echo ' selected';} ?> value="0"> - Укажите партнера - </option>
				<option<?php if ('all' == $this->partner) {echo ' selected';} ?> value="all">Все партнеры</option>
					<?php foreach($this->partners as $j => $partner):
						$selected = $partner->partner_id == $this->partner ? 'selected' : '';
						echo "<option value=\"{$partner->partner_id}\" $selected>{$partner->partner_name}</option>";
						endforeach; ?>
			</select>
			<button type="submit" class="btn btn-default"><i class="uk-icon-list"></i>&nbsp;<?php echo JText::_('COM_ANODOS_SHOW'); ?></button>
			<label><input name="onlynull" type="checkbox"<?php if ('on' == $this->onlyNull) echo ' checked="checked"'; ?>> Только непривязанные</label>
		</form>
	</div>
</div>

<div class="uk-grid">
	<div class="uk-width-1-1">
		<button class="uk-button" data-uk-modal="{target:'#createCategoryModal'}"><i class="uk-icon-building"></i>&nbsp;<span id="vendor-selected">Добавить категорию</button>
	</div>
</div>

<?php
	$col = 0;
	if (0 < sizeof($this->synonyms)) :
?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<table id="b2b-synonyms" class="uk-table">
		<thead>
			<tr>
				<th class="uk-text-center"><?php echo JText::_('COM_ANODOS_N'); $col++; ?></th>
				<th class="uk-text-left"><?php echo JText::_('COM_ANODOS_PARTNER'); $col++; ?></th>
				<th class="uk-text-left"><?php echo JText::_('COM_ANODOS_NAME'); $col++; ?></th>
				<th class="uk-text-left"><?php echo JText::_('COM_ANODOS_CATEGORY'); $col++; ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->synonyms as $i => $synonym): 
				// TODO написать ajax привязывающий категорию
			?>
			<tr data-synonym-id="<?php echo $synonym->synonym_id; ?>">
				<td class="uk-text-center"><?php echo $i+1; ?></td>
				<td class="uk-text-left"><?php echo $synonym->partner_name; ?></td>
				<td class="uk-text-left"><?php echo $synonym->synonym_name; ?></td>
				<td class="uk-text-left">
					<select
						class="select-category"
						data-synonym-id="<?php echo $synonym->synonym_id; ?>"
					>
						<?php $selected = isset($synonym->category_id) ? '' : ' selected'; ?>
						<option value="NULL"<?php echo $selected; ?>> - Без привязки - </option>
						<?php foreach($this->categories as $j => $category):
							$selected = $category->id == $synonym->category_id ? ' selected' : '';
							echo "<option value=\"{$category->id}\"{$selected}>{$category->title}</option>";
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

<div id="createCategoryModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Создать категорию?</h1>
		<div class="uk-form">
			<fieldset>
				<select id="create-category-parent" class="uk-width-1-1 inputbox" name="parent">
					<option value="1" selected> - В корень - </option>
					<?php foreach($this->parentCategoryList as $j => $category):
					echo "<option value=\"{$category->id}\">{$category->title}</option>";
					endforeach; ?>
				</select>
			</fieldset>
			<fieldset>
				<input id="create-category-name" class="uk-width-1-1" type="text" name="name" placeholder="Имя категории">
			</fieldset>
			<fieldset>
				<button id="create-category-button" class="uk-button uk-button-primary">Создать</button>
				<button class="uk-button uk-modal-close">Отменить</button>
			</fieldset>
		</div>
		<hr />
		<div id="create-category-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a><p>AJAX готов.</p></div>
		</div>
	</div>
</div>
