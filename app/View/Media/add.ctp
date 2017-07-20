<?php ?>

<div class="top">
	<h1><?php echo __('Add Media to Track'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media', array('type' => 'file', 'id' => 'AddMediaForm'));?>
		    <fieldset>
		        <legend class="section"><?php echo __('Ticket Details'); ?></legend>
		        <?php
					echo $this->Form->input('MediaDetail.ticket_ticket', array(
						'label' => array(
							'text' => __('Example Ticket'),
						),
						'div' => array('class' => 'third'),
					));
					echo $this->Form->input('MediaDetail.other_ticket', array(
						'label' => array(
							'text' => __('Other Ticket'),
						),
						'div' => array('class' => 'third'),
					));
					echo $this->Form->input('MediaDetail.tickets', array(
						'label' => array(
							'text' => __('Other Related Tickets'),
						),
						'div' => array('class' => 'third'),
						'type' => 'text',
					));
		        ?>
		    </fieldset>
		    <fieldset>
		        <legend class="section"><?php echo __('Media Details'); ?></legend>
		    	<?php
					
					echo $this->Form->input('MediaDetail.property_tag', array(
						'label' => array(
							'text' => __('Associated Property Tag'),
						),
					));
					echo $this->Form->input('Media.media_status_id', array(
						'type' => 'hidden',
						'value' => 0,
					));
					echo $this->Wrap->divClear();
					echo $this->Form->input('MediaType', array(
						'label' => array(
							'text' => __('Media Type(s)'),
						),
						'options' => $media_types,
						'multiple' => 'checkbox',
					));
					echo $this->Wrap->divClear();
					echo $this->Form->input('MediaDetail.details', array(
						'label' => array(
							'text' => __('Notes (Any notes/details you would like to include. This field is optional.)'),
						),
					));
					echo $this->Wrap->divClear();
		        ?>
		    </fieldset>
		    
		    <fieldset>
		        <legend class="section"><?php echo __('Custody Form Upload'); ?></legend>
		    	<?php
					
					$max_upload = (int)(ini_get('upload_max_filesize'));
					$max_post = (int)(ini_get('post_max_size'));
					$memory_limit = (int)(ini_get('memory_limit'));
					$upload_mb = min($max_upload, $max_post, $memory_limit);
					
					echo $this->Form->input('Upload.file', array(
						'type' => 'file',
						'between' => __('(Max file size is %sM).', $upload_mb),
					));
					// track if the file is associated with a new media.
					echo $this->Form->input('Upload.file.new_media', array('type' => 'hidden', 'value' => '1'));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Media')); ?>
	</div>
</div>

<script type="text/javascript">

var submitResults = false;

$(document).ready(function()
{
	$('#MediaDetailExampleTicket').blur(function()
	{
		if($(this).val().length)
		{
			$.post(
				"<?php echo $this->Html->url(array('controller' => 'media', 'action' => 'validate_ticket')); ?>",
				{ field: $(this).attr('id'), value: $(this).val() },
				handleNameValidation
			);
		}
	});
	
	// validate example on submit as well
	$('#AddMediaForm').submit(function()
	{
		var obj = $('#MediaDetailExampleTicket');
		if(obj.val().length)
		{
			$.post(
				"<?php echo $this->Html->url(array('controller' => 'media', 'action' => 'validate_ticket')); ?>",
				{ field: obj.attr('id'), value: obj.val() },
				handleNameValidationForSubmit
			);
			return false;
		}
	});
});//ready 


function handleNameValidation(error)
{
    if(error.length > 0)
    {
    	alert(error);
    	return false;
    }
}

function handleNameValidationForSubmit(error)
{
    if(error.length > 0)
    {
    	var r=confirm(error);
    	if (r==true)
    	{
    		$('#AddMediaForm').unbind('submit');
    		$('#AddMediaForm').submit();
    	}
    	return false;
    }
    $('#AddMediaForm').unbind('submit');
    $('#AddMediaForm').submit();
}

</script>
