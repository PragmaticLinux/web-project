<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Forms', $this -> plugin_name); ?> <a class="add-new-h2" onclick="jQuery.colorbox({title:'<?php _e('Create a New Form', $this -> plugin_name); ?>', href:'<?php echo admin_url('admin-ajax.php?action=newsletters_forms_createform'); ?>'}); return false;" href="?page=<?php echo $this -> sections -> forms; ?>&amp;method=save"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php if (!empty($forms)) : ?>
		<form id="posts-filter" action="?page=<?php echo $this -> sections -> forms; ?>" method="post">
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($forms); ?> <?php _e('forms', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), '?page=' . $this -> sections -> forms); ?></li>
				<?php endif; ?>
			</ul>
			<p class="search-box">
				<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
				<input type="submit" class="button" value="<?php _e('Search Forms', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<?php $this -> render_admin('forms' . DS . 'loop', array('forms' => $forms, 'paginate' => $paginate)); ?>
</div>