<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Templates', $this -> plugin_name); ?>
	<a class="add-new-h2" href="?page=<?php echo $this -> sections -> themes; ?>&amp;method=save"><?php _e('Add New', $this -> plugin_name); ?></a>
	<a class="add-new-h2-green" href="http://tribulant.com/emailthemes/" target="_blank"><?php _e('Get More Templates', $this -> plugin_name); ?></a>
	</h2>
	
	<!-- Default Template Setting -->
	<?php $defaulttemplate = $this -> get_option('defaulttemplate'); ?>	
	<form method="post" action="?page=<?php echo $this -> sections -> themes; ?>&amp;method=defaulttemplate">
    	<label><input <?php echo (!empty($defaulttemplate)) ? 'checked="checked"' : ''; ?> type="checkbox" name="defaulttemplate" value="1" id="defaulttemplate" /> <?php _e('Use a styled, default template for newsletters and system emails', $this -> plugin_none); ?></label>
        <input class="button" type="submit" value="<?php _e('Save', $this -> plugin_name); ?>" name="submit" />
        <?php echo $Html -> help(__('Turn this on to use a styled, default template for newsletters and system emails when none is selected.', $this -> plugin_name)); ?>
    </form>
    
    <?php if (!empty($themes)) : ?>
		<form id="posts-filter" action="<?php echo $this -> url; ?>" method="post">
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($themes); ?> <?php _e('templates', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), '?page=' . $this -> sections -> themes . "&amp;showall=1"); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> sections -> themes); ?></li>
				<?php endif; ?>
			</ul>
			<p class="search-box">
				<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
				<input type="submit" class="button" value="<?php _e('Search Templates', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<?php $this -> render_admin('themes' . DS . 'loop', array('themes' => $themes, 'paginate' => $paginate)); ?>
</div>