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

	// Привязываем синоним к производителю
	$$('.select-category').addEvent('change', function(event){
		var synonym = this.get("data-synonym-id");
		var category = this.get('value');
		var element = this;
		var synonymHTMLRequest = new Request({
			url:'/index.php',
			method:'get',
			data: 'option=com_anodos&task=updater.linkSynonymToCategory&category=' + category + '&synonym=' + synonym,
			onProgress: function(event, xhr){
			},
			onSuccess: function(responseText) {
				if ("ok\n" == responseText) {
					element.addClass('uk-form-success');
				} else {
					element.addClass('uk-form-danger');
				}
			},
			onFailure: function() {
				element.addClass('uk-form-danger');
			},
			onCancel: function() {
				element.addClass('uk-form-danger');
			}
		}).send();
	});
});
