<?php
// File: app/View/CustodyChains/add.ctp
?>
<div class="top">
	<h1><?php echo __('Add to Chain of Custody'); ?></h1>
	<?php if(isset($media)):?><h2><?php echo __('Media: %s', $media['Media']['id']. ' : '. (trim($media['MediaDetail']['ticket_ticket'])?$media['MediaDetail']['ticket_ticket']:__('(Empty)')). ' : '. (trim($media['MediaDetail']['other_ticket'])?$media['MediaDetail']['other_ticket']:__('(Empty)')). ' : '. (trim($media['MediaDetail']['property_tag'])?$media['MediaDetail']['property_tag']:__('(Empty)'))); ?></h2><?php endif;?>
</div>
<div class="center">
	<div class="posts form">
		<?php echo $this->Form->create('CustodyChain', array('type' => 'file'));?>
		    <fieldset>
		        <legend><?php echo __('Add to Chain of Custody'); ?></legend>
		    	<?php
		    		
					echo $this->Form->input('media_id', array('type' => 'hidden'));
					
		    		echo $this->Form->input('released_user_id', array(
						'label' => array(
							'text' => __('Media Released By'),
						),
						'options' => $users,
						'empty' => array('0' => __('Other (Enter Name Below)')),
						'div' => array('class' => 'third'),
					));
					
		    		echo $this->Form->input('received_user_id', array(
						'label' => array(
							'text' => __('Media Received By'),
						),
						'options' => $users,
						'empty' => array('0' => __('Other (Enter Name Below)')),
						'div' => array('class' => 'third'),
					));
					
					echo $this->Form->input('custody_chain_reason_id', array(
						'label' => array(
							'text' => __('Custody Change Reason'),
						),
						'options' => $custody_chain_reasons,
						'div' => array('class' => 'third'),
					));
					
					echo $this->Wrap->divClear();
					
		    		echo $this->Form->input('released_user_other', array(
						'label' => array(
							'text' => __('Media Released By (Other)'),
						),
						'div' => array('class' => 'third'),
					));
					
		    		echo $this->Form->input('received_user_other', array(
						'label' => array(
							'text' => __('Media Received By (Other)'),
						),
						'div' => array('class' => 'third'),
					));
					
					echo $this->Form->input('Media.id', array('type' => 'hidden'));
					echo $this->Form->input('Media.media_status_id', array(
						'label' => array(
							'text' => __('Media Status'),
						),
						'options' => $media_statuses,
						'div' => array('class' => 'third'),
					));;
					
					echo $this->Wrap->divClear();
					
					echo $this->Form->input('notes', array(
						'label' => array(
							'text' => __('Notes (Any notes/details you would like to include. This field is optional.)'),
						),
					));
					
					echo $this->Wrap->divClear();
	
					$max_temp_upload = (int)(ini_get('upload_max_filesize'));
					$max_post = (int)(ini_get('post_max_size'));
					$memory_limit = (int)(ini_get('memory_limit'));
					$temp_upload_mb = min($max_temp_upload, $max_post, $memory_limit);
					
					echo $this->Form->input('Upload.file', array(
						'type' => 'file',
						'between' => __('(Max file size is %sM).', $temp_upload_mb),
					));
					
					echo $this->Form->input('Upload.media_id', array('type' => 'hidden'));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Add Chain of Custody')); ?>
	</div>
</div>
