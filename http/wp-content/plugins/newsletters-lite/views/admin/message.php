<?php if (!empty($message)) : ?>
	<div id="error" class="updated notice is-dismissible">
		<p><i class="fa fa-check"></i> <?php echo $message; ?></p>
		<?php if (!empty($dismissable)) : ?>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo __('Dismiss this notice.', $this -> plugin_name); ?></span></button>
		<?php endif; ?>
	</div>
<?php endif; ?>