<?php

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_anodos/assets/css/anodos.css');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'vendorsynonym.cancel' || document.formvalidator.isValid(document.id('vendorsynonym-form'))) {
			Joomla.submitform(task, document.getElementById('vendorsynonym-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_anodos&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="vendorsynonym-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#main" data-toggle="tab">
						<?php echo JText::_('COM_ANODOS_MAIN');?>
					</a>
				</li>
				<li>
					<a href="#other" data-toggle="tab">
						<?php echo JText::_('COM_ANODOS_OTHER');?>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="main">
					<fieldset class="adminform">
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('vendor_id'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('vendor_id'); ?></div>
						</div>
		            </fieldset>
				</div>
				<div class="tab-pane" id="other">
					<fieldset class="adminform">
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('checked_out'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('checked_out'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('checked_out_time'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('checked_out_time'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
						</div>
					</fieldset>
				</div>
			</div>
    	</div>
		<!-- End Content -->

		<!-- Begin Sidebar -->
		<div class="span2">
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr />
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getValue('name'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
				</div>
			</fieldset>
		</div>
		<!-- End Sidebar -->
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
