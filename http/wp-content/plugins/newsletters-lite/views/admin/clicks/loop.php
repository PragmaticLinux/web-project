
	<form action="?page=<?php echo $this -> sections -> clicks; ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php _e('Are you sure you want to apply this action to the selected clicks?', $this -> plugin_name); ?>')) { return false; }">
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" name="apply" class="button" value="<?php _e('Apply', $this -> plugin_name); ?>" />
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'created' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		$colspan = 7;
		
		?>
		<table class="widefat">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" name="checkall" value="1" id="checkall" /></td>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-link_id <?php echo ($orderby == "link_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=link_id&order=' . (($orderby == "link_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Link/Referer', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('History', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="check-column"><input type="checkbox" name="checkall" value="1" id="checkall" /></td>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-link_id <?php echo ($orderby == "link_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=link_id&order=' . (($orderby == "link_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Link/Referer', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('History', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($clicks)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No clicks were found', $this -> plugin_name); ?></td>
					</tr>
				<?php else : ?>
					<?php $class = false; ?>
					<?php foreach ($clicks as $click) : ?>
						<tr class="<?php $class = (empty($class)) ? 'alternate' : false; ?>">
							<th class="check-column"><input type="checkbox" name="clicks[]" value="<?php echo $click -> id; ?>" id="clicks_<?php echo $click -> id; ?>" /></th>
							<td>
								<?php if (!empty($click -> subscriber_id)) : ?>
									<?php
									
									$Db -> model = $Subscriber -> model;
									$subscriber = $Db -> find(array('id' => $click -> subscriber_id));
									
									?>
									
									<a href="?page=<?php echo $this -> sections -> clicks; ?>&amp;subscriber_id=<?php echo $subscriber -> id; ?>" class="row-title"><?php echo $subscriber -> email; ?></a>
								<?php elseif (!empty($click -> user_id)) : ?>
									<?php $user = $this -> userdata($click -> user_id); ?>
									<?php _e('User:', $this -> plugin_name); ?> <a href="" class="row-title"><?php echo $user -> display_name; ?></a>
									<br/><small><?php echo $user -> user_email; ?></small>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
								<div class="row-actions">
									<span class="delete"><?php echo $Html -> link(__('Delete', $this -> plugin_name), '?page=' . $this -> sections -> clicks . '&amp;method=delete&amp;id=' . $click -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this click?', $this -> plugin_name) . "')) { return false; }", 'class' => "delete")); ?></span>
								</div>
							</td>
							<td>
								<?php if (!empty($click -> link_id)) : ?>
									<?php $link = $this -> Link() -> find(array('id' => $click -> link_id)); ?>
									<?php echo $Html -> link($link -> link, $link -> link, array('target' => "_blank")); ?>
								<?php elseif (!empty($click -> referer)) : ?>
									<?php echo $this -> Click() -> referer_name($click -> referer); ?>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($click -> history_id)) : ?>
									<?php
									
									$history = $this -> History() -> find(array('id' => $click -> history_id));
									
									?>
									<a href="?page=<?php echo $this -> sections -> history; ?>&amp;method=view&amp;id=<?php echo $history -> id; ?>"><?php echo $history -> subject; ?></a>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
							<td>
								<abbr title="<?php echo $click -> created; ?>"><?php echo $Html -> gen_date("Y-m-d", strtotime($click -> created)); ?></abbr>
							</td>
						</tr>
					<?php endforeach; ?>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'clicksperpage']) && $_COOKIE[$this -> pre . 'clicksperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo $p; ?>"><?php echo $p; ?> <?php _e('per page', $this -> plugin_name); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'clicksperpage'])) : ?>
							<option selected="selected" value="<?php echo $_COOKIE[$this -> pre . 'clicksperpage']; ?>"><?php echo $_COOKIE[$this -> pre . 'clicksperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					if (perpage != "") {
						document.cookie = "<?php echo $this -> pre; ?>clicksperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
					}
				}
				</script>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
	</form>