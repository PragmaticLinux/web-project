<div id="templates">
	<div class="wrap newsletters">
		<h2><?php _e('View Snippet', $this -> plugin_name); ?> : <?php echo $template -> title; ?></h2>
		
		<div style="float:none;" class="subsubsub">
			<?php echo $Html -> link(__('&larr; All Snippets', $this -> plugin_name), $this -> url, array('title' => __('Manage All Snippets', $this -> plugin_name))); ?>
		</div>
		
		<div class="tablenav">
			<div class="alignleft">				
				<a href="?page=<?php echo $this -> sections -> send; ?>&method=template&id=<?php echo $template -> id; ?>" class="button button-primary"><i class="fa fa-paper-plane"></i> <?php _e('Send', $this -> plugin_name); ?></a>
				<a href="?page=<?php echo $this -> sections -> templates_save; ?>&amp;id=<?php echo $template -> id; ?>" class="button"><i class="fa fa-pencil"></i> <?php _e('Edit', $this -> plugin_name); ?></a>
				<a href="?page=<?php echo $this -> sections -> templates; ?>&amp;method=delete&amp;id=<?php echo $template -> id; ?>" onclick="if (!confirm('<?php _e('Are you sure you wish to remove this snippet?', $this -> plugin_name); ?>')) { return false; }" class="button button-highlighted"><i class="fa fa-times"></i> <?php _e('Delete', $this -> plugin_name); ?></a>
			</div>
		</div>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e('Field', $this -> plugin_name); ?></th>
					<th><?php _e('Value', $this -> plugin_name); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e('Field', $this -> plugin_name); ?></th>
					<th><?php _e('Value', $this -> plugin_name); ?></th>
				</tr>
			</tfoot>
			<tbody>
				<tr class="alternate">
					<th><?php _e('Title', $this -> plugin_name); ?></th>
					<td><?php echo __($template -> title); ?></td>
				</tr>
				<tr>
					<th><?php _e('Created', $this -> plugin_name); ?></th>
					<td><?php echo $template -> created; ?></td>
				</tr>
				<tr class="alternate">
					<th><?php _e('Modified', $this -> plugin_name); ?></th>
					<td><?php echo $template -> modified; ?></td>
				</tr>
				<tr>
					<th><?php _e('Times Sent', $this -> plugin_name); ?></th>
					<td><?php echo $template -> sent; ?></td>
				</tr>
			</tbody>
		</table>
		<iframe width="100%" frameborder="0" scrolling="no" class="autoHeight widefat" style="width:100%; margin-top:15px;" src="<?php echo admin_url('admin-ajax.php?action=newsletters_template_iframe&id=' . $template -> id); ?>"></iframe>
		<div class="tablenav">
			
		</div>
	</div>
</div>