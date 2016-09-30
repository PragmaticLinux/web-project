<!-- Manage Subscriptions Settings -->

<?php
	
$managementauthtype = $this -> get_option('managementauthtype');
$managementshowsubscriptions = $this -> get_option('managementshowsubscriptions');	
	
?>

<table class="form-table">
	<tbody>
    	<tr class="advanced-setting">
        	<th><label for="managementpost"><?php _e('Management Post ID', $this -> plugin_name); ?></label></th>
            <td>
            	<?php $this -> get_managementpost(); ?>
            	<?php if ($this -> language_do()) : ?>
            		<?php 
					
					$el = $this -> language_getlanguages(); 
					$managementpost = $this -> get_managementpost(false);
					
					?>
					<div id="managementposttabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($el as $language) : ?>
			                 	<li><a href="#managementposttab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($el as $language) : ?>
			            	<div id="managementposttab<?php echo $tabnumber; ?>">
			            		<input type="text" name="managementpost[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> get_managementpost(false, false, $language))); ?>" id="managementpost_<?php echo $language; ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#managementposttabs').tabs();
						}
					});
					</script>
            	<?php else : ?>
            		<input type="text" name="managementpost" value="<?php echo esc_attr(stripslashes($this -> get_managementpost(false))); ?>" id="managementpost" class="widefat" style="width:65px;" />
            	<?php endif; ?>
            	<span class="howto"><?php echo sprintf(__('ID of the WordPress post with the %s shortcode in it.', $this -> plugin_name), '<code>[' . $this -> pre . 'management]</code>'); ?></span>
            </td>
        </tr>
        <tr>
			<th><label for="<?php echo $this -> pre; ?>managelinktext"><?php _e('Management Link text', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php 
					
					$el = $this -> language_getlanguages(); 
					$managelinktext = $this -> get_option('managelinktext');
					
					?>
					<div id="managelinktexttabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($el as $language) : ?>
			                 	<li><a href="#managelinktexttab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($el as $language) : ?>
			            	<div id="managelinktexttab<?php echo $tabnumber; ?>">
			            		<input type="text" name="managelinktext[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $managelinktext))); ?>" id="managelinktext_<?php echo $language; ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#managelinktexttabs').tabs();
						}
					});
					</script>
				<?php else : ?>
					<input class="widefat" type="text" id="<?php echo $this -> pre; ?>managelinktext" name="managelinktext" value="<?php echo esc_attr(stripslashes($this -> get_option('managelinktext'))); ?>" />
				<?php endif; ?>
				<span class="howto"><?php echo sprintf(__('displays subscription management page. generated by %s in content', $this -> plugin_name), '<code>[' . $this -> pre . 'manage]</code>'); ?></span>
			</td>
		</tr>
        <tr>
        	<th><label for="managementloginsubject"><?php _e('Authentication Email Subject', $this -> plugin_name); ?></label></th>
            <td>
            	<?php if ($this -> language_do()) : ?>
            		<?php 
					
					$el = $this -> language_getlanguages(); 
					$managementloginsubject = $this -> get_option('managementloginsubject');
					
					?>
					<div id="managementloginsubjecttabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($el as $language) : ?>
			                 	<li><a href="#managementloginsubjecttab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($el as $language) : ?>
			            	<div id="managementloginsubjecttab<?php echo $tabnumber; ?>">
			            		<input type="text" name="managementloginsubject[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $managementloginsubject))); ?>" id="managementloginsubject_<?php echo $language; ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#managementloginsubjecttabs').tabs();
						}
					});
					</script>
            	<?php else : ?>
            		<input type="text" class="widefat" name="managementloginsubject" value="<?php echo esc_attr(stripslashes($this -> get_option('managementloginsubject'))); ?>" id="managementloginsubject" />
            	<?php endif; ?>
                <span class="howto"><?php _e('The subject of the email when a subscriber authenticates.', $this -> plugin_name); ?></span>
            </td>
        </tr>
        <tr>
	        <th><label for="authenticatelinktext"><?php _e('Authenticate Link Text', $this -> plugin_name); ?></label></th>
	        <td>
		        <?php if ($this -> language_do()) : ?>
            		<?php 
					
					$el = $this -> language_getlanguages(); 
					$authenticatelinktext = $this -> get_option('authenticatelinktext');
					
					?>
					<div id="authenticatelinktexttabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($el as $language) : ?>
			                 	<li><a href="#authenticatelinktexttab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($el as $language) : ?>
			            	<div id="authenticatelinktexttab<?php echo $tabnumber; ?>">
			            		<input type="text" name="authenticatelinktext[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $authenticatelinktext))); ?>" id="authenticatelinktext_<?php echo $language; ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#authenticatelinktexttabs').tabs();
						}
					});
					</script>
            	<?php else : ?>
            		<input type="text" class="widefat" name="authenticatelinktext" value="<?php echo esc_attr(stripslashes($this -> get_option('authenticatelinktext'))); ?>" id="authenticatelinktext" />
            	<?php endif; ?>
		        <span class="howto"><?php _e('Text of the link in the Manage Subscriptions authentication email', $this -> plugin_name); ?></span>
	        </td>
        </tr>
        <tr class="advanced-setting">
	        <th><label for="managementauthtype_3"><?php _e('Authentication Type', $this -> plugin_name); ?></label></th>
	        <td>
		        <label><input <?php echo (!empty($managementauthtype) && $managementauthtype == 1) ? 'checked="checked"' : ''; ?> type="radio" name="managementauthtype" value="1" id="managementauthtype_1" /> <?php _e('Cookie', $this -> plugin_name); ?></label>
		        <label><input <?php echo (!empty($managementauthtype) && $managementauthtype == 2) ? 'checked="checked"' : ''; ?> type="radio" name="managementauthtype" value="2" id="managementauthtype_2" /> <?php _e('Session', $this -> plugin_name); ?></label>
		        <label><input <?php echo (!empty($managementauthtype) && $managementauthtype == 3) ? 'checked="checked"' : ''; ?> type="radio" name="managementauthtype" value="3" id="managementauthtype_3" /> <?php _e('Both (Cookie & Session)', $this -> plugin_name); ?></label>
	        </td>
        </tr>
        <tr>
	        <th><label for="managementshowsubscriptions_Y"><?php _e('Show Current Subscriptions', $this -> plugin_name); ?></label></th>
	        <td>
		        <label><input <?php echo (empty($managementshowsubscriptions) || (!empty($managementshowsubscriptions) && $managementshowsubscriptions == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="managementshowsubscriptions" value="Y" id="managementshowsubscriptions_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
		        <label><input <?php echo (!empty($managementshowsubscriptions) && $managementshowsubscriptions == "N") ? 'checked="checked"' : ''; ?> type="radio" name="managementshowsubscriptions" value="N" id="managementshowsubscriptions_N" /> <?php _e('No', $this -> plugin_name); ?></label>
	        </td>
        </tr>
        <tr>
        	<th><label for="managementallownewsubscribes_Y"><?php _e('Allow New Subscribes', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input <?php echo ($this -> get_option('managementallownewsubscribes') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="managementallownewsubscribes" value="Y" id="managementallownewsubscribes_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                <label><input <?php echo ($this -> get_option('managementallownewsubscribes') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="managementallownewsubscribes" value="N" id="managementallownewsubscribes_N" /> <?php _e('No', $this -> plugin_name); ?></label>
            	<span class="howto"><?php _e('Should other, non-private lists be shown for subscribers to subscribe to?', $this -> plugin_name); ?></span>
            </td>
        </tr>
        <tr>
        	<th><label for="managementcustomfields_Y"><?php _e('Custom Fields In Management', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input onclick="jQuery('#managementcustomfieldsdiv').show();" <?php echo ($this -> get_option('managementcustomfields') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="managementcustomfields" value="Y" id="managementcustomfields_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                <label><input onclick="jQuery('#managementcustomfieldsdiv').hide();" <?php echo ($this -> get_option('managementcustomfields') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="managementcustomfields" value="N" id="managementcustomfields_N" /> <?php _e('No', $this -> plugin_name); ?></label>
            	<span class="howto"><?php _e('Should custom fields be shown in the management section to allow subscribers to edit values?', $this -> plugin_name); ?></span>
            </td>
        </tr>
    </tbody>
</table>

<div class="advanced-setting" id="managementcustomfieldsdiv" style="display:<?php echo ($this -> get_option('managementcustomfields') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
	        	<th><label for="managementallowemailchange_Y"><?php _e('Allow Changing Email Address', $this -> plugin_name); ?></label></th>
	        	<td>
	        		<label><input <?php echo ($this -> get_option('managementallowemailchange') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="managementallowemailchange" value="Y" id="managementallowemailchange_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
	        		<label><input <?php echo ($this -> get_option('managementallowemailchange') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="managementallowemailchange" value="N" id="managementallowemailchange_N" /> <?php _e('No', $this -> plugin_name); ?></label>
	        		<span class="howto"><?php _e('Allow subscribers to change their email address?', $this -> plugin_name); ?></span>
	        	</td>
	        </tr>
	        <tr>
	        	<th><label for=""><?php _e('Allow Changing of Format', $this -> plugin_name); ?></label></th>
	        	<td>
	        		<?php $managementformatchange = $this -> get_option('managementformatchange'); ?>
	        		<label><input <?php echo (!empty($managementformatchange) && $managementformatchange == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="managementformatchange" value="Y" id="managementformatchange_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
	        		<label><input <?php echo (!empty($managementformatchange) && $managementformatchange == "N") ? 'checked="checked"' : ''; ?> type="radio" name="managementformatchange" value="N" id="managementformatchange_N" /> <?php _e('No', $this -> plugin_name); ?></label>
	        		<span class="howto"><?php _e('Let subscribers choose between HTML and TEXT formatted emails.', $this -> plugin_name); ?></span>
	        	</td>
	        </tr>
		</tbody>
	</table>
</div>