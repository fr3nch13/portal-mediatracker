<?php ?>
<!-- File: app/View/ReceivedOrg/admin_edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit Received Org'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ReceivedOrg');?>
		    <fieldset>
		        <legend><?php echo __('Edit Received Org'); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Received Org')); ?>
	</div>
</div>