<?php
// File: app/View/Uploads/edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit File: %s', $this->data['Upload']['filename']); ?></h1>
</div>
<div class="center">
	<div class="posts form">
		<?php echo $this->Form->create('Upload', array('type' => 'file'));?>
	    	<fieldset>
	    	    <legend><?php echo __('Edit File'); ?></legend>
	    		<?php
					echo $this->Form->input('id', array(
						'type' => 'hidden'
					));
	    	    	echo $this->Form->input('public', array(
						'label' => array(
							'text' => __('Public'),
							'class' => 'tipsy',
							'title' => __('If other users can see this file, or just you.'),
						)
					));
					
//					echo $this->Form->input('tags');
	    		?>
	    	</fieldset>
		<?php echo $this->Form->end(__('Update File')); ?>
	</div>
</div>
