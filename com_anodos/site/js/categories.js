window.addEvent('domready', function() {

	// Добавляем категории в базу
	$('add-category-button').addEvent('click', function(event) {
		var container = $('add-category-messages');
		var name = $('add-category-name').get('value');
		var parent = $('add-category-parent').get('value');
		var categoryHTMLRequest = new Request({
			url:'/index.php',
			method:'get',
			data: 'option=com_anodos&task=updater.addProductCategory&name=' + name + '&parent=' + parent,
			onProgress: function(event, xhr) {
				// Действия во время выполнения запроса
			},
			onSuccess: function(responseText) {
				container.set('text', responseText);
			},
			onFailure: function() {
				container.set('html', 'The request failed.');
			},
			onCancel: function() {
				container.set('html', 'The request calcelled.');
			}
		}).send();
	});

	// Вызов окна переименования категории
	$$('.rename-category').addEvent('click', function(event){
		$('rename-category-button').set("data-category-id", this.get("data-category-id"));
		$('rename-category-name').set('value', this.get("data-category-name"));
	});

	// Редактирование категории
	$('rename-category-button').addEvent('click', function(event) {
		var id = this.get("data-category-id");
		var name = $('rename-category-name').get('value');
		var container = $('rename-category-messages');

		// Обновить список производителей
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.renameCategory',
			onSuccess: function(r) {

				if (r.data) {
					// Показываем переименованную категорию
					var Msg = new Element('div', {'class': 'uk-alert', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Категория переименована: [' + r.data.id + '] '+ r.data.title + '.'});
					container.grab(Msg);
				} else {
					var Msg = new Element('div', {'class': 'uk-alert uk-alert-danger', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Не получилось переименовать категорию.'});
					container.grab(Msg);
				}
			}
		}).get({'id': id, 'name': name});
	});

	// Вызов окна удаления категории
	$$('.remove-category').addEvent('click', function(event){
		$('remove-category-button').set("data-category-id", this.get("data-category-id"));
	});

	// Удаление категории
	$('remove-category-button').addEvent('click', function(event) {
		var id = this.get("data-category-id");
		var container = $('remove-category-messages');

		// Обновить список производителей
		new Request.JSON({
			url:'/index.php?option=com_anodos&task=ajax.removeProductCategory',
			onSuccess: function(r) {

				if (r.data) {
					// Показываем удаленные категории
					for (var i = 0; i < r.data.length; i++) {
						var Msg = new Element('div', {'class': 'uk-alert', 'data-uk-alert': '', html: '<a href="" class="uk-alert-close uk-close"></a>Удалена категория: [' + r.data[i].id + '] '+ r.data[i].title + '.'});
						container.grab(Msg);
					}
				}
			}
		}).get({'id': id});
	});
});
