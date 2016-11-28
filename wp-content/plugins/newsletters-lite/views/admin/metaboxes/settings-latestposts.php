<!-- Latest Posts Subscriptions Settings -->

<?php
	
$latestpostssubscriptions = $this -> Latestpostssubscription() -> find_all();	
	
?>

<table class="widefat" id="latestposts_table">
	<thead>
		<tr>
			<th><?php _e('Subject', $this -> plugin_name); ?></th>
			<th><?php _e('Interval', $this -> plugin_name); ?></th>
			<th><?php _e('Status', $this -> plugin_name); ?></th>
			<th><?php _e('Lists', $this -> plugin_name); ?></th>
			<th><?php _e('Posts', $this -> plugin_name); ?></th>
			<th><?php _e('Actions', $this -> plugin_name); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (empty($latestpostssubscriptions)) : ?>
			<tr class="no-items">
				<td class="colspanchange" colspan="6"><?php _e('No latest posts subscriptions', $this -> plugin_name); ?></td>
			</tr>
		<?php else : ?>
			<?php foreach ($latestpostssubscriptions as $latestpostssubscription) : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>" id="latestposts_row_<?php echo $latestpostssubscription -> id; ?>">
					<td>
						<span class="row-title"><?php echo $latestpostssubscription -> subject; ?></span>
						<div class="row-actions">
							<span class="edit"><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> settings_tasks . '&amp;method=runschedule&amp;hook=newsletters_latestposts&id=' . $latestpostssubscription -> id); ?>"><?php _e('Run Now', $this -> plugin_name); ?></a></span>
							<?php if (!empty($latestpostssubscription -> history_id)) : ?>
								<span class="edit">| <a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $latestpostssubscription -> history_id); ?>"><?php _e('View Newsletter', $this -> plugin_name); ?></a></span>
							<?php endif; ?>
						</div>
					</td>
					<td>
						<?php if (!empty($latestpostssubscription -> interval)) : ?>
							<?php echo $Html -> next_scheduled('newsletters_latestposts', array((int) $latestpostssubscription -> id)); ?>
						<?php else : ?>
							<span class="newsletters_error"><?php _e('None', $this -> plugin_name); ?></span>
						<?php endif; ?>
					</td>
					<td>
						<?php if (empty($latestpostssubscription -> status) || $latestpostssubscription -> status == "active") : ?>
							<span class="newsletters_success"><i class="fa fa-check"></i></span>
						<?php else : ?>
							<span class="newsletters_error"><i class="fa fa-times"></i></span>
						<?php endif; ?>
					</td>
					<td>
						<?php if (!empty($latestpostssubscription -> lists)) : ?>
							<?php $lists = maybe_unserialize($latestpostssubscription -> lists); ?>
							<?php $l = 1; ?>
							<?php foreach ($lists as $list_id) : ?>
								<?php $list = $Mailinglist -> get($list_id); ?>
								<?php echo '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lists . '&method=view&id=' . $list_id) . '">' . __($list -> title) . '</a>'; ?>
								<?php echo ($l < count($lists)) ? ', ' : ''; ?>
								<?php $l++; ?>
							<?php endforeach; ?>
						<?php else : ?>
							<span class="newsletters_error"><?php _e('None', $this -> plugin_name); ?></span>
						<?php endif; ?>
					</td>
					<td>
						<?php 
						
						$posts_used = $this -> get_latestposts_used($latestpostssubscription);	
						
						?>
						
						<a href="" onclick="jQuery.colorbox({href:newsletters_ajaxurl + 'action=newsletters_lpsposts&id=<?php echo $latestpostssubscription -> id; ?>'}); return false;"><?php echo sprintf(__('%s posts used/sent', $this -> plugin_name), $posts_used); ?></a>
						<a onclick="if (!confirm('<?php _e('Are you sure you want to clear the posts history for this latest posts subscription?', $this -> plugin_name); ?>')) { return false; }" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> settings . '&amp;method=clearlpshistory&id=' . $latestpostssubscription -> id); ?>" class=""><i class="fa fa-trash"></i></a>
					</td>
					<td>
						<?php if (empty($latestpostssubscription -> status) || $latestpostssubscription -> status == "active") : ?>
							<a href="" id="latestpostssubscription_changestatus_<?php echo $latestpostssubscription -> id; ?>" onclick="latestposts_changestatus('<?php echo $latestpostssubscription -> id; ?>','inactive'); return false;" class="button"><i class="fa fa-pause"></i></a>
						<?php else : ?>
							<a href="" id="latestpostssubscription_changestatus_<?php echo $latestpostssubscription -> id; ?>" onclick="latestposts_changestatus('<?php echo $latestpostssubscription -> id; ?>','active'); return false;" class="button"><i class="fa fa-play"></i></a>
						<?php endif; ?>
						<a href="" title="<?php echo esc_attr(stripslashes($latestpostssubscription -> subject)); ?>" onclick="jQuery(this).colorbox({iframe:true, width:'80%', height:'80%', href:newsletters_ajaxurl + 'action=wpmllatestposts_preview&id=<?php echo $latestpostssubscription -> id; ?>'}); return false;" class="button"><i class="fa fa-eye fa-fw"></i></a>
						<a href="" onclick="jQuery.colorbox({href:newsletters_ajaxurl + 'action=newsletters_latestposts_save&id=<?php echo $latestpostssubscription -> id; ?>'}); return false;" class="button editrow"><i class="fa fa-pencil fa-fw"></i></a>
						<a href="" id="latestposts_delete_<?php echo $latestpostssubscription -> id; ?>" onclick="if (confirm('<?php _e('Are you sure you want to delete this latest posts subscription?', $this -> plugin_name); ?>')) { latestposts_del_row('<?php echo $latestpostssubscription -> id; ?>'); } return false;" class="button delrow"><i class="fa fa-trash"></i></a>	
						<span id="latestposts_loading_<?php echo $latestpostssubscription -> id; ?>" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<p>
	<a href="" class="button latestposts-addrow"><i class="fa fa-plus"></i> <?php _e('Add Instance', $this -> plugin_name); ?></a>
</p>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.latestposts-addrow').live('click', function() {
		latestposts_add_row();
		return false;
	});
});

function latestposts_changestatus(id, status) {
	jQuery('#latestpostssubscription_changestatus_' + id).attr('disabled', "disabled").html('<i class="fa fa-refresh fa-spin"></i>');
	
	jQuery.ajax({
		type: "POST",
		url: newsletters_ajaxurl + 'action=newsletters_latestposts_changestatus',
		data: {
			id:id, 
			status:status
		}
	}).done(function(response) {
		wpml_scroll('#latestposts_wrapper');
		jQuery('#latestposts_wrapper').html(response).fadeIn();
	});
}

function latestposts_add_row() {	
	jQuery.colorbox({href:newsletters_ajaxurl + 'action=newsletters_latestposts_save'});
}

function latestposts_del_row(id) {
	//jQuery('#latestposts_loading_' + id).show();
	jQuery('#latestposts_delete_' + id).attr('disabled', "disabled").html('<i class="fa fa-refresh fa-spin"></i>');
	jQuery.post(newsletters_ajaxurl + 'action=newsletters_latestposts_delete&id=' + id, false, function(response) {
		jQuery('#latestposts_row_' + id).remove();
	});
}
</script>