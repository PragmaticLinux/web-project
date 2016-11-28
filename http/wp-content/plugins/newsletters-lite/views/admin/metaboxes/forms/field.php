<!-- Default Field Template -->

<input type="hidden" name="form_fields[<?php echo $field -> id; ?>][id]" value="<?php echo esc_attr(stripslashes($form_field -> id)); ?>" />

<div class="misc-pub-section">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_label"><?php _e('Label', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="form_fields[<?php echo $field -> id; ?>][label]" id="form_fields_<?php echo $field -> id; ?>_label" value="<?php echo esc_attr(stripslashes($form_field -> label)); ?>" placeholder="<?php echo esc_attr(stripslashes(__($field -> title))); ?>" />
					<span class="howto"><?php _e('Label to show for this field. Leave empty for default.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_caption"><?php _e('Caption', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="form_fields[<?php echo $field -> id; ?>][caption]" id="form_fields_<?php echo $field -> id; ?>_caption" value="<?php echo esc_attr(stripslashes(__($form_field -> caption))); ?>" placeholder="<?php echo esc_attr(stripslashes(__($field -> caption))); ?>" />
					<span class="howto"><?php _e('Caption/description to show below this field with more details. Leave empty for default.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_placeholder"><?php _e('Placeholder', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="form_fields[<?php echo $field -> id; ?>][placeholder]" id="form_fields_<?php echo $field -> id; ?>_placeholder" value="<?php echo esc_attr(stripslashes(__($form_field -> placeholder))); ?>" placeholder="<?php echo esc_attr(stripslashes(__($field -> watermark))); ?>" />
					<span class="howto"><?php _e('Placeholder/watermark to show inside this field when it is empty/blank.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_required"><?php _e('Required?', $this -> plugin_name); ?></label></th>
				<td>
					<label><input onclick="if (jQuery(this).is(':checked')) { jQuery('#form_fields_<?php echo $field -> id; ?>_required_div').show(); } else { jQuery('#form_fields_<?php echo $field -> id; ?>_required_div').hide(); }" <?php echo (!empty($form_field -> required) || (empty($form_field -> id) && !empty($field -> required) && $field -> required == "Y")) ? 'checked="checked"' : ''; ?> type="checkbox" name="form_fields[<?php echo $field -> id; ?>][required]" value="1" id="form_fields_<?php echo $field -> id; ?>_required" /> <?php _e('Yes, this field is required', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Turn this on to require the user to make a selection or fill in a value.', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div id="form_fields_<?php echo $field -> id; ?>_required_div" style="display:<?php echo (!empty($form_field -> required) || (empty($form_field -> id) && !empty($field -> required) && $field -> required == "Y")) ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="form_fields_<?php echo $field -> id; ?>_errormessage"><?php _e('Error Message', $this -> plugin_name); ?></label></th>
					<td>
						<input type="text" class="widefat" name="form_fields[<?php echo $field -> id; ?>][errormessage]" id="form_fields_<?php echo $field -> id; ?>_errormessage" value="<?php echo esc_attr(stripslashes($form_field -> errormessage)); ?>" placeholder="<?php echo esc_attr(stripslashes(__($field -> errormessage))); ?>" />
						<span class="howto"><?php _e('Error message to display. Leave empty to use default.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>