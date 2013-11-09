function openCategory(id) {
	$('#category-'+id).removeClass('closed');
	$('#category-square-'+id).attr('onClick', 'closeCategory('+id+')');
	$('#category-square-'+id).text('⊟');
}

function closeCategory(id) {
	$('#category-'+id).addClass('closed');
	$('#category-square-'+id).attr('onClick', 'openCategory('+id+')');
	$('#category-square-'+id).text('⊞');
}

function setCategorySelected(id) {
	$('#category-selected-text').text($('#category-text-'+id).text());
	$('#form-category-selected').attr('value', id);
	if ("all" == id) {
		$('#form-subcategories').prop('checked', true);
		$('#form-subcategories').prop('disabled', true);
	} else {
		$('#form-subcategories').prop('disabled', false);
	}
	$('#selectCategoryModal').modal('hide');
}

function setVendorSelected(id) {
	$('#vendor-selected-text').text($('#vendor-'+id).text());
	$('#form-vendor-selected').attr('value', id);
	$('#selectVendorModal').modal('hide');
}
