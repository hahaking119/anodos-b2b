<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<div id="pricer-vendor-selected">
<?php
if (true == $this->vendorSelected) {
	echo "<span id=\"pricer-vendor-selected-square\" onClick=\"openVendors()\">&#8862;</span>&nbsp;";
	echo "<span id=\"pricer-vendor-selected-text\" onClick=\"openVendors()\">{$this->vendorSelected->title}</span>";
} else {
	echo "<span id=\"pricer-vendor-selected-square\" onClick=\"openVendors()\">&#8862;</span>&nbsp;";
	echo "<span id=\"pricer-vendor-selected-text\" onClick=\"openVendors()\">";
	echo JText::_('COM_PRICER_ALL_VENDORS');
	echo "</span>";
}
?>
</div>
