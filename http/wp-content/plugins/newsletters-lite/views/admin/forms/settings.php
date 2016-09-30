<!-- Form Settings -->

<div class="wrap newsletters">	
	<h2><?php _e('Form Settings', $this -> plugin_name); ?></h2>
	
	<?php $this -> render('forms' . DS . 'navigation', array('form' => $form), true, 'admin'); ?>
	
	<form action="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&amp;method=settings&amp;id=' . $form -> id); ?>" method="post" id="post" name="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo esc_attr(stripslashes($form -> id)); ?>" />
		
		<div id="newsletters-forms-settings-tabs">
			<ul>
				<li><a href="#newsletters-forms-settings-tabs-general"><?php _e('General', $this -> plugin_name); ?></a></li>
				<li><a href="#newsletters-forms-settings-tabs-confirmation"><?php _e('Confirmation', $this -> plugin_name); ?></a></li>
				<?php /*<li><a href="#newsletters-forms-settings-tabs-notifications"><?php _e('Notifications', $this -> plugin_name); ?></a></li>*/ ?>
			</ul>
			
			<div id="newsletters-forms-settings-tabs-general">
				<div class="inside">
					<h3><i class="fa fa-cogs"></i> <?php _e('General Settings', $this -> plugin_name); ?></h3>
					
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="buttontext"><?php _e('Button Text', $this -> plugin_name); ?></label></th>
								<td>
									<input type="text" class="widefat" name="buttontext" value="<?php echo esc_attr(stripslashes($form -> buttontext)); ?>" id="buttontext" />
									<span class="howto"><?php _e('Text that shows on the subscribe button', $this -> plugin_name); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="ajax"><?php _e('Enable Ajax', $this -> plugin_name); ?></label></th>
								<td>
									<label><input <?php echo (!empty($form -> ajax)) ? 'checked="checked"' : ''; ?> type="checkbox" name="ajax" value="1" id="ajax" /> <?php _e('Yes, enable Ajax form submission', $this -> plugin_name); ?></label>
									<span class="howto"><?php _e('Turn this on to submit this form with Ajax instead of page refresh.', $this -> plugin_name); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="captcha"><?php _e('Enable Captcha', $this -> plugin_name); ?></label></th>
								<td>
									<label><input <?php echo (!$this -> use_captcha()) ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($form -> captcha) && $this -> use_captcha()) ? 'checked="checked"' : ''; ?> type="checkbox" name="captcha" value="1" id="captcha" /> <?php _e('Yes, enable security captcha', $this -> plugin_name); ?></label>
									<?php if (!$this -> use_captcha()) : ?>
										<div class="newsletters_error"><?php _e('Please configure a security captcha under Newsletters > Configuration > System > Captcha in order to use this.', $this -> plugin_name); ?></div>
									<?php endif; ?>
									<span class="howto"><?php _e('Do you want to show a security captcha on this form to prevent spam subscriptions?', $this -> plugin_name); ?></span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div id="newsletters-forms-settings-tabs-confirmation">
				<h3><i class="fa fa-check"></i> <?php _e('Confirmation Settings', $this -> plugin_name); ?></h3>
				
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="confirmationtype_message"><?php _e('Confirmation Type', $this -> plugin_name); ?></label></th>
							<td>
								<label><input <?php echo (empty($form -> confirmationtype) || (!empty($form -> confirmationtype) && $form -> confirmationtype == "message")) ? 'checked="checked"' : ''; ?> onclick="jQuery('#confirmationtype_message_div').show(); jQuery('#confirmationtype_redirect_div').hide();" type="radio" name="confirmationtype" value="message" id="confirmationtype_message" /> <?php _e('Message', $this -> plugin_name); ?></label>
								<label><input <?php echo (!empty($form -> confirmationtype) && $form -> confirmationtype == "redirect") ? 'checked="checked"' : ''; ?> onclick="jQuery('#confirmationtype_message_div').hide(); jQuery('#confirmationtype_redirect_div').show();" type="radio" name="confirmationtype" value="redirect" id="confirmationtype_redirect" /> <?php _e('Redirect', $this -> plugin_name); ?></label>
							</td>
						</tr>
					</tbody>
				</table>
				
				<div id="confirmationtype_message_div" style="display:<?php echo (empty($form -> confirmationtype) || (!empty($form -> confirmationtype) && $form -> confirmationtype == "message")) ? 'block' : 'none'; ?>;">
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="confirmation_message"><?php _e('Message', $this -> plugin_name); ?></label></th>
								<td>
									<?php
										
									$settings = array(
										'media_buttons'		=>	true,
										'textarea_name'		=>	'confirmation_message',
										'textarea_rows'		=>	5,
										'quicktags'			=>	true,
										'teeny'				=>	true,
									);
									
									wp_editor(stripslashes($form -> confirmation_message), 'confirmation_message', $settings); 
										
									?>		
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<div id="confirmationtype_redirect_div" style="display:<?php echo (!empty($form -> confirmationtype) && $form -> confirmationtype == "redirect") ? 'block' : 'none'; ?>;">
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="confirmation_redirect"><?php _e('Redirect URL', $this -> plugin_name); ?></label></th>
								<td>
									<input type="text" class="widefat" name="confirmation_redirect" value="<?php echo esc_attr(stripslashes($form -> confirmation_redirect)); ?>" id="confirmation_redirect" />
									<span class="howto"><?php _e('Enter a URL to redirect to upon successful subscribe.', $this -> plugin_name); ?></span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<?php /*<div id="newsletters-forms-settings-tabs-notifications">
				<h3><i class="fa fa-envelope"></i> <?php _e('Notification Settings', $this -> plugin_name); ?></h3>
			</div>*/ ?>
		</div>
		
		<p class="submit">
			<input type="submit" name="" value="<?php _e('Save Settings', $this -> plugin_name); ?>" class="button button-primary" />
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery( "#newsletters-forms-settings-tabs" ).tabs();
});
</script>