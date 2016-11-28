<?php

if (!class_exists('wpMailCheckinit')) {
	class wpMailCheckinit {
	
		function __construct() {
			return true;	
		}
		
		function ci_initialize() {				
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			
			if (!is_plugin_active(plugin_basename($this -> plugin_file))) {			
				return;
			}
			
			add_action('wp_ajax_wpmlserialkey', array($this, 'ajax_serialkey'));
		
			if (true || !is_admin() || (is_admin() && $this -> ci_serial_valid())) {
				$this -> ci_initialization();
			} else {			
				$this -> add_action('admin_enqueue_scripts', 'ci_print_styles', 10, 1);
				$this -> add_action('admin_enqueue_scripts', 'ci_print_scripts', 10, 1);
				$this -> add_action('admin_notices');
				$this -> add_action('init', 'init', 10, 1);
				$this -> add_action('admin_menu', 'admin_menu');
			}
			
			return false;
		}
		
		function ci_initialization() {		
																					
			/* RSS Feeds */
			if ($this -> get_option('rssfeed') == "Y" && !is_admin()) { 
				global $wp_rewrite;
				if (!$wp_rewrite) $wp_rewrite = new WP_Rewrite();
				add_feed('newsletters', array($this, 'feed_newsletters'));	
			}
			
			$this -> add_filter('media_send_to_editor', 'media_insert', 20, 3);
			$this -> add_filter('attachment_fields_to_save', 'attachment_fields_to_save', null, 2);
			$this -> add_filter('attachment_fields_to_edit', 'attachment_fields_to_edit', null, 2);
			
			add_filter('get_user_option_closedpostboxes_' . "newsletters_page_" . $this -> sections -> forms, array($this, 'closed_meta_boxes_form'), 10, 3);
			add_filter('get_user_option_meta-box-order_' . "newsletters_page_" . $this -> sections -> forms, array($this, 'meta_box_order'), 10, 3);
			
			//Action hooks
			$this -> add_action('register_form', 'register_form', 999, 1);
			$this -> add_action('admin_menu');
			$this -> add_action('admin_menu', 'add_dashboard', 10, 1);
			$this -> add_action('admin_head');
			$this -> add_action('admin_head-index.php', 'dashboard_columns');
			$this -> add_action('widgets_init', 'widget_register', 10, 1);
			$this -> add_action('wp_head', 'wp_head', 15, 1);
			$this -> add_action('wp_footer');
			$this -> add_action('admin_footer');
			$this -> add_action('delete_user', 'delete_user', 10, 1);
			$this -> add_action('user_register', 'user_register', 10, 1);
			$this -> add_action('save_post', 'save_post', 10, 2);
			$this -> add_action('delete_post', 'delete_post', 10, 1);
			$this -> add_action('trashed_post', 'delete_post', 10, 1);
			$this -> add_action('init', 'init', 11, 1);
			$this -> add_action('init', 'custom_post_types', 10, 1);
			$this -> add_action('wp_login', 'end_session', 10, 1);
			$this -> add_action('wp_logout', 'end_session', 10, 1);
			$this -> add_action('init', 'init_textdomain', 10, 1);
			$this -> add_action('plugins_loaded', "plugins_loaded", 2, 1);
			
			$this -> add_filter('manage_users_columns');			 
			$this -> add_action('manage_users_custom_column', 'manage_users_custom_column', 10, 3);
			
			/* Schedules */
			$this -> add_action('newsletters_ratereviewhook', 'ratereview_hook', 10, 1);
			$this -> add_action('newsletters_optimizehook', 'optimize_hook', 10, 1);
			$this -> add_action('newsletters_emailarchivehook', 'emailarchive_hook', 10, 1);
			$this -> add_action($this -> pre . '_cronhook', 'cron_hook', 10, 1);
	        $this -> add_action($this -> pre . '_pophook', 'pop_hook', 10, 1);
			$this -> add_action('newsletters_latestposts', 'latestposts_hook', 10, 1);
			$this -> add_action($this -> pre . '_activateaction', 'activateaction_hook', 10, 1);
			$this -> add_action($this -> pre . '_autoresponders', 'autoresponders_hook', 10, 1);
			$this -> add_action($this -> pre . '_captchacleanup', 'captchacleanup_hook', 10, 1);
			$this -> add_action($this -> pre . '_importusers', 'importusers_hook', 10, 1);
			$this -> add_action('do_meta_boxes', 'do_meta_boxes', 10, 1);
			$this -> add_action('admin_notices');
			$this -> add_action('admin_init', 'tinymce');
			$this -> add_action('admin_init', 'custom_redirect', 1, 1);
			$this -> add_action('phpmailer_init', 'phpmailer_init', 999, 1);
			$this -> add_action('profile_update');
			$this -> add_action('comment_form');
			$this -> add_action('wp_insert_comment', 'comment_post', 10, 2);
			$this -> add_action('wp_enqueue_scripts', 'print_styles');
			$this -> add_action('admin_enqueue_scripts', 'print_styles');
			$this -> add_action('wp_enqueue_scripts', 'print_scripts');
			$this -> add_action('admin_enqueue_scripts', 'print_scripts');
			$this -> add_action('wp_dashboard_setup', 'dashboard_setup');
			
			//Filter hooks
			if (!$this -> ci_serial_valid()) {
				$this -> add_filter('admin_footer_text');
			}
			
			$this -> add_filter('cron_schedules', 'cron_schedules', 1, 1);
			$this -> add_filter('screen_settings', 'screen_settings', 15, 2);
			$this -> add_filter('plugin_action_links', 'plugin_action_links', 10, 4);
			$this -> add_filter('the_editor', 'the_editor', 1, 1);
	        $this -> add_filter('tiny_mce_before_init', 'override_mce_options', 10, 1);
	        $this -> add_filter('the_content');
	        $this -> add_filter('upload_mimes');
	        
		    $this -> add_action('after_plugin_row_' . $this -> plugin_name . '/wp-mailinglist.php', 'after_plugin_row', 10, 2);
			
			if ($this -> ci_serial_valid()) {	
				$this -> add_action('install_plugins_pre_plugin-information', 'display_changelog', 10, 1);
				$this -> add_filter('transient_update_plugins', 'check_update', 10, 1);
		        $this -> add_filter('site_transient_update_plugins', 'check_update', 10, 1);
		    }
	        
	        if ($this -> language_do()) {
	        	add_filter('gettext', array($this, 'language_useordefault'), 0);
	        }
			
			//WordPress Shortcodes
			global $Shortcode;
			add_shortcode($this -> pre . 'management', array($this, 'sc_management'));
			add_shortcode($this -> pre . 'subscribe', array($Shortcode, 'subscribe'));
			add_shortcode($this -> pre . 'template', array($Shortcode, 'template'));
			add_shortcode($this -> pre . 'snippet', array($Shortcode, 'template'));
			add_shortcode($this -> pre . 'history', array($Shortcode, 'history'));
			add_shortcode($this -> pre . 'meta', array($Shortcode, 'meta'));
			add_shortcode($this -> pre . 'date', array($Shortcode, 'datestring'));
			add_shortcode($this -> pre . 'post', array($Shortcode, 'posts_single'));
			add_shortcode($this -> pre . 'posts', array($Shortcode, 'posts_multiple'));
			add_shortcode($this -> pre . 'post_thumbnail', array($Shortcode, 'post_thumbnail'));	
			add_shortcode($this -> pre . 'post_permalink', array($Shortcode, 'post_permalink'));
			add_shortcode($this -> pre . 'subscriberscount', array($Shortcode, 'subscriberscount'));
			
			add_shortcode('newsletters_bloginfo', array($Shortcode, 'bloginfo'));
			add_shortcode('newsletters_management', array($this, 'sc_management'));
			add_shortcode('newsletters_subscribe', array($Shortcode, 'subscribe'));
			add_shortcode('newsletters_subscribe_link', array($Shortcode, 'subscribe_link'));
			add_shortcode('newsletters_template', array($Shortcode, 'template'));
			add_shortcode('newsletters_snippet', array($Shortcode, 'template'));
			add_shortcode('newsletters_history', array($Shortcode, 'history'));
			add_shortcode('newsletters_meta', array($Shortcode, 'meta'));
			add_shortcode('newsletters_date', array($Shortcode, 'datestring'));
			add_shortcode('newsletters_post', array($Shortcode, 'posts_single'));
			add_shortcode('newsletters_sendas', array($Shortcode, 'posts_sendas'));
			add_shortcode('newsletters_posts', array($Shortcode, 'posts_multiple'));
			add_shortcode('newsletters_post_thumbnail', array($Shortcode, 'post_thumbnail'));	
			add_shortcode('newsletters_post_permalink', array($Shortcode, 'post_permalink'));
			add_shortcode('newsletters_subscriberscount', array($Shortcode, 'subscriberscount'));
			
			add_shortcode($this -> pre . 'themailer_address', array($Shortcode, 'themailer_address'));
			add_shortcode($this -> pre . 'themailer_facebookurl', array($Shortcode, 'themailer_facebookurl'));
			add_shortcode($this -> pre . 'themailer_twitterurl', array($Shortcode, 'themailer_twitterurl'));
			add_shortcode($this -> pre . 'themailer_rssurl', array($Shortcode, 'themailer_rssurl'));
			add_shortcode($this -> pre . 'pronews_address', array($Shortcode, 'pronews_address'));
			add_shortcode($this -> pre . 'pronews_facebookurl', array($Shortcode, 'pronews_facebookurl'));
			add_shortcode($this -> pre . 'pronews_twitterurl', array($Shortcode, 'pronews_twitterurl'));
			add_shortcode($this -> pre . 'pronews_rssurl', array($Shortcode, 'pronews_rssurl'));
			add_shortcode($this -> pre . 'lagoon_address', array($Shortcode, 'lagoon_address'));
			add_shortcode($this -> pre . 'lagoon_facebookurl', array($Shortcode, 'lagoon_facebookurl'));
			add_shortcode($this -> pre . 'lagoon_twitterurl', array($Shortcode, 'lagoon_twitterurl'));
			add_shortcode($this -> pre . 'lagoon_rssurl', array($Shortcode, 'lagoon_rssurl'));
			
			/* Post Shortcodes */
			
			//add_shortcode('newsletters_latestposts_loop_wrapper', array($Shortcode, 'latestposts_loop_wrapper'));
			//add_shortcode('newsletters_posts_loop_wrapper', array($Shortcode, 'posts_loop_wrapper'));
			
			$post_shortcodes = array('post_loop', 'post_anchor', 'category_heading', 'post_id', 'post_author', 'post_date_wrapper', 'post_date', 'post_thumbnail', 'post_excerpt', 'post_content', 'post_title', 'post_link');
			foreach ($post_shortcodes as $post_shortcode) {
				add_shortcode($post_shortcode, array($Shortcode, 'shortcode_posts'));
				add_shortcode('newsletters_' . $post_shortcode, array($Shortcode, 'shortcode_posts'));
			}
			
			add_shortcode('newsletters_if', array($Shortcode, 'newsletters_if'));
			
			add_action('wp_ajax_newsletters_importfile', array($this, 'ajax_importfile'));
			
			/* Ajax */
			if (is_admin()) {								
				if (defined('DOING_AJAX')) {					
					add_action('wp_ajax_newsletters_forms_createform', array($this, 'ajax_forms_createform'));
					add_action('wp_ajax_newsletters_forms_addfield', array($this, 'ajax_forms_addfield'));
					add_action('wp_ajax_newsletters_forms_deletefield', array($this, 'ajax_forms_deletefield'));
				
					add_action('wp_ajax_newsletters_admin_mode', array($this, 'ajax_admin_mode'));
					add_action('wp_ajax_newsletters_change_themefolder', array($this, 'ajax_change_themefolder'));
					add_action('wp_ajax_newsletters_delete_option', array($this, 'ajax_delete_option'));
					add_action('wp_ajax_newsletters_pause_queue', array($this, 'ajax_pause_queue'));
					add_action('wp_ajax_newsletters_autocomplete_histories', array($this, 'ajax_autocomplete_histories'));
					add_action('wp_ajax_newsletters_autocomplete_users', array($this, 'ajax_autocomplete_users'));
					add_action('wp_ajax_newsletters_load_new_editor', array($this, 'ajax_load_new_editor'));
					add_action('wp_ajax_newsletters_latestposts_save', array($this, 'ajax_latestposts_save'));
					add_action('wp_ajax_newsletters_latestposts_changestatus', array($this, 'ajax_latestposts_changestatus'));
					add_action('wp_ajax_newsletters_latestposts_settings', array($this, 'ajax_latestposts_settings'));
					add_action('wp_ajax_newsletters_latestposts_delete', array($this, 'ajax_latestposts_delete'));
					
					add_action('wp_ajax_newsletters_mailinglist_save', array($this, 'ajax_mailinglist_save'));
					
					
					add_action('wp_ajax_newsletters_tinymce_snippet', array($this, 'ajax_tinymce_snippet'));
					add_action('wp_ajax_newsletters_tinymce_dialog', array($this, 'ajax_tinymce_dialog'));
					add_action('wp_ajax_newsletters_order_fields', array($this, 'ajax_order_fields'));
					add_action('wp_ajax_newsletters_themeedit', array($this, 'ajax_themeedit'));
					add_action('wp_ajax_newsletters_addcontentarea', array($this, 'ajax_addcontentarea'));
					add_action('wp_ajax_newsletters_deletecontentarea', array($this, 'ajax_deletecontentarea'));
					add_action('wp_ajax_subscribercount', array($this, 'ajax_subscribercount'));
					add_action('wp_ajax_newsletters_subscribercountdisplay', array($this, 'ajax_subscribercountdisplay'));
					add_action('wp_ajax_wpmltestsettings', array($this, 'ajax_testsettings'));
					add_action('wp_ajax_newsletters_mailapi_mandrill_keytest', array($this, 'ajax_mailapi_mandrill_keytest'));
					add_action('wp_ajax_newsletters_mailapi_amazonses_action', array($this, 'ajax_mailapi_amazonses_action'));
					add_action('wp_ajax_newsletters_mailapi_mailgun_action', array($this, 'ajax_mailapi_mailgun_action'));
					add_action('wp_ajax_wpmldkimwizard', array($this, 'ajax_dkimwizard'));
					add_action('wp_ajax_wpmltestbouncesettings', array($this, 'ajax_testbouncesettings'));
					add_action('wp_ajax_wpmlhistory_iframe', array($this, 'ajax_historyiframe'));
					
					$createpreview = $this -> get_option('createpreview');
					if (!empty($createpreview) && $createpreview == "Y") {
						add_action('wp_ajax_wpmlpreviewrunner', array($this, 'ajax_previewrunner'));
					}
					
					add_action('wp_ajax_newsletters_spamscorerunner', array($this, 'ajax_spamscorerunner'));
					add_action('wp_ajax_newsletters_gauge', array($this, 'ajax_gauge'));
					add_action('wp_ajax_wpmllatestposts_preview', array($this, 'ajax_latestposts_preview'));
					add_action('wp_ajax_newsletters_lpsposts', array($this, 'ajax_lps_posts'));
					add_action('wp_ajax_newsletters_delete_lps_post', array($this, 'ajax_delete_lps_post'));
					add_action('wp_ajax_wpmlwelcomestats', array($this, 'ajax_welcomestats'));
					add_action('wp_ajax_newsletters_executemultiple', array($this, 'ajax_executemultiple'));
					add_action('wp_ajax_newsletters_queuemultiple', array($this, 'ajax_queuemultiple'));
					add_action('wp_ajax_wpmlsetvariables', array($this, 'ajax_setvariables'));
					add_action('wp_ajax_wpmlgetposts', array($this, 'ajax_getposts'));
					add_action('wp_ajax_newsletters_api_newkey', array($this, 'ajax_api_newkey'));
				}
			}	
			
			add_action('wp_ajax_newsletters_api', array($this, 'api_init'));
			add_action('wp_ajax_nopriv_newsletters_api', array($this, 'api_init'));
			add_action('wp_ajax_wpmlsubscribe', array($this, 'ajax_subscribe'));
			add_action('wp_ajax_nopriv_wpmlsubscribe', array($this, 'ajax_subscribe'));
			add_action('wp_ajax_managementactivate', array($this, 'ajax_managementactivate'));
			add_action('wp_ajax_nopriv_managementactivate', array($this, 'ajax_managementactivate'));
			add_action('wp_ajax_managementsubscribe', array($this, 'ajax_managementsubscribe'));
			add_action('wp_ajax_nopriv_managementsubscribe', array($this, 'ajax_managementsubscribe'));
			add_action('wp_ajax_managementcurrentsubscriptions', array($this, 'ajax_managementcurrentsubscriptions'));
			add_action('wp_ajax_nopriv_managementcurrentsubscriptions', array($this, 'ajax_managementcurrentsubscriptions'));
			add_action('wp_ajax_managementnewsubscriptions', array($this, 'ajax_managementnewsubscriptions'));
			add_action('wp_ajax_nopriv_managementnewsubscriptions', array($this, 'ajax_managementnewsubscriptions'));
			add_action('wp_ajax_managementsavefields', array($this, 'ajax_managementsavefields'));
			add_action('wp_ajax_nopriv_managementsavefields', array($this, 'ajax_managementsavefields'));
			add_action('wp_ajax_managementcustomfields', array($this, 'ajax_managementcustomfields'));
			add_action('wp_ajax_nopriv_managementcustomfields', array($this, 'ajax_managementcustomfields'));
			add_action('wp_ajax_newsletters_importmultiple', array($this, 'ajax_importmultiple'));
			add_action('wp_ajax_newsletters_exportmultiple', array($this, 'ajax_exportmultiple'));
			add_action('wp_ajax_wpmlgetlistfields', array($this, 'ajax_getlistfields'));
			add_action('wp_ajax_nopriv_wpmlgetlistfields', array($this, 'ajax_getlistfields'));
			
			add_action('wp_ajax_newsletters_posts_by_category', array($this, 'ajax_posts_by_category'));
			add_action('wp_ajax_newsletters_template_iframe', array($this, 'ajax_template_iframe'));
			add_action('wp_ajax_newsletters_form_preview', array($this, 'ajax_form_preview'));
			
			if (is_admin()) $this -> updating_plugin();
			
			return true;
		}
		
		function ci_get_serial() {
			if ($serial = $this -> get_option('serialkey')) {
				return $serial;
			}
			
			return false;
		}
		
		function ci_serial_valid() {
			$host = $_SERVER['HTTP_HOST'];
			$result = false;
			
			if (preg_match("/^(www\.)(.*)/si", $host, $matches)) {
				$wwwhost = $host;
				$nonwwwhost = preg_replace("/^(www\.)?/si", "", $wwwhost);
			} else {
				$nonwwwhost = $host;
				$wwwhost = "www." . $host;
			}
			
			$wwwhost = strtolower($wwwhost);
			$nonwwwhost = strtolower($nonwwwhost);
			
			if ($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "localhost:" . $_SERVER['SERVER_PORT']) {
				$result = true;	
			} else {
				if ($serial = $this -> ci_get_serial()) {			
					if ($serial == strtoupper(md5($_SERVER['HTTP_HOST'] . "wpml" . "mymasesoetkoekiesisfokkenlekker"))) {
						$result = true;
					} elseif (strtoupper(md5($wwwhost . "wpml" . "mymasesoetkoekiesisfokkenlekker")) == $serial || 
								strtoupper(md5($nonwwwhost . "wpml" . "mymasesoetkoekiesisfokkenlekker")) == $serial) {
						$result = true;
					}
				}
			}
			
			$result = apply_filters($this -> pre . '_serialkey_validation', $result);
			return $result;
		}
	}
}

?>