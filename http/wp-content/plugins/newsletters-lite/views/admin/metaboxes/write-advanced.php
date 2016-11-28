<!-- Post/Page Editing Screen Metabox -->

<?php
	
global $post;
$post_id = $post -> ID;

$newsletters_sendasnewsletter = get_post_meta($post_id, 'newsletters_sendasnewsletter', true);
$newsletters_showtitle = get_post_meta($post_id, 'newsletters_showtitle', true);
$newsletters_showdate = get_post_meta($post_id, 'newsletters_showdate', true);
$scheduled = get_post_meta($post_id, 'newsletters_scheduled', true);
$sent = get_post_meta($post_id, '_newsletters_sent', true);
$history_id = get_post_meta($post_id, '_newsletters_history_id', true);
$postmailinglists = (empty($_POST[$this -> pre . 'mailinglists'])) ? get_post_meta($post_id, $this -> pre . 'mailinglists', true) : $_POST[$this -> pre . 'mailinglists'];
$theme_id = (empty($_POST[$this -> pre . 'theme_id'])) ? get_post_meta($post_id, $this -> pre . 'theme_id', true) : $_POST[$this -> pre . 'theme_id'];
$qtranslate_language = (empty($_POST[$this -> pre . 'qtranslate_language'])) ? get_post_meta($post_id, $this -> pre . 'qtranslate_language', true) : $_POST[$this -> pre . 'qtranslate_language'];
$sendonpublishef = (empty($_POST[$this -> pre . 'sendonpublishef'])) ? get_post_meta($post_id, $this -> pre . 'sendonpublishef', true) : $_POST[$this -> pre . 'sendonpublishef'];

?>

