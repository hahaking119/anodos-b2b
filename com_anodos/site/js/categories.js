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
});
