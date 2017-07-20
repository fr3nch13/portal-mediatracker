<?php 
// File: app/View/OrgGroups/admin_email_options.ctp
?>
<div class="top">
	<h1><?php echo __('Edit Email Options for: %s', $this->data['OrgGroup']['name']); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('OrgGroup');?>
		    <fieldset>
		        <legend><?php echo __('Edit Email Options for: %s', $this->data['OrgGroup']['name']); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('sendemail', array(
						'label' => __('Send Notification Emails?'),
						'options' => array(0 => __('No'), 1 => __('Yes')),
						'div' => array('class' => 'forth'),
					));
					
					$days = array(
						$this->Html->tag('label', __('Days of the week.')),
					);
					$days[] = $this->Form->input('mon', array(
						'label' => __('Mon'),
						'type' => 'checkbox',
						'div' => array('style' => 'display: inline; float: left; clear: none;'),
					));
					$days[] = $this->Form->input('tue', array(
						'label' => __('Tues'),
						'type' => 'checkbox',
						'div' => array('style' => 'display: inline; float: left; clear: none;'),
					));
					$days[] = $this->Form->input('wed', array(
						'label' => __('Wed'),
						'type' => 'checkbox',
						'div' => array('style' => 'display: inline; float: left; clear: none;'),
					));
					$days[] = $this->Form->input('thu', array(
						'label' => __('Thur'),
						'type' => 'checkbox',
						'div' => array('style' => 'display: inline; float: left; clear: none;'),
					));
					$days[] = $this->Form->input('fri', array(
						'label' => __('Fri'),
						'type' => 'checkbox',
						'div' => array('style' => 'display: inline; float: left; clear: none;'),
					));
					$days[] = $this->Form->input('sat', array(
						'label' => __('Sat'),
						'type' => 'checkbox',
						'div' => array('style' => 'display: inline; float: left; clear: none;'),
					));
					$days[] = $this->Form->input('sun', array(
						'label' => __('Sun'),
						'type' => 'checkbox',
						'div' => array('style' => 'display: inline; float: left; clear: none;'),
					));
					echo $this->Html->tag('div', implode("\n", $days), array('class' => 'half'));
					echo $this->Form->input('notify_time', array(
						'label' => __('Time of day should emails be sent. (EST)'),
						'options' => $this->Local->emailTimes(),
						'default' => '10',
						'div' => array('class' => 'forth'),
					));
/*
					echo $this->Form->input('notify_email', array(
						'label' => __('Where the Notification Email should be sent.'),
						'div' => array('class' => 'half'),
					));
*/
					echo $this->Wrap->divClear();
					echo $this->Form->input('instructions');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Email Options')); ?>
	</div>
</div>