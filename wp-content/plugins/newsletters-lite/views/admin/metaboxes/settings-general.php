<!-- General Configuration -->

<?php

$replytodifferent = $this -> get_option('replytodifferent');
$smtpreply = $this -> get_option('smtpreply');
$mailapi = $this -> get_option('mailapi');
$tracking = $this -> get_option('tracking');
$tracking_image = $this -> get_option('tracking_image');
$tracking_image_file = $this -> get_option('tracking_image_file');

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>adminemail"><?php _e('Administrator Email', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('This email address is used for general notification purposes throughout the plugin. You may use multiple, comma separated email addresses for multiple administrators eg. email1@domain.com,email2@domain.com,email3@domain.com,etc.', $this -> plugin_name)); ?></th>
			<td>
				<input type="text" class="widefat" id="<?php echo $this -> pre; ?>adminemail" name="adminemail" value="<?php echo esc_attr(stripslashes($this -> get_option('adminemail'))); ?>" />
				<span class="howto"><?php _e('Email address of the administrator for notification purposes.', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>smtpfromname"><?php _e('From Name', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Use your business name, website name or even your own name which will appear to the recipient in their email/webmail client so that they immediately know from whom the email was sent.', $this -> plugin_name)); ?></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php 
					
					$el = $this -> language_getlanguages(); 
					$smtpfromname = $this -> get_option('smtpfromname');
					
					?>
					<div id="smtpfromnametabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($el as $language) : ?>
			                 	<li><a href="#smtpfromnametab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($el as $language) : ?>
			            	<div id="smtpfromnametab<?php echo $tabnumber; ?>">
			            		<input type="text" name="smtpfromname[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $smtpfromname))); ?>" id="smtpfromname_<?php echo $language; ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#smtpfromnametabs').tabs();
						}
					});
					</script>
				<?php else : ?>
            		<input class="widefat" type="text" id="<?php echo $this -> pre; ?>smtpfromname" name="smtpfromname" value="<?php echo esc_attr(stripslashes($this -> get_option('smtpfromname'))); ?>" />
            	<?php endif; ?>
                <span class="howto"><?php _e('This is the name that will be displayed in the From field to your subscribers.', $this -> plugin_name); ?></span>
            </td>
		</tr>
		<tr>
			<th><label for="smtpfrom"><?php _e('From Address', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('This email address is used for the "From:" header in all outgoing emails and it will appear in the recipient email/webmail client as the sender from which the email was sent.', $this -> plugin_name)); ?></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php 
					
					$el = $this -> language_getlanguages(); 
					$smtpfrom = $this -> get_option('smtpfrom');
					
					?>
					<div id="smtpfromtabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($el as $language) : ?>
			                 	<li><a href="#smtpfromtab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($el as $language) : ?>
			            	<div id="smtpfromtab<?php echo $tabnumber; ?>">
			            		<input type="text" name="smtpfrom[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $smtpfrom))); ?>" id="smtpfrom_<?php echo $language; ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#smtpfromtabs').tabs();
						}
					});
					</script>
				<?php else : ?>
            		<input onkeyup="jQuery('#updatereturnpath_div').show(); if (jQuery('#updatereturnpath').attr('checked')) { jQuery('#bounceemail').val(jQuery(this).val()); }" class="widefat" type="text" id="smtpfrom" name="smtpfrom" value="<?php echo esc_attr(stripslashes($this -> get_option('smtpfrom'))); ?>" />
            		
            		<div id="updatereturnpath_div" style="display:none;">
	            		<label><input onclick="jQuery('#bounceemail').val(jQuery('#smtpfrom').val());" type="checkbox" name="updatereturnpath" value="1" id="updatereturnpath" /> <?php _e('Update "Bounce Receival Email" setting with this value as well?', $this -> plugin_name); ?></label>
	            		<?php echo $Html -> help(__('Many email servers requires the "Bounce Receival Email" (Return-Path) header value to be the same as the "From Address" (From) header value else it may not send out emails. If your emails are not going out, try making the "Bounce Receival Email" (Return-Path) and "From Address" (From) exactly the same using this checkbox.', $this -> plugin_name)); ?>
	            	</div>
            	<?php endif; ?>
                
                <span class="howto"><?php _e('This is the From email address that your subscribers will see.', $this -> plugin_name); ?></span>
            </td>
		</tr>
		<tr class="advanced-setting">
			<th><label for="replytodifferent"><?php _e('Different Reply To', $this -> plugin_name); ?></label></th>
			<td>
				<label><input onclick="if (jQuery(this).is(':checked')) { jQuery('#replytodifferent_div').show(); } else { jQuery('#replytodifferent_div').hide(); }" <?php echo (!empty($replytodifferent)) ? 'checked="checked"' : ''; ?> type="checkbox" name="replytodifferent" value="1" id="replytodifferent" /> <?php _e('Yes, set a different Reply To address.', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('By default, the Reply To is the same as the From Address', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="replytodifferent_div" style="display:<?php echo (!empty($replytodifferent)) ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="smtpreply"><?php _e('Reply To Address', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="smtpreply" value="<?php echo esc_attr(stripslashes($smtpreply)); ?>" id="smtpreply" />
					<span class="howto"><?php _e('The email address used when readers reply to an email/newsletter.', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="trackingY"><?php _e('Read Tracking', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Turn this setting on to enable the remote read tracking then you can use the shortcode [wpmltrack] inside your newsletter template or content.', $this -> plugin_name)); ?></th>
			<td>
				<label><input onclick="jQuery('#tracking_div').show();" <?php echo ($this -> get_option('tracking') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="tracking" value="Y" id="trackingY" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#tracking_div').hide();" <?php echo ($this -> get_option('tracking') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="tracking" value="N" id="trackingN" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Tracking inside newsletters to tell you how many emails were (not) read', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="tracking_div" style="display:<?php echo (!empty($tracking) && $tracking == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr class="advanced-setting">
				<th><label for="tracking_image_invisible"><?php _e('Tracking Image', $this -> plugin_name); ?></label></th>
				<td>
					<label><input onclick="jQuery('#tracking_image_div').hide();" <?php echo (empty($tracking_image) || (!empty($tracking_image) && $tracking_image == "invisible")) ? 'checked="checked"' : ''; ?> type="radio" name="tracking_image" value="invisible" id="tracking_image_invisible" /> <?php _e('Invisible', $this -> plugin_name); ?></label>
					<label><input onclick="jQuery('#tracking_image_div').show();" <?php echo (!empty($tracking_image) && $tracking_image == "custom") ? 'checked="checked"' : ''; ?> type="radio" name="tracking_image" value="custom" id="tracking_image_custom" /> <?php _e('Custom Image/Logo', $this -> plugin_name); ?></label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div id="tracking_image_div" style="display:<?php echo (!empty($tracking_image) && $tracking_image == "custom") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="tracking_image_file"><?php _e('Tracking Image/Logo', $this -> plugin_name); ?></label></th>
					<td>
						<input type="file" name="tracking_image_file" value="" id="tracking_image_file" />
						
						<?php if (!empty($tracking_image_file)) : ?>
							
							<p>
								<?php _e('Current image:', $this -> plugin_name); ?><br/>
								<img src="<?php echo $Html -> uploads_url() . '/' . $this -> plugin_name . '/' . $tracking_image_file; ?>" alt="tracking" />
							</p>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="clicktrack_Y"><?php _e('Click Tracking', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('The click tracking will convert your links to unique shortlinks automatically. When the links are clicked inside newsletters, the link, email and subscriber is tracked to create statistics.', $this -> plugin_name)); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('clicktrack') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="clicktrack" value="Y" id="clicktrack_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('clicktrack') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="clicktrack" value="N" id="clicktrack_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Should links inside newsletters be tracked as they are clicked?', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>mailtype"><?php _e('Mail Type', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Choose your preferred way of sending emails. If you are not sure, leave it on "Local Server" setting to send through your own server. Advanced users can use an "SMTP Server" if needed.', $this -> plugin_name)); ?></th>
			<td>
				<?php $mailtype = $this -> get_option('mailtype'); ?>
				<label><input <?php echo ($mailtype == "smtp") ? 'checked="checked"' : ''; ?> onclick="jQuery('#mailtypediv').show(); jQuery('#mailtypeapi').hide();" type="radio" name="mailtype" value="smtp" />&nbsp;<?php _e('SMTP Server', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Use this for any remote or local SMTP server or popular email and relay services such as Gmail, AuthSMTP, AmazonSES, SendGrid, etc.', $this -> plugin_name)); ?>
				<label><input id="<?php echo $this -> pre; ?>mailtype" <?php echo ($mailtype == "mail") ? 'checked="checked"' : ''; ?> onclick="jQuery('#mailtypediv').hide(); jQuery('#mailtypeapi').hide();" type="radio" name="mailtype" value="mail" />&nbsp;<?php _e('Local Server', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Local server uses WordPress wp_mail() which by default uses your local email exchange on this hosting. This is the recommended option as it should work without any additional setup.', $this -> plugin_name)); ?>
				<label><input <?php echo (!$this -> ci_serial_valid()) ? 'disabled="disabled"' : ''; ?> id="<?php echo $this -> pre; ?>mailtype" <?php echo ($mailtype == "api") ? 'checked="checked"' : ''; ?> onclick="jQuery('#mailtypediv').hide(); jQuery('#mailtypeapi').show();" type="radio" name="mailtype" value="api" />&nbsp;<?php _e('API', $this -> plugin_name); ?></label>
                <span class="howto"><?php _e('The method of sending out emails globally.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<!-- SMTP Server -->
<div id="mailtypediv" style="display:<?php echo $mailtypedisplay = ($mailtype == "smtp" || $mailtype == "gmail") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th>
					<label for="<?php echo $this -> pre; ?>smtphost"><?php _e('SMTP Host Name', $this -> plugin_name); ?></label>
				</th>
				<td>
                	<input class="widefat" type="text" id="<?php echo $this -> pre; ?>smtphost" name="smtphost" value="<?php echo esc_attr(stripslashes($this -> get_option('smtphost'))); ?>" />
                    <span class="howto"><?php _e('SMTP host name eg. "localhost". For Gmail, use "smtp.gmail.com".', $this -> plugin_name); ?></span>
                </td>
			</tr>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>smtpport"><?php _e('SMTP Port', $this -> plugin_name); ?></label></th>
				<td>
                	<input class="widefat" style="width:65px;" type="text" name="smtpport" value="<?php echo esc_attr(stripslashes($this -> get_option('smtpport'))); ?>" id="<?php echo $this -> pre; ?>smtpport" />
                    <span class="howto"><?php _e('This is the SMTP port number to connect to. This is usually port 25.', $this -> plugin_name); ?></span>
                </td>
			</tr>
			<tr>
				<th><label for="smtpsecure_N"><?php _e('SMTP Protocol', $this -> plugin_name); ?></label></th>
				<td>
					<?php $smtpsecure = $this -> get_option('smtpsecure'); ?>
					<label><input <?php echo ($smtpsecure == "ssl") ? 'checked="checked"' : ''; ?> type="radio" name="smtpsecure" value="ssl" id="smtpsecure_ssl" /> <?php _e('SSL', $this -> plugin_name); ?></label>
					<label><input <?php echo ($smtpsecure == "tls") ? 'checked="checked"' : ''; ?> type="radio" name="smtpsecure" value="tls" id="smtpsecure_tls" /> <?php _e('TLS', $this -> plugin_name); ?></label>
					<label><input <?php echo (empty($smtpsecure) || $smtpsecure == "N") ? 'checked="checked"' : ''; ?> type="radio" name="smtpsecure" value="N" id="smtpsecure_N" /> <?php _e('None (recommended)', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Set the connection protocol prefix.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>smtpauth"><?php _e('SMTP Authentication', $this -> plugin_name); ?></label></th>
				<td>
					<?php $smtpauth = $this -> get_option('smtpauth'); ?>
					<label><input id="<?php echo $this -> pre; ?>smtpauth" onclick="jQuery('#smtpauthdiv').show();" <?php echo $authCheck1 = ($smtpauth == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="smtpauth" value="Y" />&nbsp;<?php _e('On', $this -> plugin_name); ?></label>
					<label><input onclick="jQuery('#smtpauthdiv').hide();" <?php echo $authCheck2 = ($smtpauth == "N") ? 'checked="checked"' : ''; ?> type="radio" name="smtpauth" value="N" />&nbsp;<?php _e('Off', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Turn On if your SMTP server requires a username and password.', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<?php $authdisplay = ($smtpauth == "Y") ? 'block' : 'none'; ?>
	<div id="smtpauthdiv" style="display:<?php echo $authdisplay; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="<?php echo $this -> pre; ?>smtpuser"><?php _e('SMTP Username', $this -> plugin_name); ?></label></th>
					<td><input autocomplete="off" class="widefat" type="text" id="<?php echo $this -> pre; ?>smtpuser" name="smtpuser" value="<?php echo esc_attr(stripslashes($this -> get_option('smtpuser'))); ?>" /></td>
				</tr>
				<tr>
					<th><label for="<?php echo $this -> pre; ?>smtppass"><?php _e('SMTP Password', $this -> plugin_name); ?></label></th>
					<td><input autocomplete="off" class="widefat" type="text" id="<?php echo $this -> pre; ?>smtppass" name="smtppass" value="<?php echo esc_attr(stripslashes($this -> get_option('smtppass', false))); ?>" /></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- API -->
<div id="mailtypeapi" style="display:<?php echo (!empty($mailtype) && $mailtype == "api") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="mailapi_mandrill"><?php _e('Service', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo (!empty($mailapi) && $mailapi == "mandrill") ? 'checked="checked"' : ''; ?> type="radio" class="mailapi_select" name="mailapi" value="mandrill" id="mailapi_mandrill" /><img class="mailapi_icon" src="<?php echo $this -> render_url('images/icons/mailapis/mandrill.png', 'admin'); ?>" alt="mandrill" /> <?php _e('Mandrill', $this -> plugin_name); ?></label>
					<label><input <?php echo (!empty($mailapi) && $mailapi == "amazonses") ? 'checked="checked"' : ''; ?> type="radio" class="mailapi_select" name="mailapi" value="amazonses" id="mailapi_amazonses" /><img class="mailapi_icon" src="<?php echo $this -> render_url('images/icons/mailapis/amazonses.png', 'admin'); ?>" alt="amazonses" /> <?php _e('Amazon SES', $this -> plugin_name); ?></label>
					<label><input <?php echo (!empty($mailapi) && $mailapi == "sendgrid") ? 'checked="checked"' : ''; ?> type="radio" class="mailapi_select" name="mailapi" value="sendgrid" id="mailapi_sendgrid" /><img class="mailapi_icon" src="<?php echo $this -> render_url('images/icons/mailapis/sendgrid.png', 'admin'); ?>" alt="sendgrid" /> <?php _e('SendGrid', $this -> plugin_name); ?></label>
					<label><input <?php echo (!empty($mailapi) && $mailapi == "mailgun") ? 'checked="checked"' : ''; ?> type="radio" class="mailapi_select" name="mailapi" value="mailgun" id="mailapi_mailgun" /><img class="mailapi_icon" src="<?php echo $this -> render_url('images/icons/mailapis/mailgun.png', 'admin'); ?>" alt="mailgun" /> <?php _e('MailGun', $this -> plugin_name); ?></label>
					<?php /*<label><input <?php echo (!empty($mailapi) && $mailapi == "mailjet") ? 'checked="checked"' : ''; ?> type="radio" class="mailapi_select" name="mailapi" value="mailjet" id="mailapi_mailjet" /><img class="mailapi_icon" src="<?php echo $this -> render_url('images/icons/mailapis/mailjet.png', 'admin'); ?>" alt="mailjet" /> <?php _e('MailJet', $this -> plugin_name); ?></label>*/ ?>
					<?php do_action('newsletters_admin_mailapi_radios'); ?>					
					<script type="text/javascript">
					jQuery('.mailapi_select').on('click', function() {
						change_mailapi(jQuery(this).val())
					});
						
					function change_mailapi(api) {						
						jQuery('[id^="mailapidiv_"]').hide();
						jQuery('#mailapidiv_' + api).show();
					}
					</script>
					
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php do_action('newsletters_admin_mailapi_containers'); ?>
	
	<div id="mailapidiv_mailgun" style="display:<?php echo (!empty($mailapi) && $mailapi == "mailgun") ? 'block' : 'none'; ?>">
		
		<?php
			
		$mailgun_apikey = $this -> get_option('mailapi_mailgun_apikey');	
		$mailgun_domain = $this -> get_option('mailapi_mailgun_domain');
			
		?>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="mailapi_mailgun_apikey"><?php _e('MailGun API Key', $this -> plugin_name); ?></label></th>
					<td>
						<input type="text" placeholder="key-xxxxxx" class="widefat" name="mailapi_mailgun_apikey" value="<?php echo esc_attr(stripslashes($mailgun_apikey)); ?>" id="mailapi_mailgun_apikey" />
						<span class="howto"><?php _e('Your MailGun API key which you can get in your MailGun dashboard.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="mailapi_mailgun_domain"><?php _e('MailGun Domain', $this -> plugin_name); ?></label></th>
					<td>
						<input class="widefat" type="text" placeholder="<?php echo esc_attr(stripslashes($_SERVER['HTTP_HOST'])); ?>" name="mailapi_mailgun_domain" value="<?php echo esc_attr(stripslashes($mailgun_domain)); ?>" id="mailapi_mailgun_domain" />
						<span class="howto"><?php _e('Verified MailGun sending domain, the domain in your From Address.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for=""><?php _e('MailGun Actions', $this -> plugin_name); ?></label></th>
					<td>						
						<p>
							<a href="" class="button mailgun-action" data-action="verify"><i class="fa fa-check"></i> <?php _e('Verify API Key', $this -> plugin_name); ?></a>
							<a href="" class="button mailgun-action" data-action="checkdomains"><i class="fa fa-globe"></i> <?php _e('See Domains', $this -> plugin_name); ?></a>
							<a href="" class="button mailgun-action" data-action="adddomain"><i class="fa fa-plus-circle"></i> <?php _e('Add Domain', $this -> plugin_name); ?></a>
							<?php /*<a href="" class="button mailgun-action" data-action="events"><i class="fa fa-list"></i> <?php _e('See Events', $this -> plugin_name); ?></a>
							<a href="" class="button mailgun-action" data-action="stats"><i class="fa fa-line-chart"></i> <?php _e('See Stats', $this -> plugin_name); ?></a>*/ ?>
							<span id="mailgun_loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>
						</p>
						
						<div id="mailgun_result" style="display:none;">
							<!-- MailGun call results go here -->
						</div>
						
						<script type="text/javascript">
						jQuery('.mailgun-action').on('click', function(e) {
							e.preventDefault();
							var action = jQuery(this).data('action');
							var button = jQuery(this);
							button.attr('disabled', "disabled");
							jQuery('#mailgun_loading').show();
							jQuery('#mailgun_result').hide();
							
							jQuery.ajax({
								method: "POST",
								url: newsletters_ajaxurl + 'action=newsletters_mailapi_mailgun_action',
								data: {
									ac: action,
									key: jQuery('#mailapi_mailgun_apikey').val(),
									domain: jQuery('#mailapi_mailgun_domain').val(),
								},
								success: function(response) {
									button.removeAttr('disabled');
									jQuery('#mailgun_loading').hide();
									jQuery('#mailgun_result').html(response).show();
								},
								error: function(response) {
									//failed...
								}
							});
							
							return false;
						});
						</script>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div id="mailapidiv_mailjet" style="display:<?php echo (!empty($mailapi) && $mailapi == "mailjet") ? 'block' : 'none'; ?>">
		mailjet
	</div>
	
	<!-- Mandrill API Settings -->
	<div id="mailapidiv_mandrill" style="display:<?php echo (!empty($mailapi) && $mailapi == "mandrill") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="mailapi_mandrill_key"><?php _e('Mandrill API Key', $this -> plugin_name); ?></label></th>
					<td>
						<input type="text" name="mailapi_mandrill_key" value="<?php echo esc_attr(stripslashes($this -> get_option('mailapi_mandrill_key'))); ?>" id="mailapi_mandrill_key" class="widefat" />
						<span class="howto"><?php _e('Obtain your Mandrill API key in your Mandrill dashboard under Settings > SMTP & API Info.', $this -> plugin_name); ?></span>
						
						<p>
							<a class="button button-secondary mailapi-mandrill-keytest"><i class="fa fa-check"></i> <?php _e('Validate API Key', $this -> plugin_name); ?></a>
							<span class="mailapi-mandrill-keytest-loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>
							<span class="mailapi-mandrill-keytest-result" style="display:none;"></span>
						</p>
						
						<script type="text/javascript">
						jQuery('.mailapi-mandrill-keytest').on('click', function() {
							jQuery('.mailapi-mandrill-keytest').attr('disabled', 'disabled');
							jQuery('.mailapi-mandrill-keytest-loading').show();
							jQuery('.mailapi-mandrill-keytest-result').hide();
							
							jQuery.ajax({
								method: "POST",
								url: newsletters_ajaxurl + 'action=newsletters_mailapi_mandrill_keytest',
								data: {
									key: jQuery('#mailapi_mandrill_key').val()
								}
							}).done(function(response) {
								jQuery('.mailapi-mandrill-keytest').removeAttr('disabled');
								jQuery('.mailapi-mandrill-keytest-loading').hide();
								jQuery('.mailapi-mandrill-keytest-result').html(response).show();
							});
						});
						</script>
					</td>
				</tr>
				<tr>
					<th><label for="mailapi_mandrill_subaccount"><?php _e('Mandrill Subaccount', $this -> plugin_name); ?></label></th>
					<td>
						<input type="text" name="mailapi_mandrill_subaccount" value="<?php echo esc_attr(stripslashes($this -> get_option('mailapi_mandrill_subaccount'))); ?>" id="mailapi_mandrill_subaccount" class="widefat" />
						<span class="howto"><?php _e('(optional) Specify a subaccount ID to use for sending emails.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="mailapi_mandrill_ippool"><?php _e('Mandrill IP Pool', $this -> plugin_name); ?></label></th>
					<td>
						<input type="text" name="mailapi_mandrill_ippool" value="<?php echo esc_attr(stripslashes($this -> get_option('mailapi_mandrill_ippool'))); ?>" id="mailapi_mandrill_ippool" class="widefat" />
						<span class="howto"><?php _e('(optional) Name of the dedicated IP pool to use.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<!-- Amazon SES API Settings -->
	<div id="mailapidiv_amazonses" style="display:<?php echo (!empty($mailapi) && $mailapi == "amazonses") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="mailapi_amazonses_key"><?php _e('Amazon Key', $this -> plugin_name); ?></label></th>
					<td>
						<input class="widefat" type="text" name="mailapi_amazonses_key" value="<?php echo esc_attr(stripslashes($this -> get_option('mailapi_amazonses_key'))); ?>" id="mailapi_amazonses_key" />
						<span class="howto"><?php _e('AWS access key which can be obtained under AWS Console > IAM.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="mailapi_amazonses_secret"><?php _e('Amazon Secret', $this -> plugin_name); ?></label></th>
					<td>
						<input class="widefat" type="text" name="mailapi_amazonses_secret" value="<?php echo esc_attr(stripslashes($this -> get_option('mailapi_amazonses_secret'))); ?>" id="mailapi_amazonses_secret" />
						<span class="howto"><?php _e('AWS secret key which can be obtained under AWS Console > IAM.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="mailapi_amazonses_region"><?php _e('Amazon Region', $this -> plugin_name); ?></label></th>
					<td>
						<?php
							
						$regions = array(
							'us-east-1'				=>	'US East (N. Virginia)',
							'us-west-2'				=>	'US West (Oregon)',
							'eu-west-1'				=>	'EU (Ireland)',
						);	
						
						$mailapi_amazonses_region = $this -> get_option('mailapi_amazonses_region');
							
						?>
						
						<select name="mailapi_amazonses_region" id="mailapi_amazonses_region">
							<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
							<?php foreach ($regions as $rkey => $rval) : ?>
								<option <?php echo (!empty($mailapi_amazonses_region) && $mailapi_amazonses_region == $rkey) ? 'selected="selected"' : ''; ?> value="<?php echo $rkey; ?>"><?php echo $rval; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="howto"><?php _e('AWS region of your account.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for=""><?php _e('Actions', $this -> plugin_name); ?></label></th>
					<td>						
						<p>
							<a href="" class="button amazonses-action" data-ac="verifyemail"><i class="fa fa-check"></i> <?php _e('Verify Email Address', $this -> plugin_name); ?></a>
							<a href="" class="button amazonses-action" data-ac="getverifiedemails"><i class="fa fa-at"></i> <?php _e('Get Verified Emails', $this -> plugin_name); ?></a>
							<a href="" class="button amazonses-action" data-ac="getsendquota"><i class="fa fa-bar-chart"></i> <?php _e('Get Send Quota', $this -> plugin_name); ?></a>
							
							<span id="amazonses_loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>
						</p>
						
						<div id="amazonses_result">
							<!-- Ajax output results -->
						</div>
						
						<script type="text/javascript">
						jQuery('.amazonses-action').on('click', function(e) {
							e.preventDefault();
							var action = jQuery(this).data('ac');
							var button = jQuery(this);
							button.attr('disabled', "disabled");
							jQuery('#amazonses_loading').show();
							
							jQuery.ajax({
								method: "POST",
								url: newsletters_ajaxurl + 'action=newsletters_mailapi_amazonses_action',
								data: {
									ac: action,
									key: jQuery('#mailapi_amazonses_key').val(),
									secret: jQuery('#mailapi_amazonses_secret').val(),
									region: jQuery('#mailapi_amazonses_region').val()
								},
								success: function(response) {
									button.removeAttr('disabled');
									jQuery('#amazonses_loading').hide();
									jQuery('#amazonses_result').html(response);
								},
								error: function(response) {
									//failed...
								}
							});
							
							return false;
						});
						</script>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<!-- SendGrid API Settings -->
	
	<?php
		
	$mailapi_sendgrid_apikey = $this -> get_option('mailapi_sendgrid_apikey');	
		
	?>
	
	<div id="mailapidiv_sendgrid" style="display:<?php echo (!empty($mailapi) && $mailapi == "sendgrid") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="mailapi_sendgrid_apikey"><?php _e('SendGrid API Key', $this -> plugin_name); ?></label></th>
					<td>
						<input type="text" name="mailapi_sendgrid_apikey" value="<?php echo esc_attr(stripslashes($mailapi_sendgrid_apikey)); ?>" id="mailapi_sendgrid_apikey" class="widefat" />
						<span class="howto"><?php _e('Get an API key under Settings > API Keys in your SendGrid dashboard.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<table class="form-table">
	<tbody>
		<tr class="advanced-setting">
			<th><label for="dkim_N"><?php _e('DKIM Signing', $this -> plugin_name); ?></label> <?php echo $Html -> help(__('DKIM (DomainKeys Identified Mail) is a way to digitally sign messages and verify that the messages were sent by a particular domain. It works like a wax seal on an envelope, preventing messages from being tampered with.', $this -> plugin_name)); ?></th>
			<td>
				<label><input onclick="if (!confirm('<?php _e('The DKIM signature only works if you are using an SMTP server. If you want to use your local email server (WP Mail), please enable DKIM on the server itself and do not turn this on. The wizard will now start.', $this -> plugin_name); ?>')) { return false; } dkimwizard({domain:jQuery('#dkim_domain').val(), selector:jQuery('#dkim_selector').val()}); jQuery('#dkim_div').show(); jQuery('#dkim_wizard_div').show();" <?php echo ($this -> get_option('dkim') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="dkim" value="Y" id="dkim_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#dkim_div').hide(); jQuery('#dkim_wizard_div').hide();" <?php echo ($this -> get_option('dkim') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="dkim" value="N" id="dkim_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Turn on/off the DKIM signing of your outgoing emails. Only use this with SMTP server.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="dkim_div" style="display:<?php echo ($this -> get_option('dkim') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="dkim_domain"><?php _e('DKIM Domain', $this -> plugin_name); ?></label></th>
				<td>					
					<input type="text" name="dkim_domain" class="widefat" value="<?php echo esc_attr(stripslashes($this -> get_option('dkim_domain'))); ?>" id="dkim_domain" />
					<span class="howto"><?php _e('Use the domain name that you are sending from, the one inside the From Address value.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="dkim_selector"><?php _e('DKIM Selector', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" name="dkim_selector" class="widefat" value="<?php echo esc_attr(stripslashes($this -> get_option('dkim_selector'))); ?>" id="dkim_selector" />
					<span class="howto"><?php _e('Any string with letters only. Use "newsletters" by default', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<?php $private = $this -> get_option('dkim_private'); ?>
	<div id="dkim_private_div" style="display:<?php echo (!empty($private)) ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="dkim_private"><?php _e('DKIM Private Key', $this -> plugin_name); ?></label></th>
					<td>
						<textarea id="dkim_private" name="dkim_private" rows="4" cols="100%" class="widefat"><?php echo stripslashes($private); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th>&nbsp;</th>
			<td>
				<a id="testsettings" class="button button-primary" onclick="testsettings(); return false;" href="?page=<?php echo $this -> sections -> settings; ?>"><?php _e('Test Email Settings', $this -> plugin_name); ?> <i class="fa fa-arrow-right"></i></a>
				
				<span id="dkim_wizard_div" style="display:<?php echo ($this -> get_option('dkim') == "Y") ? 'inline-block' : 'none'; ?>;">
					<a id="dkimwizard" href="" onclick="dkimwizard({domain:jQuery('#dkim_domain').val(), selector:jQuery('#dkim_selector').val()}); return false;" class="button button-primary"><?php _e('Run DKIM Wizard', $this -> plugin_name); ?> <i class="fa fa-arrow-right"></i></a>
				</span>
				
				<span id="testsettingsloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
function testsettings() {
	jQuery('#testsettingsloading').show();
	jQuery('#testsettings').attr('disabled', "disabled");
	var formvalues = jQuery('#settings-form').serialize();
	
	jQuery.post(newsletters_ajaxurl + 'action=<?php echo $this -> pre; ?>testsettings&init=1', formvalues, function(response) {
		jQuery.colorbox({html:response}).resize();
		jQuery('#testsettingsloading').hide();
		jQuery('#testsettings').removeAttr('disabled');
	});
}

function dkimwizard(formvalues) {
	jQuery('#testsettingsloading').show();
	jQuery('#dkimwizard').attr('disabled', "disabled");
	
	jQuery.post(newsletters_ajaxurl + 'action=<?php echo $this -> pre; ?>dkimwizard', formvalues, function(response) {
		jQuery.colorbox({html:response});
		jQuery('#testsettingsloading').hide();
		jQuery('#dkimwizard').removeAttr('disabled');
	});
}
</script>