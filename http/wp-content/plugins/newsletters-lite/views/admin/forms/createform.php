<?php if (empty($ajax)) : ?>
	<div class="wrap newsletters">
		<div style="min-width:400px;" id="newsletters_forms_createform_wrapper">
<?php endif; ?>

		<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>

		<form onsubmit="newsletters_forms_createform(); return false;" action="<?php echo admin_url('admin-ajax.php?action=newsletters_forms_createform'); ?>" method="post" id="newsletters_forms_createform">
			<p>
				<label for="Subscribeform_title"><?php _e('Title', $this -> plugin_name); ?></label>
				<input class="widefat" type="text" name="Subscribeform[title]" value="" id="Subscribeform_title" />
			</p>
			
			<p class="submit">
				<input id="newsletters_forms_createform_submit" type="submit" name="createform" value="<?php _e('Create Form', $this -> plugin_name); ?>" id="createform" class="button button-primary" />
				<span id="newsletters_forms_createform_loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>
			</p>
		</form>
<?php if (empty($ajax)) : ?>
		</div>
	</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery.colorbox.resize();
	
	<?php if (!empty($success)) : ?>
		jQuery.colorbox.close(); 
		parent.location = '<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=' . $this -> Subscribeform() -> insertid); ?>';
	<?php endif; ?>
});

function newsletters_forms_createform() {
	jQuery('#newsletters_forms_createform_submit').prop('disabled', true);
	jQuery('#newsletters_forms_createform_loading').show();
	
	jQuery.ajax({
		url: newsletters_ajaxurl + 'action=newsletters_forms_createform',
		method: "POST",
		data: jQuery('#newsletters_forms_createform').serialize(),
	}).done(function(response) {
		jQuery('#newsletters_forms_createform_wrapper').html(response);
	}).error(function(response) {
		alert('<?php _e('Ajax call failed, please try again', $this -> plugin_name); ?>');
	});
	
	return false;
}
</script>