<div class="<?php echo $this -> pre; ?> newsletters newsletters_write_advanced">
	
	<?php if (!empty($scheduled)) : ?>
		<div class="misc-pub-section">
			<p class="newsletters_success"><?php _e('Note that this post is already scheduled to send out as a newsletter.', $this -> plugin_name); ?></p>
		</div>
	<?php endif; ?>
	
	<?php if (!empty($sent)) : ?>
		<?php if ($history = $this -> History() -> find(array('id' => $history_id))) : ?>
			<div class="misc-pub-section">
				<p class="newsletters_success"><?php echo sprintf(__('Note that this post has already been sent: %s', $this -> plugin_name), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id) . '">' . __($history -> subject) . '</a>'); ?></p>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<div class="misc-pub-section">
		<p>
			<label><input <?php echo (!empty($newsletters_sendasnewsletter)) ? 'checked="checked"' : ''; ?> onclick="if (jQuery(this).is(':checked')) { jQuery('#newsletters_sendasnewsletter_div').show(); } else { jQuery('#newsletters_sendasnewsletter_div').hide(); }" type="checkbox" name="newsletters_sendasnewsletter" value="1" id="newsletters_sendasnewsletter" /> <?php _e('Yes, send this post as a newsletter', $this -> plugin_name); ?></label>
			<span class="howto"><?php _e('Turn this on to send this post/page as a newsletter. Then configure it and Publish, Update or Schedule the post to execute.', $this -> plugin_name); ?></span>
		</p>
	</div>
	
	<div id="newsletters_sendasnewsletter_div" style="display:<?php echo (!empty($newsletters_sendasnewsletter)) ? 'block' : 'none'; ?>;">
		
		<div class="misc-pub-section">
			<p><a class="button button-secondary" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> settings_templates . '#sendasdiv'); ?>" target="_blank"><i class="fa fa-edit"></i> <?php _e('Edit the System Email Layout Used', $this -> plugin_name); ?></a> <?php echo $Html -> help(__('The system email layout used for sending a post/page as a newsletter can be changed according to your needs. Click the button to edit it.', $this -> plugin_name)); ?></p>
		</div>
		
		<?php if ($this -> language_do()) : ?>
			<div class="misc-pub-section">
			<p><strong><?php _e('Language', $this -> plugin_name); ?></strong></h4>
		    <p><?php _e('Choose which title/content in the editor above should be sent to the mailing list(s) chosen below.', $this -> plugin_name); ?></p>
		    <?php if ($el = $this -> language_getlanguages()) : ?>
		    	<p>
					<?php foreach ($el as $language) : ?>
		                <label><input <?php echo ((!empty($qtranslate_language) && $qtranslate_language == $language) || ($this -> language_default() == $language)) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>qtranslate_language" value="<?php echo $language; ?>" id="<?php echo $this -> pre; ?>qtranslate_language_<?php echo $language; ?>" /> <?php echo $this -> language_flag($language); ?> <?php echo stripslashes($this -> language_name($language)); ?></label><br/>
		            <?php endforeach; ?>
		        </p>
		    <?php else : ?>
		    	<p class="newsletters_error"><?php _e('No languages are available, please enable languages first.', $this -> plugin_name); ?></p>
		    <?php endif; ?>
		    </div>
		<?php endif; ?>
		
		<div class="misc-pub-section">		
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for=""><?php _e('Full/Excerpt', $this -> plugin_name); ?></label></th>
						<td>
							<label><input <?php echo ((!empty($sendonpublishef) && $sendonpublishef == "fp") || ($this -> get_option('sendonpublishef') == "fp")) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>sendonpublishef" value="fp" /> <?php _e('Full Post', $this -> plugin_name); ?></label>
							<label><input <?php echo ((!empty($sendonpublishef) && $sendonpublishef == "ep") || ($this -> get_option('sendonpublishef') == "ep")) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>sendonpublishef" value="ep" /> <?php _e('Excerpt of Post', $this -> plugin_name); ?></label>
							<span class="howto"><?php _e('Choose whether the full post or only an excerpt should be used.', $this -> plugin_name); ?></span>
						</td>
					</tr>
					<?php /*<tr>
						<th><label for="newsletters_showtitle"><?php _e('Show Title', $this -> plugin_name); ?></label></th>
						<td>
							<label><input <?php echo (!empty($newsletters_showtitle)) ? 'checked="checked"' : ''; ?> type="checkbox" name="newsletters_showtitle" value="1" id="newsletters_showtitle" /> <?php _e('Yes, include the title', $this -> plugin_name); ?></label>
							<span class="howto"><?php _e('Choose whether to show the title of the post as well.', $this -> plugin_name); ?></span>
						</td>
					</tr>*/ ?>
					<tr>
						<th><label for="newsletters_showdate"><?php _e('Show Date/Author', $this -> plugin_name); ?></label></th>
						<td>
							<label><input <?php echo (!empty($newsletters_showdate)) ? 'checked="checked"' : ''; ?> type="checkbox" name="newsletters_showdate" value="1" id="newsletters_showdate" /> <?php _e('Yes, show the date and author', $this -> plugin_name); ?></label>
							<span class="howto"><?php _e('Choose whether to show post meta such as date and author.', $this -> plugin_name); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="misc-pub-section newsletters_templates">
			<p><strong><?php _e('Select a Template', $this -> plugin_name); ?></strong></p>
			<p>
				<?php $Db -> model = $Theme -> model; ?>
				<?php if ($themes = $Db -> find_all(false, false, array('title', "ASC"))) : ?>
					<div class="scroll-list">
						<table class="widefat">
							<tbody>
						<?php $default_theme_id = $this -> default_theme_id('sending'); ?>
							<tr>
								<th class="check-column"><label><input <?php echo (empty($theme_id)) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>theme_id" value="0" id="theme0" /></label></th>
								<td><?php _e('NONE', $this -> plugin_name); ?></td>
							</tr>
					    <?php foreach ($themes as $theme) : ?>
					       <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
						        <th class="check-column"><input <?php echo ((!empty($theme_id) && $theme_id == $theme -> id) || $theme -> id == $default_theme_id) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>theme_id" value="<?php echo $theme -> id; ?>" id="theme<?php echo $theme -> id; ?>" /></th>
						        <td>
							        <label for="theme<?php echo $theme -> id; ?>"><?php echo __($theme -> title); ?></label>
							        <a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', title:'<?php echo __($theme -> title); ?>', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;" class=""><i class="fa fa-eye fa-fw"></i></a>
									<a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Template: %s', $this -> plugin_name), $theme -> title); ?>', href:newsletters_ajaxurl + 'action=newsletters_themeedit&amp;id=<?php echo $theme -> id; ?>'}); return false;" class=""><i class="fa fa-pencil fa-fw"></i></a>
						        </td>
					        </tr>
					    <?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</p>
		</div>
		
		<div class="misc-pub-section newsletters_mailinglists">
			<p><strong><?php _e('Select Mailing List(s)', $this -> plugin_name); ?></strong></p>
			<div class="scroll-list">
				<table class="widefat">
					<tbody>
						<?php if ($mailinglists = $Mailinglist -> select($privatelists = true)) : ?>
							<tr>
								<th class="check-column"><input type="checkbox" name="mailinglistsselectall" value="1" id="mailinglistsselectall" onclick="jqCheckAll(this, 'post', 'wpmlmailinglists');" /></th>
								<td><label for="mailinglistsselectall" style="font-weight:bold;"><?php _e('Select All', $this -> plugin_name); ?></label></td>
							</tr>
							<?php foreach ($mailinglists as $id => $title) : ?>
								<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
									<th class="check-column"><input id="checklist<?php echo $id; ?>" <?php echo (!empty($postmailinglists) && in_array($id, $postmailinglists)) ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo $this -> pre; ?>mailinglists[]" value="<?php echo $id; ?>" /></th>
									<td><label for="checklist<?php echo $id; ?>"><?php echo $title; ?> (<?php echo $SubscribersList -> count(array('list_id' => $id, 'active' => "Y")); ?> <?php _e('active subscribers', $this -> plugin_name); ?>)</label></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<p class="newsletters_error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></p>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		
		<br class="clear" />
	</div>
</div>