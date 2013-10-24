<?php
defined('_JEXEC') or die('Restricted Access');
?>
<div id="category-selected">
<?php
if (true == $this->categorySelected) {
	echo "<span id=\"category-selected-square\" onClick=\"openCategories()\">&#8862;</span>";
	echo "<span>&nbsp;</span>";
	echo "<span id=\"category-selected-text\" onClick=\"openCategories()\">
	{$this->categorySelected->title}
	</span>";
} else {
	echo "<span id=\"category-selected-square\" onClick=\"openCategories()\">&#8862;</span>";
	echo "<span>&nbsp;</span>";
	echo "<span id=\"category-selected-text\" onClick=\"openCategories()\">";
	echo JText::_('COM_ANODOS_SELECT_CATEGORY');
	echo "</span>";
}
?>
</div>
