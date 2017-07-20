<?php ?>
<!-- File: app/View/CustodyChainReason/admin_add.ctp -->
<div class="top">
	<h1><?php echo __('Add Custody Chain Reason'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('CustodyChainReason');?>
		    <fieldset>
		        <legend><?php echo __('Add Custody Chain Reason'); ?></legend>
		    	<?php
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Custody Chain Reason')); ?>
	</div>
</div>