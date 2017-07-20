<?php ?>
				<?php if (AuthComponent::user('id')): ?>
				<ul class="sf-menu">
					<li><?php echo $this->Html->link(__('Create New Tracked Media'), array('controller' => 'media', 'action' => 'add', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<li>
		<?php echo $this->Html->link(__('Overviews'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Overview'), array('controller' => 'main', 'action' => 'dashboard', 'admin' => false, 'plugin' => false)); ?></li>
			<li><?php echo $this->Html->link(__('My Overview'), array('controller' => 'main', 'action' => 'my_dashboard', 'admin' => false, 'plugin' => false)); ?></li>
		</ul>
	</li>
					<li>
						<?php echo $this->Html->link(__('Update Open Tracked Media'), '#', array('class' => 'top')); ?>
						<?php echo $this->element('Utilities.menu_items', array(
							'request_url' => array('controller' => 'media', 'action' => 'open', 'admin' => false, 'plugin' => false),
						));?>
					</li>
					<li>
						<?php echo $this->Html->link(__('View Tracked Media'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Open'), array('controller' => 'media', 'action' => 'open', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('All'), array('controller' => 'media', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Closed'), array('controller' => 'media', 'action' => 'closed', 'admin' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<li>
						<?php echo $this->Html->link(__('View Files'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('All Files'), array('controller' => 'uploads', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('I\'ve Uploaded'), array('controller' => 'uploads', 'action' => 'mine', 'admin' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					
					<li><?php echo $this->Html->link(__('View Users'), array('controller' => 'users', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
					
					<?php echo $this->Common->loadPluginMenuItems(); ?>
					
					<?php if (AuthComponent::user('id') and AuthComponent::user('role') == 'admin'): ?>
					<li>
						<?php echo $this->Html->link(__('Admin'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Files'), array('controller' => 'uploads', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Media Types'), array('controller' => 'media_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Media Statuses'), array('controller' => 'media_statuses', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Obtain Reasons'), array('controller' => 'obtain_reasons', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Received Orgs'), array('controller' => 'received_orgs', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Custody Change Reasons'), array('controller' => 'custody_chain_reasons', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('DB Logs'), array('controller' => 'dblogs', 'action' => 'index', 'admin' => true, 'plugin' => 'dblogger')); ?></li>
							<?php echo $this->Common->loadPluginMenuItems('admin'); ?>
							<li><?php echo $this->Html->link(__('Users'), '#', array('class' => 'sub')); ?>
								<ul>
									<li><?php echo $this->Html->link(__('All %s', __('Users')), array('controller' => 'users', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
									<li><?php echo $this->Html->link(__('Login History'), array('controller' => 'login_histories', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
									<li><?php echo $this->Html->link(__('Org Groups'), array('controller' => 'org_groups', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
								</ul>
							</li>
							<li><?php echo $this->Html->link(__('App Admin'), '#', array('class' => 'sub')); ?>
								<ul>
									<li><?php echo $this->Html->link(__('Config'), array('controller' => 'users', 'action' => 'config', 'admin' => true, 'plugin' => false)); ?></li>
									<li><?php echo $this->Html->link(__('Statistics'), array('controller' => 'users', 'action' => 'stats', 'admin' => true, 'plugin' => false)); ?></li>
									<li><?php echo $this->Html->link(__('Process Times'), array('controller' => 'proctimes', 'action' => 'index', 'admin' => true, 'plugin' => 'utilities')); ?></li> 
								</ul>
							</li>
						</ul>
					</li>
					<?php endif; ?>
				</ul>
				<?php endif; ?>