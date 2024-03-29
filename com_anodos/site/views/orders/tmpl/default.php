<?php
defined('_JEXEC') or die('Restricted access');

// Определяем уровень доступа
require_once JPATH_COMPONENT.'/helpers/anodos.php';
$canDo = AnodosHelper::getActions();

// Подключаем скрипты и стили
$doc = JFactory::getDocument();
$doc->addScript($this->baseurl.'/components/com_anodos/js/orders.js', 'text/javascript', true);
if ($canDo->get('core.sale')) {
	$doc->addScript($this->baseurl.'/components/com_anodos/js/orders-sale.js', 'text/javascript', true);
}
$doc->addStyleSheet($this->baseurl.'/components/com_anodos/css/style.css');
?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<div class="uk-form">
			<?php if ($canDo->get('core.sale')) : ?>
			<select id="client-selected">
				<?php $selected = ($this->client = 0) ? ' selected' : ''; ?>
				<option value="0"<?php echo $selected; ?>>Все заказчики</option>
				<?php foreach($this->clients as $j => $client):
					$selected = $client->id == $this->client ? ' selected' : '';
					echo "<option value=\"{$client->id}\"{$selected}>{$client->name}</option>";
				endforeach; ?>
			</select>
			<?php else : ?>
			<input id="client-selected" type="hidden" value="0"/>
			<?php endif; ?>
			<select id="contractor-selected">
				<?php $selected = ($this->contractor = 0) ? ' selected' : ''; ?>
				<option value="0"<?php echo $selected; ?>>Все юридические лица</option>
				<?php foreach($this->contractors as $j => $contractor):
					$selected = $contractor->id == $this->contractor ? ' selected' : '';
					echo "<option value=\"{$contractor->id}\"{$selected}>{$contractor->name}</option>";
				endforeach; ?>
			</select>
			<select id="stage-selected">
				<?php $selected = ($this->stage = 0) ? ' selected' : ''; ?>
				<option value="0"<?php echo $selected; ?>>Любое состояние заказа</option>
				<?php foreach($this->stages as $j => $stage):
					$selected = $stage->id == $this->stage ? ' selected' : '';
					echo "<option value=\"{$stage->id}\"{$selected}>{$stage->name}</option>";
				endforeach; ?>
			</select>
			<input id="name-filtered" type="text" placeholder="Фильтр по названию" value="<?php echo $this->name; ?>"/>
			<button id="orders-show" class="uk-button">
				<i class="uk-icon-list"></i>&nbsp;<span id="vendor-selected">Показать</span>
			</button>
		</div>
	</div>
</div>

<?php
	$col = 0;
	$lastCategoryId = 0;
	if (isset($this->orders)) :
?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<table id="orders-list" class="uk-table">
		<thead>
			<tr>
				<th class="uk-text-center"><i class="uk-icon-list-ol"></i><?php $col++; ?></th>
				<?php if ($canDo->get('core.sale')) : ?>
				<th class="uk-text-center">Партнер</th>
				<?php endif; ?>
				<th class="uk-text-center">Юридическое лицо</th>
				<th class="uk-text-center">Название</th>
				<th class="uk-text-center">Стоимость</th>
				<th class="uk-text-center">Статус</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->orders as $i => $order): ?>
			<tr>
				<td class="uk-text-center"><?php echo $i+1; ?></td>
				<?php if ($canDo->get('core.sale')) : ?>
				<td class="uk-text-left"><?php
					if (isset($order->partner_name)) {
						echo $order->partner_name;
					} elseif (isset($order->partner_name_draft)) {
						echo $order->partner_name_draft;
						echo '<span class="uk-badge uk-badge-warning">Черновик</span>';
					} else {
						echo 'Не указан';
					}
				?></td>
				<?php endif; ?>
				<td class="uk-text-left"><?php
					if (isset($order->contractor_name)) {
						echo $order->contractor_name;
					} elseif (isset($order->contractor_name_draft)) {
						echo $order->contractor_name_draft;
						echo '<span class="uk-badge uk-badge-warning">Черновик</span>';
					} else {
						echo 'Не указан';
					}
				?></td>
				<td class="uk-text-left"><?php echo $order->order_name; ?></td>
				<td class="uk-text-right"><?php echo $order->order_sum; ?>&nbsp;р.</td>
				<td class="uk-text-center">
					<button
						class="uk-button"
						data-uk-modal="{target:'#editStageModal'}"
						data-task="edit-stage"
						data-order-id="<?php echo $order->order_id; ?>">
						<i class="uk-icon-edit"></i> <?php echo $order->order_stage; ?></button>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	</div>
</div>
<?php endif; ?>
