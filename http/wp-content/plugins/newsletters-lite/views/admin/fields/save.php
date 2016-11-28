<?php

if ($this -> language_do()) {
	$el = $this -> language_getlanguages();
}

include $this -> plugin_base() . DS . 'includes' . DS . 'variables.php';
$validation = $Html -> field_value('Field[validation]');
$regex = $Html -> field_value('Field[regex]');

?>

<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Save a Custom Field', $this -> plugin_name); ?></h2>
	<?php $this -> render_admin('error', array('errors' => $errors)); ?>
	<?php $slug = $Html -> field_value('Field[slug]'); ?>
    
	<form action="?page=<?php echo $this -> sections -> fields; ?>&amp;method=save" method="post" id="Field.saveform">
		<?php echo $Form -> hidden('Field[id]'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Field_title"><?php _e('Title', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($this -> language_do()) : ?>
							<div id="tabs_title">
								<ul>
									<?php $tabs_title = 1; ?>
									<?php foreach ($el as $language) : ?>
										<li>
											<a href="#tabs_title_<?php echo $tabs_title; ?>"><?php echo $this -> language_flag($language); ?></a>
										</li>
										<?php $tabs_title++; ?>
									<?php endforeach; ?>
								</ul>
								
								<?php $tabs_title = 1; ?>
								<?php foreach ($el as $language) : ?>
									<div id="tabs_title_<?php echo $tabs_title; ?>">
										<input placeholder="<?php echo esc_attr(stripslashes(__('Enter field title/name here', $this -> plugin_name))); ?>" <?php echo ($tabs_title == 1) ? 'onkeyup="wpml_titletoslug(this.value);"' : ''; ?> type="text" class="widefat" name="Field[title][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[title]', $language))); ?>" id="Field_title_<?php echo $language; ?>" />
									</div>
									<?php $tabs_title++; ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<input placeholder="<?php echo esc_attr(stripslashes(__('Enter field title/name here', $this -> plugin_name))); ?>" onkeyup="wpml_titletoslug(this.value);" type="text" class="widefat" name="Field[title]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[title]'))); ?>" id="Field_title" />
						<?php endif; ?>
                        <span class="howto"><?php _e('Title/name for this custom field as it will be displayed on subscribe forms.', $this -> plugin_name); ?></span>
                        <?php echo $Html -> field_error('Field[title]'); ?>
                    </td>
				</tr>
				<?php if (empty($slug) || ($slug != 'email' && $slug != "list")) : ?>
	                <tr>
	                	<th><label for="Field_slug"><?php _e('Slug/Nicename', $this -> plugin_name); ?></label></th>
	                    <td>
	                    	<input placeholder="<?php echo esc_attr(stripslashes(__('Enter field slug for database and shortcodes use here', $this -> plugin_name))); ?>" id="Field_slug" type="text" class="widefat" name="Field[slug]" value="<?php echo $Html -> field_value('Field[slug]'); ?>" />
	                        <?php $fieldslugerror = $Html -> field_error('Field[slug]'); ?>
	                        <?php if (!empty($fieldslugerror)) : ?>
	                        	<div class="<?php echo $this -> pre; ?>"><?php echo $fieldslugerror; ?></div>
	                        <?php endif; ?>
	                        <span class="howto"><?php _e('Only use letters, lowercase, no spaces or other characters, please.', $this -> plugin_name); ?></span>
	                    </td>
	                </tr>
	            <?php else : ?>
	            	<input type="hidden" name="Field[slug]" value="<?php echo esc_attr(stripslashes($slug)); ?>" />
	            <?php endif; ?>
				<tr>
					<th><label for="Field.caption"><?php _e('Caption/Description', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($this -> language_do()) : ?>
							<div id="tabs_caption">
								<?php $tabs_caption = 1; ?>
								<ul>
									<?php foreach ($el as $language) : ?>
										<li>
											<a href="#tabs_caption_<?php echo $tabs_caption; ?>">
												<?php echo $this -> language_flag($language); ?>
											</a>
										</li>	
										<?php $tabs_caption++; ?>
									<?php endforeach; ?>
								</ul>
								
								<?php $tabs_caption = 1; ?>
								<?php foreach ($el as $language) : ?>
									<div id="tabs_caption_<?php echo $tabs_caption; ?>">
										<input placeholder="<?php echo esc_attr(stripslashes(__('Enter a caption/description to show below the field here', $this -> plugin_name))); ?>" type="text" class="widefat" name="Field[caption][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[caption]', $language))); ?>" id="Field_caption_<?php echo $language; ?>" />
									</div>
									<?php $tabs_caption++; ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<?php echo $Form -> text('Field[caption]', array('placeholder' => __('Enter a caption/description to show below the field here', $this -> plugin_name))); ?>
						<?php endif; ?>
						<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Give this field a descriptive caption/notation for subscribers to see.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="Field.watermark"><?php _e('Watermark', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($this -> language_do()) : ?>
							<div id="tabs_watermark">
								<ul>
									<?php $tabs_watermark = 1; ?>
									<?php foreach ($el as $language) : ?>
										<li>
											<a href="#tabs_watermark_<?php echo $tabs_watermark; ?>">
												<?php echo $this -> language_flag($language); ?>
											</a>
										</li>
										<?php $tabs_watermark++; ?>
									<?php endforeach; ?>
								</ul>
								
								<?php $tabs_watermark = 1; ?>
								<?php foreach ($el as $language) : ?>
									<div id="tabs_watermark_<?php echo $tabs_watermark; ?>">
										<input placeholder="<?php echo esc_attr(stripslashes(__('Enter watermark text here', $this -> plugin_name))); ?>" type="text" class="widefat" name="Field[watermark][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[watermark]', $language))); ?>" id="Field_watermark_<?php echo $language; ?>" />
									</div>
									<?php $tabs_watermark++; ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<?php echo $Form -> text('Field[watermark]', array('placeholder' => __('Enter watermark text here', $this -> plugin_name))); ?>
						<?php endif; ?>
						<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Watermark to show inside the input field which will disappear when the field is clicked on.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<?php if (empty($slug) || ($slug != 'email' && $slug != "list")) : ?>
					<tr>
						<th><label for="Field.requiredNo"><?php _e('Required', $this -> plugin_name); ?></label></th>
						<td>
							<?php $buttons = array('Y' => __('Yes', $this -> plugin_name), 'N' => __('No', $this -> plugin_name)); ?>
							<?php echo $Form -> radio('Field[required]', $buttons, array('separator' => false, 'default' => "N", 'onclick' => "if (this.value == 'Y') { jQuery('#errormessagediv').show(); } else { jQuery('#errormessagediv').hide(); }")); ?>
						</td>
					</tr>
				<?php else : ?>
					<input type="hidden" name="Field[required]" value="Y" />
				<?php endif; ?>
			</tbody>
		</table>
		
		<div id="errormessagediv" style="display:<?php echo ($Html -> field_value('Field[required]') == "Y") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field_validation"><?php _e('Validation', $this -> plugin_name); ?></label></th>
						<td>
							<select onchange="validation_change(this.value);" name="Field[validation]" id="Field_validation">
								<?php foreach ($validation_rules as $validation_key => $validation_rule) : ?>
									<option <?php echo (!empty($validation) && $validation == $validation_key) ? 'selected="selected"' : ''; ?> value="<?php echo $validation_key; ?>"><?php echo __($validation_rule['title']); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div id="validation_custom_div" style="display:<?php echo (!empty($validation) && $validation == "custom") ? 'block' : 'none'; ?>;">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="Field_regex"><?php _e('Custom Regex', $this -> plugin_name); ?></label></th>
							<td>
								<input type="text" name="Field[regex]" value="<?php echo esc_attr(($regex)); ?>" id="Field_regex" class="widefat" />
								<span class="howto"><?php echo sprintf(__('Specify a custom PHP regular expression, eg. %s', $this -> plugin_name), "<code>/^[0-9]*$/</code>"); ?></span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field.errormessage"><?php _e('Error Message', $this -> plugin_name); ?></label></th>
						<td>
							<?php if ($this -> language_do()) : ?>
								<div id="tabs_errormessage">
									<ul>
										<?php $tabs_errormessage = 1; ?>
										<?php foreach ($el as $language) : ?>
											<li>
												<a href="#tabs_errormessage_<?php echo $tabs_errormessage; ?>">
													<?php echo $this -> language_flag($language); ?>
												</a>
											</li>
											<?php $tabs_errormessage++; ?>
										<?php endforeach; ?>
									</ul>
									
									<?php $tabs_errormessage = 1; ?>
									<?php foreach ($el as $language) : ?>
										<div id="tabs_errormessage_<?php echo $tabs_errormessage; ?>">
											<input type="text" class="widefat" name="Field[errormessage][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[errormessage]', $language))); ?>" id="Field_errormessage_<?php echo $tabs_errormessage; ?>" />
										</div>
										<?php $tabs_errormessage++; ?>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<?php echo $Form -> text('Field[errormessage]'); ?>
							<?php endif; ?>
							<div class="howto"><?php _e('Error message which will be displayed when the field is left empty', $this -> plugin_name); ?></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php if (empty($slug) || ($slug != 'email' && $slug != "list")) : ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field.type"><?php _e('Field Type', $this -> plugin_name); ?></label></th>
						<td>
							<?php 
							
							$this -> init_fieldtypes();
							$types = $this -> get_option('fieldtypes'); 
							unset($types['special']);
							
							?>
							<?php echo $Form -> select('Field[type]', $types, array('onchange' => "if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox') { jQuery('#typediv').show(); } else { if (this.value == 'file') {  } else { if (this.value == 'hidden') { jQuery('#hiddendiv').show() } else { jQuery('#hiddendiv').hide(); } } jQuery('#typediv').hide(); }")); ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php elseif ($slug == "list") : ?>
			<input type="hidden" name="Field[type]" value="special" />
		<?php else : ?>	
			<input type="hidden" name="Field[type]" value="text" />
		<?php endif; ?>
		
		<div id="hiddendiv" style="display:<?php echo ($Html -> field_value('Field[type]') == "hidden") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for=""><?php _e('Hidden Value', $this -> plugin_name); ?></label>
						<?php echo $Html -> help(__('A hidden field is not seen by a subscriber. Custom: So that admin can fill in the value on the subscriber; Predefined: Define a static value to be used for the field; Other Variables: Dynamically fill the field with POST, GET, etc.', $this -> plugin_name)); ?></th>
						<td>
							<?php 
							
							$hidden_variable_types = array(
								'custom'			=>	__('Custom', $this -> plugin_name),
								'predefined'		=>	__('Predefined', $this -> plugin_name),
								'post'				=>	__('$_POST', $this -> plugin_name),
								'get'				=>	__('$_GET', $this -> plugin_name),
								'global'			=>	__('$GLOBALS', $this -> plugin_name),
								'cookie'			=>	__('$_COOKIE', $this -> plugin_name),
								'session'			=>	__('$_SESSION', $this -> plugin_name),
								'server'			=>	__('$_SERVER', $this -> plugin_name),
								
							);
							
							$hidden_type = $Html -> field_value('Field[hidden_type]');
							
							?>
							
							<?php foreach ($hidden_variable_types as $hk => $hv) : ?>
								<label><input <?php echo ((empty($hidden_type) && $hk == "custom") || (!empty($hidden_type) && $hidden_type == $hk)) ? 'checked="checked"' : ''; ?> type="radio" name="Field[hidden_type]" id="Field_hidden_type_<?php echo $hk; ?>" value="<?php echo $hk; ?>" /> <?php echo $hv; ?></label>
							<?php endforeach; ?>
							
							<p id="hidden_type_paragraph" style="display:<?php echo (empty($hidden_type) || $hidden_type == "custom" || $hidden_type == "predefined") ? 'none' : 'block'; ?>;">
								<code><span id="hidden_type_operator"><?php echo (empty($hidden_type)) ? "&#36;_POST" : $Html -> hidden_type_operator($hidden_type); ?></span>['<input type="text" name="Field[hidden_value]" id="Field_hidden_value" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[hidden_value]'))); ?>" />']</code>
							</p>
							
							<p id="hidden_type_predefined" style="display:<?php echo (!empty($hidden_type) && $hidden_type == "predefined") ? 'block' : 'none'; ?>;">
								<input type="text" class="widefat" name="Field[hidden_value_predefined]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[hidden_value]'))); ?>" id="Field_hidden_value_predefined" />
							</p>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('input[name="Field[hidden_type]"]').click(function() {
									var hidden_type = jQuery(this).val();
									jQuery('#hidden_type_paragraph').show();
									jQuery('#hidden_type_predefined').hide();
									
									if (hidden_type == "post") {
										var hidden_type_operator = "$_POST";
									} else if (hidden_type == "get") {
										var hidden_type_operator = "$_GET";
									} else if (hidden_type == "global") {
										var hidden_type_operator = "$GLOBALS";
									} else if (hidden_type == "cookie") {
										var hidden_type_operator = "$_COOKIE";
									} else if (hidden_type == "session") {
										var hidden_type_operator = "$_SESSION";
									} else if (hidden_type == "server") {
										var hidden_type_operator = "$_SERVER";
									} else if (hidden_type == "custom") {
										jQuery('#hidden_type_paragraph').hide();
									} else if (hidden_type == "predefined") {
										jQuery('#hidden_type_paragraph').hide();
										jQuery('#hidden_type_predefined').show();
									}
									
									jQuery('#hidden_type_operator').html(hidden_type_operator);
								});
							});
							</script>
							
							<?php echo $Html -> field_error('Field[hidden_value]'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="typediv" style="display:<?php echo ($Html -> field_value('Field[type]') == "checkbox" || $Html -> field_value('Field[type]') == "radio" || $Html -> field_value('Field[type]') == "select") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field.fieldoptions"><?php _e('Field Options', $this -> plugin_name); ?></label></th>
						<td>	
							<input type="hidden" name="Field[fieldoptions_order]" value="" id="fieldoptions_order" />
									
							<?php
								
							$fieldoptions = $Html -> field_value('Field[newfieldoptions]');
								
							?>
							
							<ul id="fieldoptions">
								<li id="fieldoptions-sample" class="fieldoptions-sortable" style="display:none;">
									<span class="fieldoptions-handle"><i class="fa fa-sort fa-fw fa-border"></i></span>
									<span class="fieldoptions-input">
										<?php if ($this -> language_do()) : ?>
											<div id="tabs_fieldoptions_sample">
												<ul>
													<?php foreach ($el as $language) : ?>
														<li><a href="#tabs_fieldoptions_sample_<?php echo $language; ?>"><?php echo $this -> language_flag($language); ?></a></li>
													<?php endforeach; ?>
												</ul>
												<?php foreach ($el as $language) : ?>
													<div id="tabs_fieldoptions_sample_<?php echo $language; ?>">
														<input class="widefat fieldoption-input-text" type="text" name="Field[fieldoptions][<?php echo $language; ?>][][value]" value="" id="" />
													</div>
												<?php endforeach; ?>
											</div>
											
											<script type="text/javascript">
											jQuery(document).ready(function() {
												jQuery('#tabs_fieldoptions_sample').tabs();
											});
											</script>
										<?php else : ?>
											<input type="text" class="widefat fieldoption-input-text" name="Field[fieldoptions][][value]" value="" id="" />
										<?php endif; ?>
									</span>
									<span class="fieldoptions-remove"><a href="" data-id="" onclick="newsletters_fieldoptions_remove(this); return false;" class="button"><i class="fa fa-trash"></i></a></span>
									<br class="clear" />
								</li>
								<?php if (!empty($fieldoptions)) : ?>
									<?php $f = 0; ?>
									<?php foreach ($fieldoptions as $fieldoption) : ?>
										<li id="<?php echo $fieldoption -> id; ?>" class="fieldoptions-sortable">
											<span class="fieldoptions-handle"><i class="fa fa-sort fa-fw fa-border"></i></span>
											<span class="fieldoptions-input">
												<?php if ($this -> language_do()) : ?>
													<div id="tabs_fieldoptions_<?php echo $fieldoption -> id; ?>">
														<ul>
															<?php foreach ($el as $language) : ?>
																<li><a href="#tabs_fieldoptions_<?php echo $fieldoption -> id; ?>_<?php echo $language; ?>"><?php echo $this -> language_flag($language); ?></a></li>
															<?php endforeach; ?>
														</ul>
														<?php foreach ($el as $language) : ?>
															<div id="tabs_fieldoptions_<?php echo $fieldoption -> id; ?>_<?php echo $language; ?>">
																<input type="hidden" name="Field[fieldoptions][<?php echo $language; ?>][<?php echo $f; ?>][id]" value="<?php echo esc_attr(stripslashes($fieldoption -> id)); ?>" />
																<input class="widefat" type="text" name="Field[fieldoptions][<?php echo $language; ?>][<?php echo $f; ?>][value]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $fieldoption -> value))); ?>" id="" />
															</div>
														<?php endforeach; ?>
													</div>
													
													<script>
													jQuery(document).ready(function() {
														jQuery('#tabs_fieldoptions_<?php echo $fieldoption -> id; ?>').tabs();
													});
													</script>
												<?php else : ?>
													<input type="hidden" name="Field[fieldoptions][<?php echo $f; ?>][id]" value="<?php echo esc_attr(stripslashes($fieldoption -> id)); ?>" />
													<input class="widefat" type="text" name="Field[fieldoptions][<?php echo $f; ?>][value]" value="<?php echo esc_attr(stripslashes(__($fieldoption -> value))); ?>" id="" />
												<?php endif; ?>
											</span>
											<span class="fieldoptions-remove"><a data-id="<?php echo esc_attr(stripslashes($fieldoption -> id)); ?>" href="" onclick="newsletters_fieldoptions_remove(this); return false;" class="button"><i class="fa fa-trash"></i></a></span>
											<br class="clear" />
										</li>
										<?php $f++; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
							
							<p class="submit">
								<a href="" class="button" onclick="newsletters_fieldoptions_add(); return false;"><i class="fa fa-plus"></i> <?php _e('Add Option', $this -> plugin_name); ?></a>
							</p>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#fieldoptions').sortable({
									handle: '.fieldoptions-handle',
									axis: 'y',
									update: function(event, ui) {
										var order = jQuery("#fieldoptions").sortable("toArray");
						                jQuery('#fieldoptions_order').val(order.join(","));
									}
								});
							});
							
							var newsletters_fieldoptions_add = function() {
								var li = jQuery('li#fieldoptions-sample').clone().removeAttr('style').removeAttr('id');
								jQuery(li).find('div#tabs_fieldoptions_sample').tabs();
								jQuery('ul#fieldoptions').append(li);
								
								jQuery(li).find('.fieldoption-input-text').focus().focus();
								return li;
							}
							
							var newsletters_fieldoptions_remove = function(element) {
								if (confirm('<?php _e('Are you sure you want to delete this option?', $this -> plugin_name); ?>')) {
									var id = jQuery(element).data('id');
									
									if (id != "") {
										jQuery.ajax({
											type: "POST",
											data: {id:id},
											url: newsletters_ajaxurl + "action=newsletters_delete_option",
										}).done(function(response) {
											jQuery(element).closest('li').remove();
										}).fail(function() {
											alert('<?php _e('Failed, please try again', $this -> plugin_name); ?>');
										});
									} else {
										jQuery(element).closest('li').remove();
									}
										
									return true;
								}
								
								return false;
							}
							</script>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php if (empty($slug) || ($slug != 'email' && $slug != "list")) : ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field_display_always"><?php _e('Display', $this -> plugin_name); ?></label></th>
						<td>
							<label><input onclick="jQuery('#displaydiv').hide();" <?php echo ($Html -> field_value('Field[display]') == "always") ? 'checked="checked"' : ''; ?> type="radio" name="Field[display]" value="always" id="Field_display_always" /> <?php _e('Always show', $this -> plugin_name); ?></label>
							<label><input onclick="jQuery('#displaydiv').show();" <?php echo ($Html -> field_value('Field[display]') == "specific") ? 'checked="checked"' : ''; ?> type="radio" name="Field[display]" value="specific" id="Field_display_specific" /> <?php _e('Specific list(s)', $this -> plugin_name); ?></label>
							<span class="howto"><?php _e('Should this field always show or only for specific mailing list(s)?', $this -> plugin_name); ?></span>
							<?php echo $Html -> field_error('Field[display]'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		
			<div id="displaydiv" style="display:<?php echo ($Html -> field_value('Field[display]') == "specific") ? 'block' : 'none'; ?>;">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="checkboxall"><?php _e('Mailing Lists', $this -> plugin_name); ?></label></th>
							<td>
								<?php $Field -> data = (array) $Field -> data; ?>
								<?php $lists = $Mailinglist -> select(true); ?>
								<?php if (!empty($lists)) : ?>
									<label style="font-weight:bold;"><input type="checkbox" id="mailinglistsselectall" name="mailinglistsselectall" value="1" onclick="jqCheckAll(this, 'Field.saveform', 'Field[mailinglists]');" /> <?php _e('Select All', $this -> plugin_name); ?></label><br/>
									<div id="newsletters-mailinglists-checkboxes" class="scroll-list">
										<?php foreach ($lists as $id => $title) : ?>
											<?php $checkedlistsbyfield = $FieldsList -> checkedlists_by_field(); ?>
											<?php $fieldslist = (empty($Field -> data[$Field -> model] -> id)) ? $_POST[$Field -> model]['mailinglists'] : $checkedlistsbyfield; ?>
											<label><input <?php echo (!empty($fieldslist) && is_array($fieldslist) && in_array($id, $fieldslist)) ? 'checked="checked"' : ''; ?> type="checkbox" id="checklist<?php echo $id; ?>" name="Field[mailinglists][]" value="<?php echo $id; ?>" /> <?php echo $title; ?></label><br/>
										<?php endforeach; ?>
									</div>
									<p><a href="#" class="button" onclick="jQuery.colorbox({title:'<?php echo esc_attr(stripslashes(__('Add a Mailing List', $this -> plugin_name))); ?>', href:newsletters_ajaxurl + 'action=newsletters_mailinglist_save&fielddiv=newsletters-mailinglists-checkboxes&fieldname=Field[mailinglists]'}); return false;"><i class="fa fa-plus"></i> <?php _e('Add Mailing List', $this -> plugin_name); ?></a></p>
									<?php echo $Html -> field_error('Field[mailinglists]'); ?>
								<?php else : ?>
									<p class="newsletters_error"><?php _e('No mailing lists were found', $this -> plugin_name); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php else : ?>
			<input type="hidden" name="Field[display]" value="always" />
		<?php endif; ?>
		
		<p class="submit">
			<?php echo $Form -> submit(__('Save Custom Field', $this -> plugin_name)); ?>
			<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php _e('Continue editing', $this -> plugin_name); ?></label>
			</div>
		</p>
	</form>
</div>

<script type="text/javascript">
function validation_change(validation) {
	if (validation == "custom") {
		jQuery('#validation_custom_div').show();
	} else {
		jQuery('#validation_custom_div').hide();
	}
}

jQuery(document).ready(function() {
	/* Tabs */
	<?php if ($this -> language_do()) : ?>
		jQuery('#tabs_title').tabs();
		jQuery('#tabs_caption').tabs();
		jQuery('#tabs_watermark').tabs();
		jQuery('#tabs_errormessage').tabs();
		jQuery('#tabs_fieldoptions').tabs();
	<?php endif; ?>
});
</script>