<div class="newsletters newsletters-management-login">
	<p><?php _e('Please fill in your subscriber email address below to manage your subscriptions.', $this -> plugin_name); ?></p>
	
	<?php if (!empty($errors)) : ?>
		<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	<?php endif; ?>
	
	<?php if (!empty($_REQUEST['updated'])) : ?>
		<?php if (!empty($_REQUEST['success'])) : ?>
			<div class="alert alert-success">
				<p><i class="fa fa-check"></i> <?php echo stripslashes($_REQUEST['success']); ?></p>
			</div>
		<?php endif; ?>
		<?php if (!empty($_REQUEST['error'])) : ?>
			<div class="alert alert-danger">
				<p><i class="fa fa-exclamation-triangle"></i> <?php echo stripslashes($_REQUEST['error']); ?></p>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<?php
	
	$email = (!empty($_POST['email'])) ? $_POST['email'] : false;
	$email = (!empty($_GET['email'])) ? $_GET['email'] : $email;
	
	?>
	
	<div class="newsletters <?php echo $this -> pre; ?>" id="subscriberauthloginformdiv">
	    <form id="subscriberauthloginform" action="<?php echo $Html -> retainquery('newsletters_method=management_login&method=login', get_permalink($this -> get_managementpost())); ?>" method="post">
	        <label><?php _e('Email Address:', $this -> plugin_name); ?></label>
	        <input type="text" placeholder="<?php echo esc_attr(stripslashes(__('Enter email address', $this -> plugin_name))); ?>" name="email" value="<?php echo esc_attr(stripslashes($email)); ?>" id="email" />
	        <input type="submit" name="authenticate" class="newsletters_button ui-button-primary" value="<?php _e('Log In', $this -> plugin_name); ?>" id="authenticate" />
	    </form>
	</div>
	
	<script type="text/javascript">jQuery(document).ready(function() { jQuery('input#authenticate').button(); });</script>
</div>