<!-- Hidden Field Template -->

<input type="hidden" name="form_fields[<?php echo $field -> id; ?>][id]" value="<?php echo esc_attr(stripslashes($form_field -> id)); ?>" />

<div class="misc-pub-section">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="form_fields_<?php echo $field -> id; ?>_required"><?php _e('Required?', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo (!empty($form_field -> required)) ? 'checked="checked"' : ''; ?> type="checkbox" name="form_fields[<?php echo $field -> id; ?>][required]" value="1" id="form_fields_<?php echo $field -> id; ?>_required" /> <?php _e('Yes, this field is required', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Turn this on to require the user to make a selection or fill in a value.', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>