<script type="text/javascript">
var wpmlAjax = '<?php echo $this -> url(); ?>/<?php echo $this -> plugin_name; ?>-ajax.php';
<?php if ($this -> language_do()) : ?>
	var newsletters_ajaxurl = '<?php echo admin_url('admin-ajax.php?lang=' . $this -> language_current() . '&'); ?>';
<?php else : ?>
	var newsletters_ajaxurl = '<?php echo admin_url('admin-ajax.php?'); ?>';
<?php endif; ?>
var wpmlUrl = '<?php echo $this -> url(); ?>';

<?php if (true || !empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) : ?>
	jQuery.noConflict();
	$ = jQuery.noConflict();

	jQuery(document).ready(function() {		
		if (jQuery.isFunction(jQuery.fn.select2)) {
			jQuery('.newsletters select, .newsletters_select2').select2({});
			
			jQuery('.newsletters select[name="perpage"]').select2({
				tags: true
			});
		}
		
		if (jQuery.isFunction(jQuery.fn.tooltip)) {
			jQuery(".wpmlhelp a").tooltip({
				tooltipClass: 'newsletters-ui-tooltip',
				content: function () {
		            return jQuery(this).prop('title');
		        },
		        show: {
			        delay: 500
		        }, 
		        close: function (event, ui) {
		            ui.tooltip.hover(
		            function () {
		                jQuery(this).stop(true).fadeTo(400, 1);
		            },    
		            function () {
		                jQuery(this).fadeOut("400", function () {
		                    jQuery(this).remove();
		                })
		            });
		        }
			});
		}
		
		<?php
			
		$admin_mode = get_user_option('newsletters_admin_mode', get_current_user_id());
		if (empty($admin_mode)) $admin_mode = 'standard';
			
		?>
		
		newsletters_admin_mode_switcher('<?php echo $admin_mode; ?>', false);
		
		jQuery('.newsletters-admin-mode-standard').click(function() { newsletters_admin_mode_switcher('standard', true); return false; });
		jQuery('.newsletters-admin-mode-advanced').click(function() { newsletters_admin_mode_switcher('advanced', true); return false; });
	});
	
	function newsletters_admin_mode_switcher(mode, savemode) {		
		if (mode == "standard") {
			jQuery('.advanced-setting').hide();
			jQuery('.newsletters-admin-mode-standard').addClass('active');
			jQuery('.newsletters-admin-mode-advanced').removeClass('active');
		} else if (mode == "advanced") {
			jQuery('.advanced-setting').show();
			jQuery('.newsletters-admin-mode-advanced').addClass('active');
			jQuery('.newsletters-admin-mode-standard').removeClass('active');
		}
		
		if (savemode == true) {
			jQuery.ajax({
				method: "POST",
				data: {
					mode: mode
				},
				url: newsletters_ajaxurl + 'action=newsletters_admin_mode',
			}).done(function (response) {
				//all good...
			});
		}
	}
<?php endif; ?>
</script>