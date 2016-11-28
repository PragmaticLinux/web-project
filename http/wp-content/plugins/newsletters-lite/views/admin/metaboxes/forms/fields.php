<!-- Form Fields -->

<?php
	
$disabled_fields = false;
if (!empty($_GET['id'])) {
	if ($form_fields = $this -> FieldsForm() -> find_all(array('form_id' => $_GET['id']))) {
		foreach ($form_fields as $form_field) {
			$disabled_fields[] = $form_field -> field_id;
		}
	}
}	
	
?>

<div id="minor-publishing">
	<div>
		<div>
			<?php $Db -> model = $Field -> model; ?>
			<?php if ($fields = $Db -> find_all()) : ?>
				<ul id="form_availablefields" class="">
					<?php foreach ($fields as $field) : ?>
						<li>
							<input <?php echo (!empty($disabled_fields) && in_array($field -> id, $disabled_fields)) ? 'disabled="disabled"' : ''; ?> type="button" class="button" id="newsletters_forms_availablefield_<?php echo $field -> id; ?>" data-slug="<?php echo esc_attr(stripslashes($field -> slug)); ?>" data-id="<?php echo esc_attr(stripslashes($field -> id)); ?>" data-type="<?php echo esc_attr(stripslashes($field -> type)); ?>" value="<?php echo esc_attr(stripslashes(__($field -> title))); ?>" />
						</li>
					<?php endforeach; ?>
				</ul>
				
				<br class="clear" />
			<?php else : ?>
				<p class="newsletters_error"><?php _e('No fields are available', $this -> plugin_name); ?></p>
			<?php endif; ?>		
		</div>
	</div>
	<div id="misc-publishing-actions">
		<div class="misc-pub-section">
			<p>
				<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> fields); ?>"><?php _e('Manage Fields', $this -> plugin_name); ?></a>
			</p>
		</div>
	</div>
</div>

