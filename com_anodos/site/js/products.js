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

	// Переименование продукта
	$('rename-product-button').addEvent('click', function(event) {
		var id = this.get("data-product-id");
		var name = $('rename-product-name').get('value');
		var container = $('rename-product-messages');
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.renameProduct',
			onSuccess: function(r) {
				if (r.data) {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-success', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Продукт переименован: [' + r.data.id + '] '+ r.data.alias + '.'});
					container.grab(Msg);
				} else {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-danger', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Не получилось переименовать продукт.'});
					container.grab(Msg);
				}
			}
		}).post({'id': id, 'name': name});
	});

	// Вызов окна перемещения продукта
	$$('.move-product').addEvent('click', function(event){
		var id = this.get("data-product-id");
		var category = this.get("data-category-id");
		$('move-product-button').set("data-product-id", id);
		$('move-product-category').set('value', category);
	});

	// Перемещение продукта
	$('move-product-button').addEvent('click', function(event) {
		var id = this.get("data-product-id");
		var category = $('move-product-category').get('value');
		var container = $('move-product-messages');
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.moveProduct',
			onSuccess: function(r) {
				if (r.data) {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-success', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Продукт перемещен: [' + r.data.id + '] '+ r.data.alias + '.'});
					container.grab(Msg);
				} else {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-danger', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Не получилось переместить продукт.'});
					container.grab(Msg);
				}
			}
		}).post({'id': id, 'category': category});
	});
});
