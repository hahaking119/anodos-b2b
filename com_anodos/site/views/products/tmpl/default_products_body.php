<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<?php foreach($this->products as $i => $product): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td id="td-<?php echo $i; ?>"><?php echo $i; ?></td>
		<td><?php echo $product->name; ?></td>
	</tr>
<?php endforeach; ?>
