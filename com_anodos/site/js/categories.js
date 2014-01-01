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

	// Вызов окна переименования категории
	jQuery('.rename-category').click(function() {
		jQuery('#rename-category-button').data("categoryId", jQuery(this).data("categoryId"));
		jQuery('#rename-category-name').val(jQuery(this).data("categoryName"));
	});

	// Переименование категории
	jQuery('#rename-category-button').click(function() {

		// Инициализируем переменные
		var id = jQuery(this).data("categoryId");
		var name = jQuery('#rename-category-name').val();

		// Выполняем запрос
		jQuery.ajax({
			cache: false ,
			data: 'id=' + id + '&name=' + name,
			dataType: 'json',
			type: 'POST',
			url: '/index.php?option=com_anodos&task=ajax.renameCategory',
			success: function(r) {
				if (r.data.status) {
					jQuery('#rename-category-messages').prepend('<div class = "uk-alert uk-alert-' + r.data.status + '" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>' + r.data.text + '</div>');
				} else {
					jQuery('#rename-category-messages').prepend('<div class = "uk-alert uk-alert-danger" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Rename Category Ajax Error</div>');
				}
			},
			error: function() {
				alert('Rename Category Ajax Error');
			}
		});
	});

	// Вызов окна удаления категории
	jQuery('.remove-category').click(function() {
		jQuery('#remove-category-button').data("categoryId", jQuery(this).data("categoryId"));
	});

	// Удаление категории
	jQuery('#remove-category-button').click(function() {

		// Инициализируем переменные
		var id = jQuery(this).data("categoryId");

		// Выполняем запрос
		jQuery.ajax({
			cache: false ,
			data: 'id=' + id,
			dataType: 'json',
			type: 'POST',
			url: '/index.php?option=com_anodos&task=ajax.removeProductCategory',
			success: function(r) {
				if (r.data.status) {
					jQuery('#remove-category-messages').prepend('<div class = "uk-alert uk-alert-' + r.data.status + '" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>' + r.data.text + '</div>');
					if (r.data.categories) {
						for (var i = 0; i < r.data.categories.length; i++) {
							jQuery('#remove-category-messages').prepend('<div class = "uk-alert uk-alert-success" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Удалена категория: ' + r.data.categories[i].title + '</div>');
						}
					}
				} else {
					jQuery('#remove-category-messages').prepend('<div class = "uk-alert uk-alert-danger" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Remove Category Ajax Error</div>');
				}
			},
			error: function() {
				alert('Remove Category Ajax Error');
			}
		});
	});
});
