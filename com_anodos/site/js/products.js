window.addEvent('domready', function() {

	// Обновить список производителей после загрузки страницы
	new Request.JSON({
		url:'/index.php?option=com_anodos&task=products.getVendorsFromCategory',
		onSuccess: function(r) {

			// Прячем производителей
			$$("button.vendor-name").addClass('hide');
			$('vendor-all').removeClass('hide');

			if (r.data) {
				// Показываем нужных производителей
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

		// Обновить список производителей
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=products.getVendorsFromCategory',
			onSuccess: function(r) {

				// Прячем производителей
				$$("button.vendor-name").addClass('hide');
				$('vendor-all').removeClass('hide');

				if (r.data) {
					// Показываем нужных производителей
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
});
