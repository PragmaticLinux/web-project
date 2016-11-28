<div id="newsletters_forms_field_<?php echo $field -> id; ?>" class="postbox ">	
	<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel: First</span><span class="toggle-indicator" aria-hidden="true"></span></button>
	<div class="newsletters_delete_handle"><a href="" onclick="if (confirm('<?php _e('Are you sure you want to delete this field?', $this -> plugin_name); ?>')) { newsletters_forms_field_delete('<?php echo $field -> id; ?>'); } return false;"><i class="fa fa-times"></i></a></div>
	<h2 class="hndle ui-sortable-handle" onclick="jQuery(this).parent().toggleClass('closed');"><span><?php echo __($field -> title) . ' <span class="newsletters_handle_more">' . $Html -> field_type($field -> type, $field -> slug) . '</span>' . ((!empty($field -> required) && $field -> required == "Y") ? ' <span class="newsletters_error"><i class="fa fa-asterisk fa-sm"></i></span>' : ''); ?></span></h2>
	
	<div class="inside">	
		<?php /*<?php $this -> render('metaboxes' . DS . 'forms' . DS . 'field', array('field' => $field), true, 'admin'); ?>*/ ?>
		<?php echo stripslashes($content); ?>
	</div>
</div>