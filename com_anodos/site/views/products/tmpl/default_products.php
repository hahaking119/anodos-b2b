<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// Инициализируем переменные
$col = 0;
$lastCategoryId = 0;
?>
<table class="data">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_('COM_PRICER_N'); $col++; ?></th>
			<th><?php echo JText::_('COM_PRICER_ARTICLE'); $col++; ?></th>
			<th><?php echo JText::_('COM_PRICER_NAME'); $col++; ?></th>
			<th><?php echo JText::_('COM_PRICER_VENDOR'); $col++; ?></th>
			<th><?php echo JText::_('COM_PRICER_PRICE_IN'); $col++; ?></th>
			<th><?php echo JText::_('COM_PRICER_PRICE_OUT'); $col++; ?></th>
			<th><?php echo JText::_('COM_PRICER_QUANTITY'); $col++; ?></th>
			<th><?php echo JText::_('COM_PRICER_DISTRIBUTOR'); $col++; ?></th>
			<th><?php echo JText::_('COM_PRICER_DELIVERY_TIME'); $col++; ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($this->products as $i => $product): ?>
		<?php if ($lastCategoryId != $product->category_id) : ?>
		<tr class="category" >
			<td colspan="<?php echo $col; ?>"><b><?php echo $product->category_title; ?></b></td>
		</tr>
		<?php endif; $lastCategoryId = $product->category_id; ?>
		<tr class="row<?php echo $i % 2; ?>" productid="<?php echo $product->product_id; ?>">
			<td class="center" id="td-<?php echo $i; ?>"><?php echo $i+1; ?></td>
			<td class="center"><?php echo $product->product_article; ?></td>
			<td class="left title"><?php echo $product->product_title; ?></td>
			<td class="center"><?php echo $product->vendor_title; ?></td>
			<td class="right"><?php echo $product->price; ?>&nbsp;<?php echo $product->currency_html; ?></td>
			<td class="right"><?php echo $product->price_rub; ?>&nbsp;p.</td>
			<td class="center"><?php echo $product->quantity; ?></td>
			<td class="center"><?php echo $product->distributor_title; ?></td>
			<td class="center"><?php echo $this->getDeliveryTime($product->delivery_time); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9"><?php echo "pagination->getListFooter()"; ?></td>
		</tr>
	</tfoot>
</table>
