jQuery(document).ready(function() {

	// Добавляем категорию
	jQuery('#create-category-button').click(function() {

		// Инициализируем переменные
		var name = jQuery('#create-category-name').val();
		var parent = jQuery('#create-category-parent').val();

		// Выполняем запрос
		jQuery.ajax({
			cache: false ,
			data: 'name=' + name + '&parent=' + parent,
			dataType: 'json',
			type: 'POST',
			url: '/index.php?option=com_anodos&task=ajax.createProductCategory',
			success: function(r) {
				if (r.data.status) {
					jQuery('#create-category-messages').prepend('<div class = "uk-alert uk-alert-' + r.data.status + '" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>' + r.data.text + '</div>');
				} else {
					jQuery('#create-category-messages').prepend('<div class = "uk-alert uk-alert-danger" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Create Category Ajax Error</div>');
				}
			},
			error: function() {
				alert('Create Category Ajax Error');
			}
		});
	});

	// Привязываем синоним к категории
	jQuery('.select-category').change(function() {

		// Инициализируем переменные
		var synonym = jQuery(this).data("synonymId");
		var category = jQuery(this).val();
		var element = this;

		// Выполняем запрос
		jQuery.ajax({
			cache: false ,
			data: 'synonym=' + synonym + '&category=' + category,
			dataType: 'json',
			type: 'POST',
			url: '/index.php?option=com_anodos&task=ajax.linkSynonymToCategory',
			success: function(r) {
				if (r.data) {
					jQuery(element).removeClass("uk-form-danger");
					jQuery(element).addClass("uk-form-success");
				} else {
					jQuery(element).removeClass("uk-form-success");
					jQuery(element).addClass("uk-form-danger");
				}
			},
			error: function() {
				jQuery(element).removeClass("uk-form-success");
				jQuery(element).addClass("uk-form-danger");
			}
		});
	});
});
