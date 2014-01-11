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
<article class="uk-article">
	<h1 class="uk-article-title">B2B-система</h1>
	<div class="uk-grid">
		<div class="uk-width-large-3-4 uk-width-medium-2-3">
			<div class="uk-panel uk-panel-box">
				<?php if ($this->tasks) : ?>
				 <h3>Задачи</h3>
				<?php endif; ?>
			</div>
			<div class="uk-panel uk-panel-box">
				<?php if ($this->orders) : ?>
				<h3>Активные заказы</h3>
				<?php endif; ?>
			</div>
		</div>
		<div class="uk-width-large-1-4 uk-width-medium-1-3">
			<div class="uk-panel uk-panel-box">
				<?php if ($this->person) : ?>
				<div class="uk-form">
					<?php foreach($this->person->info as $i => $info): ?>
					<div class="tm-form-icon uk-form-icon">
						<i class="uk-icon-<?php echo $info->type_icon ?>"></i>
						<input
							type="text"
							placeholder="<?php echo $info->name ?>"
							data-info-id="<?php echo $info->id ?>"
							value="<?php echo $info->content ?>"
						/>
					</div>
					<?php endforeach; ?>
				</div>
				<?php else: ?>
					<p>Не желаете ли авторизоваться или <a href="<?php echo $this->baseurl.'/component/users/?view=registration'?>">​​зарегистрироваться</a>?​</p>
				<?php endif; ?>
			</div>
			<div class="uk-panel uk-panel-box">
				<?php if ($this->manager) : ?>
				<h3>Менеджер</h3>
				<?php endif; ?>
			</div>
			<div class="uk-panel uk-panel-box">
				<?php if ($this->partners) : ?>
				<h3>Партнеры</h3>
				<?php endif; ?>
			</div>
			<div class="uk-panel uk-panel-box">
				<?php if ($this->contractors) : ?>
				<h3>Юридические лица</h3>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>
