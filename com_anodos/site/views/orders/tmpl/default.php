<?php
defined('_JEXEC') or die;
?>

<div class="items">
    <ul class="items_list">
<?php $show = false; ?>
        <?php foreach ($this->items as $item) : ?>
				<?php
					if($item->state == 1 || ($item->state == 0 && JFactory::getUser()->authorise('core.edit.own',' com_anodos.'.$item->id))):
						$show = true;
						?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_anodos&view=product&id=' . (int)$item->id); ?>"><?php echo $item->name; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_anodos.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-product-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1): echo JText::_("COM_ANODOS_UNPUBLISH_ITEM"); else: echo JText::_("COM_ANODOS_PUBLISH_ITEM"); endif; ?></a>
										<form id="form-product-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_anodos&task=product.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
											<input type="hidden" name="jform[category_id]" value="<?php echo $item->category_id; ?>" />
											<input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo (int)!((int)$item->state); ?>" />
											<input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
											<input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
											<input type="hidden" name="option" value="com_anodos" />
											<input type="hidden" name="task" value="product.save" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_anodos.'.$item->id)):
									?>
										<a href="javascript:deleteItem(<?php echo $item->id; ?>);"><?php echo JText::_("COM_ANODOS_DELETE_ITEM"); ?></a>
										<form id="form-product-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_anodos&task=product.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
											<input type="hidden" name="jform[category_id]" value="<?php echo $item->category_id; ?>" />
											<input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo $item->state; ?>" />
											<input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
											<input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
											<input type="hidden" name="jform[created_by]" value="<?php echo $item->created_by; ?>" />
											<input type="hidden" name="option" value="com_anodos" />
											<input type="hidden" name="task" value="product.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>
						<?php endif; ?>

<?php endforeach; ?>
        <?php
        if (!$show):
            echo JText::_('COM_ANODOS_NO_ITEMS');
        endif;
        ?>
    </ul>
</div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>


									<?php if(JFactory::getUser()->authorise('core.create','com_anodos')): ?><a href="<?php echo JRoute::_('index.php?option=com_anodos&task=product.edit&id=0'); ?>"><?php echo JText::_("COM_ANODOS_ADD_ITEM"); ?></a>
	<?php endif; ?>
