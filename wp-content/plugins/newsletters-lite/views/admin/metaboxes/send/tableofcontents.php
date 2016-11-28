<?php $this -> render('metaboxes' . DS . 'admin-mode-switcher', false, true, 'admin'); ?>

<p class="savebutton">
	<input id="savedraftbutton2" style="float:left;" type="submit" name="draft" value="<?php _e('Save Draft', $this -> plugin_name); ?>" class="button button-highlighted" />
	
	<?php $sendbutton = ($this -> get_option('sendingprogress') == "N") ? __('Queue Newsletter', $this -> plugin_name) : __('Send Newsletter', $this -> plugin_name); ?>
	<input class="button button-primary button-large" type="submit" name="send" id="sendbutton2" disabled="disabled" value="<?php echo $sendbutton; ?>" />
</p>