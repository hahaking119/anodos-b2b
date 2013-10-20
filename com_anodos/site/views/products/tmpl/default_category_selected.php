<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<div id="pricer-category-selected">
<?php
if (true == $this->categorySelected) {
	echo "<span id=\"pricer-category-selected-square\" onClick=\"openCategories()\">&#8862;</span>";
	echo "<span>&nbsp;</span>";
	echo "<span id=\"pricer-category-selected-text\" onClick=\"openCategories()\">
	{$this->categorySelected->title}
	</span>";
} else {
	echo "<span id=\"pricer-category-selected-square\" onClick=\"openCategories()\">&#8862;</span>";
	echo "<span>&nbsp;</span>";
	echo "<span id=\"pricer-category-selected-text\" onClick=\"openCategories()\">";
	echo JText::_('COM_PRICER_SELECT_CATEGORY');
	echo "</span>";
}
?>
</div>
