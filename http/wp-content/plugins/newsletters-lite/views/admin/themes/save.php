<div class="wrap <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Save a Template', $this -> plugin_name); ?></h2>
    
    <p>
    	<?php _e('This is a full HTML template and should contain at least <code>[wpmlcontent]</code> somewhere.', $this -> plugin_name); ?><br/>
        <?php _e('You may use any of the', $this -> plugin_name); ?> <a class="button button-secondary" href="" onclick="jQuery.colorbox({title:'<?php _e('Shortcodes/Variables', $this -> plugin_name); ?>', maxHeight:'80%', maxWidth:'80%', href:'<?php echo admin_url('admin-ajax.php'); ?>?action=<?php echo $this -> pre; ?>setvariables'}); return false;"> <?php _e('shortcodes/variables', $this -> plugin_name); ?></a> <?php _e('inside templates.', $this -> plugin_name); ?><br/>
        <?php _e('Upload your images, stylesheets and other elements via FTP or the media uploader in WordPress.', $this -> plugin_name); ?><br/>
        <?php _e('Please ensure that all links, images and other references use full, absolute URLs.', $this -> plugin_name); ?>
    </p>
    
    <form action="?page=<?php echo $this -> sections -> themes; ?>&amp;method=save" method="post" enctype="multipart/form-data">
    	<?php echo $Form -> hidden('Theme[id]'); ?>
    	<?php echo $Form -> hidden('Theme[name]'); ?>
    
    	<table class="form-table">
        	<tbody>
            	<tr>
                	<th><label for="Theme.title"><?php _e('Title', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('The title of this newsletter template for internal usage.', $this -> plugin_name)); ?></th>
                    <td>
                    	<?php echo $Form -> text('Theme[title]', array('placeholder' => __('Enter template title here', $this -> plugin_name))); ?>
                    </td>
                </tr>
                <tr>
                	<th><label for="Theme.type_upload"><?php _e('Template Type', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('Choose how you want to save this newsletter template. You can either paste HTML code or upload a .html file.', $this -> plugin_name)); ?></th>
                    <td>
                    	<label><input <?php echo ($Html -> field_value('Theme[type]') == "upload" || $Html -> field_value('Theme[type]') == "") ? 'checked="checked"' : ''; ?> onclick="jQuery('#typediv_upload').show(); jQuery('#typediv_paste').hide();" type="radio" name="Theme[type]" value="upload" id="Theme.type_upload" /> <?php _e('Upload an HTML File', $this -> plugin_name); ?></label>
                        <label><input <?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'checked="checked"' : ''; ?> onclick="jQuery('#typediv_paste').show(); jQuery('#typediv_upload').hide();" type="radio" name="Theme[type]" value="paste" id="Theme.type_paste" /> <?php _e('HTML Code', $this -> plugin_name); ?></label>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div id="typediv_upload" style="display:<?php echo ($Html -> field_value('Theme[type]') == "" || $Html -> field_value('Theme[type]') == "upload") ? 'block' : 'none'; ?>;">
        	<table class="form-table">
            	<tbody>
                	<tr>
                    	<th><label for=""><?php _e('Choose HTML File', $this -> plugin_name); ?></label></th>
                        <td>
                        	<input class="widefat" type="file" name="upload" value="" />
                            <?php if (!empty($Theme -> errors['upload'])) : ?>
                            	<div class="newsletters_error"><?php echo $Theme -> errors['upload']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="typediv_paste" style="display:<?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'block' : 'none'; ?>;">
	        <p><input type="button" class="button button-secondary" id="thememediaupload" value="<?php _e('Add Media', $this -> plugin_name); ?>" /></p>
	        
	        <script type="text/javascript">
        	jQuery(document).ready(function() {
				var file_frame;
				
				jQuery('#thememediaupload').live('click', function( event ){
					event.preventDefault();
					
					// If the media frame already exists, reopen it.
					if (file_frame) {
						file_frame.open();
						return;
					}
					
					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media({
						title: '<?php _e('Upload Media', $this -> plugin_name); ?>',
						button: {
							text: '<?php _e('Copy URL', $this -> plugin_name); ?>',
						},
						multiple: false  // Set to true to allow multiple files to be selected
					});
						
					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						// We set multiple to false so only get one image from the uploader
						attachment = file_frame.state().get('selection').first().toJSON();
						
						// Do something with attachment.id and/or attachment.url here
						
						//jQuery('#Slide_attachment_id').val(attachment.id);
						//jQuery('#Slide_image_file').val(attachment.url);
						//jQuery('#Slide_mediaupload_image').html('<a href="' + attachment.url + '" class="colorbox" onclick="jQuery.colorbox({href:\'' + attachment.url + '\'}); return false;"><img class="slideshow_dropshadow" style="width:100px;" src="' + attachment.sizes.thumbnail.url + '" /></a>');
						
						window.prompt("Copy to clipboard: Ctrl+C, Enter", attachment.url);
					});
					
					// Finally, open the modal
					file_frame.open();
				});
        	});
        	</script>
	        
        	<textarea name="Theme[paste]" class="widefat" contenteditable="true" id="Theme_paste" rows="10" cols="100%"><?php echo esc_attr(stripslashes($Theme -> data -> paste)); ?></textarea>
        	
        	<script type="text/javascript">
        	jQuery(document).ready(function() {	        	
            	jQuery('textarea#Theme_paste').ckeditor({
                	fullPage: true,
					allowedContent: true,
					height: 500,
					entities: false,
					//extraPlugins: 'image2,autogrow,codesnippet,tableresize',
					extraPlugins: 'image2,codesnippet,tableresize',
					autoGrow_onStartup: true
            	});
        	});
        	</script>
        </div>
        
        <?php if (true || empty($Theme -> data -> id)) : ?>
        	<table class="form-table">
	        	<tbody>
		        	<tr>
		        		<th><label for="Theme_imgprependurl"><?php _e('Image Prepend URL', $this -> plugin_name); ?></label>
		        		<?php echo $Html -> help(__('If your template has relative image paths in the source, this image prepend URL setting is very useful to automatically add an absolute URL to the source attribute of all images. Eg. <code>src="images/myimage.jpg"</code> and you fill in a prepend URL of <code>http://domain.com/</code>, it will become <code>src="http://domain.com/images/myimage.jpg"</code>', $this -> plugin_name)); ?></th>
		        		<td>
		        			<input type="text" class="widefat" name="Theme[imgprependurl]" value="<?php echo esc_attr(stripslashes($Theme -> data -> imgprependurl)); ?>" id="Theme_imgprependurl" />
		        			<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Prepend the SRC attribute of IMG tags with a URL', $this -> plugin_name); ?></span>
		        		</td>
		        	</tr>
	        	</tbody>
        	</table>
        <?php endif; ?>
        
        <?php $theme_name = $Html -> field_value('Theme[name]'); ?>
        <?php if (!empty($theme_name)) : ?>
        	<?php if ($theme_name == "themailer") : ?>
        		<h3><?php _e('The Mailer Settings', $this -> plugin_name); ?></h3>
        		<table class="form-table">
        			<tbody>
        				<tr>
        					<th><label for="themailer_address"><?php _e('Address', $this -> plugin_name); ?></label></th>
        					<td>
        						<textarea name="themailer_address" rows="4" class="widefat" id="themailer_address"><?php echo esc_attr(stripslashes($this -> get_option('themailer_address'))); ?></textarea>
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="themailer_facebook"><?php _e('Facebook URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="themailer_facebook" value="<?php echo esc_attr(stripslashes($this -> get_option('themailer_facebook'))); ?>" id="themailer_facebook" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="themailer_twitter"><?php _e('Twitter URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="themailer_twitter" value="<?php echo esc_attr(stripslashes($this -> get_option('themailer_twitter'))); ?>" id="themailer_twitter" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="themailer_rss"><?php _e('RSS URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="themailer_rss" value="<?php echo esc_attr(stripslashes($this -> get_option('themailer_rss'))); ?>" id="themailer_rss" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        			</tbody>
        		</table>
        	<?php elseif ($theme_name == "pronews") : ?>
        		<h3><?php _e('Pro News Settings', $this -> plugin_name); ?></h3>
        		<table class="form-table">
        			<tbody>
        				<tr>
        					<th><label for="pronews_address"><?php _e('Address', $this -> plugin_name); ?></label></th>
        					<td>
        						<textarea name="pronews_address" rows="4" class="widefat" id="pronews_address"><?php echo esc_attr(stripslashes($this -> get_option('pronews_address'))); ?></textarea>
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="pronews_facebook"><?php _e('Facebook URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="pronews_facebook" value="<?php echo esc_attr(stripslashes($this -> get_option('pronews_facebook'))); ?>" id="pronews_facebook" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="pronews_twitter"><?php _e('Twitter URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="pronews_twitter" value="<?php echo esc_attr(stripslashes($this -> get_option('pronews_twitter'))); ?>" id="pronews_twitter" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="pronews_rss"><?php _e('RSS URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="pronews_rss" value="<?php echo esc_attr(stripslashes($this -> get_option('pronews_rss'))); ?>" id="pronews_rss" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        			</tbody>
        		</table>
        	<?php elseif ($theme_name == "lagoon") : ?>
        		<h3><?php _e('Lagoon Settings', $this -> plugin_name); ?></h3>
        		<table class="form-table">
        			<tbody>
        				<tr>
        					<th><label for="lagoon_address"><?php _e('Address', $this -> plugin_name); ?></label></th>
        					<td>
        						<textarea name="lagoon_address" rows="4" class="widefat" id="lagoon_address"><?php echo esc_attr(stripslashes($this -> get_option('lagoon_address'))); ?></textarea>
        						<span class="howto"></span>
        					</td>
        				</tr>
        			</tbody>
        		</table>
        	<?php endif; ?>
        <?php endif; ?>
        
        <table class="form-table">
        	<tbody>
        		<tr>
        			<th><label for="Theme_inlinestyles_N"><?php _e('Inline Styles', $this -> plugin_name); ?></label>
        			<?php echo $Html -> help(__('Set this setting to "Yes" to automatically convert all CSS rules into inline, style attributes in the HTML elements. If you use this setting, be sure to create a backup of your original HTML for easier editing later on.', $this -> plugin_name)); ?></th>
        			<td>
        				<label><input onclick="if (!confirm('<?php echo __('Please ensure that you create a local copy/backup of your newsletter template HTML for editing in the future.', $this -> plugin_name); ?>')) { return false; }" type="radio" name="Theme[inlinestyles]" value="Y" id="Theme_inlinestyles_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
        				<label><input type="radio" checked="checked" name="Theme[inlinestyles]" value="N" id="Theme_inlinestyles_N" /> <?php _e('No', $this -> plugin_name); ?></label>
        				<span class="howto"><?php _e('Convert CSS rules into inline, style attributes on elements.', $this -> plugin_name); ?></span>
        			</td>
        		</tr>
        		<tr>
        			<th><label for="Theme_acolor"><?php _e('Shortcode Link Color', $this -> plugin_name); ?></label>
        			<?php echo $Html -> help(__('Set the color of the links generated from the plugin shortcodes dynamically.', $this -> plugin_name)); ?></th>
        			<td>
        				<fieldset>
        					<legend class="screen-reader-text"><span>Background Color</span></legend>
        					<div class="wp-picker-container">
        						<a tabindex="0" id="acolorbutton" class="wp-color-result" style="background-color:<?php echo $Html -> field_value('Theme[acolor]'); ?>;" title="Select Color"></a>
        						<span class="wp-picker-input-wrap">
        							<input type="text" name="Theme[acolor]" id="Theme_acolor" value="<?php echo $Html -> field_value('Theme[acolor]'); ?>" class="wp-color-picker" style="display: none;" />
        						</span>
        					</div>
						</fieldset>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#Theme_acolor').iris({
								hide: true,
								change: function(event, ui) {
									jQuery('#acolorbutton').css('background-color', ui.color.toString());
								}
							});
							
							jQuery('#Theme_acolor').click(function(event) {
								event.stopPropagation();
							});
						
							jQuery('#acolorbutton').click(function(event) {							
								jQuery(this).attr('title', "Current Color");
								jQuery('#Theme_acolor').iris('toggle').toggle();								
								event.stopPropagation();
							});
							
							jQuery('html').click(function() {
								jQuery('#Theme_acolor').iris('hide').hide();
								jQuery('#acolorbutton').attr('title', "Select Color");
							});
						});
						</script>
						<span class="howto"><?php echo sprintf(__('Control the color of the links generated by shortcodes such as %s, %s, %s, etc.', $this -> plugin_name), '[wpmlonline]', '[wpmlactivate]', '[wpmlunsubscribe]'); ?></span>
        			</td>
        		</tr>
        	</tbody>
        </table>
        
        <p class="submit">
        	<input class="button-primary" type="submit" name="save" value="<?php _e('Save Template', $this -> plugin_name); ?>" />
        	<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php _e('Continue editing', $this -> plugin_name); ?></label>
			</div>
        </p>
    </form>
</div>