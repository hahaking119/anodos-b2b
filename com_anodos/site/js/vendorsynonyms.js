window.addEvent('domready', function() {

	// Добавляем производителя в базу
	$('create-vendor-button').addEvent('click', function(event){
		var name = $('create-vendor-name').get('value');
		var container = $('create-vendor-messages');
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.createVendor',
			onSuccess: function(r) {
				if (r.data) {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-success', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Добавлен производитель: [' + r.data.id + '] '+ name + '.'});
					container.grab(Msg);
				} else {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-danger', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Не удалось добавить производителя.'});
					container.grab(Msg);
				}
			}
		}).post({'name': name});
	});

	// Привязываем синоним к производителю
	$$('.select-vendor').addEvent('change', function(event){
		var synonym = this.get("data-synonym-id");
		var vendor = this.get('value');
		var element = this;
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.linkSynonymToVendor',
			onSuccess: function(r) {
				if (r.data) {
					element.addClass('uk-form-success');
				} else {
					element.addClass('uk-form-danger');
				}
			}
		}).post({'synonym': synonym, 'vendor': vendor});
	});
});
