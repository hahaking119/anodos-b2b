function openCategories() {
	$('#b2b-categories').removeClass('closed');
	$('#category-selected-square').attr('onClick', 'closeCategories()');
	$('#category-selected-square').attr('text', '⊟');
	$('#category-selected-text').attr('onClick', 'closeCategories()');
}

function closeCategories() {
	$('#b2b-categories').addClass('closed');
	$('#category-selected-square').attr('onClick', 'openCategories()');
	$('#category-selected-square').attr('text', '⊞');
	$('#category-selected-text').attr('onClick', 'openCategories()');
}

function openCategory(id) {
	$('#category-'+id).removeClass('closed');
	$('#category-square-'+id).attr('onClick', 'closeCategory('+id+')');
	$('#category-square-'+id).attr('text', '⊟');
}

function closeCategory(id) {
	$('#category-'+id).addClass('closed');
	$('#category-square-'+id).attr('onClick', 'openCategory('+id+')');
	$('#category-square-'+id).attr('text', '⊞');
}

function setCategorySelected(id) {
	$('#category-selected-text').text($('#category-text-'+id).text());
	$('#form-category-selected').attr('value', id);
	closeCategories();
}

function openVendors() {
	$('#b2b-vendors').removeClass('closed');
	$('#vendor-selected-square').attr('onClick', 'closeVendors()');
	$('#vendor-selected-square').attr('text', '⊟');
	$('#vendor-selected-text').attr('onClick', 'closeVendors()');
}

function closeVendors() {
	$('#b2b-vendors').addClass('closed');
	$('#vendor-selected-square').attr('onClick', 'openVendors()');
	$('#vendor-selected-square').attr('text', '⊞');
	$('#vendor-selected-text').attr('onClick', 'openVendors()');
}

function setVendorSelected(id) {
	$('#vendor-selected-text').text($('#vendor-'+id).text());
	$('#form-vendor-selected').attr('value', id);
	closeVendors();
}
