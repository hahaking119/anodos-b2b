function linkSynonymToCategory(synonym) {

	category = $("#select-category-" + synonym + " option:selected").val();

	$.get(
		"/index.php",
		{
			option: "com_anodos",
			synonym: synonym,
			task: "updater.linksynonymtocategory",
			category: category
		},
		function(xml) { // Функция обработки ответа

		}
	)
}
