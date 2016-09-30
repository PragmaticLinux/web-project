<!-- Admin Mode Switcher -->

<?php
	
$admin_mode = get_user_option('newsletters_admin_mode', get_current_user_id());
if (empty($admin_mode)) $admin_mode = 'standard';
	
?>

<span class="newsletters-admin-mode-switcher">
	<label><?php _e('Admin Mode:', $this -> plugin_name); ?></label>
	<a href="" class="newsletters-admin-mode newsletters-admin-mode-standard btn btn-sm btn-info <?php echo ($admin_mode == "standard") ? 'active' : ''; ?>"><?php _e('Standard', $this -> plugin_name); ?></a>
	<a href="" class="newsletters-admin-mode newsletters-admin-mode-advanced btn btn-sm btn-warning <?php echo ($admin_mode == "advanced") ? 'active' : ''; ?>""><?php _e('Advanced', $this -> plugin_name); ?></a>
</span>