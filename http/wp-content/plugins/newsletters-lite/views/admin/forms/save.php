<?php

global $ID, $post, $post_ID, $wp_meta_boxes;

$imagespost = $this -> get_option('imagespost');
$p_id = (empty($_POST['p_id'])) ? $imagespost : $_POST['p_id'];
$ID = $p_id;
$post_ID = $p_id;

wp_enqueue_media(array('post' => $p_id));
wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);

$manualpostboxes = array();
$email_field_id = $Field -> email_field_id();
$list_field_id = $Field -> list_field_id();
if (!empty($form -> form_fields)) {
	foreach ($form -> form_fields as $form_field) {				
	    add_meta_box('newsletters_forms_field_' . $form_field -> field_id, ((empty($form_field -> label)) ? __($form_field -> field -> title) : __($form_field -> label)) . ' <span class="newsletters_handle_more">' . $Html -> field_type($form_field -> field -> type, $form_field -> field -> slug) . '</span>' . ((!empty($form_field -> required)) ? ' <span class="newsletters_error"><i class="fa fa-asterisk fa-sm"></i></span>' : ''), array($Metabox, 'forms_field'), "newsletters_page_" . $this -> sections -> forms, 'normal', 'core', array('form_field' => $form_field));
	    
		$manualpostboxes[] = array(
			'field_id'			=>	$form_field -> field_id,
			'mandatory'			=>	(($form_field -> field_id != $email_field_id && $form_field -> field_id != $list_field_id) ? false : true),
		);
	}
}

?>

<div class="wrap <?php echo $this -> pre; ?> <?php echo $this -> sections -> forms; ?> newsletters">
	<?php if (!empty($_GET['id'])) : ?>
		<h2><?php _e('Edit Form', $this -> plugin_name); ?> <a onclick="jQuery.colorbox({title:'<?php _e('Create a New Form', $this -> plugin_name); ?>', href:'<?php echo admin_url('admin-ajax.php?action=newsletters_forms_createform'); ?>'}); return false;" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save'); ?>" class="add-new-h2"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php else : ?>
		<h2><?php _e('Create Form', $this -> plugin_name); ?></h2>
	<?php endif; ?>
	
	<?php $this -> render('forms' . DS . 'navigation', array('form' => $form), true, 'admin'); ?>
	
	<form action="<?php echo admin_url('admin.php?page=' . $this -> sections -> forms . '&amp;method=save'); ?>" method="post" id="post" name="post" enctype="multipart/form-data">
		<?php wp_nonce_field($this -> sections -> forms); ?>
		
		<input type="hidden" name="fields" id="fields" value="" />
		<input type="hidden" name="id" id="id" value="<?php echo esc_attr(stripslashes($form -> id)); ?>" />
		
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
							<label class="screen-reader-text" for="title"></label>
							<input onclick="jQuery('iframe#content_ifr').attr('tabindex', '2');" tabindex="1" id="title" autocomplete="off" type="text" placeholder="<?php echo esc_attr(stripslashes(__('Enter form name here', $this -> plugin_name))); ?>" name="title" value="<?php echo esc_attr(stripslashes($form -> title)); ?>" />
						</div>
						<?php if (!empty($form -> id)) : ?>
							<div class="inside">
								<div id="edit-slug-box">
									<strong><?php _e('Shortcode:', $this -> plugin_name); ?></strong>
									<span id="sample-permalink"><code>[newsletters_subscribe form=<?php echo $form -> id; ?>]</code> <?php echo $Html -> help(__('Copy/paste this shortcode into any post/page to display this subscribe form.', $this -> plugin_name)); ?></span>
								</div>
							</div>
						<?php endif; ?>
						<?php if (!empty($errors['title'])) : ?>
							<div class="ui-state-error ui-corner-all">
								<p><i class="fa fa-exclamation-triangle"></i> <?php echo $errors['title']; ?></p>
							</div>
						<?php endif; ?>
						<div class="inside">
						<div id="edit-slug-box" class="hide-if-no-js" style="display:<?php echo (!empty($_POST['ishistory'])) ? 'block' : 'none'; ?>;">
							<?php $newsletter_url = $Html -> retainquery($this -> pre . 'method=newsletter&id=' . $_POST['ishistory'], home_url()); ?>
							<strong><?php _e('Permalink:', $this -> plugin_name); ?></strong>
							<span id="sample-permalink" tabindex="-1"><?php echo $newsletter_url; ?></span>
							<span id="view-post-btn"><a href="<?php echo $newsletter_url; ?>" target="_blank" class="button button-small"><?php _e('View Newsletter', $this -> plugin_name); ?></a></span>
							<input id="shortlink" type="hidden" value="<?php echo $newsletter_url; ?>">
							<a href="#" class="button button-small" onclick="prompt('URL:', jQuery('#shortlink').val()); return false;"><?php _e('Get Link', $this -> plugin_name); ?></a></div>
						</div>
					</div>
					<div id="<?php echo (user_can_richedit()) ? 'postdivrich' : 'postdiv'; ?>" class="postarea edit-form-section" style="position:relative;">
						<!-- Editor will go here -->
						<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<?php do_action('submitpage_box'); ?>
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> forms, 'side', $post); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> forms, 'normal', $post); ?>
                    <?php do_meta_boxes("newsletters_page_" . $this -> sections -> forms, 'advanced', $post); ?>
				</div>
			</div>
		</div>
	</form>
</div>

<style type="text/css">
.temp-placeholder {
	border: 1px dashed #b4b9be;
    margin-bottom: 20px;
    background: #FFFFFF !important;
    box-shadow: none !important;
    width: 100% !important;
    padding-left: 10px;
}

