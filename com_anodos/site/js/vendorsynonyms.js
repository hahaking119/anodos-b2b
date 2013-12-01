window.addEvent('domready', function() {

	// Добавляем производителя в базу
	$('add-vendor-button').addEvent('click', function(event){
		var container = $('add-vendor-messages');
		var name = $('add-vendor-name').get('value');
		var vendorHTMLRequest = new Request({
			url:'/index.php',
			method:'get',
			data: 'option=com_anodos&task=updater.addVendor&name=' + name,
			onProgress: function(event, xhr){
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
	$$('.select-vendor').addEvent('change', function(event){
		var synonym = this.get("data-synonym-id");
		var vendor = this.get('value');
		var element = this;

		var synonymHTMLRequest = new Request({
			url:'/index.php',
			method:'get',
			data: 'option=com_anodos&task=updater.linkSynonymToVendor&vendor=' + vendor + '&synonym=' + synonym,
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
