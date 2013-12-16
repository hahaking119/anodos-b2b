window.addEvent('domready', function() {

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
