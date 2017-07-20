<?php ?>
<!-- File: app/View/MediaStatus/admin_edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit Media Status'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('MediaStatus');?>
		    <fieldset>
		        <legend><?php echo __('Edit Media Status'); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Media Status')); ?>
	</div>
</div>