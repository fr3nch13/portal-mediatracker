<?php ?>
<!-- File: app/View/Media/edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit Media'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media'); ?>
			<?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
			<?php echo $this->Form->input('MediaDetail.id', array('type' => 'hidden')); ?>
			
			<?php if(in_array(AuthComponent::user('role'), array('admin'))): ?>
			<fieldset>
		        <legend class="section"><?php echo __('Admin Details'); ?></legend>
		        <?php
					echo $this->Form->input('Media.org_group_id', array(
						'label' => 'Org Group',
						'options' => $org_groups,
						'empty' => __('None'),
//						'div' => array('class' => 'half'),
					));
		        ?>
		    </fieldset>
			<?php endif;?>
		    <fieldset>
		        <legend class="section"><?php echo __('Ticket Details'); ?></legend>
		        <?php
					
					echo $this->Form->input('MediaDetail.ticket_ticket', array(
						'label' => array(
							'text' => __('Example Ticket'),
						),
						'div' => array('class' => 'forth'),
					));
					
					echo $this->Form->input('MediaDetail.other_ticket', array(
						'label' => array(
							'text' => __('Other Ticket'),
						),
						'div' => array('class' => 'forth'),
					));
					
					echo $this->Form->input('MediaDetail.tickets', array(
						'label' => array(
							'text' => __('Other Related Tickets'),
						),
						'div' => array('class' => 'forth'),
						'type' => 'text',
					));
					
					echo $this->Form->input('MediaDetail.fo_case_num', array(
						'label' => array(
							'text' => __('FO Case Number'),
						),
						'div' => array('class' => 'forth'),
					));
		        ?>
		    </fieldset>
		    
		    <fieldset>
		        <legend class="section"><?php echo __('Received Details'); ?></legend>
		    	<?php
					echo $this->Form->input('Media.received_user_id', array(
						'label' => 'Received By',
						'options' => $users,
						'default' => AuthComponent::user('id'),
						'div' => array('class' => 'half'),
					));
					
					echo $this->Form->input('Media.received_org_id', array(
						'label' => array(
							'text' => __('Received By Org'),
						),
						'options' => $received_orgs,
						'div' => array('class' => 'half'),
					));
					
					echo $this->Wrap->divClear();
					
					echo $this->Form->input('Media.obtain_reason_id', array(
						'label' => array(
							'text' => __('Reason Obtained'),
						),
						'options' => $obtain_reasons,
						'div' => array('class' => 'half'),
					));
					
					echo $this->Form->input('Media.obtained', array(
						'label' => array(
							'text' => __('First Obtained'),
						),
						'type' => 'datetime',
						'div' => array('class' => 'half'),
					));
					
/*** No longer on this table, on the custody chain
					echo $this->Form->input('Media.custody_chain_reason_id', array(
						'label' => array(
							'text' => __('Custody Change Purpose'),
						),
						'options' => $custody_chain_reasons,
						'div' => array('class' => 'third'),
					));
*/
					
					echo $this->Wrap->divClear();
		    	?>
		    </fieldset>
		    
		    <fieldset>
		        <legend class="section"><?php echo __('Customer Details'); ?></legend>
		    	<?php
					
					echo $this->Form->input('MediaDetail.owner', array(
						'label' => array(
							'text' => __('Media Owner Name'),
						),
						'div' => array('class' => 'half'),
					));
					
					echo $this->Wrap->divClear();
					
					echo $this->Form->input('MediaDetail.loc_building', array(
						'label' => array(
							'text' => __('Building where Media First Obtained'),
						),
						'div' => array('class' => 'half'),
					));
					
					echo $this->Form->input('MediaDetail.loc_room', array(
						'label' => array(
							'text' => __('Room where Media First Obtained'),
						),
						'div' => array('class' => 'half'),
					));
					
					echo $this->Wrap->divClear();
					
					echo $this->Form->input('MediaDetail.cust_info', array(
						'label' => array(
							'text' => __('Customer Details (username, ip, etc)'),
						),
//						'div' => array('class' => 'half'),
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
						'div' => array('class' => 'half'),
					));
					
					echo $this->Form->input('Media.media_status_id', array(
						'label' => array(
							'text' => __('Media Status'),
						),
						'options' => $media_statuses,
						'div' => array('class' => 'half'),
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
					
					echo $this->Form->input('Media.serial', array(
						'label' => array(
							'text' => __('Serial Number'),
						),
						'div' => array('class' => 'third'),
					));
					
					echo $this->Form->input('Media.make', array(
						'label' => array(
							'text' => __('Make'),
						),
						'div' => array('class' => 'third'),
					));
					
					echo $this->Form->input('Media.model', array(
						'label' => array(
							'text' => __('Model'),
						),
						'div' => array('class' => 'third'),
					));
					
					echo $this->Wrap->divClear();
					
					echo $this->Form->input('MediaDetail.details', array(
						'label' => array(
							'text' => __('Description (Description, Condition, etc)'),
						),
					));
					
					echo $this->Wrap->divClear();
		        ?>
		    </fieldset>
		<?php echo $this->Form->end(__('Update Media')); ?>
	</div>
</div>
