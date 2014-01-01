jQuery(document).ready(function() {

	// Обновить список производителей после загрузки страницы
	// Выполняем запрос
	jQuery.ajax({
		cache: false ,
		data: 'category=' + jQuery('#form-category-selected').val(),
		dataType: 'json',
		type: 'POST',
		url: '/index.php?option=com_anodos&task=ajax.getVendorsFromCategory',
		success: function(r) {
			jQuery('button.vendor-name').addClass('hide');
			jQuery('#vendor-all').removeClass('hide');
			if (r.data) {
				for (var i = 0; i < r.data.length; i++) {
					jQuery('#vendor-' + r.data[i].vendor_id).removeClass('hide');
				}
			}
		}
	});

	// Выбираем категорию
	jQuery('.category-name').click(function() {

		// Инициализируем переменные
		var id = jQuery(this).data("categoryId");
		var name = jQuery(this).text();

		// Переопределяем значения
		jQuery('#category-selected').text(name);
		jQuery('#form-category-selected').val(id);
		jQuery('#subcategories-checkbox').attr('checked', true);
		jQuery('#form-subcategories').val(1);
		if ('all' == id) {
			jQuery('#subcategories-checkbox').attr('disabled', true);
		} else {
			jQuery('#subcategories-checkbox').removeAttr('disabled');
		}

		// Выполняем запрос
		jQuery.ajax({
			cache: false ,
			data: 'category=' + id,
			dataType: 'json',
			type: 'POST',
			url: '/index.php?option=com_anodos&task=ajax.getVendorsFromCategory',
			success: function(r) {
				if (r.data) {
					jQuery('button.vendor-name').addClass('hide');
					jQuery('#vendor-all').removeClass('hide');
					if (r.data) {
						for (var i = 0; i < r.data.length; i++) {
							jQuery('#vendor-' + r.data[i].vendor_id).removeClass('hide');
						}
					}
				}
			}
		});
	});

	// Открываем категрию в дереве
	jQuery('.category-square').click(function(){

		// Инициализируем переменные
		var id = jQuery(this).data("categoryId");
		var text = jQuery(this).text();

		// Переопределяем значения
		if ('⊞' == text) {
			jQuery('#category-'+id).removeClass('closed');
			jQuery('#category-square-'+id).text('⊟');
		} else {
			jQuery('#category-'+id).addClass('closed');
			jQuery('#category-square-'+id).text('⊞');
		}
	});

	// Выбираем производителя
	jQuery('.vendor-name').click(function(){

		// Инициализируем переменные
		var id = jQuery(this).data("vendorId");
		var name = jQuery(this).text();

		// Переопределяем значения
		jQuery('#vendor-selected').text(name);
		jQuery('#form-vendor-selected').val(id);
	});

	// Выбираем подкатегорию
	jQuery('#subcategories-checkbox').change(function(){
		if (true == jQuery('#subcategories-checkbox').attr('checked')) {
			jQuery('#form-subcategories').val(1);
		} else {
			jQuery('#form-subcategories').val(0);
		}
	});

	// Нажатие на кнопке "показать"
	jQuery('#show-product-button').click(function(){
		jQuery('#form-show-product').submit();
	});

	// Вызов окна добавления продукта в заказ
	jQuery('.add-to-order').click(function(event){

		// Инициализируем переменные
		var id = jQuery(this).data("productId");
		var name = jQuery('#product-name-' + id).text();

		// Переопределяем значения
		jQuery('#add-to-order-button').data("productId", id);
		jQuery('#add-to-order-product-desc').text(name);
	});

	// Добавление продукта в заказ
	jQuery('#add-to-order-button').click(function() {

		// Инициализируем переменные
		var productId = jQuery(this).data("productId");
		var clientId = jQuery('#add-to-oder-client').val();
		var clientName = jQuery('#add-to-oder-client-name').val();
		var contractorId = jQuery('#add-to-oder-contractor').val();
		var contractorName = jQuery('#add-to-oder-contractor-name').val();
		var orderId = jQuery('#add-to-oder-order').val();
		var orderName = jQuery('#add-to-oder-order-name').val();
		var quantity = jQuery('#add-to-oder-quantity').val();

		// Выполняем запрос
		jQuery.ajax({
			cache: false ,
			data: 'productId=' + productId + '&clientId=' + clientId + '&clientName=' + clientName + '&contractorId=' + contractorId + '&contractorName=' + contractorName + '&orderId=' + orderId + '&orderName=' + orderName + '&quantity=' + quantity,
			dataType: 'json',
			type: 'POST',
			url: '/index.php?option=com_anodos&task=ajax.addToOrder',
			success: function(r) {
				// TODO
				if (r.data.status) {

					// Выводим сообщение из модели
					jQuery('#add-to-order-messages').prepend('<div class = "uk-alert uk-alert-' + r.data.status + '" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>' + r.data.text + '</div>');

					if (r.data.lines) {

						// Готовим таблицу со строками заказа
						jQuery('#add-to-order-products').removeClass('hidden');
						jQuery('#add-to-order-products-list tr').remove();

						// Показываем содержание заказа
						for (var i = 0; i < r.data.lines.length; i++) {
							n = i + 1;
							name = r.data.lines[i].product_name;
							price = String(parseFloat(r.data.lines[i].price_out).toFixed(2));
							quantity = String(parseFloat(r.data.lines[i].quantity).toFixed(0));
							sum = String(parseFloat(r.data.lines[i].price_out * r.data.lines[i].quantity).toFixed(2));
							jQuery('#add-to-order-products-list').append('<tr><td class="uk-text-center">' + n + '</td><td>' + name + '</td><td class="uk-text-right">'+ price + '</td><td class="uk-text-center">' + quantity + '</td><td class="uk-text-right">' + sum + '</td></tr>');
						}
					} else {
						jQuery('#add-to-order-messages').prepend('<div class = "uk-alert uk-alert-danger" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Не получены строки заказа.</div>');
					}
				} else {
					jQuery('#add-to-order-messages').prepend('<div class = "uk-alert uk-alert-danger" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Не удалось добавить продукт в заказ.</div>');
				}
			}
		});
	});
});
