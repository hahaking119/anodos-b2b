<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<ul>
	<li id="pricer-vendor-0" onClick="setVendorSelected(0)"><?php echo JText::_('COM_PRICER_ALL_VENDORS'); ?></li>
<?php foreach($this->vendors as $i => $vendor): ?>
	<li id="pricer-vendor-<?php echo $vendor->id; ?>" onClick="setVendorSelected(<?php echo $vendor->id; ?>)"><?php echo $vendor->title; ?></li>
<?php endforeach; ?>
</ul>
