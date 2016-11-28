<!-- Form Preview -->

<div class="wrap newsletters">	
	<h2><?php _e('Form Preview', $this -> plugin_name); ?></h2>
	
	<?php $this -> render('forms' . DS . 'navigation', array('form' => $form), true, 'admin'); ?>
	
	<div class="postbox" style="padding:10px;">
		<iframe width="100%" frameborder="0" scrolling="no" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0;" src="<?php echo admin_url('admin-ajax.php?action=newsletters_form_preview&id=' . $form -> id); ?>" id="newsletters_form_preview_<?php echo $form -> id; ?>"></iframe>
    </div>	
</div>