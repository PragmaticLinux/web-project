<?php if (!empty($message)) : ?>
	<div id="error" class="updated notice is-dismissible error">
		<p><i class="fa fa-times"></i> <?php echo $message; ?></p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo __('Dismiss this notice.', $this -> plugin_name); ?></span></button>
	</div>
<?php endif; ?>