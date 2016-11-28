<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<div style="float:left; margin:0 10px 0 0;">
		<?php echo $Html -> get_gravatar($subscriber -> email); ?>
	</div>
	<h2><?php _e('View Subscriber:', $this -> plugin_name); ?> <?php echo $subscriber -> email; ?></h2>
	<br class="clear" />
	
	<div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; All Subscribers', $this -> plugin_name), $this -> url, array('title' => __('Manage All Subscribers', $this -> plugin_name))); ?></div>
	
	<div class="tablenav">
		<div class="alignleft actions">				
			<a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=save&amp;id=<?php echo $subscriber -> id; ?>" class="button"><i class="fa fa-pencil"></i> <?php _e('Edit', $this -> plugin_name); ?></a>
			<a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=delete&amp;id=<?php echo $subscriber -> id; ?>" onclick="if (!confirm('<?php _e('Are you sure you wish to remove this subscriber?', $this -> plugin_name); ?>')) { return false; }" class="button button-highlighted"><i class="fa fa-times"></i> <?php _e('Delete', $this -> plugin_name); ?></a>
			<a href="#emails" class="button"><i class="fa fa-envelope"></i> <?php _e('Emails Sent', $this -> plugin_name); ?></a>
			<?php if (!empty($orders)) : ?>
				<a href="#orders" class="button"><i class="fa fa-money"></i> <?php _e('Paid Orders', $this -> plugin_name); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php $class = ''; ?>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e('Field', $this -> plugin_name); ?></th>
				<th><?php _e('Value', $this -> plugin_name); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e('Field', $this -> plugin_name); ?></th>
				<th><?php _e('Value', $this -> plugin_name); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Email Address', $this -> plugin_name); ?></th>
				<td><?php echo $subscriber -> email; ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Mailing List(s)', $this -> plugin_name); ?></th>
				<td>
					<?php $Db -> model = $SubscribersList -> model; ?>
					<?php if ($subscriberslists = $Db -> find_all(array('subscriber_id' => $subscriber -> id))) : ?>
						<?php $m = 1; ?>
						<?php foreach ($subscriberslists as $sl) : ?>
							<?php $Db -> model = $Mailinglist -> model; ?>
							<?php if ($mailinglist = $Db -> find(array('id' => $sl -> list_id))) : ?>
								<?php echo $Html -> link(__($mailinglist -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $mailinglist -> id); ?> <?php echo ($SubscribersList -> field('active', array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id)) == "Y") ? '<span class="newsletters_success">' . $Html -> help(__('Active', $this -> plugin_name), '<i class="fa fa-check"></i>') : '<span class="newsletters_error">' . $Html -> help(__('Inactive', $this -> plugin_name), '<i class="fa fa-times"></i>'); ?></span>
								<?php 

								if (!empty($mailinglist -> paid) && $mailinglist -> paid == "Y") {	
									if ($Mailinglist -> has_expired($subscriber -> id, $mailinglist -> id)) {
										echo '<small>(' . __('Expired', $this -> plugin_name) . ')</small>';
									} else {
										if ($expiration_date = $Mailinglist -> gen_expiration_date($subscriber -> id, $mailinglist -> id)) {
											echo '<small>' . sprintf(__('(Expires %s)', $this -> plugin_name), $Html -> gen_date(false, strtotime($expiration_date))) . '</small>';
										}
									}
								}
									
								?>
								<?php if ($m < count($subscriberslists)) : ?>
									<?php echo ', '; ?>
								<?php endif; ?>
								<?php $m++; ?>
							<?php endif; ?>
							<?php $m++; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<?php _e('none', $this -> plugin_name); ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php if (!empty($subscriber -> ip_address)) : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('IP Address', $this -> plugin_name); ?></th>
					<td><?php echo $subscriber -> ip_address; ?></td>
				</tr>
			<?php endif; ?>
            
            <!-- Custom Fields -->
			<?php $fields = $FieldsList -> fields_by_list($Subscriber -> mailinglists($subscriber -> id)); ?>
			<?php if (!empty($fields)) : ?>
				<?php foreach ($fields as $field) : ?>
					<?php if (!empty($subscriber -> {$field -> slug})) : ?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<th><?php echo __($field -> title); ?></th>
							<td>
								<?php $newfieldoptions = $field -> newfieldoptions; ?>
								<?php if ($field -> type == "radio" || $field -> type == "select") : ?>
									<?php echo __($newfieldoptions[$subscriber -> {$field -> slug}]); ?>
								<?php elseif ($field -> type == "checkbox") : ?>
									<?php $supoptions = maybe_unserialize($subscriber -> {$field -> slug}); ?>
									<?php if (!empty($supoptions) && is_array($supoptions)) : ?>
										<?php foreach ($supoptions as $supopt) : ?>
											&raquo;&nbsp;<?php echo __($newfieldoptions[$supopt]); ?><br/>
										<?php endforeach; ?>
									<?php else : ?>
										<?php _e('none', $this -> plugin_name); ?>
									<?php endif; ?>
								<?php elseif ($field -> type == "file") : ?>
									<?php echo $Html -> file_custom_field($subscriber -> {$field -> slug}); ?>
								<?php elseif ($field -> type == "pre_country") : ?>
									<?php echo $this -> Country() -> field('value', array('id' => $subscriber -> {$field -> slug})); ?>
								<?php elseif ($field -> type == "pre_date") : ?>
									<?php if (is_serialized($subscriber -> {$field -> slug})) : ?>
										<?php $date = @unserialize($subscriber -> {$field -> slug}); ?>
										<?php if (!empty($date) && is_array($date)) : ?>
											<?php echo $date['y']; ?>-<?php echo $date['m']; ?>-<?php echo $date['d']; ?>
										<?php endif; ?>
									<?php else : ?>
										<?php echo $Html -> gen_date(false, strtotime($subscriber -> {$field -> slug})); ?>
									<?php endif; ?>
                                <?php elseif ($field -> type == "pre_gender") : ?>
                                	<?php echo $Html -> gender($subscriber -> {$field -> slug}); ?>
								<?php else : ?>
									<?php echo $subscriber -> {$field -> slug}; ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Emails Sent', $this -> plugin_name); ?></th>
				<?php
					
				$Db -> model = $Email -> model;
				$emailssent = $Db -> count(array('subscriber_id' => $subscriber -> id));	
					
				?>
				<td><?php echo $emailssent; ?> <?php _e('newsletters', $this -> plugin_name); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Links Clicked', $this -> plugin_name); ?></th>
				<td><?php echo $Html -> link($this -> Click() -> count(array('subscriber_id' => $subscriber -> id)), '?page=' . $this -> sections -> clicks . '&amp;subscriber_id=' . $subscriber -> id); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Email Format', $this -> plugin_name); ?></th>
				<td><?php echo (empty($subscriber -> format) || $subscriber -> format == "html") ? __('Html', $this -> plugin_name) : __('Text', $this -> plugin_name); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Registered', $this -> plugin_name); ?></th>
				<td><?php echo (empty($subscriber -> registered) || $subscriber -> registered == "N") ? __('No', $this -> plugin_name) : __('Yes', $this -> plugin_name); ?></td>
			</tr>
			<?php if ($subscriber -> registered == "Y") : ?>
				<?php $user = $Subscriber -> get_user_by_email($subscriber -> email); ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('Registered Username', $this -> plugin_name); ?></th>
					<td><?php echo (empty($user -> user_login)) ? $user -> data -> user_login : $user -> user_login; ?></td>
				</tr>
			<?php endif; ?>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <th><?php _e('Bounces', $this -> plugin_name); ?></th>
                <td><?php echo $subscriber -> bouncecount; ?></td>
            </tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Created', $this -> plugin_name); ?></th>
				<td><?php echo $subscriber -> created; ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Modified', $this -> plugin_name); ?></th>
				<td><?php echo $subscriber -> modified; ?></td>
			</tr>
		</tbody>
	</table>
	
	<h3 id="emails"><?php _e('Emails', $this -> plugin_name); ?></h3>
	<?php $this -> render('emails' . DS . 'loop', array('emails' => $emails, 'subscriber' => $subscriber, 'paginate' => $paginate), true, 'admin'); ?>
	
	<?php if (!empty($orders)) : ?>
		<h3 id="orders"><?php _e('Subscription Orders', $this -> plugin_name); ?></h3>
		<?php $this -> render('orders' . DS . 'loop', array('orders' => $orders, 'hide_subscriber' => true), true, 'admin'); ?>
	<?php endif; ?>
</div>