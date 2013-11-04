function linkSynonymToVendor(synonym) {

	vendor = $("#select-vendor-" + synonym + " option:selected").val();

	$.get(
		"/index.php",
		{
			option: "com_anodos",
			synonym: synonym,
			task: "updater.linksynonymtovendor",
			vendor: vendor
		},
		function(xml) { // Функция обработки ответа

		}
	)
}
