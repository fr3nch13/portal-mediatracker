<?php ?>
<!-- File: app/View/ObtainReason/admin_add.ctp -->
<div class="top">
	<h1><?php echo __('Add Obtain Reason'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ObtainReason');?>
		    <fieldset>
		        <legend><?php echo __('Add Obtain Reason'); ?></legend>
		    	<?php
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Obtain Reason')); ?>
	</div>
</div>