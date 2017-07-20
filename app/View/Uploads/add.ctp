<?php

?>
<div class="top">
	<h1><?php echo __('Add File'); ?></h1>
	<?php if(isset($media)):?><h2><?php echo __('Media: %s', $media['Media']['id']. ' : '. (trim($media['MediaDetail']['example_ticket'])?$media['MediaDetail']['example_ticket']:__('(Empty)')). ' : '. (trim($media['MediaDetail']['other_ticket'])?$media['MediaDetail']['other_ticket']:__('(Empty)')). ' : '. (trim($media['MediaDetail']['property_tag'])?$media['MediaDetail']['property_tag']:__('(Empty)'))); ?></h2><?php endif;?>
</div>
<div class="center">
	<div class="posts form">
		<?php echo $this->Form->create('Upload', array('type' => 'file'));?>
		    <fieldset>
		        <legend><?php echo __('Add File'); ?></legend>
		    	<?php
					echo $this->Form->input('file', array(
						'type' => 'file',
					));
					echo $this->Form->input('Upload.media_id', array('type' => 'hidden'));
					echo $this->Form->input('Upload.custody_chain_id', array('type' => 'hidden'));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Upload File')); ?>
	</div>
</div>
