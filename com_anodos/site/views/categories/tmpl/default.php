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
		<button class="uk-button" data-uk-modal="{target:'#createCategoryModal'}"><i class="uk-icon-building"></i>&nbsp;<span id="vendor-selected">Добавить категорию</button>
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
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->categories as $i => $category): ?>
			<tr data-category-id="<?php echo $category->id; ?>">
				<td class="uk-text-center"><?php echo $i+1; ?></td>
				<td class="uk-text-center uk-form">
					<div class="uk-button-group">
						<button
							class="uk-button uk-button-mini rename-category"
							data-uk-modal="{target:'#renameCategoryModal'}"
							data-category-id="<?php echo $category->id; ?>"
							data-category-name="<?php echo $category->title; ?>">
							<i class="uk-icon-edit"></i></button>
						<button
							class="uk-button uk-button-mini remove-category"
							data-uk-modal="{target:'#removeCategoryModal'}"
							data-category-id="<?php echo $category->id; ?>">
							<i class="uk-icon-remove"></i></button>
					</div>
				</td>
				<td class="uk-text-left"><?php echo $category->name; ?></td>
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
		<h1>Добавить категорию?</h1>
		<div class="uk-form">
			<select id="create-category-parent" name="parent" class="inputbox">
				<option value="1" selected> - В корень - </option>
				<?php foreach($this->parentCategoryList as $j => $category):
				echo "<option value=\"{$category->id}\">{$category->title}</option>";
				endforeach; ?>
			</select>
			<input id="create-category-name" type="text" name="name" placeholder="Имя категории">
			<button id="create-category-button" class="uk-button">Добавить</button>
		</div>
		<hr />
		<div id="create-category-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>AJAX готов.</div>
		</div>
	</div>
</div>


<div id="renameCategoryModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Редактировать категорию?</h1>
		<div class="uk-form">
			<input id="rename-category-name" type="text" name="name" placeholder="Имя категории">
			<button id="rename-category-button" class="uk-button">Изменить</button>
		</div>
		<hr />
		<div id="rename-category-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>AJAX готов.</div>
		</div>
	</div>
</div>

<div id="removeCategoryModal" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<h1>Удалить категорию?</h1>
		<div class="uk-form">
			<fieldset>
				<button id="remove-category-button" class="uk-button uk-button-danger" data-category-id="0">Удалить</button>
				<button class="uk-button uk-modal-close">Отменить</button>
			</fieldset>
		</div>
		<hr />
		<div id="remove-category-messages">
			<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>AJAX готов.</div>
		</div>
	</div>
</div>


