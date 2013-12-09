window.addEvent('domready', function() {

	// Добавляем категории в базу
	$('create-category-button').addEvent('click', function(event) {
		var name = $('create-category-name').get('value');
		var parent = $('create-category-parent').get('value');
		var container = $('create-category-messages');
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.createProductCategory',
			onSuccess: function(r) {
				if (r.data) {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-success', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Создана категория: [' + r.data.id + '] '+ r.data.title + '.'});
					container.grab(Msg);
				} else {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-danger', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Не удалось создать категорию.'});
					container.grab(Msg);
				}
			}
		}).post({'name': name, 'parent': parent});
	});

	// Привязываем синоним к производителю
	$$('.select-category').addEvent('change', function(event){
		var synonym = this.get("data-synonym-id");
		var category = this.get('value');
		var element = this;
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.linkSynonymToCategory',
			onSuccess: function(r) {
				if (r.data) {
					element.addClass('uk-form-success');
				} else {
					element.addClass('uk-form-danger');
				}
			}
		}).post({'category': category, 'synonym': synonym});
	});
});
