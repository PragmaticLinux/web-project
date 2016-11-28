<form onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action on the selected forms?', $this -> plugin_name); ?>')) { return false; }" action="?page=<?php echo $this -> sections -> forms; ?>&amp;method=mass" method="post">
	<div class="tablenav">
		<div class="alignleft">
			<select name="action" style="width:auto;">
				<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
				<option value="delete"><?php _e('Delete Selected', $this -> plugin_name); ?></option>
			</select>            
            <input type="submit" class="button" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" />
		</div>
		<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
	</div>
	
	<?php
		
	$orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
	$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
	$otherorder = ($order == "desc") ? 'asc' : 'desc';
	
	$colspan = 4;
	
	?>
	
	<table class="widefat">
		<thead>
			<tr>
				<?php ob_start(); ?>
				<td class="check-column"><input type="checkbox" name="checkboxall" id="checkboxall" value="1" /></td>
				<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
						<span><?php _e('ID', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-title <?php echo ($orderby == "title") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=title&order=' . (($orderby == "title") ? $otherorder : "asc")); ?>">
						<span><?php _e('Title', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
						<span><?php _e('Shortcode', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th>
					<?php _e('Subscriptions', $this -> plugin_name); ?>
				</th>
				<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
						<span><?php _e('Date', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<?php 
					
				$cols_output = ob_get_clean(); 
				echo $cols_output;
				
				?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<?php echo $cols_output; ?>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($forms)) : ?>
				<?php foreach ($forms as $form) : ?>
					<?php $class = ($class == 'alternate') ? '' : 'alternate'; ?>
					<tr class="<?php echo $class; ?>" id="form_row_<?php echo $form -> id; ?>">
						<th class="check-column"><input type="checkbox" name="forms[]" value="<?php echo $form -> id; ?>" id="form_check_<?php echo $form -> id; ?>" /></th>
						<td><label for="form_check_<?php echo $form -> id; ?>"><?php echo __($form -> id); ?></label></td>
						<td>
							<a class="row-title" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=' . $form -> id); ?>"><?php echo __($form -> title); ?></a>
							<div class="row-actions">
								<span class="edit"><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=' . $form -> id); ?>"><?php _e('Edit', $this -> plugin_name); ?></a> |</span>
								<span class="edit"><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=settings&id=' . $form -> id); ?>"><?php _e('Settings', $this -> plugin_name); ?></a> |</span>
								<span class="edit"><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=preview&id=' . $form -> id); ?>"><?php _e('Preview', $this -> plugin_name); ?></a> |</span>
								<span class="edit"><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=subscriptions&id=' . $form -> id); ?>"><?php _e('Subscriptions', $this -> plugin_name); ?></a> |</span>
								<span class="delete"><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=delete&id=' . $form -> id); ?>" class="submitdelete" onclick="if (!confirm('<?php _e('Are you sure you want to delete this form?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Delete', $this -> plugin_name); ?></a></span>
							</div>
						</td>
						<td>
							<code>[newsletters_subscribe form=<?php echo $form -> id; ?>]</code>
						</td>
						<td>
							<?php
								
							$Db -> model = $SubscribersList -> model;
							echo '<a href="' . admin_url('admin.php?page=' . $this -> sections -> forms . '&method=subscriptions&id=' . $form -> id) . '">' . $Db -> count(array('form_id' => $form -> id)) . '</a>';	
								
							?>
						</td>
						<td>
							<label for="form_check_<?php echo $form -> id; ?>"><abbr title="<?php echo esc_attr(stripslashes($form -> created)); ?>"><?php echo $Html -> gen_date(null, strtotime($form -> created)); ?></abbr></label>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="no-items">
					<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php echo sprintf(__('No forms available, %s', $this -> plugin_name), '<a onclick="jQuery.colorbox({title:\'' . __('Create a New Form', $this -> plugin_name) . '\', href:\'' . admin_url('admin-ajax.php?action=newsletters_forms_createform') . '\'}); return false;" href="' . admin_url('admin.php?page=' . $this -> sections -> forms . '&amp;method=save') . '">' . __('add one', $this -> plugin_name) . '</a>'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<div class="tablenav">
		<div class="alignleft">
			<?php if (empty($_GET['showall'])) : ?>
				<select class="widefat" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
					<option value=""><?php _e('- Per Page -', $this -> plugin_name); ?></option>
					<?php $p = 5; ?>
					<?php while ($p < 100) : ?>
						<option <?php echo (!empty($_COOKIE[$this -> pre . 'formsperpage']) && $_COOKIE[$this -> pre . 'formsperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo $p; ?>"><?php echo $p; ?> <?php _e('per page', $this -> plugin_name); ?></option>
						<?php $p += 5; ?>
					<?php endwhile; ?>
					<?php if (isset($_COOKIE[$this -> pre . 'formsperpage'])) : ?>
						<option selected="selected" value="<?php echo $_COOKIE[$this -> pre . 'formsperpage']; ?>"><?php echo $_COOKIE[$this -> pre . 'formsperpage']; ?></option>
					<?php endif; ?>
				</select>
			<?php endif; ?>
			
			<script type="text/javascript">
			function change_perpage(perpage) {				
				if (perpage != "") {
					document.cookie = "<?php echo $this -> pre; ?>formsperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
				}
			}
			
			function change_sorting(field, dir) {
				document.cookie = "<?php echo $this -> pre; ?>formssorting=" + field + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
				document.cookie = "<?php echo $this -> pre; ?>forms" + field + "dir=" + dir + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
			}
			</script>
		</div>
		<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
	</div>
</form>