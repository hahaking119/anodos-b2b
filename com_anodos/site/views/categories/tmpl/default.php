<?php
defined('_JEXEC') or die('Restricted access');

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/categories.js', 'text/javascript', true);
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');

// Определяем уровень доступа
require_once JPATH_COMPONENT.'/helpers/anodos.php';
$canDo = AnodosHelper::getActions();
if (!$canDo->get('core.admin')) { die('Restricted access'); }

echo $this->msg;
?>

<div class="uk-grid">
	<div class="uk-width-1-1">
		<button class="uk-button" data-uk-modal="{target:'#addCategoryModal'}"><i class="uk-icon-building"></i>&nbsp;<span id="vendor-selected">Добавить категорию</button>
	</div>
</div>

<?php
	$col = 0;
	if (0 < sizeof($this->categories)) :
?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<table id="b2b-synonyms" class="uk-table">
		<thead>
			<tr>
				<th class="uk-text-center"><?php echo JText::_('COM_ANODOS_N'); $col++; ?></th>
				<th class="uk-text-center"><i class="uk-icon-edit"></i><?php $col++; ?></th>
				<th class="uk-text-left"><?php echo JText::_('COM_ANODOS_NAME'); $col++; ?></th>
				<th class="uk-text-left"><?php echo JText::_('COM_ANODOS_PARENT'); $col++; ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->categories as $i => $category): ?>
			<tr data-category-id="<?php echo $category->id; ?>">
				<td class="uk-text-center"><?php echo $i+1; ?></td>
				<td class="uk-form">
					<button
						class="uk-button uk-button-mini"
						data-uk-modal="{target:'#removeCategoryModal'}"
						data-category-id="<?php echo $category->id; ?>">
						<i class="uk-icon-remove"></i></button></td>
				<td class="uk-text-left"><?php echo $category->name; ?></td>
				<td class="uk-text-left">
					<select
						class="select-category"
						data-category-id="<?php echo $category->parent_id; ?>"
					>
						<?php $selected = isset($category->parent_id) ? '' : ' selected'; ?>
						<option value="1"<?php echo $selected; ?>> - В корень - </option>
						<?php foreach($this->parentCategoryList as $j => $parent):
							$selected = $parent->id == $category->parent_id ? ' selected' : '';
							echo "<option value=\"{$parent->id}\"{$selected}>{$parent->name}</option>";
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

<div id="addCategoryModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Добавить категорию?</h1>
		<div class="uk-form">
			<select id="add-category-parent" name="parent" class="inputbox">
				<option value="1" selected> - В корень - </option>
				<?php foreach($this->parentCategoryList as $j => $category):
				echo "<option value=\"{$category->id}\">{$category->title}</option>";
				endforeach; ?>
			</select>
			<input id="add-category-name" type="text" name="name" placeholder="Имя категории">
			<button id="add-category-button" class="uk-button uk-modal-close">Добавить</button>
		</div>
		<hr />
		<div id="add-category-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a><p>AJAX готов.</p></div>
		</div>
	</div>
</div>

<div id="removeCategoryModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Удалить категорию?</h1>
		<div class="uk-form">
			<fieldset>
				<button id="delete-category-button" class="uk-button uk-button-danger uk-modal-close">Удалить</button>
				<button class="uk-button uk-modal-close">Отменить</button>
			</fieldset>
		</div>
		<hr />
		<div id="edit-product-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>AJAX готов.</div>
		</div>
	</div>
</div>


