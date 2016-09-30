<?php 

$history_id = (empty($_POST['ishistory'])) ? $_GET['id'] : $_POST['ishistory'];	
$src = (!empty($history_id)) ? admin_url('admin-ajax.php?action=' . $this -> pre . 'history_iframe&id=' . $history_id) : false; 

?>

<p>
	<a href="<?php echo $src; ?>" id="newwindowbutton" <?php if (empty($history_id)) : ?>disabled="disabled"<?php endif; ?> target="_blank" class="button button-secondary"><i class="fa fa-external-link"></i> <?php _e('Open in New Window', $this -> plugin_name); ?></a>
	<a href="" id="previewrunnerbutton" onclick="previewrunner(); return false;" class="button button-primary"><i class="fa fa-eye"></i> <?php _e('Update Preview', $this -> plugin_name); ?></a>
	<span id="previewrunnerloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
</p>

<?php /*<?php
	
$fromname = (!empty($_POST['fromname'])) ? $_POST['fromname'] : $this -> get_option('smtpfromname');
$subject = (!empty($_POST['subject'])) ? $_POST['subject'] : false;
$preheader = (!empty($_POST['preheader'])) ? $_POST['preheader'] : (!empty($_POST['content']) ? $Html -> truncate($_POST['content'], 100) : false);		
	
?>

<div class="newsletters-preview" style="display:none;">
	<div class="newsletters-preview-fromname">
		<?php echo $fromname; ?>
	</div>
	<div class="newsletters-preview-subject">
		<?php echo $subject; ?>
	</div>
	<div class="newsletters-preview-preheader">
		<?php echo $preheader; ?>
	</div>
</div>*/ ?>

<iframe width="100%" height="300" frameborder="0" scrolling="auto" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0; border:1px #CCCCCC solid;" src="<?php echo $src; ?>" id="previewiframe">
	<?php _e('Nothing to show yet, please add a subject, content and choose at least one mailing list.', $this -> plugin_name); ?>
</iframe>

<script type="text/javascript">
var previewrequest = false;

<?php if (!empty($history_id)) : ?>
var history_id = "<?php echo $history_id; ?>";
<?php else : ?>
var history_id = false;
<?php endif; ?>

function previewrunner() {	
	jQuery('iframe#content_ifr').attr('tabindex', "2");
	var formvalues = jQuery('form#post').serialize();
	
	//var content = jQuery("iframe#content_ifr").contents().find("body#tinymce").html();
	//var content = tinyMCE.editors.content.getContent();
	
	var content = newsletters_tinymce_content('content');
	
	if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function") {
		tinyMCE.triggerSave();
	}
		
	if (previewrequest) { previewrequest.abort(); }
	jQuery('#previewrunnerbutton').attr('disabled', "disabled");
	jQuery('#previewrunnerloading').show();
	
	jQuery('#sendbutton, #sendbutton2').prop('disabled', true);
	jQuery('#savedraftbutton, #savedraftbutton2').prop('disabled', true);
	
	jQuery.ajaxSetup({cache:false});
	
	previewrequest = jQuery.ajax({
		data: formvalues,
		dataType: 'xml',
		url: newsletters_ajaxurl + 'action=wpmlpreviewrunner&random=' + (new Date()).getTime(),
		cache: false,
		type: "POST",
		success: function(response) {			
			history_id = jQuery("history_id", response).text();
			p_id = jQuery("p_id", response).text();
			previewcontent = jQuery("previewcontent", response).text();
			newsletter_url = jQuery("newsletter_url", response).text();
			
			if (history_id != "") { 
				
				jQuery('#newwindowbutton').removeAttr('disabled').attr('href', "<?php echo admin_url('admin-ajax.php?action=wpmlhistory_iframe&id='); ?>" + history_id);
				
				jQuery('#ishistory').val(history_id); 
				jQuery('#p_id').val(p_id);
				jQuery('#edit-slug-box').show();
				jQuery('#sample-permalink').html(newsletter_url);
				jQuery('#view-post-btn a').attr('href', newsletter_url);
				jQuery('#shortlink').attr('value', newsletter_url).val(newsletter_url);
			}
		},
		complete: function(response) {		
			if (typeof previewcontent != 'undefined') { jQuery('#previewiframe').contents().find('html').html(previewcontent); }
			jQuery('#previewrunnerbutton').removeAttr('disabled');
			jQuery('#previewrunnerloading').hide();
			
			jQuery('#sendbutton, #sendbutton2').prop('disabled', false);
			jQuery('#savedraftbutton, #savedraftbutton2').prop('disabled', false);
			
			var iframeheight = jQuery("#previewiframe").contents().find("html").outerHeight();
			jQuery("#previewiframe").height(iframeheight).css({height: iframeheight}).attr("height", iframeheight);
			
			var date = new Date();
			var year = date.getFullYear();
			var month = ("0" + (date.getMonth() + 1)).slice(-2);
			var day = ("0" + date.getDate()).slice(-2);
			var hours = ("0" + date.getHours()).slice(-2);
			var minutes = ("0" + date.getMinutes()).slice(-2);
			var today = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
			var autosavedate = year + '-' + ('0' + (month + 1)).slice(-2) + '-' + day + ' ' + hours + ':' + minutes;
			jQuery('#autosave').html('<?php _e('Draft saved at', $this -> plugin_name); ?> ' + autosavedate).show();
		}
	});
	
	return true;
}

jQuery(document).ready(function() {
	//setTimeout(previewrunner, 60000);
	setTimeout(function() {
		if (history_id != false) {
			previewrunner();
		}
		
		setInterval(previewrunner, 60000);
	}, 3000);
});
</script>