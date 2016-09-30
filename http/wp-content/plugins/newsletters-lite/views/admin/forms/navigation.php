<div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; Back to Forms', $this -> plugin_name), admin_url('admin.php?page=' . $this -> sections -> forms)); ?></div> 

<div class="wp-filter">
	<ul class="filter-links">
		<li><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=' . $form -> id); ?>" data-sort="featured" <?php echo ($_GET['method'] == "save") ? 'class="current"' : ''; ?>><i class="fa fa-edit"></i> Form Builder</a></li>
		<li><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=settings&id=' . $form -> id); ?>" data-sort="popular" <?php echo ($_GET['method'] == "settings") ? 'class="current"' : ''; ?>><i class="fa fa-cogs"></i> Settings</a></li>
		<li><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=preview&id=' . $form -> id); ?>" <?php echo ($_GET['method'] == "preview") ? 'class="current"' : ''; ?>><i class="fa fa-eye"></i> <?php _e('Preview', $this -> plugin_name); ?></a></li>
		<li><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=subscriptions&id=' . $form -> id); ?>" <?php echo ($_GET['method'] == "subscriptions") ? 'class="current"' : ''; ?>><i class="fa fa-users"></i> <?php _e('Subscriptions', $this -> plugin_name); ?></a></li>
	</ul>
	
	<div class="search-form" id="tableofcontentsdiv">
		<div class="inside">
			<?php $this -> render('forms' . DS . 'switch', false, true, 'admin'); ?>
		</div>
	</div>
</div>