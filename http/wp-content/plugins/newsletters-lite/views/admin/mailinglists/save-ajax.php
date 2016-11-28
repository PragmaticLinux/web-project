<?php if (empty($ajax)) : ?>
	<div id="newsletters-mailinglist-save-wrapper">
<?php endif; ?>

<div class="wrap">
	<h1><?php _e('Save a Mailing List', $this -> plugin_name); ?></h1>
	
	<?php if (!empty($errors)) : ?>
		<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
	<?php endif; ?>
	
	<form action="" method="post" id="newsletters-mailinglist-form">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Mailinglist.title"><?php _e('List Title', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($this -> language_do()) : ?>
							<?php $languages = $this -> language_getlanguages(); ?>
							<div id="mailinglist-title-tabs">
								<ul>
									<?php foreach ($languages as $language) : ?>
										<li><a href="#mailinglist-title-tabs-<?php echo $language; ?>"><?php echo $this -> language_flag($language); ?></a></li>
									<?php endforeach; ?>
								</ul>
								<?php foreach ($languages as $language) : ?>
									<div id="mailinglist-title-tabs-<?php echo $language; ?>">
										<input placeholder="<?php echo esc_attr(stripslashes(__('Enter mailing list title here', $this -> plugin_name))); ?>" type="text" class="widefat" name="Mailinglist[title][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $Mailinglist -> data[$Mailinglist -> model] -> title))); ?>" id="Mailinglist_title_<?php echo $language; ?>" />
									</div>
								<?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#mailinglist-title-tabs').tabs();
								}
							});
							</script>
						<?php else : ?>
							<?php echo $Form -> text('Mailinglist[title]', array('placeholder' => __('Enter mailing list title here', $this -> plugin_name))); ?>
						<?php endif; ?>
                    	<span class="howto"><?php _e('Fill in a title for your list as your users will see it.', $this -> plugin_name); ?></span>    
                    </td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-secondary"><?php _e('Cancel', $this -> plugin_name); ?></a>
			<input type="submit" id="newsletters-mailinglist-save-button" name="save" value="<?php _e('Save Mailing List', $this -> plugin_name); ?>" class="button button-primary" />
			<span id="newsletters-mailinglist-save-loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>
		</p>
	</form>
</div>

<?php if (empty($ajax)) : ?>
	</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#newsletters-mailinglist-form').submit(function() {
		jQuery('#newsletters-mailinglist-save-loading').show();
		jQuery('#newsletters-mailinglist-save-button').attr('disabled', "disabled");
		var formvalues = jQuery('#newsletters-mailinglist-form').serialize();
		
		jQuery.ajax({
			url: newsletters_ajaxurl + 'action=newsletters_mailinglist_save&fielddiv=<?php echo $fielddiv; ?>&fieldname=<?php echo $fieldname; ?>',
			data: formvalues,
			dataType: "json",
			method: "POST",
			success: function(response) {
				jQuery('#newsletters-mailinglist-save-button').removeAttr('disabled');
				jQuery('#newsletters-mailinglist-save-loading').hide();
				
				var success = response.success;
				var errors = response.errors;
				var form = response.blocks.form;
				var checklist = response.blocks.checklist;
				
				if (success == true) {
					jQuery('#<?php echo $fielddiv; ?>').html(checklist);
					jQuery.colorbox.close();
				} else {
					jQuery('#newsletters-mailinglist-save-wrapper').html(form);
					jQuery.colorbox.resize();
				}
			}
		});
		
		return false;
	});
});
</script>