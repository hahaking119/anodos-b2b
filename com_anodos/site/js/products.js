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
		$('form-subcategories').setProperty('value', 1);
		if ('all' == id) {
			$('subcategories-checkbox').setProperty('checked', true);
			$('subcategories-checkbox').setProperty('disabled', true);
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
		var clientId = $('add-to-oder-client').get('value');
		var clientName = $('add-to-oder-client-name').get('value');
		var contractorId = $('add-to-oder-contractor').get('value');
		var contractorName = $('add-to-oder-contractor-name').get('value');
		var orderId = $('add-to-oder-order').get('value');
		var orderName = $('add-to-oder-order-name').get('value');
		var quantity = $('add-to-oder-quantity').get('value');
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.addToOrder',
			onSuccess: function(r) {
				if (r.data) {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-' + r.data.status, 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>' + r.data.text });
					$('add-to-order-messages').grab(Msg);
					$('add-to-order-products').removeClass('hidden');
					$$('#add-to-order-products-list tr').destroy();
					// Показываем содержание заказа
					for (var i = 0; i < r.data.lines.length; i++) {
						n = i + 1;
						name = r.data.lines[i].product_name;
						price = String(parseFloat(r.data.lines[i].price_out).toFixed(2));
						quantity = String(parseFloat(r.data.lines[i].quantity).toFixed(0));
						sum = String(parseFloat(r.data.lines[i].price_out * r.data.lines[i].quantity).toFixed(2));
						var tr = new Element('tr', {html: '<td class="uk-text-center">' + n + '</td><td>' + name + '</td><td class="uk-text-right">'+ price + '</td><td class="uk-text-center">' + quantity + '</td><td class="uk-text-right">' + sum + '</td>'});
						$('add-to-order-products-list').grab(tr);
					}
				} else {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-danger', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Не удалось добавить продукт в заказ.'});
					$('add-to-order-messages').grab(Msg);
				}
			}
		}).post({'productId': productId, 'clientId': clientId, 'clientName': clientName, 'contractorId': contractorId, 'contractorName': contractorName, 'orderId': orderId, 'orderName': orderName, 'quantity': quantity});
	});
});
