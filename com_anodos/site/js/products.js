window.addEvent('domready', function() {

	// Обновить список производителей после загрузки страницы
	new Request.JSON({
		url:'/index.php?option=com_anodos&task=products.getVendorsFromCategory',
		onSuccess: function(r) {

			$$("button.vendor-name").addClass('hide');
			$('vendor-all').removeClass('hide');

			if (r.data) {
				for (var i = 0; i < r.data.length; i++) {
					$('vendor-' + r.data[i].vendor_id).removeClass('hide');
				}
			}
		}.bind(this),
	}).get({'category': $('form-category-selected').getProperty('value')});

	// Выбираем категорию
	$$('.category-name').addEvent('click', function(event){
		var id = this.get("data-category-id");
		var name = this.get('text');
		$('category-selected').set('text', name);
		$('form-category-selected').setProperty('value', id);
		if ('all' == id) {
			$('subcategories-checkbox').setProperty('checked', true);
			$('subcategories-checkbox').setProperty('disabled', true);
			$('form-subcategories').setProperty('value', 1);
		} else {
			$('subcategories-checkbox').removeProperty('disabled');
		}

		new Request.JSON({
			url:'/index.php?option=com_anodos&task=products.getVendorsFromCategory',
			onSuccess: function(r) {

				$$("button.vendor-name").addClass('hide');
				$('vendor-all').removeClass('hide');

				if (r.data) {
					for (var i = 0; i < r.data.length; i++) {
						$('vendor-' + r.data[i].vendor_id).removeClass('hide');
					}
				}
			}.bind(this),
		}).get({'category': id});
	});

	// Открываем категрию в дереве
	$$('.category-square').addEvent('click', function(event){
		var id = this.get("data-category-id");
		var text = this.get('text');
		if ('⊞' == text) {
			$('category-'+id).removeClass('closed');
			$('category-square-'+id).set('text', '⊟');
		} else {
			$('category-'+id).addClass('closed');
			$('category-square-'+id).set('text', '⊞');
		}
	});

	// Выбираем категорию
	$$('.vendor-name').addEvent('click', function(event){
		var id = this.get("data-vendor-id");
		var name = this.get('text');
		$('vendor-selected').set('text', name);
		$('form-vendor-selected').setProperty('value', id);
	});

	// Выбираем подкатегорию
	$('subcategories-checkbox').addEvent('change', function(event){
		if (true == $('subcategories-checkbox').getProperty('checked')) {
			$('form-subcategories').setProperty('value', 1);
		} else {
			$('form-subcategories').setProperty('value', 0);
		}
	});

	// Нажатие на кнопке "показать"
	$('show-product-button').addEvent('click', function(event){
		$('form-show-product').submit();
	});

	// Вызов окна переименования продукта
	$$('.rename-product').addEvent('click', function(event){
		var id = this.get("data-product-id");
		$('rename-product-button').set("data-product-id", this.get("data-product-id"));
		$('rename-product-name').set('text', $('product-name-' + id).get('text'));
		$('rename-product-name').set('value', $('product-name-' + id).get('text'));
	});

	// Вызов окна добавления продукта в заказ
	$$('.add-to-order').addEvent('click', function(event){
		var id = this.get("data-product-id");
		$('add-to-order-button').set("data-product-id", this.get("data-product-id"));
		$('add-to-order-product-desc').set('text', $('product-name-' + id).get('text'));
	});

	// Добавление продукта в заказ
	$('add-to-order-button').addEvent('click', function(event) {
		var productId = this.get("data-product-id");
		var orderId = $('add-to-oder-order').get('value');
		var orderName = $('add-to-oder-order-name').get('value');
		var clientId = $('add-to-oder-client').get('value');
		var clientName = $('add-to-oder-client-name').get('value');
		var quantity = $('add-to-oder-quantity').get('value');
		var container = $('add-to-order-products-from-order');
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.addToOrder',
			onSuccess: function(r) {
				if (r.data) {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-' + r.data.status, 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>' + r.data.text });
					container.grab(Msg);
					container.removeClass('hidden');
				} else {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-danger', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Не удалось добавить продукт в заказ.'});
					container.grab(Msg);
					container.removeClass('hidden');
				}
			}
		}).post({'productId': productId, 'orderId': orderId, 'orderName': orderName, 'clientId': clientId, 'clientName': clientName, 'quantity': quantity});
	});
});
