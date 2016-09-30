<!-- Subscribe Form -->

<div class="newsletters newsletters-widget-wrapper" id="newsletters-<?php echo $form -> id; ?>-wrapper">		
	<?php if (!empty($form)) : ?>
		<?php if (!empty($form -> form_fields)) : ?>
			<form class="newsletters-subscribe-form <?php echo (!empty($form -> ajax)) ? 'newsletters-subscribe-form-ajax' : ''; ?>" action="<?php echo $Html -> retainquery($this -> pre . 'method=optin'); ?>" method="post" id="newsletters-<?php echo $form -> id; ?>-form" enctype="multipart/form-data">
				<input type="hidden" name="form_id" value="<?php echo esc_attr(stripslashes($form -> id)); ?>" />
				
				<?php foreach ($form -> form_fields as $field) : ?>
					<?php $this -> render_field($field -> field_id, false, $form -> id, false, false, false, false, $errors, $form -> id, $field); ?>
				<?php endforeach; ?>
				
				<?php if (!empty($form -> captcha)) : ?>
					<?php if ($captcha_type = $this -> use_captcha()) : ?>		
						<div class="newsletters-fieldholder newsletters-captcha">
							<?php if ($captcha_type == "rsc") : ?>
						    	<?php 
						    	
						    	$captcha = new ReallySimpleCaptcha();
						    	$captcha -> bg = $Html -> hex2rgb($this -> get_option('captcha_bg')); 
						    	$captcha -> fg = $Html -> hex2rgb($this -> get_option('captcha_fg'));
						    	$captcha_size = $this -> get_option('captcha_size');
						    	$captcha -> img_size = array($captcha_size['w'], $captcha_size['h']);
						    	$captcha -> char_length = $this -> get_option('captcha_chars');
						    	$captcha -> font_size = $this -> get_option('captcha_font');
						    	$captcha_word = $captcha -> generate_random_word();
						    	$captcha_prefix = mt_rand();
						    	$captcha_filename = $captcha -> generate_image($captcha_prefix, $captcha_word);
						        $captcha_file = plugins_url() . '/really-simple-captcha/tmp/' . $captcha_filename; 
						    	
						    	?>
						    	<div class="form-group<?php echo (!empty($errors['captcha_code'])) ? ' has-error' : ''; ?>">
						            <label class="control-label" for="<?php echo $this -> pre; ?>captcha_code"><?php _e('Please fill in the code below:', $this -> plugin_name); ?></label>
						            <input <?php echo $Html -> tabindex('newsletters-' . $form -> id); ?> class="form-control <?php echo $this -> pre; ?>captchacode <?php echo $this -> pre; ?>text <?php echo (!empty($errors['captcha_code'])) ? 'newsletters_fielderror' : ''; ?>" type="text" name="captcha_code" id="<?php echo $this -> pre; ?>captcha_code" value="" />
						            <img src="<?php echo $captcha_file; ?>" alt="captcha" />
						            <input type="hidden" name="captcha_prefix" value="<?php echo $captcha_prefix; ?>" />
						    	</div>
							<?php elseif ($captcha_type == "recaptcha") : ?>
								<?php 
								
								$recaptcha_publickey = $this -> get_option('recaptcha_publickey');
								$recaptcha_language = $this -> get_option('recaptcha_language'); 
								$recaptcha_theme = $this -> get_option('recaptcha_theme');
								$recaptcha_customcss = $this -> get_option('recaptcha_customcss');
								
								?>
								
								<div id="newsletters-<?php echo $form -> id; ?>-recaptcha" class="newsletters_recaptcha_widget" style="">
									<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_publickey; ?>" data-theme="<?php echo $recaptcha_theme; ?>" data-tabindex="<?php echo $Html -> tabindex('newsletters-' . $form -> id, true); ?>"></div>
						            <script type="text/javascript" data-cfasync="false" async="async" defer="defer" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $recaptcha_language; ?>"></script>
						            <input type="hidden" name="captcha_recaptcha" value="1" />
								</div>
							<?php endif; ?>
							
							<?php if (!empty($errors['captcha_code'])) : ?>
								<div id="newsletters-<?php echo $number; ?>-captcha-error" class="newsletters-field-error alert alert-danger">
									<p><i class="fa fa-exclamation-triangle"></i> <?php echo stripslashes($errors['captcha_code']); ?></p>
								</div>
							<?php endif; ?>
						</div>
				    <?php endif; ?>
				<?php endif; ?>
				
				<div class="newslettername-wrapper" style="display:none;">
			    	<input type="text" name="newslettername" value="" id="newsletters-<?php echo $form -> id; ?>newslettername" class="newslettername" />
			    </div>
				
				<div class="clearfix"></div>
				
				<div id="newsletters-form-<?php echo $form -> id; ?>-submit" class="newsletters-fieldholder newsletters_submit">
					<div class="form-group">
						<span class="newsletters_buttonwrap">
							<input type="submit" name="subscribe" id="newsletters-<?php echo $form -> id; ?>-button" value="<?php echo esc_attr(stripslashes(__($form -> buttontext))); ?>" class="btn btn-primary button" />
						</span>
						<span id="newsletters-<?php echo $form -> id; ?>-loading" class="newsletters_loading_wrapper" style="display:none;">
							<i class="fa fa-refresh fa-spin fa-fw"></i>
						</span>
					</div>
				</div>
				
				<div class="newsletters-progress col-md-12" style="display:none;">
					<div class="clearfix"></div>
					<div class="progress">
						<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
							<span class="sr-only">0% Complete</span>
						</div>
					</div>
				</div>
				
				<div class="clearfix"></div>
			</form>
		<?php endif; ?>
	<?php endif; ?>
</div>