.sortable-placeholder {
	width: 100% !important;
	height: 35px !important;
	background: #FFFFFF !important;
	box-shadow: none !important;
}
</style>

<script type="text/javascript">
var warnMessage = "<?php echo addslashes(__('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', $this -> plugin_name)); ?>";

function newsletters_forms_field_delete(field_id) {
	jQuery('#newsletters_forms_field_' + field_id).remove();
	jQuery('#newsletters_forms_availablefield_' + field_id).removeAttr('disabled');
	
	jQuery.ajax({
		url: newsletters_ajaxurl + "action=newsletters_forms_deletefield",
		method: "POST",
		data: {field_id:field_id, form_id:jQuery('input#id').val()},
	}).done(function(response) {
		//all good
	});
	
	return true;
}

function newsletters_forms_field_add(element, target) {
	jQuery(target).attr('disabled', "disabled");
	jQuery('.temp-placeholder').remove();
	var field_id = jQuery(element).data('id');
	var field_type = jQuery(element).data('type');
	var field_slug = jQuery(element).data('slug');
	
	var loading = '<div id="newsletters_forms_loading_' + field_id + '" class="newsletters_loading postbox"><h2 class="hndle ui-sortable-handle"><span><i class="fa fa-refresh fa-spin"></i> <?php _e('Loading field...', $this -> plugin_name); ?></span></h2></div>';
    
    if (jQuery('#normal-sortables > div').length > 0 && index != 0) {					    	    
	    jQuery("#normal-sortables > div:nth-child(" + index + ")").after(loading);
	} else if (index == 0) {
		jQuery('#normal-sortables').prepend(loading);
	} else {								
		jQuery('#normal-sortables').append(loading);
	}
	
	jQuery.ajax({
		url: newsletters_ajaxurl + 'action=newsletters_forms_addfield',
		method: "POST",
		data: {
			id: field_id,
			type: field_type,
			slug: field_slug,
		},
		success: function(response) {					
			jQuery('#newsletters_forms_loading_' + field_id).remove();
											
			if (jQuery('#normal-sortables > div').length > 0 && index != 0) {						
				jQuery("#normal-sortables > div:nth-child(" + index + ")").after(response);
			} else if (index == 0) {
				jQuery('#normal-sortables').prepend(response);
			} else {						
				jQuery('#normal-sortables').append(response);
			}
				
			jQuery('#normal-sortables').sortable('refresh');
			//postboxes.add_postbox_toggles("newsletters_page_newsletters-forms");
		}
	});
}

var index = 0;
var hasdropped = false;

jQuery(document).ready(function() {	
	jQuery('form#post').on('submit', function() {
		jQuery('#normal-sortables').sortable('refresh');
		var sortable = jQuery('#normal-sortables').sortable('toArray');
		jQuery('#fields').val(sortable);
	});
	
	jQuery('#normal-sortables').sortable({
		placeholder: 'ui-placeholder',
		over: function() {
	        jQuery('.temp-placeholder').hide();
	    },
	    out: function() {
	        jQuery('.temp-placeholder').show();
	    },
	    stop: function() {
	        jQuery('.temp-placeholder').remove();
	    },
		start: function(e, ui) {						
	        ui.placeholder.width(ui.item.width());
	    },
	    receive: function(event, ui) {
		    
	    },
	    update: function(event, ui) {		  
		    var element = ui.item;
		    index = jQuery(element).index();
		    hasdropped = true;
		    
		    window.onbeforeunload = function () {			    
		        if (warnMessage != null) return warnMessage;
		    }
	    }
	});
	
	jQuery('#form_availablefields li input').draggable({
		cancel: false,
		connectToSortable: "#normal-sortables",
		helper: "clone",
		revert: "invalid",
		start: function(event, ui) {		
			hasdropped = false;	
			jQuery(ui.helper).css('width', jQuery('#normal-sortables').width());	
		},
		stop: function(event, ui) {		
			if (hasdropped == true) {
				// the field has been dropped				
				newsletters_forms_field_add(ui.helper, event.target);
			}
				
			jQuery('.temp-placeholder').remove();
			jQuery(ui.helper).remove();
		}
	}).on('click', function(e) {
		index = jQuery('#normal-sortables > div').length;
		newsletters_forms_field_add(e.target, jQuery(this));
	});
	
	jQuery('ul, li').disableSelection();
	
	<?php if (!empty($manualpostboxes)) : ?>
		<?php foreach ($manualpostboxes as $postbox) : ?>
			var postboxhtml = '';
			<?php if (empty($postbox['mandatory'])) : ?>
				postboxhtml += '<div class="newsletters_delete_handle"><a href="" onclick="if (confirm(\'<?php _e('Are you sure you want to delete this field?', $this -> plugin_name); ?>\')) { newsletters_forms_field_delete(\'<?php echo $postbox['field_id']; ?>\'); } return false;"><i class="fa fa-times"></i></a></div>';
			<?php endif; ?>
			jQuery('#newsletters_forms_field_<?php echo $postbox['field_id']; ?>').find('.hndle').before(postboxhtml);
		<?php endforeach; ?>
	<?php else : ?>
		jQuery('#normal-sortables').append('<div class="temp-placeholder" style="width:auto; height:auto;"><p><i class="fa fa-reply"></i> <?php _e('Drag fields here to add them to the form', $this -> plugin_name); ?></p></div>');
	<?php endif; ?>

    jQuery('input:not(:button,:submit),textarea,select').change(function() {    
        window.onbeforeunload = function () {
            if (warnMessage != null) return warnMessage;
        }
    });
    
    jQuery('input:submit').click(function(e) {
        warnMessage = null;
    });
});
</script>