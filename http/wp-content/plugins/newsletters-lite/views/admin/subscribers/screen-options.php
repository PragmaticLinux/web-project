<!-- Subscribers Screen Options -->

<?php
	
$show_avatars = get_option('show_avatars');	
	
?>

<div class="newsletters">
	<form action="" method="post">
		<input type="hidden" name="screenoptions" value="1" />
	
		<?php if (!empty($fields)) : ?>
	    	<?php $curfields = maybe_unserialize($this -> get_option('screenoptions_subscribers_fields')); ?>
	    	<?php $curcustomfields = maybe_unserialize($this -> get_option('screenoptions_subscribers_custom')); ?>
			<h5><?php _e('Show on screen', $this -> plugin_name); ?></h5>
	        <div class="metabox-prefs">
	        	<label><input <?php echo (empty($show_avatars)) ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($curcustomfields) && in_array('gravatars', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="gravatars" id="custom_gravatars" /> <?php _e('Image', $this -> plugin_name); ?></label>
	        	<label><input <?php echo (!empty($curcustomfields) && in_array('format', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="format" id="custom_format" /> <?php _e('Format (HTML/TEXT)', $this -> plugin_name); ?></label>
	        	<label><input <?php echo (!empty($curcustomfields) && in_array('device', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="device" id="custom_device" /> <?php _e('Device', $this -> plugin_name); ?></label>
	        	<label><input <?php echo (!empty($curcustomfields) && in_array('mandatory', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="mandatory" id="custom_mandatory" /> <?php _e('Mandatory', $this -> plugin_name); ?></label>
	        	<label><input <?php echo (!empty($curcustomfields) && in_array('ip_address', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="ip_address" id="custom_ip_address" /> <?php _e('IP Address', $this -> plugin_name); ?></label>
	        	<?php foreach ($fields as $field) : ?>
	            	<label><input <?php echo (!empty($curfields) && in_array($field -> id, $curfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="fields[]" value="<?php echo $field -> id; ?>" id="fields_<?php echo $field -> id; ?>" /> <?php echo __($field -> title); ?></label>
	            <?php endforeach; ?>
	        </div>
	    <?php endif; ?>
	
		<input onclick="" type="submit" class="button" value="<?php _e('Apply', $this -> plugin_name); ?>" />
	</form>
</div>