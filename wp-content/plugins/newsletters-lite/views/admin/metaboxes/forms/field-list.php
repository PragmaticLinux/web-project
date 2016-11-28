<!-- Mailing List Form Field -->

<?php

$settings = maybe_unserialize($form_field -> settings);	
	
?>

<input type="hidden" name="form_fields[<?php echo $field -> id; ?>][id]" value="<?php echo esc_attr(stripslashes($form_field -> id)); ?>" />
<input type="hidden" name="form_fields[<?php echo $field -> id; ?>][required]" value="1" />

<div class="misc-pub-section">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_label"><?php _e('Label', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="form_fields[<?php echo $field -> id; ?>][label]" id="form_fields_<?php echo $field -> id; ?>_label" value="<?php echo esc_attr(stripslashes((empty($form_field -> label) ? __($field -> title) : __($form_field -> label)))); ?>" />
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_caption"><?php _e('Caption', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="form_fields[<?php echo $field -> id; ?>][caption]" id="form_fields_<?php echo $field -> id; ?>_caption" value="<?php echo esc_attr(stripslashes((empty($form_field -> caption) ? __($field -> caption) : __($form_field -> caption)))); ?>" />
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_errormessage"><?php _e('Error Message', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="form_fields[<?php echo $field -> id; ?>][errormessage]" id="form_fields_<?php echo $field -> id; ?>_errormessage" value="<?php echo esc_attr(stripslashes($form_field -> errormessage)); ?>" placeholder="<?php echo esc_attr(stripslashes(__($field -> errormessage))); ?>" />
					<span class="howto"><?php _e('Error message to display. Leave empty to use default.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_listchoice_admin"><?php _e('Mailing List(s)', $this -> plugin_name); ?></label></th>
				<td>
					<label><input onclick="jQuery('#form_fields_<?php echo $field -> id; ?>_listchoice_user_div').hide(); jQuery('#form_fields_<?php echo $field -> id; ?>_listchoice_admin_div').show();" <?php echo (!empty($settings['listchoice']) && $settings['listchoice'] == "admin") ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo $field -> id; ?>][settings][listchoice]" value="admin" id="form_fields_<?php echo $field -> id; ?>_listchoice_admin" /> <?php _e('Admin Choice', $this -> plugin_name); ?></label>
					<label><input onclick="jQuery('#form_fields_<?php echo $field -> id; ?>_listchoice_admin_div').hide(); jQuery('#form_fields_<?php echo $field -> id; ?>_listchoice_user_div').show();" <?php echo (empty($settings['listchoice']) || (!empty($settings['listchoice']) && $settings['listchoice'] == "user")) ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo $field -> id; ?>][settings][listchoice]" value="user" id="form_fields_<?php echo $field -> id; ?>_listchoice_user" /> <?php _e('User Choice', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Do you want to specify list(s) to subscribe users to or should they choose their list(s)?', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div id="form_fields_<?php echo $field -> id; ?>_listchoice_admin_div" style="display:<?php echo (!empty($settings['listchoice']) && $settings['listchoice'] == "admin") ? 'block' : 'none'; ?>;">		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="form_fields_<?php echo $field -> id; ?>_settings_adminlists_checkall"><?php _e('Choose List(s)', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($lists = $Mailinglist -> select(true)) : ?>
							<label style="font-weight:bold;"><input type="checkbox" name="form_fields_<?php echo $field -> id; ?>_settings_adminlists_checkall" onclick="jqCheckAll(this, false, 'form_fields[<?php echo $field -> id; ?>][settings][adminlists]');" value="1" id="form_fields_<?php echo $field -> id; ?>_settings_adminlists_checkall" /> <?php _e('Select all', $this -> plugin_name); ?></label>
							<div class="scroll-list">
								<?php foreach ($lists as $list_id => $list_title) : ?>
									<label><input <?php echo (!empty($settings['adminlists']) && in_array($list_id, $settings['adminlists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="form_fields[<?php echo $field -> id; ?>][settings][adminlists][]" value="<?php echo $list_id; ?>" id="form_fields_<?php echo $field -> id; ?>_settings_adminlists_<?php echo $list_id; ?>" /> <?php _e($list_title); ?></label><br/>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<span class="newsletters_error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></span>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div id="form_fields_<?php echo $field -> id; ?>_listchoice_user_div" style="display:<?php echo (empty($settings['listchoice']) || (!empty($settings['listchoice']) && $settings['listchoice'] == "user")) ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="form_fields_<?php echo $field -> id; ?>_listchoice_user_type_select"><?php _e('Type', $this -> plugin_name); ?></label></th>
					<td>
						<label><input <?php echo (empty($settings['listchoice_user_type']) || (!empty($settings['listchoice_user_type']) && $settings['listchoice_user_type'] == "select")) ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo $field -> id; ?>][settings][listchoice_user_type]" value="select" id="form_fields_<?php echo $field -> id; ?>_listchoice_user_type_select" /> <?php _e('Single (Select)', $this -> plugin_name); ?></label>
						<label><input <?php echo (!empty($settings['listchoice_user_type']) && $settings['listchoice_user_type'] == "checkboxes") ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo $field -> id; ?>][settings][listchoice_user_type]" value="checkboxes" id="form_fields_<?php echo $field -> id; ?>_listchoice_user_type_checkboxes" /> <?php _e('Multiple (Checkbox)', $this -> plugin_name); ?></label>
						<span class="howto"><?php _e('Specify the selection type, select drop down or checkboxes list.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="form_fields_<?php echo $field -> id; ?>_settings_includelists_checkall"><?php _e('Include Only', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($lists = $Mailinglist -> select(true)) : ?>
							<label style="font-weight:bold;"><input onclick="jqCheckAll(this, false, 'form_fields[<?php echo $field -> id; ?>][settings][includelists]');" type="checkbox" name="form_fields_<?php echo $field -> id; ?>_settings_includelists_checkall" value="1" id="form_fields_<?php echo $field -> id; ?>_settings_includelists_checkall" /> <?php _e('Select all', $this -> plugin_name); ?></label><br/>
							<div class="scroll-list">
								<?php foreach ($lists as $list_id => $list_title) : ?>
									<label><input <?php echo (!empty($settings['includelists']) && in_array($list_id, $settings['includelists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="form_fields[<?php echo $field -> id; ?>][settings][includelists][]" value="<?php echo $list_id; ?>" id="form_fields_<?php echo $field -> id; ?>_settings_includelists_<?php echo $list_id; ?>" /> <?php _e($list_title); ?></label><br/>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<span class="newsletters_error"><?php _e('No mailing lists are available.', $this -> plugin_name); ?></span>
						<?php endif; ?>
						<span class="howto"><?php _e('Choose which lists should be included in the available selection. Leave empty for all.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>