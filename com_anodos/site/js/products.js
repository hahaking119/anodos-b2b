function openCategories() {
	$('categories').removeClass('close');
	$('category-selected-square').set('onClick', 'closeCategories()');
	$('category-selected-square').set('text', '⊟');
	$('category-selected-text').set('onClick', 'closeCategories()');
}

function closeCategories() {
	$('categories').addClass('close');
	$('category-selected-square').set('onClick', 'openCategories()');
	$('category-selected-square').set('text', '⊞');
	$('category-selected-text').set('onClick', 'openCategories()');
}

function openCategory(id) {
	$('category-'+id).removeClass('close');
	$('category-square-'+id).set('onClick', 'closeCategory('+id+')');
	$('category-square-'+id).set('text', '⊟');
}

function closeCategory(id) {
	$('category-'+id).addClass('close');
	$('category-square-'+id).set('onClick', 'openCategory('+id+')');
	$('category-square-'+id).set('text', '⊞');
}

function setCategorySelected(id) {
	$('category-selected-text').set('text', $('category-text-'+id).get('text'));
	$('form-category-selected').set('value', id);
	closeCategories();
}

function openVendors() {
	$('vendors').removeClass('close');
	$('vendor-selected-square').set('onClick', 'closeVendors()');
	$('vendor-selected-square').set('text', '⊟');
	$('vendor-selected-text').set('onClick', 'closeVendors()');
}

function closeVendors() {
	$('vendors').addClass('close');
	$('vendor-selected-square').set('onClick', 'openVendors()');
	$('vendor-selected-square').set('text', '⊞');
	$('vendor-selected-text').set('onClick', 'openVendors()');
}

function setVendorSelected(id) {
	$('vendor-selected-text').set('text', $('vendor-'+id).get('text'));
	$('form-vendor-selected').set('value', id);
	closeVendors();
}
