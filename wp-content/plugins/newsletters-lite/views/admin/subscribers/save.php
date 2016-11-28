<!-- Save a Subscriber -->

<?php

$mandatory = $Html -> field_value('Subscriber[mandatory]');
	
?>

<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Save a Subscriber', $this -> plugin_name); ?></h2>
	<?php $this -> render('error', array('errors' => $errors)); ?>
	<form id="optinform<?php echo $subscriber -> id; ?>" name="optinform<?php echo $subscriber -> id; ?>" action="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=save" method="post" enctype="multipart/form-data">
		<?php echo $Form -> hidden('Subscriber[id]'); ?>
		<input type="hidden" name="Subscriber[active]" value="Y" />
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Subscriber.email"><?php _e('Email Address', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('This is the email address of the subscriber on which the subscriber will receive email newsletters and other notifications.', $this -> plugin_name)); ?></th>
					<td>
						<?php echo $Form -> text('Subscriber[email]', array('placeholder' => __('Enter email address here', $this -> plugin_name))); ?>
						<span class="howto"><?php _e('Valid email address of the subscriber to receive newsletters.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="checkboxall"><?php _e('Mailing List(s)', $this -> plugin_name); ?><label>
					<?php echo $Html -> help(__('Choose the mailing list(s) to subscribe this user to. Sending to any of the list(s) that you subscribe this user to will result in this user receiving the email newsletter.', $this -> plugin_name)); ?></th>
					<td>
						<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
							<div><label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" onclick="jqCheckAll(this, 'Subscriber[mailinglists]', false);" /> <?php _e('Select all', $this -> plugin_name); ?></label></div>
							<div class="scroll-list" id="newsletters-mailinglists-checkboxes">
								<?php foreach ($mailinglists as $list_id => $list_title) : ?>
									<?php $mailinglist = $Mailinglist -> get($list_id); ?>
									<label><input onclick="jQuery('#mailinglist_<?php echo $list_id; ?>_expiration').toggle();" <?php echo (!empty($Subscriber -> data -> mailinglists) && in_array($list_id, $Subscriber -> data -> mailinglists)) ? 'checked="checked"' : ''; ?> type="checkbox" name="Subscriber[mailinglists][]" value="<?php echo $list_id; ?>" id="Subscriber_mailinglists_<?php echo $list_id; ?>" /> <?php echo __($list_title); ?> (<?php echo $SubscribersList -> count(array('list_id' => $list_id)); ?> <?php _e('subscribers', $this -> plugin_name); ?>)</label><br/>
									<?php if (!empty($mailinglist -> paid) && $mailinglist -> paid == "Y") : ?>
										<div id="mailinglist_<?php echo $list_id; ?>_expiration" style="display:<?php echo (!empty($Subscriber -> data -> mailinglists) && in_array($list_id, $Subscriber -> data -> mailinglists)) ? 'block' : 'none'; ?>;">
											<?php _e('Expires: ', $this -> plugin_name); ?>
											<input type="text" name="Subscriber[listexpirations][<?php echo $list_id; ?>]" value="<?php echo esc_attr(stripslashes($Mailinglist -> gen_expiration_date($Subscriber -> data -> id, $list_id))); ?>" id="" />
											<?php echo $Html -> help(__('Choose the date on which this paid subscription should expire. Leave empty to keep inactive.', $this -> plugin_name)); ?>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<p class="newsletters_error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></p>
						<?php endif; ?>
						<?php /*<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
							<?php foreach ($mailinglists as $key => $val) : ?>
								<?php $mailinglists[$key] = $val . ' (' . $SubscribersList -> count(array('list_id' => $key)) . ' ' . __('subscribers', $this -> plugin_name) . ')'; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<div id="newsletters-mailinglists-checkboxes" class="scroll-list">
							<?php echo $Form -> checkbox('Subscriber[mailinglists][]', $mailinglists); ?>
						</div>*/ ?>
						<p><a href="#" class="button" onclick="jQuery.colorbox({title:'<?php echo esc_attr(stripslashes(__('Add a Mailing List', $this -> plugin_name))); ?>', href:newsletters_ajaxurl + 'action=newsletters_mailinglist_save&fielddiv=newsletters-mailinglists-checkboxes&fieldname=Subscriber[mailinglists]'}); return false;"><i class="fa fa-plus"></i> <?php _e('Add Mailing List', $this -> plugin_name); ?></a></p>
						<?php echo $Html -> field_error('Subscriber[mailinglists]'); ?>
						<span class="howto"><?php _e('All ticked/checked subscriptions are activated immediately.', $this -> plugin_name); ?></span>													
					</td>
				</tr>
				<tr>
					<th><label for="preventautoresponders"><?php _e('Prevent Autoresponders?', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('Tick this box to prevent the automatic creation of autoresponder emails as you save this subscriber.', $this -> plugin_name)); ?></th>
					<td>
						<label><input type="checkbox" name="preventautoresponders" value="1" id="preventautoresponders" /> <?php _e('Yes, prevent creation of autoresponders', $this -> plugin_name); ?></label>
					</td>
				</tr>
				<?php if (apply_filters($this -> pre . '_admin_subscriber_save_register', true)) : ?>										
				<tr>
					<th><?php _e('Register as WordPress user?', $this -> plugin_name); ?>
					<?php echo $Html -> help(__('Would you like to register this subscriber as a WordPress user? The subscribers are separate from WordPress users at all times and is not the same list of emails. In this case you can add this subscriber as a user in WordPress.', $this -> plugin_name)); ?></th>
					<td>
						<?php $registered = array('Y' => __('Yes', $this -> plugin_name), 'N' => __('No', $this -> plugin_name)); ?>
						<?php echo $Form -> radio('Subscriber[registered]', $registered, array('separator' => false, 'default' => "N", 'onclick' => "if (this.value == 'Y') { jQuery('#registereddiv').show(); } else { jQuery('#registereddiv').hide(); }")); ?>
					</td>
				</tr>	
				<?php endif; ?>			
			</tbody>
		</table>
		
		<?php if (apply_filters($this -> pre . '_admin_subscriber_save_register', true)) : ?>
		<div id="registereddiv" style="display:<?php echo ($Html -> field_value('Subscriber[registered]') == "Y") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e('Wordpress Username', $this -> plugin_name); ?></th>
						<td><?php echo $Form -> text('Subscriber[username]', array('placeholder' => __('Enter username here', $this -> plugin_name))); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e('Email Format', $this -> plugin_name); ?>
					<?php echo $Html -> help(__('The preferred email format that this subscriber wants to receive. Available formats are HTML and PLAIN TEXT. If you are going to send multi-mime emails, this setting is ineffective and the email/webmail client of the subscriber will automatically decide.', $this -> plugin_name)); ?></th>
					<td>
						<?php $formats = array('html' => __('Html', $this -> plugin_name), 'text' => __('Text', $this -> plugin_name)); ?>
						<?php echo $Form -> radio('Subscriber[format]', $formats, array('default' => "html", 'separator' => false)); ?>
						
						<span class="howto"><?php _e('it is recommended that you use HTML format and turn on multi-part emails under Newsletters > Configuration for compatibility.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php endif; ?>
		
		<?php
		
		global $wpdb;
		$fieldsquery = "SELECT * FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list' ORDER BY `order` ASC";
		
		$query_hash = md5($fieldsquery);
		if ($ob_fields = $this -> get_cache($query_hash)) {
			$fields = $ob_fields;
		} else {
			$fields = $wpdb -> get_results($fieldsquery);
			$this -> set_cache($query_hash, $fields);
		}
		
		?>
		
        <?php if (!empty($fields)) : ?>
			<br/>
			<h3><?php _e('Custom Fields', $this -> plugin_name); ?> (<?php echo $Html -> link(__('show/hide', $this -> plugin_name), '#void', array('onclick' => "jQuery('#customfieldsdiv').toggle();")); ?>)
			<?php echo $Html -> help(__('Click "show/hide" to display the available custom fields and fill in values for the custom fields for this subscriber.', $this -> plugin_name)); ?></h3>
			<div id="customfieldsdiv" style="display:block;">
				<table class="form-table">
					<tbody>
                    	<?php $optinid = rand(1, 999); ?>
						<?php foreach ($fields as $field) : ?>
							<tr>
								<th><label for="<?php echo $field -> slug; ?>"><?php echo __($field -> title); ?></label></th>
								<td><?php $this -> render_field($field -> id, false, $optinid); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Subscriber_mandatory_N"><?php _e('Mandatory?', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('A mandatory subscriber is a subscriber that must be subscribed and stay subscribed and cannot unsubscribe. This could be a client or a site administrator that must always be subscribed.', $this -> plugin_name)); ?></th>
					<td>
						<label><input <?php echo (!empty($mandatory) && $mandatory == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="Subscriber[mandatory]" value="Y" id="Subscriber_mandatory_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
						<label><input <?php echo (empty($mandatory) || (!empty($mandatory) && $mandatory == "N")) ? 'checked="checked"' : ''; ?> type="radio" name="Subscriber[mandatory]" value="N" id="Subscriber_mandatory_N" /> <?php _e('No', $this -> plugin_name); ?></label>
						<span class="howto"><?php _e('A mandatory subscriber cannot unsubscribe', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<?php echo $Form -> submit(__('Save Subscriber', $this -> plugin_name)); ?>
			<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php _e('Continue editing', $this -> plugin_name); ?></label>
			</div>
		</p>
	</form>
</div>