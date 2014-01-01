jQuery(document).ready(function() {

	// Вызов окна переименования продукта
	jQuery('.rename-product').click(function(){

		// Инициализируем переменные
		var id = jQuery(this).data("productId");
		var name = jQuery('#product-name-' + id).text();

		// Переопределяем значения
		jQuery('#rename-product-button').data("productId", id);
		jQuery('#rename-product-name').val(name);
	});

	// Переименование продукта TODO
	jQuery('#rename-product-button').click(function() {

		// Инициализируем переменные
		var id = jQuery(this).data("productId");
		var name = jQuery('#rename-product-name').val();

		// Выполняем запрос
		jQuery.ajax({
			cache: false ,
			data: 'id=' + id + '&name=' + name,
			dataType: 'json',
			type: 'POST',
			url: '/index.php?option=com_anodos&task=ajax.renameProduct',
			success: function(r) {
				if (r.data.status) {
					jQuery('#rename-product-messages').prepend('<div class = "uk-alert uk-alert-' + r.data.status + '" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>' + r.data.text + '</div>');
					if (r.data.product.name) {
						jQuery('#product-name-' + id).text(r.data.product.name);
					}
				} else {
					jQuery('#rename-product-messages').prepend('<div class = "uk-alert uk-alert-danger" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Rename Product Ajax Error</div>');
				}
			}
		});
	});

	// Вызов окна перемещения продукта
	jQuery('.move-product').click(function(event){

		// Инициализируем переменные
		var productId = jQuery(this).data("productId");
		var categoryId = jQuery(this).data("categoryId");

		// Переопределяем значения
		jQuery('#move-product-button').data("productId", productId);
		jQuery('#move-product-category').val(categoryId);
	});

	// Перемещение продукта
	jQuery('#move-product-button').click(function(event) {

		// Инициализируем переменные
		var id = jQuery(this).data("productId");
		var category = jQuery('#move-product-category').val();

		// Выполняем запрос
		jQuery.ajax({
			cache: false ,
			data: 'id=' + id + '&category=' + category,
			dataType: 'json',
			type: 'POST',
			url: '/index.php?option=com_anodos&task=ajax.moveProduct',
			success: function(r) {
				if (r.data.status) {
					jQuery('#move-product-messages').prepend('<div class = "uk-alert uk-alert-' + r.data.status + '" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>' + r.data.text + '</div>');
				} else {
					jQuery('#move-product-messages').prepend('<div class = "uk-alert uk-alert-danger" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Move Product Ajax Error</div>');
				}
			}
		});
	});
});
