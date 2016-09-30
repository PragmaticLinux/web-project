<?php
	
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('wpMailPlugin')) {
	class wpMailPlugin extends wpMailCheckinit {
	
		var $plugin_base;
		var $pre = 'wpml';	
		var $version = '4.6.3';
		var $dbversion = '1.2.3';
		var $debugging = false;			//set to "true" to turn on debugging
		var $debug_level = 2; 			//set to 1 for only database errors and var dump; 2 for PHP errors as well
		var $post_errors = array();
		var $intervals = array();
		var $menus = array();
		var $cache = array();
		
		var $sections = array(
			//'about'						=>	"newsletters-about",
			'welcome'					=> 	"newsletters",
			'submitserial'				=>	"newsletters-submitserial",
			'forms'						=>	"newsletters-forms",
			'send'						=>	"newsletters-create",
			'autoresponders'			=>	"newsletters-autoresponders",
			'autoresponderemails'		=>	"newsletters-autoresponderemails",
			'lists'						=>	"newsletters-lists",
			'groups'					=>	"newsletters-groups",
			'subscribers'				=>	"newsletters-subscribers",
			'fields'					=>	"newsletters-fields",
			'importexport'				=>	"newsletters-importexport",
			'themes'					=>	"newsletters-themes",
			'templates'					=>	"newsletters-templates",
			'templates_save'			=>	"newsletters-templates-save",
			'queue'						=>	"newsletters-queue",
			'history'					=>	"newsletters-history",
			'emails'					=>	"newsletters-emails",
			'links'						=>	"newsletters-links",
			'clicks'					=>	"newsletters-links-clicks",
			'orders'					=>	"newsletters-orders",
			'settings'					=>	"newsletters-settings",
			'settings_subscribers'		=>	"newsletters-settings-subscribers",
			'settings_templates'		=>	"newsletters-settings-templates",
			'settings_system'			=>	"newsletters-settings-system",
			'settings_tasks'			=>	"newsletters-settings-tasks",
			'settings_updates'			=>	"newsletters-settings-updates",
			'settings_api'				=>	"newsletters-settings-api",
			'extensions'				=>	"newsletters-extensions",
			'extensions_settings'		=>	"newsletters-extensions-settings",
			'support'					=>	"newsletters-support",
		);
		
		var $blocks = array(
			'newsletters_admin_send_sendtoroles'
		);
		
		var $extensions = array();
		
		var $classes = array(
			'Subscriber',
			'Bounce',
			'Unsubscribe',
			'Mailinglist',
			'Queue',
			//'Latestpost',
			'FieldsList',
			'Field',
			//'History',
			'HistoriesList',
			'HistoriesAttachment',
			'Email',
			//'wpmlOrder',
			//'Post',
			'Theme',
			//'Template',
			'SubscribersList',
			//'wpmlCountry',
			//'Autoresponder',
			//'AutorespondersList',
			//'Autoresponderemail',
			//'wpmlGroup',
		);
		
		var $models = array('Link', 'Click', 'Subscribeform', 'Option', 'SubscribersOption', 'Content', 'Latestpost', 'Latestpostssubscription', 'History', 'Order', 'Post', 'Template', 
		'Country', 'Group', 'Autoresponder', 'Autoresponderemail', 'AutorespondersList', 'FieldsForm', 'SubscriberMeta');
		
		var $helpers = array('Checkinit', 'Html', 'Form', 'Metabox', 'Shortcode', 'Auth', 'Db');	
		var $tables = array();
		var $tablenames = array();
		
		public function __call($method = null, $args = null) {			
			global $Db;
			
			if (!empty($method) && in_array($method, $this -> models)) {
				$class = $this -> pre . $method;
							
				if (!class_exists($class)) {
					$file = $this -> plugin_base() . DS . 'models' . DS . strtolower($method) . '.php';
					if (file_exists($file)) {
						include($file);
					}
				}
				
				if (empty($this -> {$method}) || !is_object($this -> {$method})) {												
					if (class_exists($class)) {							
						$newmethod = new $class($args);
						$this -> {$method} = $newmethod;
						return $newmethod;
					}
				} else {									
					if (empty($this -> {$method} -> data)) {
						if (!empty($Db -> {$method} -> data)) {
							$this -> {$method} -> data = $Db -> {$method} -> data;
						}
					}
					
					if (empty($this -> {$method} -> insertid) && !empty($Db -> {$method} -> insertid)) {
						$this -> {$method} -> insertid = $Db -> {$method} -> insertid;
					}
					
					if (empty($this -> {$method} -> errors) && !empty($this -> {$method} -> {$method} -> errors)) {
						$this -> {$method} -> errors = $this -> {$method} -> {$method} -> errors;
					}
					
					if (empty($this -> {$method} -> data) && !empty($this -> {$method} -> {$method} -> data)) {
						$this -> {$method} -> data = $this -> {$method} -> {$method} -> data;
					}
						
					if (empty($this -> {$method} -> model)) {	
						$this -> {$method} -> model = $method;	
					}
					
					return $this -> {$method};
				}
			}
			
			return false;
		}
		
		/**
		 * Register the plugin
		 * Sets the plugin name and base directory for universal use.
		 * @param STRING. Name of the plugin
		 * @param STRING. Base directory of the plugin
		 *
		 */
		function register_plugin($name = null, $base = null) {			
			$this -> api_key = $this -> get_option('api_key');
			$this -> plugin_name = basename(dirname(__FILE__));			
			$this -> plugin_base = rtrim(dirname($base), DS);
			$this -> plugin_file = plugin_basename($base);
			if (!defined('NEWSLETTERS_LOG_FILE')) { define("NEWSLETTERS_LOG_FILE", $this -> plugin_base() . DS . "newsletters.log"); }
			$this -> sections = apply_filters('newsletters_sections', (object) $this -> sections);
			$this -> set_timezone();
			$this -> extensions = $this -> get_extensions();
					
			### Get our models ready for action!
			$this -> initialize_classes();
			
			global $wpdb;
			$wpdb -> query("SET sql_mode = '';");
			@ini_set('asp_tags', false);
			//$wpdb -> query("SET innodb_lock_wait_timeout = 360;");
			
			$debugging = get_option('tridebugging');
			$this -> debugging = (empty($debugging)) ? $this -> debugging : true;
			$this -> debugging($this -> debugging);
			//add_action('plugins_loaded', array($this, 'ci_initialize'));
			$this -> add_action('plugins_loaded');
			
			if (is_admin()) {
				global $extensions;
				require_once($this -> plugin_base() . DS . 'includes' . DS . 'extensions.php');
				$this -> extensions = $extensions;
			}
			
			global $newsletters_cache;
			if (empty($newsletters_cache)) $newsletters_cache = array();
		}
		
		function set_cache($hash = null, $data = null, $type = 'query') {
			global $newsletters_cache;	    
		    $newsletters_cache[$type][$hash] = maybe_serialize($data);		    
		    return true;
	    }
	    
	    function get_cache($hash = null, $type = 'query') {		      
	    	global $newsletters_cache;
		    if (isset($newsletters_cache[$type][$hash])) {
			    return maybe_unserialize($newsletters_cache[$type][$hash]);
		    }
		    
		    return false;
	    }
	    
	    function delete_cache($hash = null, $type = 'query') {	 
	    	global $newsletters_cache;   
		    if (isset($newsletters_cache[$type][$hash])) {
			    unset($newsletters_cache[$type][$hash]);
			    return true;
		    }
		    
		    return false;
	    }
	    
	    function delete_all_cache($type = 'query') {	
	    	global $newsletters_cache;    
		    
		    switch ($type) {
			    case 'all'					:
			    	$newsletters_cache = array();
			    	break;
			    case 'query'				:
			    default 					:
		    		$newsletters_cache[$type] = array();
		    		break;
		    }
		    
		    return true;
	    }
	    
	    function get_device() {
		    $device = false;
		    
		    require_once($this -> plugin_base() . DS . 'vendors' . DS . 'mobile-detect' . DS . 'Mobile_Detect.php');
			$detect = new Mobile_Detect();
			
			if ($detect -> isTablet()) {
				$device = "tablet";
			} elseif ($detect -> isMobile()) {
				$device = "mobile";
			} else {
				$device = "desktop";
			}
			
			return $device;
	    }
	    
	    function is_php_module($module = null) {
		    if (!empty($module)) {
			    ob_start();
				phpinfo(INFO_MODULES);
				$contents = ob_get_clean();
				
				if (strpos($contents, $module) !== false) {
					return true;
				}
		    }
		    
		    return false;
	    }
		
		function media_insert($html = null, $id = null, $attachment = null) {
			$align = get_post_meta($id, '_wpmlalign', true);
			$hspace = get_post_meta($id, '_wpmlhspace', true);
			
			if (!empty($align) || !empty($hspace)) {
				$dom = new DOMDocument();
				$dom -> loadHTML($html);
				
				foreach ($dom -> getElementsByTagName('img') as $img) {				
					if (!empty($align) && $align != "none") {
						$img -> setAttribute('align', $align);
					}
					
					if (!empty($hspace)) {
						$img -> setAttribute('hspace', $hspace);
					}
					
					//$img -> removeAttribute('width');
					//$img -> removeAttribute('height');
				}
				
				$html = $dom -> saveHTML();
				$html = trim(preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $html));
			}
			
			return $html;
		}
		
		function attachment_fields_to_save($post = null, $attachment = null) {
		
			if (!empty($attachment[$this -> pre . 'align'])) {
				update_post_meta($post['ID'], '_wpmlalign', $attachment[$this -> pre . 'align']);
			} else {
				delete_post_meta($post['ID'], '_wpmlalign');
			}
			
			if (!empty($attachment[$this -> pre . 'hspace'])) {
				update_post_meta($post['ID'], '_wpmlhspace', $attachment[$this -> pre . 'hspace']);
			} else {
				delete_post_meta($post['ID'], '_wpmlhspace');
			}
			
			return $post;
		}
		
		function attachment_fields_to_edit($form_fields = null, $post = null) {		
			$align = get_post_meta($post -> ID, "_wpmlalign", true);
			$hspace = get_post_meta($post -> ID, "_wpmlhspace", true);
		
	        $html = '<label for="attachments_' . $post -> ID . '_wpmlalign_none"><input ' . ((empty($align) || (!empty($align) && $align == "none")) ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="none" id="attachments_' . $post -> ID . '_wpmlalign_none" /> ' . __('None', $this -> plugin_name) . '</label>';
	        $html .= ' <label for="attachments_' . $post -> ID . '_wpmlalign_left"><input ' . ((!empty($align) && $align == "left") ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="left" id="attachments_' . $post -> ID . '_wpmlalign_left" /> ' . __('Left', $this -> plugin_name) . '</label>';
	        $html .= ' <label for="attachments_' . $post -> ID . '_wpmlalign_right"><input ' . ((!empty($align) && $align == "right") ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="right" id="attachments_' . $post -> ID . '_wpmlalign_right" /> ' . __('Right', $this -> plugin_name) . '</label>';
	
	        $form_fields['wpmlalign'] = array(
	            'label' =>  __('Email Align', $this -> plugin_name),
	            'input' =>  'html',
	            'html'	=>	$html,
	            'value'	=>	$align,
	        );
	        
	        $form_fields['wpmlhspace'] = array(
	        	'label'	=>	__('Email Hspace', $this -> plugin_name),
	        	'input'	=>	'html',
	        	'html'	=>	'<input type="text" style="width:45px;" class="widefat" name="attachments[' . $post -> ID . '][wpmlhspace]" value="' . $hspace . '" id="attachments_' . $post -> ID . '_wpmlhspace" /> px',
	        	'value'	=>	$hspace,
	        );
		
		    return $form_fields;
		}
		
		function debugging($debug = false) {
			global $wpdb;
		
			if (!empty($debug) && $debug == true) {
				if (!defined('WP_DEBUG')) { define('WP_DEBUG', true); }
				$wpdb -> show_errors();
				
				if (!empty($this -> debug_level) && $this -> debug_level == 2) {
					error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
					@ini_set('display_errors', 1);
					@ini_set("log_errors", 1);
				}
			} else {
				$wpdb -> hide_errors();
				error_reporting(0);
				@ini_set('display_errors', 0);
				@ini_set("log_errors", 0);
			}
		}
		
		function after_plugin_row($plugin_name = null) {
	        $key = $this -> get_option('serialkey');
	        $update = $this -> vendor('update');
	        $version_info = $update -> get_version_info();
	        
	        if (!empty($version_info) && $version_info['is_valid_key'] == "0") {
		        echo '<tr id="newsletters-plugin-update-tr" class="plugin-update-tr">';
		        echo '<td colspan="3" class="plugin-update">';
		        echo '<div class="update-message">';

				if (!$this -> ci_serial_valid()) {
					echo sprintf(__('You are running Newsletters LITE. To remove limits, you can submit a serial key or %s.', $this -> plugin_name), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">' . __('Upgrade to PRO', $this -> plugin_name) . '</a>');
				} else {
					echo sprintf('Your download for the Newsletter plugin has expired, please <a href="%s" target="_blank">renew it</a> for updates!', $version_info['url']);
				}	        

		        echo '</div>';
		        echo '</td>';
		        echo '</tr>';
		        
		        ?>
		        
		        <script type="text/javascript">
			    jQuery(document).ready(function() {
				    var row = jQuery('#newsletters-plugin-update-tr').closest('tr').prev();
				    jQuery(row).addClass('update');
			    });
			    </script>
		        
		        <?php
	        }
	    }
	    
		/**
		 * This function outputs the changelog on the 'Plugins' page when the "View Details" link is clicked.
		 */
	    function display_changelog() {	    	
	    	if (!empty($_GET['plugin']) && $_GET['plugin'] == $this -> plugin_name) {			
		    	$update = $this -> vendor('update');
		    	if ($changelog = $update -> get_changelog()) {				
					$this -> render('changelog', array('changelog' => $changelog), true, 'admin');
		    	}
		    	
		    	exit();
	    	}
	    }
		
		function has_update($cache = true) {
			$update = $this -> vendor('update');
	        $version_info = $update -> get_version_info($cache);
	        return version_compare($this -> version, $version_info["version"], '<');
	    }
		
		function check_update($option, $cache = true) {
			if ($update = $this -> vendor('update')) {
		        $version_info = $update -> get_version_info($cache);
		
		        if (!$version_info) { return $option; }
		        $plugin_path = $this -> plugin_file;
		        
		        if(empty($option -> response[$plugin_path])) {
					$option -> response[$plugin_path] = new stdClass();
		        }
		
		        //Empty response means that the key is invalid. Do not queue for upgrade
		        if(empty($version_info['is_valid_key']) || version_compare($this -> version, $version_info["version"], '>=')){
		            unset($option -> response[$plugin_path]);
		        } else {
		            $option -> response[$plugin_path] -> url = "http://tribulant.com";
		            $option -> response[$plugin_path] -> slug = $this -> plugin_name;
		            $option -> response[$plugin_path] -> package = $version_info['url'];
		            $option -> response[$plugin_path] -> new_version = $version_info["version"];
		            $option -> response[$plugin_path] -> id = "0";
		        }
	        }
	
	        return $option;
	    }
	    
	    function ajax_importfile() {
		    define('DOING_AJAX', true);
		    define('SHORTINIT', true);
		    
		    global $Db, $Html, $Field;
		    
		    ob_start();
		    
		    if ( ! function_exists( 'wp_handle_upload' ) ) {
			    require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			
			$uploadedfile = $_FILES['file'];
			
			$upload_overrides = array( 'test_form' => false );
			
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			
			if ( $movefile && ! isset( $movefile['error'] ) ) {
			    
			    $csvtypes = array('text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/octet-stream', 'application/vnd.msexcel', 'text/anytext', 'text/plain');
			    $filetype = wp_check_filetype($movefile['file']);
			    
			    $delimiter = $Html -> detectDelimiter($movefile['file']);
			    $rows = 3;
			    $done = 1;
			    $headingdone = false;			    
			    $fields = $Field -> get_all();
			    
			    if (!empty($filetype['type']) && in_array($filetype['type'], $csvtypes)) {
				    $preview = '';
				    $preview .= '<h3>' . __('Preview of File', $this -> plugin_name) . '</h3>';
				    
				    if ($fp = file($movefile['file'])) {
				    	$count = count($fp);
				    	$preview .= '<p>' . sprintf(__('The file contains %s records in total.', $this -> plugin_name), '<b>' . $count . '</b>') . '</p>';
					}
				    
				    $preview .= '<table class="widefat">';
				    
				    if (($fh = fopen($movefile['file'], "r")) !== false) {
					    while (($row = fgetcsv($fh, 1000, $delimiter)) !== false && $done <= $rows) {						    
						    if (empty($headingdone)) {							    
							    $preview .= '<thead>';
							    $preview .= '<tr>';
							    for ($i = 1; $i <= count($row); $i++) {
								    $preview .= '<th>' . sprintf(__('Column %s', $this -> plugin_name), $i) . '</th>';
							    }
							    $preview .= '</tr>';
								
								$preview .= '<tr>';
								for ($i = 1; $i <= count($row); $i++) {
									$preview .= '<td>';
									$preview .= '<select name="columns[' . $i . ']">';
									$preview .= '<option value="">' . __('- Do not import -', $this -> plugin_name) . '</option>';
									$preview .= '<option value="email">' . __('Email Address', $this -> plugin_name) . '</option>';
									$preview .= '<option value="ip_address">' . __('IP Address', $this -> plugin_name) . '</option>';
									$preview .= '<option value="created">' . __('Created Date', $this -> plugin_name) . '</option>';
									if (!empty($fields)) {
										foreach ($fields as $field) {
											$preview .= '<option value="' . $field -> slug . '">' . __($field -> title) . '</option>';
										}
									}
									$preview .= '</select>';
									$preview .= '</td>';
								}
								$preview .= '</tr>';
								
							    $preview .= '</thead>';
							    $preview .= '<tbody>';
							    $headingdone = true;
						    }
						    
						    $class = (empty($class)) ? 'alternate' : '';
						    $preview .= '<tr class="' . $class . '">';
						    foreach ($row as $cell) {
							    $preview .= '<td>' . $cell . '</td>';
						    }
						    $preview .= '</tr>';
						    $done++;
					    }
				    }
				    
				    $preview .= '</tbody></table>';
				}
				
				if (function_exists('mb_detect_encoding')) {
					$encoding = mb_detect_encoding($preview);	
					if (empty($encoding) || $encoding != "UTF-8") {
						$preview = utf8_encode($preview);
					}
				}
			    
			    $response = array(
				    'success'			=>	true,
				    'movefile'			=>	$movefile,
				    'preview'			=>	$preview,
				    'delimiter'			=>	$delimiter,
			    );
			} else {			    
			    $response = array(
				    'success'			=>	false,
				    'errormessage'		=>	$movefile['error'],
			    );
			}
			
			$process = ob_get_clean();						
			echo json_encode($response);
		    
		    exit();
		    die();
	    }
	    
	    function ajax_importmultiple() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Queue, $Subscriber, $SubscribersList, $Unsubscribe, $Bounce;
		
			if (!empty($_REQUEST['subscribers'])) {
				
				foreach ($_REQUEST['subscribers'] as $skey => $subscriber) {
					//$subscriber = maybe_unserialize(stripslashes($_REQUEST['subscriber']));				
					$subscriber = stripslashes_deep($subscriber);
					$subscriber['justsubscribe'] = true;
					$email = $subscriber['email'];
					$confirmation_subject = stripslashes($_REQUEST['confirmation_subject']);
					$confirmation_email = stripslashes($_REQUEST['confirmation_email']);
					$import_preventbu = $_REQUEST['import_preventbu'];
					$import_overwrite = $_REQUEST['import_overwrite'];				
					
					//if ($current_id = $Subscriber -> email_exists($email) && $import_overwrite == "Y") {
					if (true) {
						$Db -> model = $Unsubscribe -> model;
						if (empty($import_preventbu) || $import_preventbu == "N" || ($import_preventbu == "Y" && !$Db -> find(array('email' => $email)))) {
							$Db -> model = $Bounce -> model;
							if (empty($import_preventbu) || $import_preventbu == "N" || ($import_preventbu == "Y" && !$Db -> find(array('email' => $email)))) {
								if (!empty($subscriber)) {
									$subscriber['fromregistration'] = true;
									$subscriber['username'] = $email;
									
									$skipsubscriberupdate = false;
									
									if ($current_id = $Subscriber -> email_exists($email)) {
										$subscriber['id'] = $current_id;
										
										if (empty($import_overwrite) || $import_overwrite == "N") {
											$skipsubscriberupdate = true;
										} else {												
											$skipsubscriberupdate = false;
										}
									} else {									
										$skipsubscriberupdate = false;
										$subscriber['id'] = false;
									}
								
									if ($Subscriber -> save($subscriber, true, false, $skipsubscriberupdate)) {
										$subscriber_id = $Subscriber -> insertid;
										$afterlists = $subscriber['afterlists'];
									
										if (!empty($afterlists)) {
											foreach ($afterlists as $mailinglist) {
												$sl_data = array('subscriber_id' => $subscriber_id, 'list_id' => $mailinglist['id'], 'paid' => $mailinglist['paid'], 'active' => $mailinglist['active']);
												$sl_query = $SubscribersList -> save($sl_data, false, true);
												
												if (!empty($sl_query)) {
													$wpdb -> query($sl_query);
												}
											}
										}
										
										if ($subscriber['active'] == "N") {	
											if (!empty($subscriber['mailinglists'])) {	
												$allmailinglists = $subscriber['mailinglists'];
												$Db -> model = $Subscriber -> model;
												$subscriber = $Db -> find(array('id' => $subscriber_id));
											
												foreach ($allmailinglists as $mailinglist_id) {						
													$subscriber -> mailinglist_id = $mailinglist_id;
													$subject = $confirmation_subject;
													$message = $confirmation_email;
													
													$Queue -> save(
														$subscriber,
														false, 
														$subject, 
														$message, 
														false, 
														false, 
														false, 
														false, 
														$this -> default_theme_id('system'), 
														false
													);
												}
											}
										}
										
										$success = "Y<|>" . $email;
										$message = __('Subscriber was imported.', $this -> plugin_name);
									} else {
										$success = "N<|>" . $email;
										$message = implode(" | ", $Subscriber -> errors);
									}
								} else {
									$success = "N<|>" . $email;
									$message = __('No subscriber data is available.', $this -> plugin_name);
								}
							} else {
								$success = "N<|>" . $email;
								$message = __('Subscriber has previously bounced', $this -> plugin_name);
							}
						} else {
							$success = "N<|>" . $email;
							$message = __('Subscriber has previously unsubscribed', $this -> plugin_name);
						}
					} else {
						$success = "Y<|>" . $email;
						$message = __('Subscriber exists, not updating/overwriting', $this -> plugin_name);
					}
					
					echo $success . "<|>" . $message . "<||>";
				}				
			} else {
				$success = "N<|>" . $email;
				$message = __('No data was posted, blank row?', $this -> plugin_name);
				echo $success . "<|>" . $message . "<||>";
			}
			
			exit();
			die();
		}
	    
	    function ajax_admin_mode() {
		    define('DOING_AJAX', true);
		    define('SHORTINIT', true);
		    
		    $user_id = get_current_user_id();
		    $mode = (empty($_POST['mode'])) ? 'standard' : $_POST['mode'];		    
		    update_user_option($user_id, 'newsletters_admin_mode', $mode);
		    
		    exit();
		    die();
	    }
	    
	    function ajax_mailinglist_save() {
		    define('DOING_AJAX', true);
		    define('SHORTINIT', true);
		    
		    global $wpdb, $Db, $Mailinglist, $SubscribersList;
		    
		    $success = false;
		    $errors = false;
		    
		    $fielddiv = $_GET['fielddiv'];
		    $fieldname = $_GET['fieldname'];
		    
		    if (!empty($_POST)) {
			    $_POST['Mailinglist']['privatelist'] = "N";
			    
			    if ($Mailinglist -> save($_POST)) {
				    $success = true;
			    } else {
				    $errors = $Mailinglist -> errors;
			    }
			    
			    $checklist = '';
			    if ($mailinglists = $Mailinglist -> select(true)) {
				    foreach ($mailinglists as $id => $title) {
						$Db -> model = $SubscribersList -> model;
						$checklist .= '<label><input type="checkbox" name="' . $fieldname . '[]" value="' . $id . '" id="checklist' . $id . '" /> ' . __($title) . ' (' . $Db -> count(array('list_id' => $id)) . ' ' . __('subscribers', $this -> plugin_name) . ')</label><br/>';
				    }
			    }
		    
			    $output = array(
				    'success'			=>	$success,
				    'errors'			=>	$errors,
				    'blocks'			=>	array(
					    'form'				=>	$this -> render('mailinglists' . DS . 'save-ajax', array('fielddiv' => $fielddiv, 'fieldname' => $fieldname, 'ajax' => true, 'errors' => $errors, 'success' => $success), false, 'admin'),
					    'checklist'			=>	$checklist,
				    ),
			    );
			    
			    echo json_encode($output);
		    } else {
			    $this -> render('mailinglists' . DS . 'save-ajax', array('fielddiv' => $fielddiv, 'fieldname' => $fieldname), true, 'admin');
		    }
		    
		    exit();
		    die();
	    }
	    
	    function ajax_posts_by_category() {
		    define('DOING_AJAX', true);
		    define('SHORTINIT', true);
		    
		    header('Content-Type: application/json');
		    
		    $posts_by_category = array();
			
			$arguments = array(
				'numberposts'			=>	"-1",
				'orderby'				=>	'post_title',
				'order'					=>	"ASC",
				'post_type'				=>	"post",
				'post_status'			=>	"publish",
			);
			
			if (!empty($_REQUEST['cat_id']) && $_REQUEST['cat_id'] > 0) {
				$arguments['category'] = $_REQUEST['cat_id'];	
			}
			
			if (!empty($_REQUEST['post_type'])) {
				$arguments['post_type'] = $_REQUEST['post_type'];
			}
			
			if ($posts = get_posts($arguments)) {	
				$posts_by_category[] = array('text' => __('- Select -', $this -> plugin_name), 'value' => false);
										
				foreach ($posts as $post) {
					if ($this -> language_do()) {
						$posts_by_category[] = array('text' => __($this -> language_use($_REQUEST['language'], $post -> post_title, false)), 'value' => $post -> ID);
					} else {
						$posts_by_category[] = array('text' => __($post -> post_title), 'value' => $post -> ID);
					}
				}
			} else {
				$posts_by_category[] = array('text' => __('No posts in this category', $this -> plugin_name), 'value' => false);
			}
			
			echo json_encode($posts_by_category);
		    
		    exit();
		    die();
	    }
	    
	    function ajax_getposts() {
	    	define('DOING_AJAX', true);
			define('SHORTINIT', true);
	    
		    if (!empty($_POST)) {
			    $arguments = array(
			    	'numberposts'			=>	"-1",
			    	'orderby'				=>	"post_title",
			    	'order'					=>	"ASC",
			    	'post_type'				=>	"post",
			    	'post_status'			=>	"publish",
			    );
			    
			    if (!empty($_POST['posttype'])) { $arguments['post_type'] = $_POST['posttype']; }
			    if (!empty($_POST['category'])) { $arguments['category'] = $_POST['category']; }
			    
			    if ($posts = get_posts($arguments)) {
				    ?>
				    
				    <ul class="insertfieldslist">
				    	<li>
				    		<span class="insertfieldslistcheckbox">
				    			<input onclick="jqCheckAll(this, false, 'insertposts');" type="checkbox" name="checkall_insertposts" id="checkall_insertposts" value="1" />
				    		</span>
				    		<span class="">
				    			<label for="checkall_insertposts" style="font-weight:bold;"><?php _e('Select All', $this -> plugin_name); ?></label>
				    		</span>
				    	</li>
				    	<?php foreach ($posts as $post) : ?>
				    		<li>
				    			<span class="insertfieldslistcheckbox"><input type="checkbox" name="insertposts[]" value="<?php echo $post -> ID; ?>" id="insertposts_<?php echo $post -> ID; ?>" /></span>
				    			
				    			<?php if ($this -> language_do()) : ?>
				    				<span class="insertfieldslistbutton"><a href="" onclick="insert_post('<?php echo $post -> ID; ?>', false); return false;" class="button button-secondary press"><?php echo $this -> language_use($_POST['language'], $post -> post_title, false); ?></a></span>
				    			<?php else : ?>
				    				<span class="insertfieldslistbutton"><a href="" onclick="insert_post('<?php echo $post -> ID; ?>', false); return false;" class="button button-secondary press"><?php echo __($post -> post_title); ?></a></span>
				    			<?php endif; ?>
				    		</li>
				    	<?php endforeach; ?>
				    </ul>
				    
				    <span><input onclick="insert_single_multiple();" class="button button-primary" type="button" name="insert" value="<?php _e('Insert Selected', $this -> plugin_name); ?>" /></span>
				    
				    <?php
			    }
		    }
		    
		    exit();
		    die();
	    }
	    
	    function ajax_api_newkey() {
		    define('DOING_AJAX', true);
		    define('SHORTINIT', true);
		
			$key = strtoupper(md5(time()));
			$this -> update_option('api_key', $key);
			echo $key;
			
			exit();
			die();
		}
	    
	    function ajax_welcomestats() {
	    	define('DOING_AJAX', true);
			define('SHORTINIT', true);
		    global $wpdb, $Html, $Subscriber, $Email, $Bounce, $Unsubscribe;
		    
		    $chart = (empty($_GET['chart'])) ? "bar" : $_GET['chart'];
		    $type = (empty($_GET['type'])) ? "days" : $_GET['type'];
			$fromdate = (empty($_GET['from'])) ? date_i18n("Y-m-d", strtotime("-13 days")) : $_GET['from'];
			$todate = (empty($_GET['to'])) ? date_i18n("Y-m-d", time()) : $_GET['to'];
			$history_id = (empty($_GET['history_id'])) ? false : $_GET['history_id'];
			
			$history_condition = (!empty($history_id)) ? " `history_id` = '" . $history_id . "' AND" : false;
			$history_condition = apply_filters('newsletters_stats_history_condition', $history_condition, $type, $fromdate, $todate, $history_id);
			$subscribers_condition = apply_filters('newsletters_stats_subscriber_condition', false, $type, $fromdate, $todate, $history_id);
			$clicks_condition = apply_filters('newsletters_stats_clicks_condition', false, $type, $fromdate, $todate, $history_id);
			$emails_condition = apply_filters('newsletters_stats_emails_condition', false, $type, $fromdate, $todate, $history_id);
			$bounces_condition = apply_filters('newsletters_stats_bounces_condition', false, $type, $fromdate, $todate, $history_id);
			$unsubscribes_condition = apply_filters('newsletters_stats_unsubscribes_condition', false, $type, $fromdate, $todate, $history_id);
			$reads_condition = apply_filters('newsletters_stats_reads_condition', false, $type, $fromdate, $todate, $history_id);
			
			switch ($type) {
				case 'years'			:
					// Subscribers
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`created`)";
				    $records = $wpdb -> get_results($query);
				    
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("Y", strtotime($record -> date))] = $record -> subscriberscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }
				    
				    // Clicks
				    $query = "SELECT COUNT(`id`) as `clickscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $this -> Click() -> table . "` WHERE" . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
					$records = $wpdb -> get_results($query);
					
				    $clicks_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$clicks_array[date_i18n("Y", strtotime($record -> date))] = $record -> clickscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> clickscount > $y_max)) {
								$y_max = $record -> clickscount;
							}
					    }
				    }
				    
				    // Emails Sent
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE" . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`created`)";
				    $records = $wpdb -> get_results($query);
				    
				    $emails_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("Y", strtotime($record -> date))] = $record -> emailscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }
				    
				    // Bounces
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE" . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`modified`)";
				    $records = $wpdb -> get_results($query);
				    
				    $bounces_array = array();
				    if (!empty($records)) {	    
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("Y", strtotime($record -> date))] = $record -> bouncecount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }
				    
				    // Unsubscribes
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE" . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`modified`)";
				    $records = $wpdb -> get_results($query);
				    
				    $unsubscribes_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("Y", strtotime($record -> date))] = $record -> unsubscribescount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }
				    
				    // Reads
				    $query = "SELECT COUNT(id) as `readscount`, DATE(read_date) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE" . $history_condition . " `read` = 'Y' AND CAST(`read_date` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`read_date`)";
				    $records = $wpdb -> get_results($query);
				    
				    $reads_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$reads_array[date_i18n("Y", strtotime($record -> date))] = $record -> readscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> readscount;
							}
					    }
				    }
				    
				    $dates_data = array();
				    $subscribers_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    $reads_data = array();
				    
				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$yearsdiff = round(abs($tostamp - $fromstamp) / (60 * 60 * 24 * 365));
					
					$d = new DateTime($todate);
					$d -> modify("next year");
				    
				    $j = 0;
				    for ($i = 0; $i <= $yearsdiff; $i++) {
					    $d -> modify("previous year");
				    	//$datestring = date_i18n("Y", strtotime("-" . $i . " years", $tostamp));
					    //$dates_data[$j] = date_i18n("Y", strtotime("-" . $i . " years", $tostamp));
					    $datestring = $d -> format("Y");
					    $dates_data[$j] = $d -> format("Y");
					    
					    if (empty($history_id)) {
						    if (!empty($subscribers_array[$datestring])) {
							    $subscribers_data[$j] = $subscribers_array[$datestring];
						    } else {
							    $subscribers_data[$j] = 0;
						    }
						}
						
						if (!empty($clicks_array[$datestring])) {
						    $clicks_data[$j] = $clicks_array[$datestring];
					    } else {
						    $clicks_data[$j] = 0;
					    }
					    
					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }
					    
					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }
					    
					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }
					    
					    if (!empty($reads_array[$datestring])) {
						    $reads_data[$j] = $reads_array[$datestring];
					    } else {
						    $reads_data[$j] = 0;
					    }
					    
					    $j++;
				    }
				    
				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $bounces_data = array_reverse($bounces_data);
				    $clicks_data = array_reverse($clicks_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
				    $reads_data = array_reverse($reads_data);
					break;
				case 'months'			:
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`created`)";
					$records = $wpdb -> get_results($query);
					
				    $subscribers_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("mY", strtotime($record -> date))] = $record -> subscriberscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }
				    
				    // Clicks
				    $query = "SELECT COUNT(`id`) as `clickscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $this -> Click() -> table . "` WHERE" . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
					$records = $wpdb -> get_results($query);
					
				    $clicks_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$clicks_array[date_i18n("mY", strtotime($record -> date))] = $record -> clickscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> clickscount > $y_max)) {
								$y_max = $record -> clickscount;
							}
					    }
				    }
				    
				    // Emails Sent
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE" . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`created`)";
				    $records = $wpdb -> get_results($query);
				    
				    $emails_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("mY", strtotime($record -> date))] = $record -> emailscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }
				    
				    // Bounces
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE" . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`modified`)";
				    $records = $wpdb -> get_results($query);
				    
				    $bounces_array = array();
				    if (!empty($records)) {	    
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("mY", strtotime($record -> date))] = $record -> bouncecount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }
				    
				    // Unsubscribes
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE" . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`modified`)";
				    $records = $wpdb -> get_results($query);
				    
				    $unsubscribes_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("mY", strtotime($record -> date))] = $record -> unsubscribescount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }
				    
				    // Reads
				    $query = "SELECT COUNT(id) as `readscount`, DATE(read_date) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE" . $history_condition . " `read` = 'Y' AND CAST(`read_date` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`read_date`)";
				    $records = $wpdb -> get_results($query);
				    
				    $reads_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$reads_array[date_i18n("mY", strtotime($record -> date))] = $record -> readscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> readscount;
							}
					    }
				    }
				    
				    $dates_data = array();
				    $subscribers_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    $reads_data = array();
				    
				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$monthsdiff = round(abs($tostamp - $fromstamp) / 2628000);
					
					$d = new DateTime($todate);
					//$d -> modify("next month");
				    
				    $j = 0;
				    for ($i = 0; $i <= $monthsdiff; $i++) {
						$d -> modify("previous month");
					    
				    	//$datestring = date_i18n("mY", strtotime("-" . $i . " month", $tostamp), false);
					    //$dates_data[$j] = date_i18n("F Y", strtotime("-" . $i . " month", $tostamp), false);
					    $datestring = $d -> format("mY");
					    $dates_data[$j] = $d -> format("F Y");
					    
					    if (empty($history_id)) {
						    if (!empty($subscribers_array[$datestring])) {
							    $subscribers_data[$j] = $subscribers_array[$datestring];
						    } else {
							    $subscribers_data[$j] = 0;
						    }
						}
						
						if (!empty($clicks_array[$datestring])) {
						    $clicks_data[$j] = $clicks_array[$datestring];
					    } else {
						    $clicks_data[$j] = 0;
					    }
					    
					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }
					    
					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }
					    
					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }
					    
					    if (!empty($reads_array[$datestring])) {
						    $reads_data[$j] = $reads_array[$datestring];
					    } else {
						    $reads_data[$j] = 0;
					    }
					    
					    $j++;
				    }
				    
				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $bounces_data = array_reverse($bounces_data);
				    $clicks_data = array_reverse($clicks_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
				    $reads_data = array_reverse($reads_data);
					break;
				case 'days'				:
				default 				:	
				
					// Subscribers			
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE " . $subscribers_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
					$records = $wpdb -> get_results($query);
					
				    $subscribers_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("dmY", strtotime($record -> date))] = $record -> subscriberscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }
				    
				    // Clicks
				    $query = "SELECT COUNT(`id`) as `clickscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $this -> Click() -> table . "` WHERE " . $clicks_condition . " " . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
					$records = $wpdb -> get_results($query);
					
				    $clicks_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$clicks_array[date_i18n("dmY", strtotime($record -> date))] = $record -> clickscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> clickscount > $y_max)) {
								$y_max = $record -> clickscount;
							}
					    }
				    }
				    
				    // Emails Sent
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE " . $emails_condition . " " . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
				    $records = $wpdb -> get_results($query);
				    
				    $emails_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("dmY", strtotime($record -> date))] = $record -> emailscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }
				    
				    // Bounces
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE " . $bounces_condition . " " . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`modified`)";
				    $records = $wpdb -> get_results($query);
				    
				    $bounces_array = array();
				    if (!empty($records)) {	    
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("dmY", strtotime($record -> date))] = $record -> bouncecount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }
				    
				    // Unsubscribes
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE " . $unsubscribes_condition . " " . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`modified`)";
				    $records = $wpdb -> get_results($query);
				    
				    $unsubscribes_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("dmY", strtotime($record -> date))] = $record -> unsubscribescount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }
				    
				    // Reads
				    $query = "SELECT COUNT(id) as `readscount`, DATE(read_date) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE " . $reads_condition . " " . $history_condition . " `read` = 'Y' AND CAST(`read_date` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`read_date`)";
				    $records = $wpdb -> get_results($query);
				    
				    $reads_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$reads_array[date_i18n("dmY", strtotime($record -> date))] = $record -> readscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> readscount;
							}
					    }
				    }
				    
				    $dates_data = array();
				    $subscribers_data = array();
				    $clicks_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    $reads_data = array();
				    
				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$daysdiff = round(abs($tostamp - $fromstamp) / 86400);
					
					$d = new DateTime($todate);
					$d -> modify("next day");
				    
				    $j = 0;
				    for ($i = 0; $i <= $daysdiff; $i++) {
				    	//$datestring = date_i18n("m-d", strtotime("-" . $i . " days", $tostamp));
					    //$dates_data[$j] = date_i18n("M j", strtotime("-" . $i . " days", $tostamp));
					    $d -> modify("previous day");
					    $datestring = $d -> format("dmY");
					    $dates_data[$j] = $d -> format("M j");
					    
					    if (empty($history_id)) {
						    if (!empty($subscribers_array[$datestring])) {
							    $subscribers_data[$j] = $subscribers_array[$datestring];
						    } else {
							    $subscribers_data[$j] = 0;
						    }
						}
						
						if (!empty($clicks_array[$datestring])) {
						    $clicks_data[$j] = $clicks_array[$datestring];
					    } else {
						    $clicks_data[$j] = 0;
					    }
					    
					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }
					    
					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }
					    
					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }
					    
					    if (!empty($reads_array[$datestring])) {
						    $reads_data[$j] = $reads_array[$datestring];
					    } else {
						    $reads_data[$j] = 0;
					    }
					    
					    $j++;
				    }
				    
				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $clicks_data = array_reverse($clicks_data);
				    $bounces_data = array_reverse($bounces_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
				    $reads_data = array_reverse($reads_data);
			}
			
			$data = array();
		    $data['labels'] = $dates_data;
		    $data['datasets'] = array();
		    
		    $data['datasets'][] = array(
				'label'					=>	__('Emails Sent', $this -> plugin_name),
	            'fillColor'				=>	"#4D5360",
	            'highlightFill'			=>	"#616774",
	            'strokeColor'			=>	"#4D5360",
	            'pointColor'			=>	"#4D5360",
	            'pointStrokeColor'		=>	"#4D5360",
	            'pointHighlightFill'	=>	"#616774",
	            'pointHighlightStroke'	=>	"#616774",
	            'data'					=>	$emails_data,
			);
		    
		    if (empty($history_id)) {
			    $data['datasets'][] = array(
					'label'					=>	__('Subscribers', $this -> plugin_name),
		            'fillColor'				=>	"#4679bf", //"#46BFBD",
		            'highlightFill'			=>	"#4679bf", //"#5AD3D1",
		            'strokeColor'			=>	"#4679bf",
		            'pointColor'			=>	"#4679bf",
		            'pointStrokeColor'		=>	"#4679bf",
		            'pointHighlightFill'	=>	"#4679bf",
		            'pointHighlightStroke'	=>	"#4679bf",
		            'data'					=>	$subscribers_data,
				);
			}
			
			$data['datasets'][] = array(
				'label'					=>	__('Clicks', $this -> plugin_name),
	            'fillColor'				=>	"#949FB1",
	            'highlightFill'			=>	"#A8B3C5",
	            'strokeColor'			=>	"#949FB1",
	            'pointColor'			=>	"#949FB1",
	            'pointStrokeColor'		=>	"#949FB1",
	            'pointHighlightFill'	=>	"#A8B3C5",
	            'pointHighlightStroke'	=>	"#A8B3C5",
	            'data'					=>	$clicks_data,
			);
			
			$data['datasets'][] = array(
				'label'					=>	__('Unsubscribes', $this -> plugin_name),
	            'fillColor'				=>	"#FDB45C",
	            'highlightFill'			=>	"#FFC870",
	            'strokeColor'			=>	"#FDB45C",
	            'pointColor'			=>	"#FDB45C",
	            'pointStrokeColor'		=>	"#FDB45C",
	            'pointHighlightFill'	=>	"#FFC870",
	            'pointHighlightStroke'	=>	"#FFC870",
	            'data'					=>	$unsubscribes_data,
			);
			
			$data['datasets'][] = array(
				'label'					=>	__('Reads', $this -> plugin_name),
	            'fillColor'				=>	"#46BFBD",
	            'highlightFill'			=>	"#5AD3D1",
	            'strokeColor'			=>	"#46BFBD",
	            'pointColor'			=>	"#46BFBD",
	            'pointStrokeColor'		=>	"#46BFBD",
	            'pointHighlightFill'	=>	"#5AD3D1",
	            'pointHighlightStroke'	=>	"#5AD3D1",
	            'data'					=>	$reads_data,
			);
			
			$data['datasets'][] = array(
				'label'					=>	__('Bounces', $this -> plugin_name),
				'fillColor'				=>	"#F7464A",
				'highlightFill'			=>	"#FF5A5E",
				'strokeColor'			=>	"#F7464A",
	            'pointColor'			=>	"#F7464A",
	            'pointStrokeColor'		=>	"#F7464A",
	            'pointHighlightFill'	=>	"#FF5A5E",
	            'pointHighlightStroke'	=>	"#FF5A5E",
				'data'					=>	$bounces_data,
			);
		    
		    echo json_encode($data);
		    
		    exit();
		    die();
	    }
	    
	    /* Forms Ajax */
	    function ajax_forms_createform() {
		    global $Field;
		    
		    define('DOING_AJAX', true);
		    define('SHORTINIT', true);
		    
		    $ajax = false;
		    $success = false;
		    $errors = false;
		    
		    if (!empty($_POST)) {
			    if ($this -> Subscribeform() -> save($_POST[$this -> Subscribeform() -> model])) {
				    $success = true;				    
				    $insertid = $this -> Subscribeform() -> insertid;
				    
				    // Create the default email address and mailing list fields
				    $emailfield = $Field -> email_field();
				    $fieldform_data = array(
						'id'						=>	false,
						'form_id'					=>	$insertid,
						'field_id'					=>	$emailfield -> id,
						'order'						=>	1,
						'label'						=>	false,
						'caption'					=>	false,
						'placeholder'				=>	false,
						'required'					=>	1,
					);
					
					$this -> FieldsForm() -> save($fieldform_data);
					
					$listfield = $Field -> list_field();
					$fieldform_data = array(
						'id'						=>	false,
						'form_id'					=>	$insertid,
						'field_id'					=>	$listfield -> id,
						'order'						=>	2,
						'label'						=>	false,
						'caption'					=>	false,
						'placeholder'				=>	false,
						'required'					=>	1,
					);
					
					$this -> FieldsForm() -> save($fieldform_data);
			    }
			    
			    $ajax = true;
			    $errors = $this -> Subscribeform() -> errors;
		    }
		    
		    $this -> render('forms' . DS . 'createform', array('ajax' => $ajax, 'success' => $success, 'errors' => $errors), true, 'admin');
		    
		    exit();
		    die();
	    }
	    
	    function ajax_forms_addfield() {
		    define('DOING_AJAX', true);
		    define('SHORTINIT', true);
		    
		    global $wpdb, $Db, $Field;
		    $Db -> model = $Field -> model;
		    $field = $Db -> find(array('id' => $_POST['id']));
		    
		    global $Metabox;
		    $form_field = new stdClass();
		    $form_field -> field = $field;
		    $form_field -> field_id = $field -> id;
		    $metabox = array('args' => array('form_field' => $form_field));
		    
		    ob_start();
		    $Metabox -> forms_field(false, $metabox);
		    $content = ob_get_clean();
		    $this -> render('forms' . DS . 'field', array('field' => $field, 'form_field' => $form_field, 'content' => $content), true, 'admin');
		    
		    //$this -> render('forms' . DS . 'field', array('field' => $field), true, 'admin');
		    
		    exit();
		    die();
	    }
	    
	    function ajax_forms_deletefield() {
		    define('DOING_AJAX', true);
		    define('SHORTINIT', true);
		    
		    if (!empty($_POST['field_id']) && !empty($_POST['form_id'])) {
		   		$this -> FieldsForm() -> delete_all(array('field_id' => $_POST['field_id'], 'form_id' => $_POST['form_id']));
		   	}
		    
		    exit();
		    die();
	    }
		
		function ajax_setvariables() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$this -> render('setvariables', array('noinsert' => true), true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_change_themefolder() {
			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			$message = false;
			if (!empty($_POST['themefolder'])) {
				$this -> update_option('theme_folder', $_POST['themefolder']);
				$this -> delete_all_cache('all');
				$this -> theme_folder_functions($_POST['themefolder']);
				$message = __('Theme folder has been changed, please reconfigure styles/scripts below', $this -> plugin_name);
			}
			
			$this -> render('settings' . DS . 'defaultscriptsstyles', array('successmessage' => $message), true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_delete_option() {
			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			$success = false;
			
			if (!empty($_REQUEST['id'])) {
				if ($this -> Option() -> delete($_REQUEST['id'])) {
					$success = true;
				}
			}
			
			echo $success;
			
			exit();
			die();
		}
		
		function ajax_pause_queue() {
			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			$success = false;
			
			if (!empty($_POST['status'])) {
				$status = $_POST['status'];
				
				if ($this -> update_option('queue_status', $status)) {
					$success = true;
					$this -> scheduling();
					$this -> delete_option('hidemessage_queue_status');
				}
			}
			
			echo $success;
			
			exit();
			die();
		}
		
		function ajax_executemultiple() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber, $HistoriesAttachment;
			$subscribers = (object) stripslashes_deep($_REQUEST['subscribers']);
			
			if (!empty($subscribers)) {
				$historyquery = "SELECT id, message, subject FROM " . $wpdb -> prefix . $this -> History() -> table . " WHERE id = '" . $_REQUEST['history_id'] . "' LIMIT 1";
				$history = $wpdb -> get_row($historyquery);
			
				if (!empty($history)) {		
					foreach ($subscribers as $subscriber_request) {	
						$subscriber_request = (object)$subscriber_request;
								
						if (!empty($subscriber_request -> user_id)) {
							$user = $this -> userdata($subscriber_request -> user_id);
							$email = $user -> user_email;
							$eunique = md5($user -> ID . $history -> id . date_i18n("YmdH"));
							$subscriber = false;
						} else {
							$subscriber = $Subscriber -> get($subscriber_request -> id, false);
							$email = $subscriber -> email;
							$subscriber -> mailinglist_id = $subscriber_request -> mailinglist_id;
							$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, $subscriber_request -> mailinglists);
							$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $history -> id . date_i18n("YmdH"));
							$user = false;
						}
						
						$content = $history -> message;
						$subject = stripslashes($history -> subject);
						$history_id = $_REQUEST['history_id'];
						$post_id = $_REQUEST['post_id'];
						$theme_id = $_REQUEST['theme_id'];
						$shortlinks = true;
						
						$newattachments = array();
						$Db -> model = $HistoriesAttachment -> model;
						if ($attachments = $Db -> find_all(array('history_id' => $history_id))) {
							foreach ($attachments as $attachment) {
								$newattachments[] = array(
									'id'					=>	$attachment -> id,
									'title'					=>	$attachment -> title,
									'filename'				=>	$attachment -> filename,
								);	
							}
						}
						
						$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id, 'post_id' => $post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $theme_id, true);
						
						if ($this -> execute_mail($subscriber, $user, $subject, $message, $newattachments, $history_id, $eunique, true, "newsletter")) {
							$success = "Y<|>" . $email . "<|>" . __('Success', $this -> plugin_name) . "<||>";
						} else {
							global $mailerrors;
							$success = "N<|>" . $email . "<|>" . strip_tags($mailerrors) . "<||>";
						}
						
						echo $success;
					}
				} else {
					$success = "N<|>" . $email . "<|>" . __('History email could not be read', $this -> plugin_name) . "<||>";
					echo $success;
				}
			} else {
				$success = "N<|>" . $email . "<|>" . __('No data was posted', $this -> plugin_name) . "<||>";
				echo $success;
			}
		
			exit();
			die();
		}
		
		function ajax_queuemultiple() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber, $Queue, $HistoriesAttachment;
			
			$subscribers = stripslashes_deep($_REQUEST['subscribers']);
			
			if (!empty($subscribers)) {
				$historyquery = "SELECT id, message, subject FROM " . $wpdb -> prefix . $this -> History() -> table . " WHERE id = '" . $_REQUEST['history_id'] . "' LIMIT 1";
				$history = $wpdb -> get_row($historyquery);
			
				if (!empty($history)) {	
					foreach ($subscribers as $subscriber_request) {
						$subscriber_request = (object) $subscriber_request;
									
						if (!empty($subscriber_request -> user_id)) {
							$subscriber = false;
							$user = $this -> userdata($subscriber_request -> user_id);
							$email = $user -> user_email;
							$eunique = md5($user -> ID . $history -> id . date_i18n("YmdH"));
						} else {
							$user = false;
							$subscriber = $Subscriber -> get($subscriber_request -> id, false);
							$email = $subscriber -> email;
							$subscriber -> mailinglist_id = $subscriber_request -> mailinglist_id;
							$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, $subscriber_request -> mailinglists);
							$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $history -> id . date_i18n("YmdH"));
						}
														
						$content = $history -> message;
						$subject = stripslashes($history -> subject);
						$history_id = $_REQUEST['history_id'];
						$post_id = $_REQUEST['post_id'];
						$theme_id = $_REQUEST['theme_id'];
						$senddate = $_REQUEST['senddate'];
						$shortlinks = true;
						
						$newattachments = array();
						$Db -> model = $HistoriesAttachment -> model;
						if ($attachments = $Db -> find_all(array('history_id' => $history_id))) {
							foreach ($attachments as $attachment) {
								$newattachments[] = array(
									'id'					=>	$attachment -> id,
									'title'					=>	$attachment -> title,
									'filename'				=>	$attachment -> filename,
								);	
							}
						}
						
						if ($Queue -> save($subscriber, $user, $subject, $content, $newattachments, $post_id, $history_id, false, $theme_id, $senddate)) {
							$success = "Y<|>" . $email . "<|>" . __('Success', $this -> plugin_name) . "<||>";
						} else {
							$success = "N<|>" . $email . "<|>" . $Queue -> errors[0] . "<||>";
						}
						
						echo $success;
					} 
				} else {
					$success = "N<|>" . $email . "<|>" . __('History email could not be read', $this -> plugin_name) . "<||>";
					echo $success;
				}
			} else {
				$success = "N<|>" . $email . "<|>" . __('No data was posted', $this -> plugin_name) . "<||>";
				echo $success;
			}
		
			exit();
			die();
		}
		
		function ajax_exportmultiple() {		
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $Html;
			$exportfilename = $_REQUEST['exportfile'];
			$exportfilepath = $Html -> uploads_path() . '/' . $this -> plugin_name . '/export/';
			$exportfilefull = $exportfilepath . $exportfilename;
				
			if ($fp = fopen($exportfilefull, "a")) {				
				$csvdelimiter = $this -> get_option('csvdelimiter');
				$delimiter = (empty($_REQUEST['delimiter'])) ? $csvdelimiter : $_REQUEST['delimiter'];
				$headings = $_REQUEST['headings'];
				$subscribers = stripslashes_deep($_REQUEST['subscribers']);
				
				$headings_keys = array();
				foreach ($headings as $hkey => $hval) {
					$headings_keys[$hkey] = '';
				}
				
				if (!empty($subscribers)) {
					foreach ($subscribers as $subscriber) {
						$subscriber = array_merge($headings_keys, $subscriber);
						
						if (!empty($subscriber)) {
							fputcsv($fp, $subscriber, $delimiter, '"');
						}
						
						echo $subscriber['email'] . "<|>";
					}
				}
				
				fclose($fp);
			}
			
			exit();
			die();
		}
		
		function ajax_subscribe() {			
			global $Subscriber, $Mailinglist, $Html;
			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$widget_id = $_GET['widget_id'];
			$number = $_GET['number'];
			$instance = $this -> widget_instance($number);
			
			if (!empty($_POST['instance'])) {
				$r = wp_parse_args($_POST['instance'], $instance);
				$instance = $r;
			}
			
			$action = ($this -> language_do()) ? $this -> language_converturl($_SERVER['REQUEST_URI'], $instance['language']) : $_SERVER['REQUEST_URI'];
			$action = $Html -> retainquery($this -> pre . 'method=optin', $action) . '#' . $widget_id;
			
			if ($subscriber_id = $Subscriber -> optin($_POST)) {
				if (empty($_POST['form_id'])) {
					echo '<div class="newsletters-acknowledgement">' . wpautop(__($instance['acknowledgement'])) . '</div>';
					
					if ($paidlist_id = $Mailinglist -> has_paid_list($_POST['list_id'])) {
						$subscriber = $Subscriber -> get($subscriber_id, false);
						$paidlist = $Mailinglist -> get($paidlist_id, false);
						//$this -> paidsubscription_form($subscriber, $paidlist, true);
						$this -> redirect($Html -> retainquery('method=paidsubscription&subscriber_id=' . $subscriber -> id . '&list_id=' . $paidlist -> id . '&extend=0', $this -> get_managementpost(true)));
					}
				
					if ($this -> get_option('subscriberedirect') == "Y") {
						$subscriberedirecturl = $this -> get_option('subscriberedirecturl');
						
						if (!empty($_POST['list_id']) && (!is_array($_POST['list_id']) || count($_POST['list_id']) == 1)) {
							if ($subscribelist = $Mailinglist -> get($_POST['list_id'][0])) {
								if (!empty($subscribelist -> subredirect)) {
									$subscriberedirecturl = $subscribelist -> subredirect;
								}
							}
						}
						
						$this -> redirect($subscriberedirecturl, false, false, true);
					}
				} elseif (!empty($_POST['form_id'])) {
					if ($form = $this -> Subscribeform() -> find(array('id' => $_POST['form_id']))) {
						
						if ($paidlist_id = $Mailinglist -> has_paid_list($_POST['list_id'])) {
							$subscriber = $Subscriber -> get($subscriber_id, false);
							$paidlist = $Mailinglist -> get($paidlist_id, false);
							//$this -> paidsubscription_form($subscriber, $paidlist, true);
							$this -> redirect($Html -> retainquery('method=paidsubscription&subscriber_id=' . $subscriber -> id . '&list_id=' . $paidlist -> id . '&extend=0', $this -> get_managementpost(true)));
						}
						
						if (empty($form -> confirmationtype) || $form -> confirmationtype == "message") {
							if (!empty($form -> confirmation_message)) {
								echo '<div class="newsletters-acknowledgement">' . wpautop(__($form -> confirmation_message)) . '</div>';
							}
						} elseif ($form -> confirmationtype == "redirect") {
							if (!empty($form -> confirmation_redirect)) {
								$this -> redirect($form -> confirmation_redirect, false, false, true);
							}
						}
					}
				}
			} else {			
				$errors = $Subscriber -> errors;
				if (empty($_POST['form_id'])) {
					$this -> render('widget', array('action' => $action, 'widget' => $_GET['widget'], 'errors' => $errors, 'args' => $widget, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), true, 'default');
				} elseif (!empty($_POST['form_id'])) {
					if ($form = $this -> Subscribeform() -> find(array('id' => $_POST['form_id']))) {
						$this -> render('subscribe', array('form' => $form, 'errors' => $Subscriber -> errors), true, 'default');
					}
				}
			}
		
			exit();
			die();
		}
		
		function ajax_template_iframe() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
		
			global $Db;
			$template = $this -> Template() -> find(array('id' => $_REQUEST['id']));
			$this -> render('templates' . DS . 'iframe', array('template' => $template), true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_form_preview() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			if (!empty($_GET['id'])) {
				if ($form = $this -> Subscribeform() -> find(array('id' => $_GET['id']))) {
					?>
					
					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
							<title><?php echo urldecode($_GET['title']); ?></title>
                            
                            <?php
                            
							wp_enqueue_script('jquery');
							wp_enqueue_script($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/js/' . $this -> plugin_name . '.js', array('jquery'), '1.0', false);	
							wp_enqueue_style($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/views/' . $this -> get_option('theme_folder') . '/css/style.css', false, $this -> version, "all");
							
							?>
                            
                            <?php wp_head(); ?>
						</head>
						<body style="background:none;">
							<div class="entry">
								<?php $this -> render('subscribe', array('form' => $form), true, 'default'); ?>
							</div>
							<div style="display:none;">
								<?php wp_footer(); ?>
							</div>
						</body>
					</html>
						
					<?php
				} else {
						
				}
			} else {
				
			}
			
			exit();
			die();
		}
		
		function widget_object() {
			$widget = new Newsletters_Widget();
			return $widget;
		}
		
		function widget_settings() {
			$widget = new Newsletters_Widget();
			$settings = $widget -> get_settings();
			return $settings;
		}
		
		function widget_instance($number = null, $atts = array()) {
			if (!empty($number)) {
				$widget = new Newsletters_Widget();
				$settings = $widget -> get_settings();
				
				if (!empty($settings[$number])) {
					$instance = $settings[$number];
					
					if ($this -> language_do()) {
						$instance['lang'] = $this -> language_current();
					}
				} else {
					if ($embed = $this -> get_option('embed')) {
						$instance = wp_parse_args($atts, $embed);
						
						if (empty($instance['list'])) {
							if ($instance['type'] == "list") {
								$instance['list'] = $instance['id'];
							} else {
								$instance['list'] = $instance['type'];
								if (empty($instance['lists']) && !empty($instance['id'])) {
									$instance['lists'] = $instance['id'];
								}
							}
						}
						
						unset($instance['type']);
						unset($instance['id']);
					}
				}
				
				return $instance;
			}
			
			return false;
		}
		
		function ajax_getlistfields() {		
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $FieldsList, $Subscriber;
			$widget_id = $_GET['widget_id'];
			$instance = $_POST['instance'];
			
			$Subscriber -> data = $_POST;
			
			if ($fields = $FieldsList -> fields_by_list($_POST['list_id'], "order", "ASC")) {		
				foreach ($fields as $field) {				
					$this -> render_field($field -> id, true, $widget_id, true, true, $instance);
				}
			}
			
			?>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				if (jQuery.isFunction(jQuery.fn.select2)) {
					jQuery('.newsletters select').select2();
				}
				
				jQuery('#<?php echo $widget_id; ?>-form .newsletters-list-checkbox').on('click', function() { newsletters_refreshfields('<?php echo $widget_id; ?>'); });
				jQuery('#<?php echo $widget_id; ?>-form .newsletters-list-select').on('change', function() { newsletters_refreshfields('<?php echo $widget_id; ?>'); });
				jQuery('.newsletters-management .newsletters-fieldholder, .entry-content .newsletters-fieldholder, .post-entry .newsletters-fieldholder, .entry .newsletters-fieldholder').addClass('col-md-6');
			});
			</script>
			
			<?php 
			
			exit();
			die();
		}
		
		function ajax_testsettings() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $Subscriber;
			$errors = array();
			$success = false;
			
			if (!empty($_GET['init']) && !empty($_POST)) {		
				foreach ($_POST as $pkey => $pval) {
					$this -> update_option($pkey, $pval);
					
					switch ($pkey) {
						case 'smtpfromname'			:
						case 'smtpfrom'				:
						case 'excerpt_more'			:								
							if ($this -> language_do()) {
								$this -> update_option($pkey, $this -> language_join($pval));
							} else {
								$this -> update_option($pkey, $pval);	
							}
							break;
					}
				}
			}
			
			if (empty($_GET['init']) && !empty($_POST)) {		
				if (empty($_POST['testemail'])) { $errors[] = __('Please fill in an email address', $this -> plugin_name); }
				elseif (!$Subscriber -> email_validate($_POST['testemail'])) { $errors[] = __('Please fill in a valid email address', $this -> plugin_name); }
				if (empty($_POST['subject'])) { $errors[] = __('Please fill in a subject', $this -> plugin_name); }
				if (empty($_POST['message'])) { $errors[] = __('Please fill in a message', $this -> plugin_name); }
				
				if (empty($errors)) {
					$subscriber = new stdClass();
					$subscriber -> email = $_POST['testemail'];
					$subject = stripslashes($_POST['subject']);
					$message = $_POST['message'];
					
					$attachments = false;
					if (!empty($_POST['testattachment']) && $_POST['testattachment'] == 1) {
						$attachments = array(
							array(
								'filename'				=>	$this -> plugin_base() . DS . 'images' . DS . 'wp-mailinglist.jpg',
							)
						);
					}
					 
					if ($this -> execute_mail($subscriber, false, $subject, $message, $attachments, false, false, false, "testing")) {
						$success = true;
						$errors[] = __('Email was successfully sent, your settings are working!', $this -> plugin_name);	
					} else {
						global $mailerrors;
						$errors[] = $mailerrors;
					}
				}
			}
			
			echo '<div id="testsettingswrapper">';
			$this -> render('testsettings', array('errors' => $errors, 'success' => $success), true, 'admin');
			echo '</div>';
			
			exit();	
			die();
		}
		
		function ajax_mailapi_mandrill_keytest() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			require_once($this -> plugin_base() . DS . 'vendors' . DS . 'mailapis' . DS . 'mandrill' . DS . 'Mandrill.php');
			
			try {
			    $mandrill = new Mandrill($_POST['key']);
			    $result = $mandrill -> users -> ping();
			    echo '<span class="newsletters_success"><i class="fa fa-check"></i></span>';
			} catch(Mandrill_Error $e) {
			    echo '<span class="newsletters_error"><i class="fa fa-times"></i> ' . $e -> getMessage() . '</span>';
			}
			
			exit(); die();
		}
		
		function ajax_mailapi_mailgun_action() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			if (!empty($_POST['ac'])) {
				$mailgun_apikey = $_POST['key'];
				$mailgun_domain = $_POST['domain'];
				
				require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
				$mg = new Mailgun\Mailgun($mailgun_apikey);
				
				switch ($_POST['ac']) {
					case 'verify'					:
						try {
							$result = $mg -> get($mailgun_domain . '/events');
							$this -> render_message(__('MailGun API key works', $this -> plugin_name), false, false);
							
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
					case 'checkdomains'				:
						try {
							$result = $mg -> get('domains');
							
							$items = $result -> http_response_body -> items;
							if (!empty($items)) {
								echo '<p>';
								foreach ($items as $item) {
									$class = (!empty($item -> state) && $item -> state == "active") ? 'newsletters_success' : 'newsletters_warning';
									echo '<span class="' . $class . '">' . $item -> name . '</span><br/>';
								}
								echo '</p>';
							} else {
								$this -> render_error(__('No domains available.', $this -> plugin_name), false, false);
							}
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
					case 'adddomain'				:
						try {
							$password = wp_generate_password(12, false, false);
							$result = $mg -> post("domains", array(
							    'name'          => $mailgun_domain,
							    'smtp_password' => $password,
							));	
							
							$this -> render_message($result -> http_response_body -> message, false, false);
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
					case 'events'					:
						try {
							$result = $mg -> get($mailgun_domain . '/events');
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
					case 'stats'					:
						try {
							$result = $mg -> get($mailgun_domain . '/stats');
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
				}
			}
			
			exit();
			die();
		}
		
		function ajax_mailapi_amazonses_action() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			$success = false;
			$message = false;
			$error = false;
			
			if (!empty($_REQUEST['ac'])) {
				require_once($this -> plugin_base() . DS . 'vendors' . DS . 'amazonses' . DS . 'SimpleEmailService.php');
				$ses = new SimpleEmailService($_REQUEST['key'], $_REQUEST['secret'], 'email.' . $_REQUEST['region'] . '.amazonaws.com');
			
				switch ($_REQUEST['ac']) {
					case 'verifyemail'				:
						$email = __($this -> get_option('smtpfrom'));
						if ($result = $ses -> verifyEmailAddress($email)) {
							$success = true;
							$message = sprintf(__('Email address %s has been verified.', $this -> plugin_name), $email);
						} else {
							//failed	
							$error = __('Email could not be verified', $this -> plugin_name);
						}
						break;
					case 'getverifiedemails'		:
						if ($result = $ses -> listVerifiedEmailAddresses()) {
							$success = true;
							
							$message = __('Currently verified email addresses:', $this -> plugin_name);
							$message .= '<ul class="newsletters_success">';
							foreach ($result['Addresses'] as $address) {
								$message .= '<li>' . $address . '</li>';
							}
							$message .= '</ul>';
						} else {
							$error = __('Could not get list of verified email addresses.', $this -> plugin_name);
						}
						break;
					case 'getsendquota'				:
						if ($result = $ses -> getSendQuota()) {
							$success = true;
							$message = '';
							$message .= sprintf(__('Max 24 Hour Send: %s', $this -> plugin_name), $result['Max24HourSend']);
							$message .= '<br/>';
							$message .= sprintf(__('Max Send Rate: %s', $this -> plugin_name), $result['MaxSendRate']);
							$message .= '<br/>';
							$message .= sprintf(__('Sent in Last 24 Hours: %s', $this -> plugin_name), $result['SentLast24Hours']);
						} else {
							$error = __('Could not get send quota.', $this -> plugin_name);
						}
						break;
				}	
			}
			
			if (!empty($success)) {
				echo '<p class="newsletters_success">' . $message . '</p>';
			} elseif (!empty($error)) {
				echo '<p class="newsletters_error">' . $error . '</p>';
			}
			
			exit(); die();
		}
		
		function ajax_dkimwizard() {			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			switch ($_POST['goto']) {
				case 'step2'				:
					$this -> render('dkim' . DS . 'step2', array('domain' => $_POST['domain'], 'selector' => $_POST['selector'], 'public' => $_POST['public'], 'private' => $_POST['private']), true, 'admin');
					break;
				case 'step3'				:
					$this -> render('dkim' . DS . 'step3', array('domain' => $_POST['domain'], 'selector' => $_POST['selector'], 'public' => $_POST['public'], 'private' => $_POST['private']), true, 'admin');
					break;
				case 'step1'				:
				default 					:
					require_once $this -> plugin_base() . DS . 'vendors' . DS . 'dkim' . DS . 'Crypt' . DS . 'RSA.php';
					$rsa = new Crypt_RSA();
					$keys = $rsa -> createKey();					
					$this -> render('dkim' . DS . 'step1', array('domain' => $_POST['domain'], 'selector' => $_POST['selector'], 'public' => $keys['publickey'], 'private' => $keys['privatekey']), true, 'admin');
					break;
			}
			
			exit();
			die();
		}
		
		function get_pop3_status() {
			$pop3_status = false;
			
			require_once($this -> plugin_base() . DS . 'vendors' . DS . 'class.pop3_new.php');
			$pop_host = $this -> get_option('bouncepop_host');
			$pop_port = $this -> get_option('bouncepop_port');
			$pop_user = $this -> get_option('bouncepop_user');
			$pop_pass = $this -> get_option('bouncepop_pass');
	        $pop3 = new POP3();
	        
	        if ($pop3 -> connect($pop_host, $pop_port)) {
	        	if ($pop3 -> user($pop_user)) {
	        		$count = $pop3 -> pass($pop_pass);
	        		
	        		if ($count === false) {
						$pop3_status = $pop3 -> ERROR;
	        		} elseif ($count === 0) {
		        		$pop3_status = __('There are no emails in the mailbox', $this -> plugin_name);
	        		} elseif ($count > 0) {
		        		$pop3_status = sprintf(__('There are %s emails in the mailbox', $this -> plugin_name), $count);
	        		}
	        	} else {
		        	$pop3_status = $pop3 -> ERROR;
	        	}
	        } else {
		        $pop3_status = $pop3 -> ERROR;
	        }
	        
	        return $pop3_status;
		}
		
		function ajax_autocomplete_users() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			$args = array(
				'search'				=>	'*' . esc_attr($_REQUEST['q']) . '*',
			);
			
			$user_query = new WP_User_Query($args);
			$users = $user_query -> get_results();
			
			$json = json_encode(array());
			
			if (!empty($users)) {
				$titles = array();
				$t = 0;
				foreach ($users as $user) {
					$titles[$t]['id'] = $user -> ID;
					$titles[$t]['text'] = $user -> display_name;
					$t++;
				}
				
				$json = json_encode($titles);
			}
			
			echo $json;
			
			exit();
			die();
		}
		
		function ajax_autocomplete_histories() {
			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			global $Db;
			
			$conditions = array();
			if (!empty($_REQUEST['q'])) {
				$conditions['subject'] = "LIKE '%" . $_REQUEST['q'] . "%'";
			}
			
			if ($histories = $this -> History() -> find_all($conditions)) {
				if (!empty($histories)) {
					$t = 0;
				
					foreach ($histories as $history) {
						$titles[$t]['id'] = $history -> id;
						$titles[$t]['text'] = __($history -> subject);
						$t++;
					}
					
					$json = json_encode($titles);
				}
			}
			
			echo $json;
			
			exit();
			die();
		}
		
		function ajax_testbouncesettings() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
		
	        require_once($this -> plugin_base() . DS . 'vendors' . DS . 'class.pop3_new.php');
	        $pop3 = new POP3();
	        
	        if ($pop3 -> connect($_POST['host'], $_POST['port'])) {
	        	if ($pop3 -> user($_POST['user'])) {
	        		$count = $pop3 -> pass($_POST['pass']);
	        		
	        		if ($count === false) {
		        		$success = false;
						$error = $pop3 -> ERROR;
	        		} elseif ($count === 0) {
		        		$success = true;
		        		$message = __('There are no emails in the mailbox', $this -> plugin_name);
	        		} elseif ($count > 0) {
		        		$success = true;
		        		$message = sprintf(__('There are %s emails in the mailbox', $this -> plugin_name), $count);
	        		}
	        	} else {
		        	$success = false;
		        	$error = $pop3 -> ERROR;
	        	}
	        } else {
	        	$success = false;
		        $error = $pop3 -> ERROR;
	        }
	        
	        $pop3 -> quit();	        
	        $this -> render('testbouncesettings', array('success' => $success, 'message' => $message, 'error' => $error), true, 'admin');
		
			exit();
			die();
		}
		
		function ajax_deletecontentarea() {
			
			if (!empty($_POST['number']) && !empty($_POST['history_id'])) {
				$this -> Content() -> delete_all(array('number' => $_POST['number'], 'history_id' => $_POST['history_id']));
			}
			
			exit();
			die();
		}
		
		function ajax_order_fields() {
			global $Db, $FieldsList, $Field;
	
			if (!empty($_REQUEST)) {				
				if (!empty($_REQUEST['fields'])) {
					foreach ($_REQUEST['fields'] as $order => $field_id) {
						$Db -> model = $Field -> model;
						$Db -> save_field('order', $order, array('id' => $field_id));
						
						$Db -> model = $FieldsList -> model;
						$Db -> save_field('order', $order, array('field_id' => $field_id));
					}
					
					_e('Custom fields order has been successfully saved', $this -> plugin_name);
				} else {
					_e('No fields are available', $this -> plugin_name);
				}
			} else {
				_e('No data posted', $this -> plugin_name);
			}
			
			exit();
			die();
		}
		
		function ajax_themeedit() {
			global $Db, $Theme;
			$success = false;
			$errors = array();
			
			if (!empty($_REQUEST)) {
				if (!empty($_REQUEST['id'])) {
					if (!empty($_REQUEST['Theme'])) {
						$Db -> model = $Theme -> model;
						if ($Db -> save($_REQUEST)) {
							$success = true;
						} else {
							$errors = $Theme -> errors;
						}
					}
				
					$Db -> model = $Theme -> model;
					$Db -> find(array('id' => $_GET['id']));
					$Theme -> data -> paste = $Theme -> data -> content;
				} else {
					$errors[] = __('No template was specified', $this -> plugin_name);
				}
			} else {
				$errors[] = __('No data was specified', $this -> plugin_name);
			}
			
			$this -> render('themes' . DS . 'save-ajax', array('success' => $success, 'errors' => $errors), true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_addcontentarea() {				
			?>
			
			<div class="postbox">
				<h3 class="hndle"><span><?php echo sprintf(__('Content Area %s', $this -> plugin_name), $_POST['contentarea']); ?></span></h3>
				<div class="inside">
					<?php 
					
					$settings = array(
						'media_buttons'		=>	true,
						'textarea_name'		=>	'contentarea[' . $_POST['contentarea'] . ']',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
					);
					
					wp_editor(false, 'contentarea' . $_POST['contentarea'], $settings); 
					
					?>
				</div>
			</div>
			
			<?php
			
			exit();
			die();
		}
		
		function ajax_previewrunner($justsave = false) {
	    	global $wpdb, $Db, $Html;
	    	define('DOING_AJAX', true);
	    	define('SHORTINIT', true);
	    	
	    	if (empty($_POST['content'])) { exit(); }
	    	if (empty($_POST['subject']) || $_POST['subject'] == __('Enter email subject here', $this -> plugin_name)) { exit(); }
	    	
	    	ob_start();
	    	$history_data = array(
		    	'from'				=>	$_POST['from'],
		    	'fromname'			=>	$_POST['fromname'],
	    		'post_id'			=>	$_POST['post_id'],
				'subject'			=>	stripslashes($_POST['subject']),
				'message'			=>	$_POST['content'],
				'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? strip_tags($_POST['customtext']) : false),
				'theme_id'			=>	$_POST['theme_id'],
				'condquery'			=>	serialize($_POST['condquery']),
				'conditions'		=>	maybe_serialize($_POST['fields']),
				'conditionsscope'	=>	$_POST['fieldsconditionsscope'],
				'daterange'			=>	$_POST['daterange'],
				'daterangefrom'		=>	$_POST['daterangefrom'],
				'daterangeto'		=>	$_POST['daterangeto'],
				'mailinglists'		=>	serialize($_POST['mailinglists']),
				'groups'			=>	serialize($_POST['groups']),
				'roles'				=>	maybe_serialize($_POST['roles']),
				'senddate'			=>	$_POST['senddate'],
				'scheduled'			=>	$_POST['scheduled'],
				'format'			=>	$_POST['format'],
			);
			
			if (!empty($_POST['ishistory'])) { 
				$history_data['id'] = $_POST['ishistory']; 
				$history_curr = $this -> History() -> find(array('id' => $history_data['id']));
				$history_data['sent'] = $history_curr -> sent;
			}
			
			if (!empty($_POST['sendrecurring'])) {
				if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
					$history_data['recurring'] = "Y";
					$history_data['recurringvalue'] = $_POST['sendrecurringvalue'];
					$history_data['recurringinterval'] = $_POST['sendrecurringinterval'];
					
					if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
						$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
					} else {
						$history_data['recurringdate'] = $_POST['sendrecurringdate'];
					}
					
					$history_data['recurringlimit'] = $_POST['sendrecurringlimit'];
				}
			}
			
			if ($this -> History() -> save($history_data, false)) {
				$history_id = $this -> History() -> insertid;
				$p_id = $this -> History() -> field('p_id', array('id' => $history_id));
			
				if (!empty($_POST['contentarea'])) {
					foreach ($_POST['contentarea'] as $number => $content) {
						$content_data = array(
							'number'			=>	$number,
							'history_id'		=>	$history_id,
							'content'			=>	$content,
						);
						
						$this -> Content() -> save($content_data, true);
					}	
				}
			}
			
			$history_id = $this -> History() -> insertid;
	    	$_GET['id'] = $history_id;
	    	
	    	if (!empty($justsave)) {
		    	return $history_id;
	    	}
	    	
	    	$output = ob_get_clean();
	    	$preview = $this -> ajax_historyiframe(true);
	    	
	    	header("Content-Type: text/xml; charset=UTF-8");
	    	
	    	?>
	    	
	    	<result>
				<history_id><?php echo $history_id; ?></history_id>
				<p_id><?php echo $p_id; ?></p_id>
				<previewcontent><![CDATA[<?php echo $preview; ?>]]></previewcontent>
				<newsletter_url><![CDATA[<?php echo $Html -> retainquery($this -> pre . 'method=newsletter&id=' . $history_id, home_url()); ?>]]></newsletter_url>
			</result>
	    	
	    	<?php
		    
		    exit();
		    die();
	    }
	    
	    function ajax_spamscorerunner() {
		    define('DOING_AJAX', true);
	    	define('SHORTINIT', true);
		    
	    	global $Db, $Html, $Subscriber, $newsletters_presend, $newsletters_emailraw;
	    	$newsletters_presend = true;
	    	$subscriber_id = $Subscriber -> admin_subscriber_id();
	    	$subscriber = $Subscriber -> get($subscriber_id, false);
	    	$subject = stripslashes($_POST['subject']);
	    	$history_id = $_POST['ishistory'];
			$message = $this -> render_email('send', array('message' => $_POST['content'], 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, $_POST['theme_id']);
			$this -> execute_mail($subscriber, false, $subject, $message, $history -> attachments, $history_id, false, false);
			$spamscore = $this -> spam_score($newsletters_emailraw, "long");
			
			// Save the 'spamscore'
			if (!empty($spamscore -> score)) {
				$this -> History() -> save_field('spamscore', $spamscore -> score, array('id' => $history_id));
			}
			
			if (empty($spamscore -> success) || $spamscore -> success == false) {
				$score = 0;
			} else {
				$score = $spamscore -> score;
			}
			
			$output = "";
			ob_start();
			
			?>
			
			<p id="spamscore_report_link_holder" style="text-align:center; display:block;"><a href="#spamscore_report" onclick="jQuery.colorbox({inline:false, html:jQuery('#spamscore_report').html(), maxWidth:'80%', maxHeight:'80%'}); return false;"><?php _e('See Report', $this -> plugin_name); ?></a></p>
			<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo admin_url('admin-ajax.php?action=newsletters_gauge&value=' . $score); ?>"></iframe>
			
			<div style="display:none;">
				<div id="spamscore_report">
					<div class="wrap newsletters">
						<h2><?php _e('Spam Score Report', $this -> plugin_name); ?></h2>						
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php _e('Close Report', $this -> plugin_name); ?></a></p>
						
						<p><?php echo sprintf(__('The spam score is %s out of 10 for this email', $this -> plugin_name), $score); ?></p>
						
						<?php if (is_wp_error($spamscore)) : ?>
							<p class="newsletters_error"><?php echo $spamscore -> get_error_message(); ?></p>
						<?php elseif (empty($spamscore -> success) || $spamscore -> success == false) : ?>
							<p class="newsletters_error"><?php echo $spamscore -> message; ?></p>
						<?php else : ?>
							<h3><?php _e('Report', $this -> plugin_name); ?></h3>
							<p><pre><?php echo ($spamscore -> report); ?></pre></p>
						<?php endif; ?>
						
						<h3><?php _e('RAW Email', $this -> plugin_name); ?></h3>
						<p><a href="" onclick="jQuery('#rawemail-holder').toggle(); return false;" class="button button-secondary"><?php _e('Toggle RAW Email', $this -> plugin_name); ?></a></p>
						<div id="rawemail-holder" style="display:none;">
							<p><pre><?php echo htmlspecialchars($newsletters_emailraw); ?></pre></p>
						</div>
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php _e('Close Report', $this -> plugin_name); ?></a></p>
					</div>
				</div>
			</div>
			
			<?php		
				
			$output = ob_get_clean();
			
			header("Content-Type: text/xml; charset=UTF-8");
	    	
	    	?>
	    	
	    	<result>
				<success><?php echo $spamscore -> success; ?></success>
				<report><![CDATA[<?php echo nl2br($spamscore -> report); ?>]]></report>
				<score><?php echo $score; ?></score>
				<output><![CDATA[<?php echo $output; ?>]]></output>
			</result>
	    	
	    	<?php
		    
		    exit();
		    die();
	    }
	    
	    function spam_score($email = null, $options = "long") {
			$data = array("email" => $email, "options" => $options);                                                                    
			$data_string = json_encode($data);  
			
			$header = array(
				'Accept: application/json',                                                                    
			    'Content-Type: application/json',                                                                                
			    'Content-Length: ' . strlen($data_string),
			);
			
			$url = 'http://spamcheck.postmarkapp.com/filter';
			
			$args = array(
				'method'			=>	"POST",
				'body'				=>	$data,
				'headers'			=>	$header,
				'timeout'			=>	120,
			);
			
			$response = wp_remote_request($url, $args);
			
			if (!is_wp_error($response)) {
				return json_decode($response['body']);
			} else {
				return $response;
			}
		}
	    
	    function ajax_tinymce_dialog() {
		    $this -> render('tinymce-dialog', false, true, 'admin');
		    
		    exit();
		    die();
	    }
	    
	    function ajax_tinymce_snippet() {
	    	define('DOING_AJAX', true);
	    	define('SHORTINIT', true);
	    
		    $this -> render('tinymce' . DS . 'snippet', false, true, 'admin');
		    
		    exit();
		    die();
	    }
	    
	    function ajax_gauge() {	    
	    	$value = (empty($_REQUEST['value'])) ? 0 : $_REQUEST['value'];
		    
		    ?>
		    
		    <html>
		    	<body style="margin:0; padding:0;">
				    <script type="text/javascript" src="<?php echo $this -> render_url('js/justgage.js', 'admin', false); ?>"></script>
				    <script type="text/javascript" src="<?php echo $this -> render_url('js/raphael.js', 'admin', false); ?>"></script>
				    <div id="gauge"></div>
				    
				    <script>
					  var g = new JustGage({
					    id: "gauge", 
					    value: <?php echo $value; ?>, 
					    min: 0,
					    max: 10,
					    title: "<?php echo ($value >= 5) ? __('This is spam!', $this -> plugin_name) : __('This is safe!', $this -> plugin_name); ?>",
					    label: "<?php _e('Spam Score', $this -> plugin_name); ?>",
					    levelColorsGradient: false
					  }); 
					</script>
		    	</body>
		    </html>
		    
		    <?php
		    
		    exit();
		    die();
	    }
		
		function ajax_historyiframe($returnoutput = false) {	
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $Db, $Subscriber;
			$email = $this -> History() -> find(array('id' => $_GET['id']));
			
			$subscriber_id = $Subscriber -> admin_subscriber_id();
			$subscriber = $Subscriber -> get($subscriber_id);
			
			if (!empty($email -> post_id)) {
				if ($thepost = get_post($email -> post_id)) {
					global $post;
					$post = $thepost;
				}
			}
			
			//$message = $this -> render('newsletter', array('email' => $email, 'subscriber' => $subscriber), false, 'default');
			$message = $email -> message;
			$content = $this -> render_email('send', array('message' => $message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $_GET['id']), false, true, true, $email -> theme_id, true);
			
			$output = "";
			ob_start();
			echo do_shortcode(stripslashes($content));
			$output = ob_get_clean();
			
			ob_start();
			echo $this -> process_set_variables($subscriber, $user, $output, $email -> id);
			$output = ob_get_clean();
			
			if (!empty($email -> format) && $email -> format == "text") {
				$output = wpautop($output);
			}
			
			if ($returnoutput) {
				return $output;
			}
			
			echo $output;
			exit();
			die();
		}
		
		function ajax_serialkey() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$errors = array();
			$success = false;
			
			if (!empty($_GET['delete'])) {
				$this -> delete_option('serialkey');
				$errors[] = __('Serial key has been deleted.', $this -> plugin_name);
			} else {
				if (!empty($_POST)) {
					if (empty($_REQUEST['serialkey'])) { $errors[] = __('Please fill in a serial key.', $this -> plugin_name); }
					else { 
						$this -> update_option('serialkey', $_REQUEST['serialkey']);	//update the DB option
						$this -> delete_all_cache('all');
						
						if (!$this -> ci_serial_valid()) { $errors[] = __('Serial key is invalid, please try again.', $this -> plugin_name); }
						else {
							delete_transient($this -> pre . 'update_info');
							$success = true; 
						}
					}
				}
			}
			
			if (empty($_POST)) { ?><div id="<?php echo $this -> pre; ?>submitserial"><?php }
			$this -> render('submitserial', array('errors' => $errors, 'success' => $success), true, 'admin');
			if (empty($_POST)) { ?></div><?php }
			
			exit();
			die();
		}
		
		function ajax_managementcustomfields() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber, $FieldsList;
			
			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;
				
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']), false, false, true, true, false)) {
					$lists = array();
					if (!empty($subscriber -> subscriptions)) {
						foreach ($subscriber -> subscriptions as $subscription) {
							$lists[] = $subscription -> mailinglist -> id;	
						}
					}
					
					$fields = $FieldsList -> fields_by_list($lists, "order", "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));
				} else {
					$errors[] = __('Subscriber could not be read.', $this -> plugin_name);
				}	
			}
			
			$this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields), true, 'default');
			$this -> render('js' . DS . 'management', false, true, 'default');
			
			exit();
			die();	
		}
		
		function ajax_managementsavefields() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			global $wpdb, $Db, $Field, $Subscriber, $FieldsList;
			
			$errors = array();
			$oldpost = $_POST;
			$success = false;
			$successmessage = "";
			
			if (!empty($_POST)) {
				$Db -> model = $Subscriber -> model;
				
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']), false, false, true, true, false)) {
					$lists = array();
					if (!empty($subscriber -> subscriptions)) {
						foreach ($subscriber -> subscriptions as $subscription) {
							$lists[] = $subscription -> mailinglist -> id;	
						}
					}				
					
					$fields = $FieldsList -> fields_by_list($lists, 'order', "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));
					
					$_POST = $oldpost;
					unset($_POST['action']);
					unset($_POST['subscriber_id']);
					
					if (!empty($_POST)) {
						$emailfield = $Field -> email_field();
					
						if (!empty($_POST['email'])) {
							if (!$Subscriber -> email_validate($_POST['email'])) { $errors['email'] = __($emailfield -> errormessage); }
							elseif ($_POST['email'] != $subscriber -> email && $Subscriber -> email_exists($_POST['email'])) { $errors[] = __('Email address is already in use, try another.', $this -> plugin_name); }
						} else {
							$errors['email'] = __($emailfield -> errormessage);
						}
						
						$_POST['list_id'] = $lists;
						
						$_POST = $data = $Field -> validate_optin($_POST, 'management');						
						if (!empty($Field -> errors)) {
							$errors = array_merge($errors, $Field -> errors);
						}
					
						if (empty($errors)) {							
							if (!empty($_POST['email']) && $_POST['email'] != $subscriber -> email) {
								$Db -> model = $Subscriber -> model;
								$Db -> save_field('email', $_POST['email'], array('id' => $subscriber -> id));
							}
							
							$data['justsubscribe'] = true;
							
							if ($Subscriber -> save($data, true, false, false, true)) {
								$success = true;
								$successmessage = __('Additional data has been saved.', $this -> plugin_name);
								
								$this -> delete_all_cache('all');
								$Db -> model = $Subscriber -> model;
								$subscriber = $Db -> find(array('id' => $subscriber -> id), false, false, true, true, false);
							} else {
								$errors[] = __('Could not be saved', $this -> plugin_name);
							}
						} else {
							$_POST[$this -> pre . 'errors'] = $errors;
						}
					} else {
						$errors[] = __('No data was posted.', $this -> plugin_name);	
					}
				} else {
					$errors[] = __('Subscriber could not be read.', $this -> plugin_name);
				}
			} else {
				$errors[] = __('No data was posted.', $this -> plugin_name);
			}
			
			$this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields, 'success' => $success, 'successmessage' => $successmessage, 'errors' => $errors), true, 'default');
		
			exit();
			die();	
		}
		
		function ajax_managementcurrentsubscriptions() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber;
			
			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;
				
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']), false, false, true, true, false)) {				
					$this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber), true, 'default');
				}
			}
			
			exit();
			die();	
		}
		
		function ajax_managementnewsubscriptions() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber, $Mailinglist;
			$otherlists = array();
			
			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;
							
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']), false, false, true, true, false)) {				
					if ($mailinglists = $Mailinglist -> select(false)) {
						foreach ($mailinglists as $mkey => $mval) {
							$otherlists[$mkey] = $mkey;	
						}
								
						if (!empty($subscriber -> subscriptions)) {			
							foreach ($subscriber -> subscriptions as $subscription) {						
								if (in_array($subscription -> mailinglist -> id, $otherlists)) {
									unset($otherlists[$subscription -> mailinglist -> id]);
								}
							}
						}
						
						$this -> render('management' . DS . 'newsubscriptions', array('subscriber' => $subscriber, 'otherlists' => $otherlists), true, 'default');
					}
				}
			}
			
			exit();
			die();
		}
		
		function ajax_subscribercountdisplay() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			global $wpdb, $Subscriber;
			
			$subscribercount_query = get_transient('newsletters_subscribercount');
			if (!empty($subscribercount_query)) {
				
				$total_subscribers = count($wpdb -> get_results($subscribercount_query));
				
				$perpage = 50;
				$start = (empty($_GET['start'])) ? 0 : $_GET['start'];
				$realstart = ($start + 1);
				$end = ($start + $perpage);
				$realend = ($end > $total_subscribers) ? $total_subscribers : $end;
				$prevstart = ($start - $perpage);
				$nextstart = ($end);
				
				$subscribercount_query .= " LIMIT " . $start . ", " . $perpage . "";
				
				if ($subscribers = $wpdb -> get_results($subscribercount_query)) {
					
					?>
					
					<p><?php echo sprintf(__('Showing %s - %s of %s subscribers', $this -> plugin_name), $realstart, $realend, $total_subscribers); ?></p>
					
					<table class="widefat">
						<tbody>
							<?php foreach ($subscribers as $subscriber) : ?>
								<?php $subscriber = $Subscriber -> get($subscriber -> id); ?>
								<tr class="<?php echo $class = (empty($class)) ? 'alternate' : false; ?>">
									<td>
										<?php echo $subscriber -> email; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					
					<p>
					<?php if ($start > 0) : ?>
						<a href="" class="button button-secondary" onclick="jQuery.colorbox({width:'80%', height:'80%', title:'<?php _e('Selected Subscribers', $this -> plugin_name); ?>', href:'<?php echo admin_url('admin-ajax.php?action=newsletters_subscribercountdisplay&start=' . $prevstart); ?>'}); return false;"><i class="fa fa-caret-left"></i> <?php _e('Previous Page', $this -> plugin_name); ?></a>
					<?php endif; ?>
					<?php if ($total_subscribers > $end) : ?>
						<a href="" class="button button-secondary" onclick="jQuery.colorbox({width:'80%', height:'80%', title:'<?php _e('Selected Subscribers', $this -> plugin_name); ?>', href:'<?php echo admin_url('admin-ajax.php?action=newsletters_subscribercountdisplay&start=' . $nextstart); ?>'}); return false;"><?php _e('Next Page', $this -> plugin_name); ?> <i class="fa fa-caret-right"></i></a>
					<?php endif; ?>
					</p>
					
					<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><i class="fa fa-times"></i> <?php _e('Close', $this -> plugin_name); ?></a></p>
					
					<?php
				}
			}
			
			exit(); die();
		}
		
		function ajax_subscribercount($count = true) {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			$status = (empty($_POST['status'])) ? 'active' : $_POST['status'];
		
			global $wpdb, $Db, $Field, $Subscriber, $Mailinglist, $SubscribersList;		
			$subscribercount = 0;
			
			if (!empty($_POST['groups'])) {
				foreach ($_POST['groups'] as $group_key => $group_id) {
					$Db -> model = $Mailinglist -> model;
					
					if ($lists = $Db -> find_all(array('group_id' => $group_id), array('id'))) {
						foreach ($lists as $list) {
							$_POST['mailinglists'][] = $list -> id;	
						}
					}
				}
			}
			
			// Count the users based on roles
			$users_count = 0;
			if (!empty($_POST['roles'])) {
				if ($count_users = count_users()) {								
					foreach ($count_users['avail_roles'] as $role => $count) {										
						if (array_key_exists($role, $_POST['roles'])) {
							$users_count += $count;
						}
					}
				}
			}
			
			if (!empty($_POST['mailinglists'])) {
				$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id FROM " . $wpdb -> prefix . "" . $SubscribersList -> table . " LEFT JOIN 
				" . $wpdb -> prefix . "" . $Subscriber -> table . " ON 
				" . $wpdb -> prefix . "" . $SubscribersList -> table . ".subscriber_id = " . $wpdb -> prefix . "" . $Subscriber -> table . ".id LEFT JOIN 
				" . $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id = " . $wpdb -> prefix . $Mailinglist -> table . ".id LEFT JOIN 
				" . $wpdb -> prefix . $this -> SubscribersOption() -> table . " ON " . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $this -> SubscribersOption() -> table . ".subscriber_id WHERE (";
				
				$m = 1;
				foreach ($_POST['mailinglists'] as $mailinglist_id) {
					$query .= "" . $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $mailinglist_id . "'";	
					
					if ($m < count($_POST['mailinglists'])) {
						$query .= " OR ";	
					}
					
					$m++;
				}
				
				if (empty($status) || $status == "active") {
					$query .= ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";
				} elseif ($status == "inactive") {
					$query .= ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'N'";
				} elseif ($status == "all") {
					$query .= ")";
				}
					
				$query .= " AND (" . $wpdb -> prefix . $SubscribersList -> table . ".paid_sent < " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval 
				OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval IS NULL OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval = '')";
				
				if (!empty($_POST['fields'])) {	
					$query .= " AND (";			
					$supportedfields = array('text', 'hidden', 'radio', 'checkbox', 'select', 'pre_country', 'pre_gender');
					$scopeall = (empty($_POST['fieldsconditionsscope']) || $_POST['fieldsconditionsscope'] == "all") ? true : false;
					$f = 1;
					
					$checkboxjoinquery = "";
					
					foreach ($_POST['fields'] as $fkey => $field) {					
						if (!empty($field[0]) && $field[1] != "") {
							if (preg_match("/\d+/si", $field[0], $matches)) {
								$field_id = $matches[0];
								$Db -> model = $Field -> model;	
								if ($customfield = $Db -> find(array('id' => $field_id), array('id', 'slug', 'type'))) {	
									
									switch ($customfield -> type) {
										case 'checkbox'						:		
											$condition = $_POST['condquery'][$customfield -> slug];
											switch ($condition) {
												case 'contains'				:
													$query .= " wp_wpmlsubscribers.id IN (SELECT subscriber_id FROM " . $wpdb -> prefix . $this -> SubscribersOption() -> table . " WHERE `field_id` = '" . $field_id . "' AND `option_id` = '" . $field[1] . "')";
													break;
												case 'equals'				:
													$query .= " wp_wpmlsubscribers.id IN (SELECT subscriber_id FROM " . $wpdb -> prefix . $this -> SubscribersOption() -> table . " WHERE `field_id` = '" . $field_id . "' AND `option_id` = '" . $field[1] . "')";
													break;
											}
											
											if ($f < count($_POST['fields'])) {
												switch ($condition) {
													case 'contains'			:
														$query .= " OR";
														break;
													case 'equals'			:
														$query .= " AND";
														break;
												}
											}
											
											break;
										default 							:
											$condition = $_POST['condquery'][$customfield -> slug];
																	
											if (in_array($customfield -> type, $supportedfields)) {									
												switch ($condition) {
													case 'smaller'			:
														$query .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " < " . $field[1] . "";
														break;
													case 'larger'			:
														$query .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " > " . $field[1] . "";
														break;
													case 'contains'			:
														$query .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " LIKE '%" . $field[1] . "%'";
														break;
													case 'equals'			:
													default  				:
														$query .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " = '" . $field[1] . "'";
														break;
												}
												
												if ($f < count($_POST['fields'])) {
													$query .= ($scopeall) ? " AND" : " OR";
												}
											}	
											break;
									}
								}
							}
						}
						
						$f++;
					}
					
					$query .= ")";
				}
				
				$query = str_replace(" AND ()", "", $query);
				
				if (!empty($_POST['daterange']) && $_POST['daterange'] == "Y") {
					if (!empty($_POST['daterangefrom']) && !empty($_POST['daterangeto'])) {
						$daterangefrom = date_i18n("Y-m-d", strtotime($_POST['daterangefrom']));
						$daterangeto = date_i18n("Y-m-d", strtotime($_POST['daterangeto']));
						$query .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . $daterangefrom . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . $daterangeto . "')";
					}
				}
				
				$query_hash = md5($query);
				if ($ob_subscribers = $this -> get_cache($query_hash)) {
					$subscribers = $ob_subscribers;
				} else {
					$subscribers = $wpdb -> get_results($query);
					$this -> set_cache($query_hash, $subscribers);
				}
				
				if (!empty($subscribers)) {
					$subscribercount = count($subscribers);
				}
			}
			
			if (!empty($users_count)) {
				$subscribercount += $users_count;
			}
			
			if (!empty($subscribercount)) {
				do_action('newsletters_admin_createnewsletter_subscribercount_result', $subscribercount);
				set_transient('newsletters_subscribercount', $query, (60 * 60));
				echo '<a href="" onclick="jQuery.colorbox({width:\'80%\', height:\'80%\', title:\'' . __('Selected Subscribers', $this -> plugin_name) . '\', href:\'' . admin_url('admin-ajax.php?action=newsletters_subscribercountdisplay') . '\'}); return false;"><i class="fa fa-eye"></i> ' . $subscribercount . ' ' . __('subscribers total', $this -> plugin_name) . '</a>';	
			} else {
				echo 0;	
			}
				
			exit();
			die();
		}
		
		function ajax_managementsubscribe() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Auth, $Subscriber, $Mailinglist;
			
			$errors = array();	
			$success = false;
			$successmessage = "";
			$otherlists = array();
			
			if ($mailinglists = $Mailinglist -> select()) {
				foreach ($mailinglists as $mailinglist_id => $mailinglist_title) {
					$otherlists[] = $mailinglist_id;	
				}
			}
			
			if (!empty($_POST['subscriber_id']) && !empty($_POST['mailinglist_id'])) {
				$Db -> model = $Subscriber -> model;
				
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']), false, false, true, true, false)) {
					
					$data = (array) $subscriber;
					
					$data['email'] = $subscriber -> email;
					$data['format'] = $subscriber -> format;
					$data['cookieauth'] = $Auth -> read_cookie();
					$Db -> model = $Mailinglist -> model;
					
					if ($mailinglist = $Db -> find(array('id' => $_POST['mailinglist_id']))) {
						$data['mailinglists'] = $data['list_id'] = array($_POST['mailinglist_id']);
						
						if ($mailinglist -> paid == "Y") {
							$data['active'] = "N";	
						} else {
							$data['active'] = "Y";
						}
					}
					
					do_action('newsletters_management_subscribe_before', $data);
					
					if ($Subscriber -> optin($data, false, false, false, true)) {
						$success = true;
						
						do_action('newsletters_management_subscribe_success', $data);
						
						$this -> delete_all_cache('all');
						$Db -> model = $Subscriber -> model;
						$subscriber = $Subscriber -> find(array('id' => $subscriber -> id), false, false, true, true, false);
						
						if ($mailinglist -> paid == "Y") {
							$successmessage = __('Subscription successful, please click the "Pay Now" button under current subscriptions to make a payment and activate your subscription.', $this -> plugin_name);
						} else {
							$successmessage = __('Subscription successful and activated.', $this -> plugin_name);
						}
					} else {
						$errors[] = __('Subscription was not successful.', $this -> plugin_name);
					}
					
					// Other lists…
					$subscribedlists = $Subscriber -> mailinglists($subscriber -> id, false, false, false);
					$alllists = $Mailinglist -> select(false);
					$otherlists = array();
					
					$otherlists = array();
					if (!empty($alllists)) {
						foreach ($alllists as $alist_id => $alist_title) {
							if (empty($subscribedlists) || (!empty($subscribedlists) && !in_array($alist_id, $subscribedlists))) {
								$otherlists[] = $alist_id;
							}
						}
					}
				} else {
					$errors[] = __('Subscriber cannot be read.', $this -> plugin_name);
				}
			} else {
				$errors[] = __('No subscriber/mailing list data posted.', $this -> plugin_name);
			}
			
			$this -> render('management' . DS . 'newsubscriptions', array('subscriber' => $subscriber, 'success' => $success, 'successmessage' => $successmessage, 'errors' => $errors, 'otherlists' => $otherlists), true, 'default');
			
			exit();
			die();
		}
		
		function ajax_managementactivate() {	
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$success = false;
			$successmessage = "";
			$errors = array();
			
			if (!empty($_POST)) {
				if (!empty($_POST['subscriber_id']) && !empty($_POST['mailinglist_id']) && !empty($_POST['activate'])) {
					global $wpdb, $Db, $Subscriber, $SubscribersList, $Html, $Auth, $Mailinglist, $Unsubscribe;
					
					if ($subscriber = $Auth -> logged_in()) {
						if ($subscriber -> id == $_POST['subscriber_id']) {
							$Db -> model = $Mailinglist -> model;
							$query = "SELECT * FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE `id` = '" . $_POST['mailinglist_id'] . "'";
							$mailinglist = $wpdb -> get_row($query);
							
							$paid = $mailinglist -> paid;
							$subscriber -> mailinglist_id = $mailinglist -> id;
							$Db -> model = $SubscribersList -> model;
							
							if ($_POST['activate'] == "N") {
								if (empty($subscriber -> mandatory) || $subscriber -> mandatory != "Y") {
									if ($Db -> delete_all(array('subscriber_id' => $_POST['subscriber_id'], 'list_id' => $_POST['mailinglist_id']))) {								
										$Db -> model = $Unsubscribe -> model;
										$unsubscribe_data = array('email' => $subscriber -> email, 'mailinglist_id' => $_POST['mailinglist_id'], 'comments' => $_POST['comments']);
										$_POST[$this -> pre . 'comments'] = $_POST['comments'];
										$Db -> save($unsubscribe_data, true);
									
										$Db -> model = $this -> Autoresponderemail() -> model;
										$Db -> delete_all(array('subscriber_id' => $_POST['subscriber_id'], 'list_id' => $_POST['mailinglist_id']));
										
										//send the administrator a notice
										$this -> admin_unsubscription_notification($subscriber, $_POST['mailinglist_id']);
										$this -> user_unsubscription_notification($subscriber, array($_POST['mailinglist_id']));
										
										//Should the subscriber be deleted?
										$deleted = false;
										if ($this -> get_option('unsubscribedelete') == "Y") {											
											$subscribedlists = $Subscriber -> mailinglists($subscriber -> id);	//all subscribed mailing lists		
											if (empty($subscribedlists) || !is_array($subscribedlists) || count($subscribedlists) <= 0) {																		
												$Db -> model = $Subscriber -> model;
												$Db -> delete($subscriber -> id);
												$deleted = true;
											}
										}
										
										$this -> delete_all_cache('all');
									
										if (!empty($deleted) || $deleted == true) {
											$message = __('You were deleted since no subscriptions remained but you can resubscribe at any time.', $this -> plugin_name);
											
											$afterdeleteurl = $Html -> retainquery('updated=1&success=' . $message, $this -> get_managementpost(true));
											$this -> redirect($afterdeleteurl, 'success', false, true);
										} else {
											$success = true;
											$successmessage = __('Subscription has been removed.', $this -> plugin_name);
											$subscriber = $Auth -> logged_in();
										}
									} else {
										$errors[] = __('Subscription could not be removed.', $this -> plugin_name);	
									}
								} else {
									$errors[] = __('You are a mandatory subscriber and cannot unsubscribe', $this -> plugin_name);
								}
							} else {							
								if (false && $this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {		
									$success = true;
									$successmessage = __('A confirmation email has been sent through to your email address.', $this -> plugin_name);
									$this -> subscription_confirm($subscriber);	
								} else {
									$Db -> model = $SubscribersList -> model;
									
									if ($Db -> save_field('active', "Y", array('subscriber_id' => $_POST['subscriber_id'], 'list_id' => $_POST['mailinglist_id']))) {
										$success = true;
										$successmessage = __('Subscription has been activated.', $this -> plugin_name);
										$this -> delete_all_cache('all');
										$subscriber = $Auth -> logged_in();
									} else {
										$errors[] = __('Subscription could not be activated.', $this -> plugin_name);	
									}
								}
							}
						} else {
							$errors[] = __('You are logged in as a different subscriber.', $this -> plugin_name);	
						}
					} else {
						$errors[] = __('You are not currently logged in.', $this -> plugin_name);	
					}
				} else {
					$errors[] = __('No subscriber/mailing list data posted.', $this -> plugin_name);
				}
			} else {
				$errors[] = __('No data was posted.', $this -> plugin_name);	
			}
			
			$this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber, 'errors' => $errors, 'success' => $success, 'successmessage' => $successmessage), true, 'default');
			
			exit();
			die();
		}
		
		function autoresponders_send($subscriber = null, $mailinglist = null) {
			global $wpdb, $Db, $HistoriesAttachment, $Subscriber, $SubscribersList, $Html, $Email;
			
			do_action('newsletters_autoresponders_send', $subscriber, $mailinglist);
		
			if (!empty($subscriber) && !empty($mailinglist)) {				
				$subscriber_id = $subscriber -> id;
				if ($autoresponserslists = $this -> AutorespondersList() -> find_all(array('list_id' => $mailinglist -> id))) {																												
					foreach ($autoresponserslists as $al) {
						$Db -> model = $this -> Autoresponder() -> model;
						if ($autoresponder = $Db -> find(array('id' => $al -> autoresponder_id, 'sendauto' => 1))) {														
							if (!empty($autoresponder -> status) && $autoresponder -> status == "active") {								
								//Send the 0 delay autoresponders right now
								if (!defined('NEWSLETTERS_IMPORTING') && (empty($autoresponder -> delay) || $autoresponder -> delay <= 0)) {																											
									$Db -> model = $SubscribersList -> model;
									$subscriberslist = $Db -> find(array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id));								
									if (!empty($subscriberslist -> active) && $subscriberslist -> active == "Y") {								
										$Db -> model = $this -> Autoresponderemail() -> model;
										if ((!empty($autoresponder -> alwayssend) && $autoresponder -> alwayssend == "Y") || (!$Db -> find(array('subscriber_id' => $subscriber -> id, 'autoresponder_id' => $autoresponder -> id)))) {									
											$history = $this -> History() -> find(array('id' => $autoresponder -> history_id));
											$history -> attachments = array();
											$attachmentsquery = "SELECT id, title, filename FROM " . $wpdb -> prefix . $HistoriesAttachment -> table . " WHERE history_id = '" . $history -> id . "'";
											
											if ($attachments =  $wpdb -> get_results($attachmentsquery)) {
												foreach ($attachments as $attachment) {
													$history -> attachments[] = array(
														'id'					=>	$attachment -> id,
														'title'					=>	$attachment -> title,
														'filename'				=>	$attachment -> filename,
													);	
												}
											}
											
											$subscriber -> mailinglist_id = $mailinglist -> id;
											$eunique = $Html -> eunique($subscriber, $history -> id);
											
											$autoresponderemail_data = array(
												'autoresponder_id'				=>	$autoresponder -> id,
												'list_id'						=>	$mailinglist -> id,
												'subscriber_id'					=>	$subscriber -> id,
												'senddate'						=>	date_i18n("Y-m-d H:i:s", strtotime("+" . $autoresponder -> delay . " " . $autoresponder -> delayinterval)),
												'status'						=>	'unsent',
											);
											
											$this -> Autoresponderemail() -> save($autoresponderemail_data, true);
											global $wpdb;
											$ae_id = $wpdb -> insert_id;
											
											$Db -> model = $Email -> model;
											$message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);
											
											if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique, true, "newsletter")) {								
												$this -> Autoresponderemail() -> save_field('status', "sent", array('id' => $ae_id));
												$this -> History() -> save_field('sent', 1, array('id' => $history -> id));
											}		
										}
									}
								//Save the 1+ delay autoresponders to send later
								} else {																		
									$Db -> model = $SubscribersList -> model;
									$subscriberslist = $Db -> find(array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id));								
									if (!empty($subscriberslist -> active) && $subscriberslist -> active == "Y") {																		
										$Db -> model = $this -> Autoresponderemail() -> model;
										if (!$Db -> find(array('subscriber_id' => $subscriber -> id, 'autoresponder_id' => $autoresponder -> id))) {																					
											if (empty($autoresponder -> delay) || empty($autoresponder -> delayinterval)) {
												$senddate = date_i18n("Y-m-d H:i:s");
											} else {
												$senddate = date_i18n("Y-m-d H:i:s", strtotime("+" . $autoresponder -> delay . " " . $autoresponder -> delayinterval));
											}											
											
											$autoresponderemail_data = array(
												'autoresponder_id'				=>	$autoresponder -> id,
												'list_id'						=>	$mailinglist -> id,
												'subscriber_id'					=>	$subscriber -> id,
												'status'						=>	'unsent',
												'senddate'						=>	$senddate,
											);
											
											$Db -> model = $this -> Autoresponderemail() -> model;
											$Db -> save($autoresponderemail_data, true);		
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
		function paidsubscription_form($subscriber = null, $mailinglist = null, $autosubmit = true, $target = "_self", $extend = false, $pmethod = "paypal") {
			global $Html;
		
			if (!empty($subscriber) && !empty($mailinglist)) {				
				if ($pmethod == "2co") {
					$checkoutdata = array(
						'sid'					=>	$this -> get_option('tcovendorid'),
						'cart_order_id'			=>	$subscriber -> id . $mailinglist -> id,
						'total'					=>	$mailinglist -> price,
						'id_type'				=>	1,
						'c_prod'				=>	$mailinglist -> id . ',1',
						'c_name'				=>	__($mailinglist -> title),
						'c_description'			=>	__($mailinglist -> title),
						'c_price'				=>	$mailinglist -> price,
						'quantity'				=>	1,
						'return_url'			=>	home_url() . '/?' . $this -> pre . 'method=twocheckout',
						'x_receipt_link_url'	=>	home_url() . '/?' . $this -> pre . 'method=twocheckout',
						$this -> pre . 'method'	=>	'twocheckout',
						'subscriber_id'			=>	$subscriber -> id,
						'subscriber_email'		=>	$subscriber -> email,
						'mailinglist_id'		=>	$mailinglist -> id,
						'fixed'					=>	'Y',
						'demo'					=>	$this -> get_option('tcodemo'),
						'currency_code'			=>	$this -> get_option('currency'),
						'email'					=>	$subscriber -> email,
					);
					
					if (!empty($extend)) {
						$checkoutdata['subscription_extend'] = 1;
					}
					
					$formid = '2co_paidsubscriptionform' . $mailinglist -> id;
					$this -> render('twocheckout-form', array('checkoutdata' => $checkoutdata, 'extend' => $extend, 'autosubmit' => $autosubmit, 'formid' => $formid, 'target' => $target));
				} elseif ($pmethod == "paypal") {				
					$pp_return = ($this -> get_option('paypalsubscriptions') == "Y" && $mailinglist -> interval != "once") ?
					$Html -> retainquery('method=paidsubscriptionsuccess', $this -> get_managementpost(true)) :
					$Html -> retainquery('method=paidsubscriptionsuccess', $this -> get_managementpost(true));
				
					$checkoutdata = array(
						'charset'							=>	get_option('blog_charset'),
						'return'							=>	$pp_return,
						'rm'								=>	2,
						'notify_url'						=>	home_url() . '/?' . $this -> pre . 'method=paypal',
						'cbt'								=>	__('Click here to complete your order', $this -> plugin_name),
						'currency_code'						=>	$this -> get_option('currency'),
						'business'							=>	$this -> get_option('paypalemail'),
						'item_name'							=>	__($mailinglist -> title),
						'item_number'						=>	$mailinglist -> id,
						'custom'							=>	array(
							'subscriber_id'			=>	$subscriber -> id,
							'mailinglist_id'		=>	$mailinglist -> id,
						),
						'no_shipping'						=>	1,
						'no_note'							=>	1,
						'bn'								=>	"TribulantSoftware_SP",
					);
					
					if (!empty($extend)) {
						$checkoutdata['custom']['subscription_extend'] = 1;
					}
					
					$checkoutdata['custom'] = urlencode(maybe_serialize($checkoutdata['custom']));
					
					if ($this -> get_option('paypalsubscriptions') == "Y" && $mailinglist -> interval != "once" && $extend == false) {
						$checkoutdata['cmd'] = "_xclick-subscriptions";
						$checkoutdata['a3'] = number_format($mailinglist -> price, 2, '.', '');
						$checkoutdata['p3'] = $Html -> getpptd($mailinglist -> interval);
						$checkoutdata['t3'] = $Html -> getppt($mailinglist -> interval);
						$checkoutdata['src'] = 1;
						$checkoutdata['sra'] = 1;
					} else {
						$checkoutdata['cmd'] = "_xclick";
						$checkoutdata['amount'] = $mailinglist -> price;
						$checkoutdata['quantity'] = 1;
					}
					
					$formid = 'paypal_paidsubscriptionform' . $mailinglist -> id;
					$this -> render('paypal-form', array('checkoutdata' => $checkoutdata, 'extend' => $extend, 'autosubmit' => $autosubmit, 'formid' => $formid, 'target' => $target));
				}
			}
		}
		
		function sc_management($atts = array(), $content = null) {
			global $wpdb, $Db, $Mailinglist, $Subscriber, $SubscribersList, $Auth, $Unsubscribe, 
			$newsletters_errors, $Field, $Html;
			
			if (!defined('NEWSLETTERS_IS_MANAGEMENT')) {
				define('NEWSLETTERS_IS_MANAGEMENT', true);
			}
			
			$errors = array();
			
			$output = "";
			$defaults = array();
			extract(shortcode_atts($defaults, $atts));
			$emailfield = $Field -> email_field();
			
			ob_start();
			
			$method = (!empty($_GET['method'])) ? $_GET['method'] : false;
			
			switch ($method) {
				case 'paidsubscription'			:			
					$subscriber_id = esc_html($_REQUEST['subscriber_id']);
					$list_id = esc_html($_REQUEST['list_id']);
					$extend = esc_html($_REQUEST['extend']);
						
					if (!empty($subscriber_id)) {
						if (!empty($list_id)) {
							
							$subscriber = $Subscriber -> get($subscriber_id);
							$data = (array) $subscriber;
							$data['list_id'] = $list_id;
							$mailinglist = $Mailinglist -> get($list_id);
							$extend = (empty($extend)) ? false : true;
							
							$Field -> validate_optin($data, 'management');
							
							if (empty($Field -> errors)) {
								$this -> render('management' . DS . 'paidsubscription', array('subscriber' => $subscriber, 'mailinglist' => $mailinglist, 'extend' => $extend), true, 'default');
							} else {
								$url = $Html -> retainquery('updated=1&error=' . __('Some profile fields are required, please complete them.', $this -> plugin_name) . '#profile', $this -> get_managementpost(true));
								$this -> redirect($url);	
							}							
						} else {
							$errors[] = __('No mailing list was specified', $this -> plugin_name);
						}
					} else {
						$errors[] = __('No subscriber was specified', $this -> plugin_name);
					}
				
					break;
				case 'paidsubscriptionsuccess'	:
					$message = __('Thank you for your payment! Please allow a moment for the subscription to be activated.', $this -> plugin_name);
					$this -> redirect($this -> get_managementpost(true), "success", $message);
					break;
				case 'subscribe'			:
				
					$errors = false;
					$success = false;
					
					if (!empty($_GET['id'])) {
						if (!empty($_GET['mailinglists'])) {
							$Db -> model = $Subscriber -> model;
							if ($subscriber = $Db -> find(array('id' => $_GET['id']))) {
								
								$subscriber -> mailinglists = explode(",", $_GET['mailinglists']);
								$subscriber -> active = "Y";
								
								if ($Subscriber -> optin($subscriber, false, false, false)) {
									$success = true;
									
									$Auth -> set_emailcookie($subscriber -> email, "+30 days");
									if (empty($subscriber -> cookieauth)) {
										$subscriberauth = $Auth -> gen_subscriberauth();
										$Db -> model = $Subscriber -> model;
										$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
									} else {
										$subscriberauth = $subscriber -> cookieauth;
									}
									
									$Auth -> set_cookie($subscriberauth, "+30 days", true);
								} else {
									$errors = $Subscriber -> errors;
								}
							} else {
								$errors[] = __('Subscriber cannot be read', $this -> plugin_name);
							}
						} else {
							$errors[] = __('No list was specified', $this -> plugin_name);
						}
					} else {
						$errors[] = __('No subscriber was specified', $this -> plugin_name);
					}
					
					$this -> render('management' . DS . 'subscribe', array('subscriber' => $subscriber, 'errors' => $errors, 'success' => $success), true, 'default');
					break;
				case 'resubscribe'			:				
					$errors = false;
					$success = false;
				
					if (!empty($_GET['email']) && (!empty($_GET['mailinglists']))) {
						
						$Db -> model = $Subscriber -> model;
						if (!$subscriber = $Db -> find(array('email' => $_GET['email']))) {
							$subscriber = array();
						}
						
						$subscriber = (array) $subscriber;
											
						$subscriber['email'] = $_GET['email'];
						$subscriber['mailinglists'] = explode(",", $_GET['mailinglists']);
						$subscriber['active'] = "Y";
					
						if ($Subscriber -> optin($subscriber, false, false, false)) {
							$success = true;
							
							$Auth -> set_emailcookie($subscriber -> email, "+30 days");
							if (empty($subscriber -> cookieauth)) {
								$subscriberauth = $Auth -> gen_subscriberauth();
								$Db -> model = $Subscriber -> model;
								$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
							} else {
								$subscriberauth = $subscriber -> cookieauth;
							}
							
							$Auth -> set_cookie($subscriberauth, "+30 days", true);
						} else {
							$errors = $Subscriber -> errors;
						}
					} else {
						$errors[] = __('Some data is missing, please try again', $this -> plugin_name);
					}
					
					$this -> render('resubscribe', array('subscriber' => $subscriber, 'errors' => $errors, 'success' => $success), true, 'default');
					break;
				case 'unsubscribe'			:
					global $wpdb, $Html, $Auth, $Db, $Subscriber, $Mailinglist, $SubscribersList, $Queue;
					
					$data = $_REQUEST;
					$dorender = true;
					$error = false;
					$success = false;
					$deleted = false;
					$userfile = false;
					
					$history_id = esc_html($data[$this -> pre . 'history_id']);
					$history = $this -> History() -> find(array('id' => $history_id));
					$subscriber_id = esc_html($data[$this -> pre . 'subscriber_id']);
					
					if (!empty($subscriber_id) || !empty($data['user_id'])) {
						if (($mailinglists = explode(",", $data[$this -> pre . 'mailinglist_id'])) !== false) {
							//do nothing, it's good...
							$includeonly = (empty($history -> mailinglists)) ? $mailinglists : $history -> mailinglists;
							$mailinglists = $Subscriber -> mailinglists($subscriber_id, $includeonly, false, false);
						} else {
							$mailinglists = false;
						}
					
						if (!empty($data[$this -> pre . 'subscriber_id'])) {
							$subscriber_query = "SELECT * FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE id = '" . $data[$this -> pre . 'subscriber_id'] . "'";
							
							$subscriber = $wpdb -> get_row($subscriber_query);
									
							if (!empty($subscriber)) {
								if ($subscriber -> authkey == $_GET['authkey']) {
									if (empty($subscriber -> mandatory) || $subscriber -> mandatory == "N") {
										/* Management Auth */
										if (empty($data['cookieauth'])) {
											$Auth -> set_emailcookie($subscriber -> email);
											$subscriberauth = $Auth -> gen_subscriberauth();
											$Db -> model = $Subscriber -> model;
											$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
											$Auth -> set_cookie($subscriberauth);
										}
									
										$subscriber -> mailinglists = $mailinglists;
										
										if (empty($subscriber -> mailinglists)) {
											$errors[] = __('This email was not sent to any lists.', $this -> plugin_name);
										}
									} else {
										$dorender = false;
										$errors[] = __('You are a mandatory subscriber and cannot unsubscribe', $this -> plugin_name);
									}
								} else {
									$dorender = false;
									$errors[] = __('You are not authorised to use this link', $this -> plugin_name);
								}
							} else {
								$errors[] = __('Your subscriber record cannot be read, please try again.', $this -> plugin_name);
							}	
						} elseif (!empty($data['user_id'])) {
							if ($user = $this -> userdata($data['user_id'])) {
								// all good
								$userfile = "-user";
							} else {
								$errors[] = __('User cannot be read', $this -> plugin_name);
							}
						} else {
							$errors[] = __('No subscriber or user was specified', $this -> plugin_name);
						}
					} else {
						$errors[] = __('No subscriber ID was specified, please try again.', $this -> plugin_name);
					}
					
					$clicktrack = $this -> get_option('clicktrack');
					if (!empty($clicktrack) && $clicktrack == "Y") {
						$click_data = array(
							'referer'			=>	"unsubscribe",
							'history_id'		=>	$_GET[$this -> pre . 'history_id'],
							'user_id'			=>	$_GET[$this -> pre . 'user_id'],
							'subscriber_id'		=>	$_GET[$this -> pre . 'subscriber_id'],
							'device'			=>	$this -> get_device()
						);
						
						$this -> Click() -> save($click_data, true);
					}
					
					if (!empty($data['confirm']) || $this -> get_option('unsubscribeconfirmation') == "N") {					
						$unsubscribeemails = $this -> get_option('unsubscribeemails');
						$unsubscribelists = $data['unsubscribelists'];
					
						if (!empty($data[$this -> pre . 'subscriber_id'])) {
							if ($this -> get_option('unsubscribeconfirmation') == "N") {
								$data['unsubscribelists'] = $mailinglists;
							}
						
							if (!empty($data['unsubscribelists'])) {	
								$this -> user_unsubscription_notification($subscriber, $mailinglists);
								
								$subscribedlists = $Subscriber -> mailinglists($subscriber -> id);	//all subscribed mailing lists	
																
								foreach ($data['unsubscribelists'] as $unsubscribelist_id) {
									$Db -> model = $Mailinglist -> model;
									$mailinglist = $Db -> find(array('id' => $unsubscribelist_id));
									$subscriber -> mailinglist_id = $unsubscribelist_id;
									
									if (!empty($unsubscribeemails) && $unsubscribeemails == "multiple") {
										$this -> admin_unsubscription_notification($subscriber, $unsubscribelist_id);
									}
									
									if (!empty($subscriber -> id) && !empty($unsubscribelist_id)) {
										$subscribedlists = array_diff($subscribedlists, array($unsubscribelist_id));
										$SubscribersList -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $unsubscribelist_id));
										$Db -> model = $Queue -> model;
										$Db -> delete_all(array('subscriber_id' => $subscriber -> id, 'mailinglist_id' => $unsubscribelist_id));
										$Db -> model = $this -> Autoresponderemail() -> model;
										$Db -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $unsubscribelist_id));
										
										$Db -> model = $Unsubscribe -> model;
										$unsubscribe_data = array('email' => $subscriber -> email, 'mailinglist_id' => $unsubscribelist_id, 'history_id' => $data[$this -> pre . 'history_id'], 'comments' => $data[$this -> pre . 'comments']);
										$Db -> save($unsubscribe_data, true);
									}
								}
								
								if (!empty($unsubscribeemails) && $unsubscribeemails == "single") {
									$this -> admin_unsubscription_notification($subscriber, $unsubscribelists);						
								}
								
								do_action('newsletters_subscriber_unsubscribe', $subscriber -> id, $data['unsubscribelists']);
													
								if ($this -> get_option('unsubscriberemoveallsubscriptions') == "Y") {						
									foreach ($subscribedlists as $subscribedlist_id) {
										if (!empty($subscriber -> id) && !empty($subscribedlist_id)) {
											$SubscribersList -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $subscribedlist_id));
											$Db -> model = $Queue -> model;
											$Db -> delete_all(array('subscriber_id' => $subscriber -> id, 'mailinglist_id' => $subscribedlist_id));
										}
									}
								}
								
								//Should the subscriber be deleted?
								if ($this -> get_option('unsubscribedelete') == "Y") {																				
									if (empty($subscribedlists) || !is_array($subscribedlists) || count($subscribedlists) <= 0) {							
										$Db -> model = $Subscriber -> model;
										$Db -> delete($subscriber -> id);
										$deleted = true;
									}
								}
								$success = true;
							} else {
								$errors[] = __('You did not select any list(s) to unsubscribe from.', $this -> plugin_name); 
								$success = false;
							}
						} elseif (!empty($data['user_id'])) {
							$Db -> model = $Unsubscribe -> model;
							$unsubscribe_data = array('user_id' => $user -> ID, 'email' => $user -> user_email, 'mailinglist_id' => false, 'history_id' => $data[$this -> pre . 'history_id'], 'comments' => $data[$this -> pre . 'comments']);
							$Db -> save($unsubscribe_data, true);
							$success = true;
							$errors = false;
						} else {
							$errors[] = __('No subscriber or user was specified', $this -> plugin_name);
						}
					}
				
					$this -> render('unsubscribe-success' . $userfile, array('subscriber' => $subscriber, 'history' => $history, 'dorender' => $dorender, 'user' => $user, 'data' => $data, 'errors' => $errors, 'success' => $success, 'deleted' => $deleted), true, 'default');
					break;
				case 'logout'				:
					global $wpmljavascript;
					$subscriberemailauth = $Auth -> read_emailcookie();
					$subscriberauth = $Auth -> read_cookie();
					
					$managementauthtype = $this -> get_option('managementauthtype');
					switch ($managementauthtype) {
						case 1					:
							$Auth -> delete_cookie($Auth -> cookiename, $subscriberauth);
							$Auth -> delete_cookie($Auth -> emailcookiename, $subscriberemailauth);
							break;
						case 2					:
							$this -> end_session();
							break;
						case 3					:
						default 				:
							$Auth -> delete_cookie($Auth -> cookiename, $subscriberauth);
							$Auth -> delete_cookie($Auth -> emailcookiename, $subscriberemailauth);
							$this -> end_session();
							break;
					}
							
					echo $wpmljavascript;
					$this -> render('management' . DS . 'logout-auth', false, true, 'default');
					break;
				case 'loginauth'			:				
					if (empty($_GET['email'])) {
						$subscriberemailauth = $_POST['email'] = $Auth -> read_emailcookie();
					} else {
						$subscriberemailauth = $_GET['email'];	
					}
					
					$subscriberauth = $_GET['subscriberauth'];
					
					if (!empty($subscriberemailauth)) {
						if (!empty($subscriberauth)) {
							$Db -> model = $Subscriber -> model;							
							if ($subscriber = $Db -> find(array('email' => $subscriberemailauth, 'cookieauth' => $subscriberauth))) {
								global $wpmljavascript;
								$Auth -> set_cookie($subscriber -> cookieauth);
								$Auth -> set_emailcookie($subscriberemailauth);
							} else {
								$errors[] = __('Authentication failed, please try again.', $this -> plugin_name);
							}
						} else {
							$errors[] = __('No authentication string passed, please click the link again.', $this -> plugin_name);
						}
					} else {
						$errors[] = __('No email saved, please try again.', $this -> plugin_name);
					}
					
					if (empty($errors)) {
						$this -> render('management' . DS . 'login-auth', array('subscriberauth' => $subscriberauth, 'subscriberemailauth' => $subscriberemailauth), true, 'default');
					} else {
						$this -> render('management' . DS . 'login', array('errors' => $errors), true, 'default');	
					}
					break;
				case 'login'				:
					$errors = array_merge($errors, $newsletters_errors);					
					$this -> render('management' . DS . 'login', array('errors' => $errors), true, 'default');	
					break;
				default						:						
					if (!empty($_GET['subscriberauth'])) {
						$_COOKIE['subscriberauth'] = $_GET['subscriberauth'];
					}
							
					if ($subscriber = $Auth -> logged_in()) {
						if ($this -> get_option('subscriptions') == "Y") {
							$SubscribersList -> check_expirations(false, false, true, $subscriber -> id);
						}
						
						$subscriber = $Auth -> logged_in();
						$this -> render('management' . DS . 'index', array('subscriber' => $subscriber), true, 'default');
					} else {
						$this -> render('management' . DS . 'login', false, true, 'default');	
					}
					break;	
			}
			
			global $wpmljavascript;
			if (!empty($wpmljavascript)) {
				echo $wpmljavascript;
			}
			
			$output = ob_get_clean();			
			global $Html;
			$output = $Html -> fragment_cache($output, 'this', 'sc_management', false);
			return $output;
		}
		
		function is_plugin_screen($screen = null) {
			if (!empty($_GET['page'])) {
				if (!empty($screen)) {
					if (in_array($_GET['page'], (array) $this -> sections -> {$screen})) {
						return true;
					}
				} else {
					if (in_array($_GET['page'], (array) $this -> sections)) {
						return true;	
					}
				}
			}
			
			return false;
		}
		
		function plugins_loaded() {
			$this -> initialize_classes();
			$this -> ci_initialize();
			$this -> theme_folder_functions();
			
			return;
		}
		
		function manage_users_columns($columns = array()) {
		    $columns['newsletters'] = __('Newsletters', $this -> plugin_name);
		    return $columns;
		}
			
		function manage_users_custom_column($value = null, $column_name = null, $user_id = null) {				
			switch ($column_name) {
				case 'newsletters'				:
					global $Db, $Email;
					
					$newsletters = 0;
					
					if (!empty($user_id)) {
						$Db -> model = $Email -> model;
						if ($emails_count = $Db -> count(array('user_id' => $user_id))) {
							$newsletters = $emails_count;
						}
					}
					
					return $newsletters;
					break;
			}
				
		    return $value;
		}
		
		function language_useordefault($content) {				
			$text = $content;
			
			if (!empty($text)) {
				$current_language = $this -> language_current();
				$language = (empty($current_language)) ? $this -> language_default() : $current_language;
				$text = $this -> language_use($language, $content, false);
			}
			
			return $text;
		}
		
		function language_use($lang = null, $text = null, $show_available = false) {
		
			if (!$this -> language_isenabled($lang)) { 
				return $text;
			}
			
			if (is_array($text) || is_object($text)) {				
				// handle arrays recursively
				if (is_array($text)) {
					foreach($text as $key => $t) {
						$text[$key] = $this -> language_use($lang, $text[$key], $show_available);
					}
				} elseif (is_object($text)) {
					foreach($text as $key => $t) {
						$text -> {$key} = $this -> language_use($lang, $text -> {$key}, $show_available);
					}
				}
				
				return $text;
			}
			
			if(is_object($text) && get_class($text) == '__PHP_Incomplete_Class') {
				foreach(get_object_vars($text) as $key => $t) {
					$text->$key = $this -> language_use($lang,$text -> $key,$show_available);
				}
				return $text;
			}
			
			// prevent filtering weird data types and save some resources
			if(!is_string($text) || $text == '') {
				return $text;
			}
			
			// get content
			$content = $this -> language_split($text);
			
			if (!is_array($content)) {
				return $content;
			}
			
			// find available languages
			$available_languages = array();
			foreach($content as $language => $lang_text) {
				$lang_text = trim($lang_text);
				if(!empty($lang_text)) $available_languages[] = $language;
			}
			
			// if no languages available show full text
			if(sizeof($available_languages)==0) return $text;
			// if content is available show the content in the requested language
			if(!empty($content[$lang])) {
				return $content[$lang];
			}
			// content not available in requested language (bad!!) what now?
			if(!$show_available){
				// check if content is available in default language, if not return first language found. (prevent empty result)
				if($lang != $this -> language_default()) {
					//if (!is_admin()) {
						$str = $this -> language_use($this -> language_default(), $text, $show_available);
					//}
					
					if ($q_config['show_displayed_language_prefix'])
						$str = "(". $this -> language_name($this -> language_default()) .") " . $str;
					return $str;
				}
				foreach($content as $language => $lang_text) {
					$lang_text = trim($lang_text);
					if (!empty($lang_text)) {
						$str = $lang_text;
						if ($q_config['show_displayed_language_prefix'])
							$str = "(". $this -> language_name($language) .") " . $str;
						return $str;
					}
				}
			}
			// display selection for available languages
			$available_languages = array_unique($available_languages);
			$language_list = "";
			if(preg_match('/%LANG:([^:]*):([^%]*)%/',$q_config['not_available'][$lang],$match)) {
				$normal_seperator = $match[1];
				$end_seperator = $match[2];
				// build available languages string backward
				$i = 0;
				foreach($available_languages as $language) {
					if($i==1) $language_list  = $end_seperator.$language_list;
					if($i>1) $language_list  = $normal_seperator.$language_list;
					$language_list = "<a href=\"". $this -> language_converturl('', $language)."\">". $this -> language_name($language) ."</a>".$language_list;
					$i++;
				}
			}
			return "<p>".preg_replace('/%LANG:([^:]*):([^%]*)%/', $language_list, $q_config['not_available'][$lang])."</p>";
		}
		
		function language_converturl($url = null, $language = null) {
			global $newsletters_languageplugin;
		
			if (!empty($url) && !empty($language)) {
				switch ($newsletters_languageplugin) {
					case 'qtranslate'				:
						$url = qtrans_convertURL($url, $language);
						break;
					case 'qtranslate-x'				:
						$url = qtranxf_convertURL($url, $language);
						break;
					case 'wpml'						:
						if (function_exists('icl_get_languages')) {
							$languages = icl_get_languages();
							$language = $this -> language_current();
							
							if (!empty($languages[$language]['url'])) {
								//$url = $languages[$language]['url'];
							}
						}
						break;
				}
			}
			
			return $url;
		}
		
		function language_default() {		
			global $newsletters_languageplugin, $newsletters_languagedefault;
			$default = false;
			
			if (!empty($newsletters_languagedefault)) {
				return $newsletters_languagedefault;
			}
			
			switch ($newsletters_languageplugin) {
				case 'qtranslate'				:
				case 'qtranslate-x'				:
					global $q_config;
					$default = $q_config['default_language'];
					break;
				case 'wpml'						:
					global $sitepress;
					if (method_exists($sitepress, 'get_default_language')) {
						$default = $sitepress -> get_default_language();
					}
					break;
			}
			
			$newsletters_languagedefault = $default;
			return $default;
		}
		
		function language_name($language = null) {
			$name = false;
		
			if (!empty($language)) {
				global $newsletters_languageplugin, ${'newsletters_languagename_' . $language};
				
				if (!empty(${'newsletters_languagename_' . $language})) {
					return ${'newsletters_languagename_' . $language};
				}
				
				switch ($newsletters_languageplugin) {
					case 'qtranslate'				:
					case 'qtranslate-x'				:
						global $q_config;
						$name = $q_config['language_name'][$language];
						break;
					case 'wpml'						:
						if (function_exists('icl_get_languages')) {
							$languages = icl_get_languages();
							if (!empty($languages[$language]['translated_name'])) {
								$name = $languages[$language]['translated_name'];
							}
						}
						break;
				}
			}
			
			${'newsletters_languagename_' . $language} = $name;
			return $name;
		}
		
		function language_do() {
			global $newsletters_languageplugin;
		
			if (empty($newsletters_languageplugin)) {			
				if ($this -> is_plugin_active('qtranslate')) {
					$newsletters_languageplugin = "qtranslate";
					return true;
				} elseif ($this -> is_plugin_active('qtranslate-x')) {
					$newsletters_languageplugin = 'qtranslate-x';
					return true;
				} elseif ($this -> is_plugin_active('wpml')) {
					$newsletters_languageplugin = "wpml";
					
					if (!empty($_GET['lang']) && $_GET['lang'] == "all") {
						return false;
					}
				
					return true;
				}
			} else {
				return true;
			}
			
			return false;
		}
		
		function language_set($language = null) {
			global $newsletters_languageplugin, $newsletters_languagecurrent;
			$this -> language_do();
			
			if (!empty($language) && !empty($newsletters_languageplugin)) {
				$newsletters_languagecurrent = $language;
				
				switch ($newsletters_languageplugin) {
					case 'qtranslate-x'					:
						if (function_exists('qtranxf_set_language_cookie')) {
							qtranxf_set_language_cookie($language);
						}
						break;
					case 'wpml'							:
						global $sitepress;
						if (method_exists($sitepress, 'switch_lang')) {
							$sitepress -> switch_lang($language, true);
						}
						break;
				}
				
				return true;
			}
			
			return false;
		}
		
		function language_current() {
			global $newsletters_languageplugin, $newsletters_languagecurrent;
			$current = false;
			
			if (!empty($newsletters_languagecurrent)) {
				return $newsletters_languagecurrent;
			}
			
			switch ($newsletters_languageplugin) {
				case 'qtranslate'			:
					if (function_exists('qtrans_getLanguage')) {
						$current = qtrans_getLanguage();
					}
					break;
				case 'qtranslate-x'			:
					if (function_exists('qtranxf_getLanguage')) {
						$current = qtranxf_getLanguage();
					}
					break;
				case 'wpml'					:
					//$current = ICL_LANGUAGE_CODE;
					global $sitepress;
					$current = $sitepress -> get_current_language();
					break;
			}
			
			$newsletters_languagecurrent = $current;
			return $current;
		}
		
		function language_flag($language = null) {
			global $newsletters_languageplugin, ${'newsletters_languageflag_' . $language};
			$flag = false;
			
			if (!empty(${'newsletters_languageflag_' . $language})) {
				return ${'newsletters_languageflag_' . $language};
			}
		
			switch ($newsletters_languageplugin) {
				case 'qtranslate'			:
				case 'qtranslate-x'			:
					global $q_config;
					$flag = '<img src="' . content_url() . '/' . $q_config['flag_location'] . '/' . $q_config['flag'][$language] . '" alt="' . $language . '" />';
					break;
				case 'wpml'					:
					if (function_exists('icl_get_languages')) {
						$languages = icl_get_languages();
						$flag = '<img src="' . $languages[$language]['country_flag_url'] . '" alt="' . $language . '" />';
					}
					break;
			}
			
			${'newsletters_languageflag_' . $language} = $flag;
			return $flag;
		}
		
		function language_isenabled($language = null) {
			$enabled = false;
		
			if (!empty($language)) {
				global $newsletters_languageplugin, ${'newsletters_languageenabled_' . $language};
				
				if (!empty(${'newsletters_languageenabled_' . $language})) {
					return ${'newsletters_languageenabled_' . $language};
				}
			
				switch ($newsletters_languageplugin) {
					case 'qtranslate'				:
						$enabled = qtrans_isEnabled($language);
						break;
					case 'qtranslate-x'				:
						$enabled = qtranxf_isEnabled($language);
						break;
					case 'wpml'						:
						if (function_exists('icl_get_languages')) {
							$languages = icl_get_languages();
							if (!empty($languages[$language])) {
								$enabled = true;
							}
						}
						break;
				}
			}
			
			${'newsletters_languageenabled_' . $language} = $enabled;
			return $enabled;
		}
		
		function language_join($texts = array(), $tagTypeMap = array(), $strip_tags = false) {
			if(!is_array($texts)) $texts = $this -> language_split($texts, false);
			$split_regex = "#<!--more-->#ism";
			$max = 0;
			$text = "";
			$languages = $this -> language_getlanguages();
			
			foreach ($languages as $language) {
				$tagTypeMap[$language] = true;
			}
			
			foreach($languages as $language) {
				if (!empty($texts[$language])) {
					$texts[$language] = preg_split($split_regex, $texts[$language]);
					if(sizeof($texts[$language]) > $max) $max = sizeof($texts[$language]);
				}
			}
			
			for ($i = 0; $i < $max; $i++) {
				if($i>=1) {
					$text .= '<!--more-->';
				}
				foreach($languages as $language) {
					if (isset($texts[$language][$i]) && $texts[$language][$i] !== '') {
						
						if ($strip_tags) {
							$texts[$language][$i] = strip_tags($texts[$language][$i]);
						}
						
						if (empty($tagTypeMap[$language]))
							$text .= '<!--:'.$language.'-->'.$texts[$language][$i].'<!--:-->';
						else
							$text .= "[:{$language}]{$texts[$language][$i]}";
					}
				}
			}
			
			return $text;
		}
		
		function language_split($text, $quicktags = true, array $languageMap = NULL) {
			$array = false;
			
			if (!empty($text)) {	
				//init vars
				$split_regex = "#(<!--[^-]*-->|\[:[a-z-]{2,10}\])#ism";
				$current_language = "";
				$result = array();
				
				$languages = $this -> language_getlanguages();
				foreach ($languages as $language) {
					$result[$language] = "";
				}
				
				// split text at all xml comments
				$blocks = preg_split($split_regex, $text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
				
				foreach($blocks as $block) {
					# detect language tags
					if(preg_match("#^<!--:([a-z-]{2,10})-->$#ism", $block, $matches)) {
						if($this -> language_isenabled($matches[1])) {
							$current_language = $matches[1];
							$languageMap[$current_language] = false;
						} else {
							$current_language = "invalid";
						}						
						continue;
					// detect quicktags
					} elseif($quicktags && preg_match("#^\[:([a-z-]{2,10})\]$#ism", $block, $matches)) {						
						if($this -> language_isenabled($matches[1])) {
							$current_language = $matches[1];
							$languageMap[$current_language] = true;
						} else {
							$current_language = "invalid";
						}
						
						continue;
					} elseif(preg_match("#^<!--:-->$#ism", $block, $matches)) {
						$current_language = "";
						continue;
					} elseif(preg_match("#^<!--more-->$#ism", $block, $matches)) {
						foreach($languages as $language) {
							$result[$language] .= $block;
						}
						
						continue;
					}
					
					if($current_language == "") {
						foreach($languages as $language) {
							$result[$language] .= $block;
						}
					} elseif($current_language != "invalid") {
						$result[$current_language] .= $block;
					}
				}
				
				foreach($result as $lang => $lang_content) {
					$result[$lang] = str_replace('[:]', '', preg_replace("#(<!--more-->|<!--nextpage-->)+$#ism", "", $lang_content));
				}
				
				return $result;
			}
			
			return $array;
		}
		
		function language_getlanguages() {
			global $newsletters_languageplugin, $newsletters_languagelanguages;
			$languages = false;
			
			if (!empty($newsletters_languagelanguages)) {
				return $newsletters_languagelanguages;
			}
		
			switch ($newsletters_languageplugin) {
				case 'qtranslate'					:
					if (function_exists('qtrans_getSortedLanguages')) {
						$languages = qtrans_getSortedLanguages();
					}
					break;
				case 'qtranslate-x'					:
					if (function_exists('qtranxf_getSortedLanguages')) {
						$languages = qtranxf_getSortedLanguages();
					}
					break;
				case 'wpml'							:
					if (function_exists('icl_get_languages')) {
						$icl_languages = icl_get_languages();
						$languages = array();
						foreach ($icl_languages as $lang => $icl_language) {
							$languages[] = $lang;
						}
					}
					break;
			}
			
			$newsletters_languagelanguages = $languages;
			return $languages;
		}
		
		function paginate($model = null, $fields = '*', $sub = null, $conditions = false, $searchterm = null, $per_page = 10, $order = array('modified', "DESC"), $conditions_and = null) {
			global $wpdb, $Db, $Subscriber, $SubscribersList, $Mailinglist, 
			${$model}, $Mailinglist, $Unsubscribe, $Bounce;
			
			$object = (!is_object(${$model})) ? $this -> {$model}() : ${$model};						
			$conditions = apply_filters('newsletters_admin_paginate_' . strtolower($object -> model) . '_conditions', $conditions);
			$conditions_and = apply_filters('newsletters_admin_paginate_' . strtolower($object -> model) . '_conditions_and', $conditions_and);
		
			if (!empty($model)) {			
				global $paginate;
				$paginate = $this -> vendor('paginate');
				$paginate -> plugin_name = $this -> plugin_name;
				$paginate -> model = $model;
				$paginate -> table = $wpdb -> prefix . $this -> pre . $object -> controller;
				$paginate -> sub = (empty($sub)) ? $object -> controller : $sub;
				$paginate -> fields = (empty($fields)) ? '*' : $fields;
				$paginate -> where = (empty($conditions)) ? false : $conditions;
				$paginate -> where_and = (empty($conditions_and)) ? false : $conditions_and;
				$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
				$paginate -> per_page = $per_page;
				$paginate -> order = $order;
			
				$page = (empty($_GET[$this -> pre . 'page'])) ? 1 : $_GET[$this -> pre . 'page'];	
				$data = $paginate -> start_paging($page);
				
				if (!empty($data)) {
					$newdata = array();
					$n = 0;
				
					foreach ($data as $record) {
						//$newdata[$n] = $this -> init_class($model, $record);
						$newdata[$n] = $record;
						
						switch ($model) {
							case 'Bounce'						:
							case 'Unsubscribe'					:
								$newdata[$n] = $this -> init_class($model, $record);
								break;
							case 'History'						:
								$newdata[$n] = $this -> init_class($this -> History() -> model, $record);
								break;
							case 'Subscriber'					:							
								$Db -> model = $SubscribersList -> model;
								if ($subscriberslists = $Db -> find_all(array('subscriber_id' => $record -> id))) {
									foreach ($subscriberslists as $sl) {
										$listquery = "SELECT * FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE id = '" . $sl -> list_id . "' LIMIT 1";
										$list = $wpdb -> get_row($listquery);
										$newdata[$n] -> Mailinglist[] = $list;
									}
								}
								break;
							case 'SubscribersList'				:
								$Db -> model = $Subscriber -> model;
								
								if ($subscriber = $Db -> find(array('id' => $record -> subscriber_id))) {								
									$newdata[$n] = $subscriber;
									
									foreach ($record as $rkey => $rval) {
										$newdata[$n] -> {$rkey} = $rval;	
									}
								}
								break;
							case 'Autoresponder'				:	
								/* Pending Emails */					
								//$newdata[$n] -> pending = $this -> Autoresponderemail() -> count(array('autoresponder_id' => $record -> id, 'status' => "unsent"));
								
								/* Mailing Lists */
								$newdata[$n] -> mailinglists = array();
								
								if ($autoresponderslists = $this -> AutorespondersList() -> find_all(array('autoresponder_id' => $record -> id))) {													
									foreach ($autoresponderslists as $autoresponderslist) {
										$Db -> model = $Mailinglist -> model;
										$newdata[$n] -> lists[] = $autoresponderslist -> list_id;
										$newdata[$n] -> mailinglists[] = $Db -> find(array('id' => $autoresponderslist -> list_id));
									}
								}
								break;
							case 'Autoresponderemail'			:
								/* Autoresponder */
								$Db -> model = $this -> Autoresponder() -> model;
								$newdata[$n] -> autoresponder = $Db -> find(array('id' => $record -> autoresponder_id), false, false, false, false);
								
								/* Subscriber */
								$Db -> model = $Subscriber -> model;
								$newdata[$n] -> subscriber = $Db -> find(array('id' => $record -> subscriber_id), false, false, false, false);
								break;	
						}
						
						$n++;
					}
					
					$data = array();
					$data[$model] = (object) $newdata;
					$data['Paginate'] = $paginate;
				}
		
				return $data;
			}
			
			return false;
		}
		
		function ci_print_styles() {
			wp_enqueue_style('newsletters', $this -> render_url('css/wp-mailinglist.css', 'admin', false), null, $this -> version, "all");
			wp_enqueue_style('colorbox', plugins_url() . '/' . $this -> plugin_name . '/css/colorbox.css', false, $this -> version, "all");
		}
		
		function ci_print_scripts() {
			wp_enqueue_script($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/js/wp-mailinglist.js', array('jquery'), '1.0', true);	
			wp_enqueue_script('colorbox', plugins_url() . '/' . $this -> plugin_name . '/js/colorbox.js', array('jquery'), false, true);
		}
		
		function print_scripts() {
			$this -> enqueue_scripts();	
		}
		
		function print_styles() {
			$this -> enqueue_styles();	
		}
		
		function enqueue_scripts() {	
			global $wp_locale, $Html, $Db;
			
			//enqueue jQuery JS Library
			if (apply_filters('newsletters_enqueuescript_jquery', true)) { wp_enqueue_script('jquery'); }			
	
			if (is_admin() && !defined('DOING_AJAX')) {	
				
				$donotloadpages = array(
					'codestyling-localization/codestyling-localization.php'
				);
				
				if (!empty($_GET['page']) && in_array($_GET['page'], $donotloadpages)) {
					return;
				}
				
				if (apply_filters('newsletters_enqueuescript_jqueryuicore', true)) { wp_enqueue_script('jquery-ui-core'); }
				if (apply_filters('newsletters_enqueuescript_jqueryuiwidget', true)) { wp_enqueue_script('jquery-ui-widget'); }
				
				$screen = get_current_screen();
				
				// Charts
				if ($screen -> id == "dashboard" || preg_match("/(index.php)/si", $_SERVER['REQUEST_URI']) || 
					(!empty($_GET['page']) && ($_GET['page'] == $this -> sections -> welcome || $_GET['page'] == $this -> sections -> history))) {
					wp_enqueue_script('chartjs', $this -> render_url('js/chartjs/Chart.js', 'admin', false), array('jquery'), false, false);
				}
					
				// Tooltips
				if (preg_match("/(index\.php|widgets\.php|post\.php|post\-new\.php)/", $_SERVER['REQUEST_URI'], $matches)) {									
					wp_enqueue_script('jquery-ui-tooltip', false, array('jquery'), false, true);
				}
				
				if ((!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) || preg_match("/(post\.php|post\-new\.php)/", $_SERVER['REQUEST_URI'], $matches)) {
					
					// Select 2
					if (in_array($_GET['page'], (array) $this -> sections)) {						
						wp_deregister_script('select2');
						wp_deregister_script('wc-enhanced-select');
						wp_enqueue_script('select2', $this -> render_url('js/select2.js', 'admin', false), array('jquery'), '4.0.0', false);
					}
					
					if ($_GET['page'] != $this -> sections -> send) {
						wp_enqueue_media();
					}
					
					// CKEditor
					wp_enqueue_script('ckeditor', '//cdn.ckeditor.com/4.4.7/full-all/ckeditor.js', array('jquery'), '4.4.7', false);
					wp_enqueue_script('ckeditor-jquery', $this -> render_url('vendors/ckeditor/adapters/jquery.js', 'admin', false), array('ckeditor', 'jquery'), "4.4.7", false);
				
					// Color Picker
					wp_enqueue_script('iris', admin_url('js/iris.min.js'), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1);
				    wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array( 'iris' ), false, 1);
				    wp_enqueue_script('jquery-ui-tooltip', false, array('jquery'), false, true);
				    //wp_enqueue_script('jquery-ui-button', false, array('jquery'), false, true);
				}
			
				if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {					
					wp_enqueue_script('jquery-autoheight', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.autoheight.js', array('jquery'), false, true);
					wp_enqueue_script('jquery-form', false, array('jquery'), false, true);
					
					//countdown script
					if (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> queue) {
						wp_enqueue_script('jquery-countdown', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.countdown.js', array('jquery'), false, true);
					}
					
					//sortables
					if ($_GET['page'] == $this -> sections -> fields && $_GET['method'] == "order") {
						wp_enqueue_script('jquery-ui-sortable', false, false, false, true);
					}
					
					/* Progress Bar */
					if ($_GET['page'] == $this -> sections -> importexport ||
						$_GET['page'] == $this -> sections -> send) {
						wp_enqueue_script('jquery-ui-progressbar', false, array('jquery-ui-core', 'jquery-ui-widget'));
					}
					
					if ($_GET['page'] == $this -> sections -> welcome ||
						$_GET['page'] == $this -> sections -> send || 
						$_GET['page'] == $this -> sections -> forms || 
						$_GET['page'] == $this -> sections -> autoresponders ||
						$_GET['page'] == $this -> sections -> templates_save ||
						$_GET['page'] == $this -> sections -> settings ||
						$_GET['page'] == $this -> sections -> settings_templates ||
						$_GET['page'] == $this -> sections -> settings_subscribers ||
						$_GET['page'] == $this -> sections -> settings_system ||
						$_GET['page'] == $this -> sections -> extensions_settings) {																																							
							//meta boxes
							wp_enqueue_script('common', false, false, false, true);
							wp_enqueue_script('wp-lists', false, false, false, true);
							wp_enqueue_script('postbox', false, false, false, true);
							
							//editor
							wp_enqueue_script('editor', false, false, false, true);
							wp_enqueue_script('quicktags', false, false, false, true);
							wp_enqueue_script('wplink', false, false, false, true);
							wp_enqueue_script('wpdialogs-popup', false, false, false, true);
							wp_enqueue_style('wp-jquery-ui-dialog', false, false, false, true);
							wp_enqueue_script('word-count', false, false, false, true);
							wp_enqueue_script('media-upload', false, false, false, true);
							wp_admin_css();
							wp_enqueue_script('utils', false, false, false, true);
							
							//editors files
							if ($_GET['page'] == $this -> sections -> welcome) { wp_enqueue_script('welcome-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/welcome-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> send) { wp_enqueue_script('send-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/send-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> forms) { wp_enqueue_script('forms-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/forms-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> templates_save) { wp_enqueue_script('templates-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/templates-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings) { wp_enqueue_script('settings-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_templates) { wp_enqueue_script('settings-editor-templates', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-templates.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_subscribers) { wp_enqueue_script('settings-editor-subscribers', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-subscribers.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_system) { wp_enqueue_script('settings-editor-system', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-system.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> extensions_settings) { wp_enqueue_script('settings-editor-extensions-settings', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-extensions-settings.js', array('jquery'), false, true); }
					}
				}
				
				add_thickbox();				
				wp_enqueue_script($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/js/wp-mailinglist.js', array('jquery'), '1.0', true);
				wp_enqueue_script('jquery-ui-tabs');
				wp_enqueue_script('jquery-shiftclick', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.shiftclick.js', array('jquery'));
				wp_enqueue_script('jquery-ui-droppable');
				wp_enqueue_script('jquery-ui-datepicker');
				wp_enqueue_script('colorbox', plugins_url() . '/' . $this -> plugin_name . '/js/colorbox.js', array('jquery'), false, true);
				
				//add our instantiator js
			    wp_enqueue_script('datepicker-i18n', $this -> render_url('js/datepicker-i18n.js', 'admin', false), array('jquery-ui-datepicker'));
			    
			    $isRTL = (empty($wp_locale -> is_rtl)) ? false : true;
			 
			    //localize our js
			    $aryArgs = array(
			        'closeText'         => __('Done', $this -> plugin_name),
			        'currentText'       => __('Today', $this -> plugin_name),
			        'monthNames'        => $Html -> strip_array_indices($wp_locale -> month),
			        'monthNamesShort'   => $Html -> strip_array_indices($wp_locale -> month_abbrev),
			        'monthStatus'       => __('Show a different month', $this -> plugin_name),
			        'dayNames'          => $Html -> strip_array_indices($wp_locale -> weekday),
			        'dayNamesShort'     => $Html -> strip_array_indices($wp_locale -> weekday_abbrev),
			        'dayNamesMin'       => $Html -> strip_array_indices($wp_locale -> weekday_initial),
			        'dateFormat'        => $Html -> date_format_php_to_js(get_option('date_format')),
			        'firstDay'          => get_option('start_of_week'),
			        'isRTL'             => $isRTL,
			    );
			 
			    // Pass the localized array to the enqueued JS
			    wp_localize_script('datepicker-i18n', 'objectL10n', $aryArgs);
				
			/* Front-End Scripts */
			} else {					
				$loadscripts = $this -> get_option('loadscripts');
				$loadscripts_handles = $this -> get_option('loadscripts_handles');
				$loadscripts_pages = $this -> get_option('loadscripts_pages');
				
				include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');
				
				if (!empty($defaultscripts)) {
					foreach ($defaultscripts as $handle => $script) {						
						$custom_handle = (empty($loadscripts_handles[$handle])) ? $handle : $loadscripts_handles[$handle];
						$custom_pages = (empty($loadscripts_pages[$handle])) ? false : explode(",", $loadscripts_pages[$handle]);

						if ((!empty($loadscripts) && in_array($handle, $loadscripts)) || wpml_is_management()) {						
							if (empty($custom_pages) || (!empty($custom_pages) && (is_single($custom_pages) || is_page($custom_pages)))) {	
									if (apply_filters('newsletters_enqueuescript_' . $handle, true)) {																				
										wp_enqueue_script($custom_handle, $script['url'], $script['deps'], $script['version'], $script['footer']);
										do_action('newsletters_enqueuescript_after', $handle, $script);
									}
							}
						}
					}
				}
				
				if (apply_filters('newsletters_enqueuescript_' . $this -> plugin_name, true)) { wp_enqueue_script($this -> plugin_name, $this -> render_url('js/wp-mailinglist.js', 'admin', false), array('jquery'), false, true); }
				wp_enqueue_script('newsletters-functions', $this -> render_url('js/functions.js', 'default', false), array('jquery'), false, true);
			}
			
			return true;
		}
		
		function enqueue_styles() {
			$load = false;
			$theme_folder = $this -> get_option('theme_folder');
			
			// Admin dashboard
			if (is_admin() && !defined('DOING_AJAX')) {			
				wp_enqueue_style('wp-color-picker');
				wp_enqueue_style('colorbox', $this -> render_url('css/colorbox.css', 'admin', false), false, $this -> version, "all");
				wp_enqueue_style('fontawesome', $this -> render_url('css/fontawesome.css', 'admin', false), false, false, "all");
				
				if ((preg_match("/(widgets\.php)/", $_SERVER['REQUEST_URI'], $matches)) || 
					(!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections))) {
					$uisrc = $this -> render_url('css/jquery-ui.css', 'admin', false);
					wp_enqueue_style('jquery-ui', $uisrc, false, '1.0', "all");
					wp_enqueue_style('bootstrap', $this -> render_url('css/bootstrap.css', 'admin', false), false, false, "all");
					wp_deregister_style('select2');
					wp_enqueue_style('select2', $this -> render_url('css/select2.css', 'admin', false), false, false, "all");
				}
				
				// Count Down
				if (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> queue) {
					wp_enqueue_style('jquery-countdown', $this -> render_url('css/jquery-countdown.css', 'admin', false), false, false, "all");
				}
				
				$stylesource = plugins_url() . '/' . $this -> plugin_name . '/css/wp-mailinglist.css';
				wp_enqueue_style($this -> plugin_name, $stylesource, false, $this -> version, "screen");
								
			// Front-end
			} else {				
				$loadstyles = $this -> get_option('loadstyles');
				$loadstyles_handles = $this -> get_option('loadstyles_handles');
				$loadstyles_pages = $this -> get_option('loadstyles_pages');
				
				include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');
				
				if (!empty($defaultstyles)) {
					foreach ($defaultstyles as $handle => $style) {						
						$custom_handle = (empty($loadstyles_handles[$handle])) ? $handle : $loadstyles_handles[$handle];
						$custom_pages = (empty($loadstyles_pages[$handle])) ? false : explode(",", $loadstyles_pages[$handle]);

						if ((!empty($loadstyles) && in_array($handle, $loadstyles)) || wpml_is_management()) {						
							if (empty($custom_pages) || (!empty($custom_pages) && (is_single($custom_pages) || is_page($custom_pages)))) {	
									if (apply_filters('newsletters_enqueuestyle_' . $handle, true)) {																				
										wp_enqueue_style($custom_handle, $style['url'], $style['deps'], $style['version'], $style['media']);
										do_action('newsletters_enqueuestyle_after', $handle, $style);
									}
							}
						}
					}
				}
			}
			
			return true;
		}
		
		function delete_default_themes() {
			global $wpdb, $Theme;
			
			$themes = array(
				'blueretro'			=>	array('title' => __('Blue Retro', $this -> plugin_name), 'name' => "blueretro"),
				'getaway'			=>	array('title' => __('Getaway', $this -> plugin_name), 'name' => "getaway"),
				'nightlife'			=>	array('title' => __('Night Life', $this -> plugin_name), 'name' => "nightlife"),
				'paperphase'		=>	array('title' => __('Paper Phase', $this -> plugin_name), 'name' => "paperphase"),
				'redray'			=>	array('title' => __('Red Ray', $this -> plugin_name), 'name' => "redray"),
				'simplyelegant'		=>	array('title' => __('Simply Elegant', $this -> plugin_name), 'name' => "simplyelegant"),
				'snazzy'			=>	array('title' => __('Snazzy', $this -> plugin_name), 'name' => "snazzy"),
				'pronews'			=>	array('title' => __('Pro News', $this -> plugin_name), 'name' => "pronews"),
				'lagoon'			=>	array('title' => __('Lagoon', $this -> plugin_name), 'name' => "lagoon"),
				'themailer'			=>	array('title' => __('The Mailer', $this -> plugin_name), 'name' => "themailer"),
			);
			
			foreach ($themes as $theme) {
				$query = "DELETE FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `name` = '" . $theme['name'] . "'";
				$wpdb -> $query($query);
			}
			
			return true;
		}
		
		function initialize_default_themes() {
			if (!is_admin()) return;
		
			$premade_include = $this -> plugin_base() . DS . 'includes' . DS . 'themes' . DS . 'premade.php';
			include($premade_include);
			
			return true;
		}
		
		function set_timezone() {			
			$timezone_set = $this -> get_option('timezone_set');
			if (!empty($timezone_set)) {				
				$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
				$current_offset = get_option('gmt_offset');
				$tzstring = get_option('timezone_string');
				$check_zone_info = true;
				if (false !== strpos($tzstring,'Etc/GMT')) { $tzstring = ''; }
						
				if (empty($tzstring)) {
					$check_zone_info = false;
					if (0 == $current_offset) {
						$tzstring = 'UTC+0';
					} elseif ($current_offset < 0) {
						$tzstring = 'UTC' . $current_offset;
					} else {
						$tzstring = 'UTC+' . $current_offset;
					}
				}
				
				@putenv("TZ=" . $tzstring);
				@ini_set('date.timezone', $tzstring);
				
				if (function_exists('date_default_timezone_set')) {
					@date_default_timezone_set($tzstring);
				}
			}
		}
		
		function init_class($name = null, $params = array()) {		
			if (!empty($name)) {
				$name = (!preg_match("/" . $this -> pre . "/si", $name)) ? $this -> pre . $name : $name;			
				if (class_exists($name)) {
					if ($class = new $name($params)) {	
						if (!empty($this -> plugin_name)) {
							$class -> plugin_name = $this -> plugin_name;
						}
						return $class;
					}
				}
			}
			
			return false;
		}
		
		function register_external_model($model = null) {
			if (!empty($model)) {
				if ($this -> {$model}()) {
					$this -> tablenames[$this -> pre . $this -> {$model}() -> controller] = $wpdb -> prefix . $this -> {$model}() -> table;
					$this -> tables[$this -> pre . $this -> {$model}() -> controller] = (empty($this -> {$model}() -> table_fields)) ? $this -> {$model}() -> fields : $this -> {$model}() -> table_fields;
					$this -> indexes[$this -> pre . $this -> {$model}() -> controller] = (!empty($this -> {$model}() -> indexes)) ? $this -> {$model}() -> indexes : false;
					
					return true;
				}
			}
			
			return false;
		}
		
		function initialize_classes() {
			global $wpdb;
			
			if (!empty($this -> helpers)) {
				foreach ($this -> helpers as $helper) {
					global ${$helper};
					
					$helpername = $this -> pre . $helper . 'Helper';					
					if (!is_object(${$helper})) {
						${$helper} = $this -> init_class($helpername);
					}
				}
			}
		
			//make sure that we have some classes defined.
			if (!empty($this -> classes)) {			
				//loop our classes
				foreach ($this -> classes as $class) {	
					global ${$class};
				
					if (!is_object(${$class})) {						
						switch ($class) {
							//case 'wpmlGroup'		:
							//case 'wpmlOrder'		:
							case 'wpmlCountry'		:
								${$class} = $this -> init_class($class);
								break;
							default					:
								${$class} = $this -> init_class($this -> pre . $class);
								break;
						}
						
						if (!empty(${$class} -> table_fields)) {
							${$class} -> table_fields = apply_filters('newsletters_db_table_fields', ${$class} -> table_fields, $class);
						} elseif (!empty(${$class} -> fields)) {
							${$class} -> fields = apply_filters('newsletters_db_table_fields', ${$class} -> fields, $class);
						}
							
						$this -> tablenames[$this -> pre . ${$class} -> controller] = $wpdb -> prefix . ${$class} -> table;
						$this -> tables[$this -> pre . ${$class} -> controller] = (empty(${$class} -> table_fields)) ? ${$class} -> fields : ${$class} -> table_fields;
						$this -> indexes[$this -> pre . ${$class} -> controller] = (!empty(${$class} -> indexes)) ? ${$class} -> indexes : false;
					}
					
					if (empty($this -> {$class}) || !is_object($this -> {$class})) {
						$this -> {$class} = ${$class};
					}
				}
			}
			
			$this -> tables = apply_filters('newsletters_db_tables', $this -> tables);
			
			do_action('newsletters_initialize_classes_done', $this -> tables);
			
			return true;
		}
		
		function activateaction_scheduling() {
			$activateaction = $this -> get_option('activateaction');
			
			if (!empty($activateaction) && $activateaction != "none") {
				$timestamp = time();
				
				if (!wp_next_scheduled($this -> pre . '_activateaction')) {
					wp_schedule_event($timestamp, "hourly", $this -> pre . '_activateaction');
				}
			}
			
			return true;
		}
		
		function latestposts_scheduling($interval = null, $startdate = null, $args = null) {	
			if (!empty($interval) && !empty($args[0])) {
				wp_clear_scheduled_hook('newsletters_latestposts', $args);
				$schedules = wp_get_schedules();
				
				if (empty($startdate) || strtotime($startdate) < time()) {
					$new_timestamp = time() + $schedules[$interval]['interval'];
				} else {
					$new_timestamp = strtotime($startdate);
				}
				
				$next_scheduled = wp_next_scheduled('newsletters_latestposts', $args);				
				if (empty($next_scheduled)) {
					wp_schedule_event($new_timestamp, $interval, 'newsletters_latestposts', $args);
				}
			}
		
			return true;	
		}
	
	    function pop_scheduling() {
	        wp_clear_scheduled_hook($this -> pre . '_pophook');	
	        if ($this -> get_option('bouncemethod') == "pop") {
	            $schedules = wp_get_schedules();
				$interval = $this -> get_option('bouncepop_interval');
				$new_timestamp = time() + $schedules[$interval]['interval'];
				
				if (!wp_next_scheduled($this -> pre . '_pophook')) {
					wp_schedule_event($new_timestamp, $interval, $this -> pre . '_pophook');
				}
	        }
	
	        return;
	    }
		
		function importusers_scheduling() {
			wp_clear_scheduled_hook($this -> pre . '_importusers');			
			if ($this -> get_option('importusers') == "Y") {
				$schedules = wp_get_schedules();
				$interval = $this -> get_option('importusersscheduling');
				$interval = (empty($interval)) ? "hourly" : $interval;
				$new_timestamp = time() + $schedules[$interval]['interval'];
				
				if (!wp_next_scheduled($this -> pre . '_importusers')) {
					wp_schedule_event($new_timestamp, $interval, $this -> pre . '_importusers');
				}
			}
		}
		
		function autoresponder_scheduling() {
			$schedules = wp_get_schedules();
			$interval = $this -> get_option('autoresponderscheduling');
			$new_timestamp = time() + $schedules[$interval]['interval'];
			
			if (!wp_next_scheduled($this -> pre . '_autoresponders')) {
				wp_schedule_event($new_timestamp, $interval, $this -> pre . '_autoresponders');
			}
			
			return true;
		}
		
		function captchacleanup_scheduling() {
			wp_clear_scheduled_hook($this -> pre . '_captchacleanup');			
			$schedules = wp_get_schedules();
			$interval = $this -> get_option('captchainterval');
			$new_timestamp = time() + $schedules[$interval]['interval'];
			
			if (!wp_next_scheduled($this -> pre . '_captchacleanup')) {
				wp_schedule_event($new_timestamp, $interval, $this -> pre . '_captchacleanup');
			}
			
			return true;
		}
		
		function scheduling($increase = false) {
			wp_clear_scheduled_hook($this -> pre . '_cronhook');
			if ($this -> get_option('scheduling') == "Y") {			
				if ($this -> get_option('schedulecrontype') == "wp") {			
					$schedules = wp_get_schedules();
					$interval = $this -> get_option('scheduleinterval');
					$interval = (empty($interval)) ? '2minutes' : $interval;
					$new_timestamp = time() + $schedules[$interval]['interval'];
					if ($increase == true) { $new_timestamp += 300; }
					
					if (!wp_next_scheduled($this -> pre . '_cronhook')) {
						wp_schedule_event($new_timestamp, $interval, $this -> pre . '_cronhook');
					}
				}
			}
		}
		
		function optimize_scheduling() {
			if (!wp_next_scheduled('newsletters_optimizehook')) {
				wp_schedule_event(time(), 'daily', 'newsletters_optimizehook');
			}
		}
		
		function emailarchive_scheduling() {		
			if (!wp_next_scheduled('newsletters_emailarchivehook')) {
				wp_schedule_event(time(), 'daily', 'newsletters_emailarchivehook');
			}
		}
		
		function get_custom_post_types($removedefaults = true) {
			if ($post_types = get_post_types(null, 'objects')) {
				$default_types = array('post', 'page', 'attachment', 'revision', 'nav_menu_item');
				
				if ($removedefaults) {
					foreach ($default_types as $dpt) {
						unset($post_types[$dpt]);
					}
				}
				
				return $post_types;
			}
			
			return false;
		}
		
		function check_tables() {
			global $wpdb;
			$this -> initialize_classes();
			
			if (!empty($this -> models)) {
				foreach ($this -> models as $model) {		
					if (!empty($this -> {$model}() -> table_fields)) {
						$this -> {$model}() -> table_fields = apply_filters('newsletters_db_table_fields', $this -> {$model}() -> table_fields, $model);								
					}
						
					$this -> {$model}() -> fields = apply_filters('newsletters_db_table_fields_new', $this -> {$model}() -> fields, $model);										
					$this -> tablenames[$this -> pre . $this -> {$model}() -> controller] = $wpdb -> prefix . $this -> {$model}() -> table;
					$this -> tables[$this -> pre . $this -> {$model}() -> controller] = (!empty($this -> {$model}() -> table_fields)) ? $this -> {$model}() -> table_fields : $this -> {$model}() -> fields;
					$this -> indexes[$this -> pre . $this -> {$model}() -> controller] = (!empty($this -> {$model}() -> indexes)) ? $this -> {$model}() -> indexes : false;
				}
			}
			
			if (!empty($this -> tablenames)) {
				foreach ($this -> tablenames as $controller => $tablename) {			
					$this -> check_table($controller);
				}
			}
		}
		
		function check_table($name = null) {
			//global WP variables
			global $wpdb;		
			if (!is_admin()) return;
		
			//ensure that a "name" was passed
			if (!empty($name)) {
				//add the WP prefix to the table name
				$oldname = $name;
				$name = $wpdb -> prefix . $name;
				
				//make sure that the table fields are available
				if (!empty($this -> tables[$oldname])) {																		
					//check if the table exists. boolean value returns				
					$query = "SHOW TABLES LIKE '" . $name . "'";
					if (!$wpdb -> get_var($query)) {																			
						//let's start the query for a new table!
						$query = "CREATE TABLE `" . $name . "` (";
						$c = 1;
						
						//loop the table fields.
						foreach ($this -> tables[$oldname] as $field => $attributes) {
							//we might need to use a KEY declaration
							//in case not "key", continue with normal attributes set.
							if ($field != "key") {
								//append the field name and attributes
								$query .= "`" . $field . "` " . $attributes . "";
							} else {
								//this is a "key" field. declare it
								$query .= "" . $attributes . "";
							}
							
							//the last query doesn't get a comma at the end.
							//ensure that it is not the last query section.
							if ($c < count($this -> tables[$oldname])) {
								//append a comma "," to the query
								$query .= ",";
							}
							
							$c++;
						}
						
						// Commented... do not add another `id` index to the database table, it is redundant
						/*if (array_key_exists('id', $this -> tables[$oldname])) {
							$query .= ", INDEX (`id`)";
						}*/
						
						//end the query!
						$query .= ") ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=UTF8 COLLATE=utf8_general_ci;";
						
						if (!empty($query)) {
							$this -> table_query[$name] = $query;
						}
					} else {							
						//get the current fields for this table.
						$field_array = $this -> get_fields($oldname);
						
						//loop the fields of the table
						foreach ($this -> tables[$oldname] as $field => $attributes) {					
							//make sure that its not a KEY value.
							if ($field != "key") {
								//add the database table field.
								$this -> add_field($oldname, $field, $attributes);
							}
						}
						
						global $wpdb, $Db, $Field, $Subscriber;
						switch ($oldname) {
							case $Subscriber -> table			:							
								$Db -> model = $Field -> model;
								if ($fields = $Db -> find_all()) {
									foreach ($fields as $field) {
										$this -> add_field($oldname, $field -> slug);
									}
								}
								break;
							case $this -> Latestpost() -> table 			:
								$query = "ALTER TABLE `" . $wpdb -> prefix . $this -> Latestpost() -> table . "` DROP INDEX `post_id`";
								$wpdb -> query($query);
								break;
						}
						
						if (!empty($this -> indexes[$oldname])) {
							$indexes = $this -> indexes[$oldname];
							
							// drop any redundant `id` index
							if ($wpdb -> get_var("SHOW INDEX FROM `" . $name . "` WHERE `Key_name` = 'id'")) {
								$query = "ALTER TABLE `" . $name . "` DROP INDEX `id`";
								$wpdb -> query($query);
							}
							
							foreach ($indexes as $index) {
								$query = "SHOW INDEX FROM `" . $name . "` WHERE `Key_name` = '" . $index . "'";
								if (!$wpdb -> get_row($query)) {
									$query = "ALTER TABLE `" . $name . "` ADD INDEX(`" . $index . "`);";
									$wpdb -> query($query);	
								}	
							}
						}
					}
					
					//make sure that the query is not empty.
					if (!empty($this -> table_query)) {						
						foreach ($this -> table_query as $query) {
							$wpdb -> query($query);
						}
					}
				}
			} else {
				return false;
			}
		}
		
		/**
		 * Retrieves all the fields for a specific database table
		 * @param $table STRING The name of the table to check.
		 * @return $field_array ARRAY An array of fields for the specific table.
		 *
		 **/
		function get_fields($table = null) {	
			global $wpdb;
		
			//make sure the table nae is available
			if (!empty($table)) {
				$fullname = $wpdb -> prefix . $table;
				
				$field_array = array();
				if ($fields = $wpdb -> get_results("SHOW COLUMNS FROM " . $fullname)) {
					foreach ($fields as $field) {
						$field_array[] = $field -> Field;
					}
				}
				
				return $field_array;
			}
			
			return false;
		}
		
		function delete_field($table = null, $field = null) {
			global $wpdb;
			
			if (!empty($table)) {			
				if (!empty($field)) {				
					$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` DROP `" . $field . "`";
					
					if ($wpdb -> query($query)) {
						return true;
					}
				}
			}
	
			return false;
		}
		
		function change_field($table = null, $field = null, $newfield = null, $attributes = "TEXT NOT NULL") {
			global $wpdb;
			
			if (!empty($table)) {		
				if (!empty($field)) {			
					if (!empty($newfield)) {				
						$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` CHANGE `" . $field . "` `" . $newfield . "` " . $attributes . "";
	
						if ($wpdb -> query($query)) {
							return true;
						}
					}
				}
			}
			
			return false;
		}
		
		function add_field($table = null, $field = null, $attributes = "TEXT NOT NULL") {
			global $wpdb;
		
			if (!empty($table)) {
				if (!empty($field)) {
					$field_array = $this -> get_fields($table);
	
					if (!empty($field_array)) {				
						if (!in_array($field, $field_array)) {				
							$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` ADD `" . $field . "` " . $attributes . "";
							if ($field == "id") { $query .= ", ADD PRIMARY KEY (id)"; }
							
							if ($wpdb -> query($query)) {
								return true;
							}
						}
					}
				}
			}
			
			return false;
		}
		
		function add_user_option($user_id = null, $option = null, $value = null) {
			$user_id = (empty($user_id)) ? get_current_user_id() : $user_id;
			
			if (!empty($user_id) && !empty($option)) {
				if (add_user_meta($user_id, 'newsletters_' . $option, $value, false)) {
					return true;
				}
			}
			
			return false;
		}
		
		function update_user_option($user_id = null, $option = null, $value = null) {
			$user_id = (empty($user_id)) ? get_current_user_id() : $user_id;
			
			if (!empty($user_id) && !empty($option)) {
				if (update_user_meta($user_id, 'newsletters_' . $option, $value)) {
					return true;
				}
			}
			
			return false;
		}
		
		function get_user_option($user_id = null, $option = null, $single = true) {
			$user_id = (empty($user_id)) ? get_current_user_id() : $user_id;
			
			if (!empty($user_id) && !empty($option)) {
				if ($value = get_user_meta($user_id, 'newsletters_' . $option, $single)) {
					return $value;
				}
			}
			
			return false;
		}
		
		function delete_user_option($user_id = null, $option = null) {
			$user_id = (empty($user_id)) ? get_current_user_id() : $user_id;
			
			if (!empty($user_id) && !empty($option)) {
				if (delete_user_meta($user_id, 'newsletters_' . $option)) {
					return true;
				}
			}
			
			return false;
		}
		
		function userdata($user_id = null) {
			if (!empty($user_id)) {
				if ($user = get_userdata($user_id)) {
					return $user;
				}
			}
		
			return false;
		}
		
		function user_role($user_id = null, $user = null) {
			if (!empty($user_id)) {
				if (!empty($user) || $user = $this -> userdata($user_id)) {
					$user_roles = $user -> roles;
					$user_role = array_shift($user_roles);
					
					return $user_role;
				}
			}
			
			return false;
		}
	
		function vendor($name = null, $pre = 'class', $classit = true) {
			if (!empty($name)) {
				$filename = $pre . '.' . strtolower($name) . '.php';
				$filepath = rtrim(dirname(__FILE__), DS) . DS . 'vendors' . DS;
				$filefull = $filepath . $filename;
			
				if (file_exists($filefull)) {
					require_once($filefull);
					
					if ($classit == true) {
						$class = $this -> pre . $name;
						
						if (${$name} = new $class) {
							return ${$name};
						}
					} else {
						return true;
					}
				}
			}
		
			return false;
		}
		
		function render_field($field_id = null, $fieldset = true, $optinid = null, $showcaption = true, $watermark = true, $instance = null, $offsite = false, $errors = array(), $form_id = null, $form_field = null) {
			global $Html, $Field, $wpmltabindex, $Mailinglist, $Subscriber, 
			${'newsletters_fields_count_' . $optinid}, $newsletters_is_management;
			
			if (empty(${'newsletters_fields_count_' . $optinid})) {
				${'newsletters_fields_count_' . $optinid} = 1;
			}
		
			if (!empty($field_id)) {
				if ($field = $Field -> get($field_id)) {
					$Subscriber -> data = (array) $Subscriber -> data;
																
					$list = (empty($instance['list'])) ? ((!empty($Subscriber -> data['list_id'])) ? __($Subscriber -> data['list_id'][0]) : false) : __($instance['list']);
					if ($this -> language_do() && !empty($instance['lang'])) {
						$list = $this -> language_use($instance['lang'], $list, false);
					}
					
					$col = '';
						
					if (!empty($form_id) && !empty($form_field)) {
						$settings = maybe_unserialize($form_field -> settings);
						$list = false;
						if ($field -> slug == "list") {
							if (empty($settings['listchoice']) || $settings['listchoice'] == "user") {
								if (empty($settings['listchoice_user_type']) || $settings['listchoice_user_type'] == "checkboxes") {
									$list = "checkboxes";
									$listsselectall = '<div class="checkbox"><input onclick="jqCheckAll(this, false, \'list_id\');" type="checkbox" name="listsselectall" value="1" id="listsselectall" /></div>';
								} else {
									$list = "select";
								}
								
								$includelists = (!empty($settings['includelists'])) ? $settings['includelists'] : false;
							} elseif ($settings['listchoice'] == "admin") {
								$list = "admin";
								$adminlists = $settings['adminlists'];
							}
						}
						
						$visible = true;
						if ($field -> type == "hidden" || ($field -> type == "special" && $field -> slug == "list" && !empty($list) && (is_numeric($list) || $list == "all" || $list == "admin")) || (!empty($newsletters_is_management) && $field -> slug == "list")) {
							$visible = false;
							$newsletters_is_management = false;
						}
						
						echo '<div class="newsletters-fieldholder ' . ((empty($visible) && (!is_admin() || defined('DOING_AJAX'))) ? 'newsletters-fieldholder-hidden hidden' : 'newsletters-fieldholder-visible') . '">';
						echo '<div class="form-group' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' has-error' : '') . '">';						
						echo '<label for="' . ((!empty($list) && $list == "checkboxes") ? 'listsselectall' : '') . '" class="control-label' . ((!empty($list) && $list == "checkboxes") ? ' form-inline' : '') . ' ' . $this -> pre . 'customfield ' . $this -> pre . 'customfield' . $field_id . '">' . ((!empty($list) && $list == "checkboxes") ? $listsselectall . ' ' : '') . ((empty($form_field -> label)) ? __($field -> title) : __($form_field -> label)) . ((!empty($form_field -> required)) ? ' <sup class="newsletters_required text-danger"><i class="fa fa-asterisk"></i></sup>' : '') . '</label>';
						
						$watermark = (empty($form_field -> placeholder)) ? ((empty($field -> watermark)) ? false : __($field -> watermark)) : __($form_field -> placeholder);
						if (!empty($watermark)) {
							$placeholder = ' placeholder="' . $watermark . '"';
						}
					} else {	
						$visible = true;
						if ($field -> type == "hidden" || ($field -> type == "special" && $field -> slug == "list" && !empty($list) && (is_numeric($list) || $list == "all")) || (!empty($newsletters_is_management) && $field -> slug == "list")) {
							$visible = false;
							$newsletters_is_management = false;
						}
											
						$placeholder =  ' placeholder="' . ((!empty($field -> watermark) && $watermark == true && empty($offsite)) ? esc_attr(stripslashes(__($field -> watermark))) : '') . '"';
									
						echo '<div id="newsletters-' . $optinid . $field -> slug . '-holder" class="' . $col . ' newsletters-fieldholder ' . ((empty($visible) && (!is_admin() || defined('DOING_AJAX'))) ? 'newsletters-fieldholder-hidden hidden' : 'newsletters-fieldholder-visible') . ' ' . $field -> slug . '">';
						echo '<div class="form-group' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' has-error' : '') . '">';
					
						if ($fieldset == true && $field -> type != "special" && $field -> type != "hidden") {
							if ($field -> type == "file") {
								?><label for="file_upload_<?php echo $field -> id; ?><?php echo $optinid; ?>" class="control-label <?php echo $this -> pre . 'customfield'; ?> <?php echo $this -> pre; ?>customfield<?php echo $field_id; ?>"><?php	
							} else {
								echo '<label for="' . $this -> pre . '-' . $optinid . $field -> slug . '" class="control-label ' . $this -> pre . 'customfield ' . $this -> pre . 'customfield' . $field_id . '">';
							}
								
							_e($field -> title);
							if ($field -> required == "Y") { echo ' <sup class="newsletters_required text-danger"><i class="fa fa-asterisk"></i></sup>'; };
							echo '</label>';
						}
					}
					
					$fieldname = $field -> slug;
					if (is_admin() && !defined('DOING_AJAX')) {
						$fieldname = 'Subscriber[' . $field -> slug . ']';
					}
				
					switch ($field -> type) {
						case 'hidden'			:						
							switch ($field -> hidden_type) {
								case 'post'					:
									$hidden_value = $_REQUEST[$field -> hidden_value];
									break;
								case 'predefined'			:
									$hidden_value = $field -> hidden_value;
									break;
								case 'custom'				:
								default  					:
									$hidden_value = $Subscriber -> data[$field -> slug];
									break;
								case 'get'					:
									$hidden_value = $_GET[$field -> hidden_value];
									break;
								case 'global'				:
									$hidden_value = $GLOBALS[$field -> hidden_value];
									break;
								case 'cookie'				:
									$hidden_value = $_COOKIE[$field -> hidden_value];
									break;
								case 'session'				:
									$hidden_value = $_SESSION[$field -> hidden_value];
									break;
								case 'server'				:
									$hidden_value = $_SERVER[$field -> hidden_value];
									break;
							}
							
							//parse any shortcodes in the hidden field value
							$hidden_value = stripslashes(do_shortcode($hidden_value));
							
							if (!is_admin() || defined('DOING_AJAX') || (!empty($_GET['method']) && $_GET['method'] == "offsitewizard")) {
								echo '<input type="hidden" name="' . $fieldname . '" value="' . esc_attr(stripslashes($hidden_value)) . '" />';
							} else {
								echo '<input type="text" class="" name="' . $fieldname . '" value="' . esc_attr(stripslashes($hidden_value)) . '" />';
							}
							break;
						case 'text'				:						
							echo '<input' . $placeholder . ' class="form-control ' . $this -> pre . ' ' . $this -> pre . 'text' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $Html -> tabindex($optinid) . ' type="text" name="' . $fieldname . '" value="' . esc_attr(stripslashes($Subscriber -> data[$field -> slug])) . '" />';
							break;
						case 'special'			:						
							switch ($field -> slug) {
								case 'list'				:
								default 				:	
									// Form	
									if (!empty($form_id) && !empty($form_field)) {
										if (!empty($list)) {																						
											switch ($list) {
												case 'admin'			:
													$lists = $Mailinglist -> select(true, $adminlists);
													if (!empty($lists)) {
														foreach ($lists as $list_id => $list_title) {
															echo '<input type="hidden" name="list_id[]" value="' . $list_id . '" />';
														}
													}
													break;
												case 'select'			:
													if (!empty($includelists)) {
														$lists = $Mailinglist -> select(true, $includelists);	
													} else {
														$lists = $Mailinglist -> select(false);
													}
													
													if (!empty($lists)) {
														echo '<select ' . $Html -> tabindex($optinid) . ' class="' . ((!empty($Subscriber -> errors['list_id'])) ? ' newsletters_fielderror ' : '') . $this -> pre . ' autowidth ' . $this -> pre . 'select newsletters-list-select" id="' . $optinid . $field -> slug . '-list-select" name="list_id[]">';
														echo '<option value="">' . __('- Select -', $this -> plugin_name) . '</option>';
														
														foreach ($lists as $list_id => $list_title) {
															echo '<option' . ((!empty($Subscriber -> data['list_id']) && $Subscriber -> data['list_id'][0] == $list_id) ? ' selected="selected"' : '') . ' value="' . $list_id . '">' . __($list_title) . '</option>';
														}
														
														echo '</select>';
													}
													break;
												case 'checkboxes'		:
													if (!empty($includelists)) {
														$lists = $Mailinglist -> select(true, $includelists);	
													} else {
														$lists = $Mailinglist -> select(false);
													}
													
													if (!empty($lists)) {
														foreach ($lists as $list_id => $list_title) {
															echo '<div class="checkbox">';
															echo '<label class="wpmlcheckboxlabel ' . $this -> pre . '">';
															echo '<input' . ((!empty($Subscriber -> data['list_id']) && in_array($list_id, $Subscriber -> data['list_id'])) ? ' checked="checked"' : '') . ' type="checkbox" name="list_id[]" value="' . $list_id . '" class="newsletters-list-checkbox" id="' . $optinid . $field -> slug . '-list-checkbox" /> ';
															echo __($list_title) . '</label>';
															echo '</div>';
														}
													}
													break;
											}
										}
									// Not Form
									} else {							
										if (!empty($errors['list_id'])) {
											$errors['list'] = $errors['list_id'];
										}
																								
										preg_match("/[0-9]/si", $optinid, $matches);
										$number = $matches[0];
										
										if (!empty($list)) {
											if (is_numeric($list)) {
												echo '<input type="hidden" name="list_id[]" value="' . $list . '" />';
											} else {
												if (empty($form_id) && (empty($list) || $list != "all")) {
													echo '<label class="' . $this -> pre . 'customfield ' . $this -> pre . 'customfield' . $field_id . '">';
													_e($field -> title);
													if ($field -> required == "Y") { echo ' <sup class="newsletters_required text-danger"><i class="fa fa-asterisk"></i></sup>'; };
													echo '</label>';
												}
												
												$instance['lists'] = array_filter(explode(",", $instance['lists']));
												$lists = (empty($instance['lists'])) ? $Mailinglist -> select(false) : $Mailinglist -> select(true, $instance['lists']);
												
												if ($list == "checkboxes") {
													foreach ($lists as $list_id => $list_title) {
														echo '<div class="checkbox">';
														echo '<label class="wpmlcheckboxlabel ' . $this -> pre . '">';
														echo '<input' . ((!empty($Subscriber -> data['list_id']) && in_array($list_id, $Subscriber -> data['list_id'])) ? ' checked="checked"' : '') . ' type="checkbox" name="list_id[]" value="' . $list_id . '" class="newsletters-list-checkbox" id="' . $optinid . $field -> slug . '-list-checkbox" /> ';
														echo __($list_title) . '</label>';
														echo '</div>';
													}
												} elseif ($list == "all") {
													echo '<input type="hidden" name="list_id[]" value="all" />';
												} else {
													echo '<select ' . $Html -> tabindex($optinid) . ' class="' . ((!empty($Subscriber -> errors['list_id'])) ? ' newsletters_fielderror ' : '') . $this -> pre . ' autowidth ' . $this -> pre . 'select newsletters-list-select" id="' . $optinid . $field -> slug . '-list-select" name="list_id[]">';
													echo '<option value="">' . __('- Select -', $this -> plugin_name) . '</option>';
													
													foreach ($lists as $list_id => $list_title) {
														echo '<option' . ((!empty($Subscriber -> data['list_id']) && $Subscriber -> data['list_id'][0] == $list_id) ? ' selected="selected"' : '') . ' value="' . $list_id . '">' . __($list_title) . '</option>';
													}
													
													echo '</select>';
												}
												
												if (!empty($field -> caption) && $showcaption == true && $field -> type != "special") {
													echo '<span class="' . $this -> pre . 'customfieldcaption">' . __(stripslashes($field -> caption)) . '</span>';
												}
											}
										}	
									}							
									break;
							}
							break;
						case 'textarea'			:
							echo '<textarea' . $placeholder . ' class="form-control ' . $this -> pre . ' ' . $this -> pre . 'textarea' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $Html -> tabindex($optinid) . ' rows="4" name="' . $fieldname . '">' . strip_tags($Subscriber -> data[$field -> slug]) . '</textarea>';
							break;
						case 'select'			:						
							echo '<select' . $placeholder . ' class="form-control ' . $this -> pre . ' ' . $this -> pre . 'select' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" style="width:auto;" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $Html -> tabindex($optinid) . ' name="' . $fieldname . '">';
							echo '<option value="">' . __('- Select -', $this -> plugin_name) . '</option>';
							
							if (!empty($field -> newfieldoptions)) {
								foreach ($field -> newfieldoptions as $option_id => $option_value) {
									$select = (!empty($Subscriber -> data[$field -> slug]) && ($Subscriber -> data[$field -> slug] == $option_id || $Subscriber -> data[$field -> slug] == __($option_value))) ? 'selected="selected"' : '';
									echo '<option ' . $select . ' value="' . $option_id . '">' . __($option_value) . '</option>';
								}
							}
							
							echo '</select>';
							break;
						case 'radio'			:							
							if (!empty($field -> newfieldoptions)) {
								foreach ($field -> newfieldoptions as $option_id => $option_value) {																		
									$checked = ($Subscriber -> data[$field -> slug] == $option_id || (!empty($Subscriber -> data[$field -> slug]) && $Subscriber -> data[$field -> slug] == __($value))) ? 'checked="checked"' : '';
									echo '<div class="radio">';
									echo '<label class="control-label wpmlradiolabel ' . $this -> pre . '">';
									echo '<input class="' . $this -> pre . 'radio' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" ' . $Html -> tabindex($optinid) . ' type="radio" ' . $checked . ' name="' . $fieldname . '" value="' . $option_id . '" /> ' . __($option_value);
									echo '</label>';
									echo '</div>';
								}
							}
							break;
						case 'checkbox'			:
							$subscribercheckboxes = false;
							if (!empty($Subscriber -> data[$field -> slug])) {
								$subscribercheckboxes = maybe_unserialize($Subscriber -> data[$field -> slug]);
							}
													
							if (!empty($field -> newfieldoptions)) {
								foreach ($field -> newfieldoptions as $option_id => $option_value) {
									$checked = (!empty($subscribercheckboxes) && (is_array($subscribercheckboxes) && in_array($option_id, $subscribercheckboxes))) ? 'checked="checked"' : '';
									echo '<div class="checkbox">';
									echo '<label class="control-label wpmlcheckboxlabel ' . $this -> pre . '">';
									echo '<input class="' . $this -> pre . 'checkbox' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" ' . $Html -> tabindex($optinid) . ' type="checkbox" ' . $checked . ' name="' . $fieldname . '[]" value="' . $option_id . '" /> ' . __($option_value);
									echo '</label>';
									echo '</div>';
								}
							}
							break;
						case 'file'				:						
							$filetypes = false;
							if (!empty($field -> filetypes)) {
								if (($types = @explode(",", $field -> filetypes)) !== false) {
									if (is_array($types)) {
										$f = 1;
										foreach ($types as $type) {
											$filetypes .= '*' . $type;
											if ($f < count($types)) { $filetypes .= "; "; }
											$f++;
										}
									}
								}
							}
						
							?>
							
							<div class="clearfix"></div>
							<button class="btn btn-default btn-file">
								<?php _e('Browse...', $this -> plugin_name); ?> <input onchange="jQuery('#file_upload_<?php echo $field -> id; ?>_info').html(jQuery(this).val());" type="file" <?php echo $Html -> tabindex($optinid); ?> name="<?php echo esc_attr(stripslashes($fieldname)); ?>" id="file_upload_<?php echo $field -> id; ?>" />
							</button>
							<span class="label label-info" id="file_upload_<?php echo $field -> id; ?>_info"></span>
							
							<?php						
							break;
						case 'pre_country'		:
							global $Db, $Form;
							
							if ($countries = $this -> Country() -> select()) {						
								?>
								
								<select class="<?php echo $this -> pre; ?>country <?php echo $this -> pre; ?> <?php echo $this -> pre; ?>precountry<?php echo ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : ''); ?>" id="<?php echo $this -> pre; ?>-<?php echo $optinid . $field -> slug; ?>" <?php echo $Html -> tabindex($optinid); ?> name="<?php echo $fieldname; ?>">
									<option value=""><?php _e('- Select Country -', $this -> plugin_name); ?></option>
									<?php foreach ($countries as $id => $value) : ?>
										<option <?php echo (!empty($Subscriber -> data[$field -> slug]) && $Subscriber -> data[$field -> slug] == $id) ? 'selected="selected"' : ''; ?> value="<?php echo $id; ?>"><?php echo $value; ?></option>
									<?php endforeach; ?>
								</select>
								
								<?php
							}
							break;
						case 'pre_date'			:						
							ob_start();
							
							$field_value = false;
							if (!empty($Subscriber -> data[$field -> slug])) {
								$field_value = maybe_unserialize($Subscriber -> data[$field -> slug]);
							}
							
							$currentDate = "";
							if (!empty($field_value) && $field_value != "0000-00-00") {
								if (is_array($field_value)) {
									$currentDate = date_i18n(get_option('date_format'), strtotime($field_value['d'] . '/' . $field_value['m'] . '/' . $field_value['y']));
								} else {
									$currentDate = date_i18n(get_option('date_format'), strtotime($field_value));
								}
							}
							
							if (!empty($currentDate)) {
								$defaultDate = 'new Date(' . date_i18n("Y", strtotime($currentDate)) . ', ' . date_i18n("m", strtotime($currentDate)) . ', ' . date_i18n("d", strtotime($currentDate)) . ')';
							} else {
								$defaultDate = 'new Date(' . date_i18n("Y") . ', ' . date_i18n("m") . ', ' . date_i18n("d") . ')';
							}
							
							?>
							
							<input type="text" class="<?php echo $this -> pre; ?>predate <?php echo $this -> pre; ?>text <?php echo $this -> pre; ?> <?php echo $this -> pre; ?>predate<?php echo ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : ''); ?>" value="<?php echo $currentDate; ?>" name="<?php echo $fieldname; ?>" id="<?php echo $this -> pre; ?>-<?php echo $optinid . $field -> slug; ?>" />
							
							<?php if (empty($offsite)) : ?>
								<script type="text/javascript">
								jQuery(document).ready(function() {
									jQuery('#<?php echo $this -> pre; ?>-<?php echo $optinid . $field -> slug; ?>').datepicker({
										changeMonth: true,
										changeYear: true,
										yearRange: "<?php echo date_i18n("Y", strtotime("-100 years")); ?>:<?php echo date_i18n("Y", strtotime("+100 years")); ?>",
										showOn: "both",
										dateFormat: "<?php echo $Html -> dateformat_PHP_to_jQueryUI(get_option('date_format')); ?>",
										defaultDate: <?php echo $defaultDate; ?>
									})
								});
								</script>
							<?php endif; ?>
							
							<?php
								
							$datepicker_output = ob_get_clean();
							echo apply_filters('newsletters_datepicker_output', $datepicker_output, $optinid, $field);
							break;
						case 'pre_gender'		:
							?>
							
							<select<?php echo $placeholder; ?> class="<?php echo $this -> pre; ?> <?php echo $this -> pre; ?>pregender<?php echo ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : ''); ?>" style="width:auto;" id="<?php echo $this -> pre; ?>-<?php echo $optinid; ?><?php echo $field -> slug; ?>" <?php echo $Html -> tabindex($optinid); ?> name="<?php echo $fieldname; ?>">
								<option value=""><?php _e('- Select Gender -', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($Subscriber -> data[$field -> slug]) && $Subscriber -> data[$field -> slug] == "male") ? 'selected="selected"' : ''; ?> value="male"><?php _e('Male', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($Subscriber -> data[$field -> slug]) && $Subscriber -> data[$field -> slug] == "female") ? 'selected="selected"' : ''; ?> value="female"><?php _e('Female', $this -> plugin_name); ?></option>
							</select>
							
							<?php
							break;
					}
					
					if (!empty($form_id) && !empty($form_field)) {
						$caption = (empty($form_field -> caption)) ? ((empty($field -> caption)) ? false : __($field -> caption)) : __($form_field -> caption);	
						if (!empty($caption)) {
							echo '<p class="help-block">' . $caption . '</p>';
						}
					} else {
						$docaption = false;
						if (!empty($field -> caption) && $showcaption == true && $field -> type != "special" && $field -> type != "hidden") {
							echo '<p class="help-block ' . $this -> pre . 'customfieldcaption">' . __(stripslashes($field -> caption)) . '</p>';
							$docaption = true;
						}
					}
					
					if (!empty($errors[$field -> slug])) {
						?>
						
						<div id="newsletters-<?php echo $optinid; ?>-<?php echo $field -> slug; ?>-error" class="newsletters-field-error alert alert-danger ui-state-error ui-corner-all">
							<p><i class="fa fa-exclamation-triangle"></i> <?php echo stripslashes($errors[$field -> slug]); ?></p>
						</div>
						
						<?php
					}
					
					if (!empty($field -> type) && $field -> type == "file") {
						if (!empty($field -> filesizelimit)) { echo '<small>' . sprintf(__('Maximum file size of <strong>%s</strong>', $this -> plugin_name), $field -> filesizelimit) . '</small><br/>'; }	
						if (!empty($filetypes)) { echo '<small>' . sprintf(__('Allowed file types are <strong>%s</strong>', $this -> plugin_name), $filetypes) . '</small><br/>'; }
						
						if (!empty($Subscriber -> data[$field -> slug])) {
							echo $Html -> file_custom_field($Subscriber -> data[$field -> slug], $field -> filesizelimit, $filetypes, $field, true);
							echo '<input id="newsletters_oldfile_' . $field -> id . '" type="hidden" name="oldfiles[' . $field -> slug . ']" value="' . esc_attr(stripslashes($Subscriber -> data[$field -> slug])) . '" />';
						}
					}
					
					echo '</div>';
					echo '</div>' . "\r\n";
					
					if (!empty($visible) && $visible == true) {
						if (${'newsletters_fields_count_' . $optinid}%2 == 0) {
							?><div class="clearfix"></div><?php
						}
						
						${'newsletters_fields_count_' . $optinid}++;
					}
				}
			}
			
			return true;
		}
	
	    function output_custom_fields($subscriber = null) {
	        global $Db, $Html, $Field;
	        $customfields = "";
	        ob_start();
	
	        if (!empty($subscriber)) {
	            $fields = $Field -> get_all();
	
	            if (!empty($fields)) {
	                $class = "alternate";
	
	                ?><table><tbody><?php
	
	  			    foreach ($fields as $field) {
						if (!empty($subscriber -> {$field -> slug})) {
							
							$fieldoptions = $field -> newfieldoptions;
							
	                        ?>
	
							<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
								<th nowrap="nowrap"><?php _e($field -> title); ?></th>
								<td>
									<?php if ($field -> type == "radio" || $field -> type == "select") : ?>
										<?php echo __($fieldoptions[$subscriber -> {$field -> slug}]); ?>
									<?php elseif ($field -> type == "checkbox") : ?>
										<?php $supoptions = maybe_unserialize($subscriber -> {$field -> slug}); ?>
										<?php if (!empty($supoptions) && is_array($supoptions)) : ?>
											<?php foreach ($supoptions as $supopt) : ?>
												&raquo;&nbsp;<?php echo __($fieldoptions[$supopt]); ?><br/>
											<?php endforeach; ?>
										<?php else : ?>
											<?php _e('none', $this -> plugin_name); ?>
										<?php endif; ?>
									<?php elseif ($field -> type == "file") : ?>
										<?php echo $Html -> file_custom_field($subscriber -> {$field -> slug}); ?>
									<?php elseif ($field -> type == "pre_country") : ?>
										<?php echo $this -> Country() -> field('value', array('id' => $subscriber -> {$field -> slug})); ?>
									<?php elseif ($field -> type == "pre_date") : ?>
										<?php if (is_serialized($subscriber -> {$field -> slug})) : ?>
											<?php $date = @unserialize($subscriber -> {$field -> slug}); ?>
											<?php if (!empty($date) && is_array($date)) : ?>
												<?php echo $date['y']; ?>-<?php echo $date['m']; ?>-<?php echo $date['d']; ?>
											<?php endif; ?>
										<?php else : ?>
											<?php echo date_i18n(get_option('date_format'), strtotime($subscriber -> {$field -> slug})); ?>
										<?php endif; ?>
									<?php else : ?>
										<?php echo $subscriber -> {$field -> slug}; ?>
									<?php endif; ?>
								</td>
							</tr>
	
	                        <?php
						}
					}
	
	                ?></tbody></table><?php
				}
	        }
	        
	        $customfields = ob_get_clean();
	        return $customfields;
	    }
		
		function gen_auth($subscriber_id = null, $mailinglist_id = null) {
			$mailinglist_id = false;
						
			if (!empty($subscriber_id)) {
				global $Db, $Subscriber, $SubscribersList;
				$Db -> model = $Subscriber -> model;
				$subscriber = $Db -> find(array('id' => $subscriber_id));
				$authkey = (empty($subscriber -> authkey)) ? md5($subscriber_id) : $subscriber -> authkey;
				
				if (!empty($mailinglist_id)) {
					$Db -> model = $SubscribersList -> model;
					if ($subscriberslist = $Db -> find(array('subscriber_id' => $subscriber_id, 'list_id' => $mailinglist_id))) {
						if ($subscriberslist -> authinprog == "Y" && !empty($subscriberslist -> authkey) && $subscriberslist -> authkey == $authkey) {
							$authkey = $subscriberslist -> authkey;
						} else {
							$Db -> model = $SubscribersList -> model;
							$Db -> save_field('authkey', $authkey, array('list_id' => $mailinglist_id, 'subscriber_id' => $subscriber_id));
							$Db -> model = $SubscribersList -> model;
							$Db -> save_field('authinprog', "Y", array('list_id' => $mailinglist_id, 'subscriber_id' => $subscriber_id));
						}
					}
				} else {
					if (!empty($subscriber)) {
						if ($subscriber -> authinprog == "Y" && !empty($subscriber -> authkey)) {
							$authkey = $subscriber -> authkey;
						} else {
							$Db -> model = $Subscriber -> model;
							$Db -> save_field('authkey', $authkey, array('id' => $subscriber_id));
							$Db -> model = $Subscriber -> model;
                            $Db -> save_field('cookieauth', $authkey, array('id' => $subscriber_id));
							$Db -> model = $Subscriber -> model;
							$Db -> save_field('authinprog', "Y", array('id' => $subscriber_id));
						}
					}
				}
			}
			
			return $authkey;
		}
		
		function htmltf($format = 'html') {
			switch ($format) {
				case 'html'			:
					return true;
					break;
				case 'text'			:
					return false;
					break;
			}
			
			return true;
		}
		
		function gen_subscribe_url($subscriber = null, $mailinglists = null) {
			global $Html;
			$url = "";
			
			if (!empty($subscriber)) {
				if (!empty($mailinglists) && is_array($mailinglists)) {
					$mailinglists = implode(",", $mailinglists);
				}
				
				$querystring = 'method=subscribe&id=' . $subscriber -> id . '&mailinglists=' . $mailinglists;
				$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
			}
			
			return $url;
			
		}
		
		function gen_resubscribe_link($subscriber = null, $urlonly = false) {
			global $Html;
			$link = "";
			
			if (!empty($subscriber)) {
				$querystring = 'method=resubscribe&email=' . $subscriber -> email . '&mailinglists=' . implode(",", $subscriber -> mailinglists);
				$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
				
				if (!empty($urlonly)) {
					$link = $url;
				} else {
					$link = '<a class="newsletters_resubscribe newsletters_link" href="' . $url . '">' . __($this -> get_option('resubscribetext')) . '</a>';
				}
			}
			
			return $link;
		}
		
		function gen_unsubscribe_link($subscriber = null, $user = null, $theme_id = null, $history_id = null, $alllists = false, $urlonly = false) {
			global $Db, $Html, $Subscriber, $HistoriesList;
		
			if (!empty($subscriber) || !empty($user)) {				
				$linktext = __($this -> get_option('unsubscribetext'));
				$auth_id = (empty($subscriber)) ? $user -> ID : $subscriber -> id;
				$auth_string = (empty($subscriber)) ? $user -> roles[0] : ((!empty($subscriber -> mailinglist_id)) ? $subscriber -> mailinglist_id : false);
				$authkey = $this -> gen_auth($auth_id, $auth_string);
				
				if (!empty($theme_id)) {
					global $wpdb, $Theme;
					
					$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . $theme_id . "' LIMIT 1";
					
					$query_hash = md5($acolorquery);
					if ($ob_acolor = $this -> get_cache($query_hash)) {
						$acolor = $ob_acolor;
					} else {
						$acolor = $wpdb -> get_var($acolorquery);
						$this -> set_cache($query_hash, $acolor);
					}
					
					$style = "color:" . $acolor . ";";
				}
				
				if (!empty($subscriber)) {
					$mailinglists = "";					
					if (!empty($alllists) && $alllists == true) {										
						$slists = $Subscriber -> mailinglists($subscriber -> id);
						$mailinglists = @implode(",", $slists);
						$linktext = __($this -> get_option('unsubscribealltext'));
					} else {	
						
						$history = $this -> History() -> find(array('id' => $history_id));
						$history_lists = maybe_unserialize($history -> mailinglists);
						$includeonly = (empty($history_lists)) ? $subscriber -> mailinglists : $history_lists;
						$subscriber_lists = maybe_unserialize($Subscriber -> mailinglists($subscriber -> id, $includeonly, false, false));
						$mailinglists = (empty($subscriber_lists)) ? $history_lists : $subscriber_lists;
																					
						/*$slists = maybe_unserialize($subscriber -> mailinglists);
						if (!empty($slists)) {
							$mailinglists = implode(",", $slists);
						} elseif (!empty($subscriber -> mailinglist_id)) {
							$mailinglists = $subscriber -> mailinglist_id;
						} elseif (!empty($history_id)) {
							$Db -> model = $HistoriesList -> model;
							$lists = array();
							if ($historieslists = $Db -> find_all(array('history_id' => $history_id))) {
								foreach ($historieslists as $hl) {
									$lists[] = $hl -> list_id;
								}
							}
							
							$mailinglists = implode(",", $lists);
						}*/
					}
					
					$mailinglists = implode(",", $mailinglists);
					$querystring = 'method=unsubscribe&' . $this -> pre . 'history_id=' . $history_id . '&' . $this -> pre . 'subscriber_id=' . $subscriber -> id . '&' . $this -> pre . 'mailinglist_id=' . $mailinglists . '&authkey=' . $authkey;
				} elseif (!empty($user)) {
					$querystring = 'method=unsubscribe&' . $this -> pre . 'history_id=' . $history_id . '&user_id=' . $user -> ID . '&authkey=' . $authkey;
				}
					
				$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
				if (empty($urlonly) || $urlonly == false) {
					$unsubscribelink = '<a class="newsletters_unsubscribe newsletters_link" href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
				} else {
					$unsubscribelink = $url;
				}
				
				return $unsubscribelink;
			}
			
			return false;
		}
		
		function gen_manage_link($subscriber = array(), $theme_id = null, $history_id = null) {
			global $Db, $Subscriber, $Html, $Auth;
		
			if (!empty($subscriber)) {
				$linktext = __($this -> get_option('managelinktext'));
				
				if (empty($subscriber -> cookieauth)) {
					$subscriberauth = $Auth -> gen_subscriberauth();
					$Db -> model = $Subscriber -> model;
					$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
				} else {
					$subscriberauth = $subscriber -> cookieauth;
				}
				
				$url = $Html -> retainquery('method=loginauth&email=' . $subscriber -> email . '&subscriberauth=' . $subscriberauth, $this -> get_managementpost(true));
				
				if (!empty($theme_id)) {
					global $wpdb, $Theme;
					
					$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . $theme_id . "' LIMIT 1";
					
					$query_hash = md5($acolorquery);
					if ($ob_acolor = $this -> get_cache($query_hash)) {
						$acolor = $ob_acolor;
					} else {
						$acolor = $wpdb -> get_var($acolorquery);
						$this -> set_cache($query_hash, $acolor);
					}
						
					$style = "color:" . $acolor . ";";
				}
				
				if (empty($subscriber -> format) || $subscriber -> format == "html") {
					$managelink = '<a class="newsletters_manage newsletters_link" href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
				} else {
					$managelink = $url;
				}
				
				return $managelink;
			}
			
			return false;
		}
		
		function gen_online_link($subscriber = null, $user = null, $history_id = null, $onlyurl = false, $theme_id = null, $print = false) {	
			if (!empty($history_id)) {
				global $Db, $Html;				
				if ($email = $this -> History() -> find(array('id' => $history_id))) {
					$auth_id = (empty($subscriber)) ? $user -> ID : $subscriber -> id;
					$authkey = $this -> gen_auth($auth_id);
					
					if (!empty($subscriber)) {
						$url = home_url() . '/?' . $this -> pre . 'method=newsletter&id=' . $email -> id . '&mailinglist_id=' . ((!empty($subscriber -> mailinglist_id)) ? $subscriber -> mailinglist_id : false)  . '&subscriber_id=' . $subscriber -> id . '&authkey=' . $authkey;
					} else {
						$querystring = $this -> pre . 'method=newsletter&id=' . $email -> id . '&user_id=' . $user -> ID . '&authkey=' . $authkey;
						$url = $Html -> retainquery($querystring, home_url());
					}
					
					if (!empty($print) && $print == true) {
						$url = $Html -> retainquery('print=1', $url);
					}
					
					if (!empty($theme_id)) {
						global $wpdb, $Theme;
						
						$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . $theme_id . "' LIMIT 1";
						
						$query_hash = md5($acolorquery);
						if ($ob_acolor = $this -> get_cache($query_hash)) {
							$acolor = $ob_acolor;
						} else {
							$acolor = $wpdb -> get_var($acolorquery);
							$this -> set_cache($query_hash, $acolor);
						}
						
						$style = "color:" . $acolor . ";";
					}
					
					if (!empty($print)) {
						$url = 'http://www.printfriendly.com/print?url=' . urlencode($url);
					}
					
					if (!empty($onlyurl) && $onlyurl == true) {
						return $url;
					} else {
						if (empty($subscriber -> format) || $subscriber -> format == "html") {
							$text = (empty($print)) ? __($this -> get_option('onlinelinktext')) : __($this -> get_option('printlinktext'));
							$onlinelink = '<a class="newsletters_online newsletters_link" href="' . $url . '" style="' . $style . '">' . $text . '</a>';
						} else {
							$onlinelink = $url;
						}
					}
					
					return $onlinelink;
				}
			}
			
			return false;
		}
		
		function gen_activation_link($subscriber = array(), $theme_id = null, $urlonly = false) {
			global $Html;
		
			if (!empty($subscriber)) {
				$linktext = apply_filters('newsletters_activation_link_text', __($this -> get_option('activationlinktext')));
				
				$authkey = $this -> gen_auth($subscriber -> id, (!empty($subscriber -> mailinglist_id) ? $subscriber -> mailinglist_id : false));
				
				if (!empty($theme_id)) {
					global $wpdb, $Theme;
					
					$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . $theme_id . "' LIMIT 1";
					
					$query_hash = md5($acolorquery);
					if ($ob_acolor = $this -> get_cache($query_hash)) {
						$acolor = $ob_acolor;
					} else {
						$acolor = $wpdb -> get_var($acolorquery);
						$this -> set_cache($query_hash, $acolor);
					}
					
					$style = "color:" . $acolor . ";";
				}
				
				$mailinglist_id = (empty($subscriber -> mailinglists)) ? $subscriber -> mailinglist_id : @implode(",", $subscriber -> mailinglists);
				
				$querystring = $this -> pre . 'method=activate&' . $this -> pre . 'subscriber_id=' . $subscriber -> id . '&' . $this -> pre . 'mailinglist_id=' . $mailinglist_id . '&authkey=' . $authkey;
				$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
				
				if (empty($subscriber -> format) || $subscriber -> format == "html" || !empty($urlonly)) {
					$activationlink = '<a class="newsletters_activate newsletters_link" href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
				} else {
					$activationlink = $url;
				}
				
				return apply_filters('newsletters_activation_link', $activationlink, $url, $linktext, $style, $linktext);
			}
			
			return false;
		}
		
		function gen_tracking_link($eunique = null) {
			$tracking = "";
			
			if (!empty($eunique)) {
				if ($this -> get_option('tracking') == "Y") {
					$tracking = '<img class="newsletters-tracking" src="' . add_query_arg(array($this -> pre . 'method' => "track", 'id' => $eunique), home_url()) . '" />';
				}	
			}
			
			return apply_filters('newsletters_tracking_image', $tracking, $eunique);
		}
		
		function strip_set_variables($message = null) {
			if (!empty($message)) {			
				$newpatterns = array(
					"/\[" . $this -> pre . "email\]/", 
					'/\[' . $this -> pre . 'field name="email"\]/',
					"/\[" . $this -> pre . "unsubscribe\]/", 
					"/\[" . $this -> pre . "blogname\]/", 
					"/\[" . $this -> pre . "siteurl\]/", 
					"/\[" . $this -> pre . "activate\]/", 
					"/\[" . $this -> pre . "manage\]/", 
					"/\[" . $this -> pre . "online\]/", 
					"/\[" . $this -> pre . "track\]/", 
					"/\[" . $this -> pre . "mailinglist\]/",
					"/\[" . $this -> pre . "subject\]/",
					"/\[" . $this -> pre . "historyid\]/",
					"/\[" . $this -> pre . "unsubscribecomments\]/",
					"/\[" . $this -> pre . "bouncecount\]/",
					"/\[" . $this -> pre . "customfields\]/",
				);
				
				$message = preg_replace($newpatterns, "", $message);
				
				$pattern = get_shortcode_regex(array("wpmlfield", "newsletters_field"));
				$message = preg_replace("/" . $pattern . "/si", "", $message);
				
				$message = apply_filters('newsletters_strip_set_variables', $message);
				return $message;
			}
			
			return false;
		}
		
		function gen_role_names($user = null) {
			$role_names = "";
			
			if (!empty($user -> roles) && is_array($user -> roles)) {
				global $wp_roles;
				$role_names = array();
				
				foreach ($user -> roles as $role_key) {
					$role_names[] = $wp_roles -> role_names[$role_key];
				}
				
				$role_names = implode(", ", $role_names);
			}
			
			return $role_names;
		}
		
		function gen_mailinglist_names($subscriber = null) {
			global $wpdb, $Mailinglist;
			$mailinglist_names = "";
			$titles = array();
			
			if (empty($subscriber -> mailinglists)) {			
				if (!empty($_POST['mailinglists'])) {
					$subscriber -> mailinglists = $_POST['mailinglists'];
				} else {
					$subscriber -> mailinglists = array($subscriber -> mailinglist_id);
				}
			}
		
			if (!empty($subscriber -> mailinglists)) {			
				foreach ($subscriber -> mailinglists as $list_id) {
					if ($title = $Mailinglist -> get_title_by_id($list_id)) {
						$titles[] = __($title);
					}
				}
				
				$mailinglist_names = implode(", ", $titles);
			}
			
			return $mailinglist_names;
		}
		
		function autoembed_callback($match = null) {						
			$url = trim(strip_tags($match[0]));			
			
	        if (!empty($url)) {		        
		        require_once(ABSPATH . WPINC . DS . 'class-oembed.php');
		        $embed = new WP_oEmbed();
		        if ($provider = $embed -> get_provider($url)) {			        
			        if ($response = $embed -> fetch($provider, $url)) {				        
				        $play_icon = $this -> render_url('img/play.png', 'default');
						
						$imagesize = getimagesize($response -> thumbnail_url);
						$width = $imagesize[0];
						$height = $imagesize[1];
						
						$output .= '<table border="0" width="' . $width . '" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td background="' . $response -> thumbnail_url . '" style="padding:0 !important; border:0; background-image: url(\'' . $response -> thumbnail_url . '\'); background-repeat:no-repeat; text-align:center; vertical-align:middle;" align="center" valign="middle" bgcolor="#FFFFFF" width="100%" height="' . $height . '">
										<!--[if gte mso 9]>
											<v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:' . $width . 'px; height:' . $height . 'px;" strokecolor="none">
											<v:fill type="tile" color="#ffffff" src="' . $response -> thumbnail_url . '" /></v:fill>
										<![endif]-->
											<table width="' . $width . '" border="0" cellspacing="0" cellpadding="0">
								            	<tbody>
								            		<tr>
								            			<td style="padding:0 !important; border:0; text-align:center; vertical-align:middle;" width="100%" height="' . $height . '"" valign="middle" align="center">
															<a href="' . $url . '"><img src="' . $play_icon . '" /></a>
														</td>
													</tr>
												</tbody>
											</table>
										<!--[if gte mso 9]>
											</v:rect>
										<![endif]-->
									</td>
								</tr>
							</tbody>
						</table>';
		
						return $output;
					}
				}
	        }
	        
	        return $url;
	    }
		
		function process_set_variables($subscriber = null, $user = null, $message = null, $history_id = null, $eunique = null, $issubject = false) {
			global $wpdb, $Db, $Mailinglist, $Subscriber, $Html, $Theme;
			
			if (!empty($issubject) && $issubject == true) {
				$subject = $message;
			}
			
			if (!empty($message)) {									
				// Process shortcodes from subscriber			
				if (!empty($subscriber)) {	
					global $current_subscriber;
					$current_subscriber = $subscriber;
					
					if (!empty($history_id)) {
						$history = $this -> History -> find(array('id' => $history_id));
						$subject = $this -> History() -> field('subject', array('id' => $history_id));
						$post_id = $this -> History() -> field('post_id', array('id' => $history_id));
						if (!empty($post_id) && $getpost = get_post($post_id)) {
							global $post;
							$post = $getpost;
						}
						
						$themeidquery = "SELECT `theme_id` FROM `" . $wpdb -> prefix . $this -> History() -> table . "` WHERE `id` = '" . $history_id . "' LIMIT 1";
						
						$query_hash = md5($themeidquery);
						if ($ob_theme_id = $this -> get_cache($query_hash)) {
							$theme_id = $ob_theme_id;
						} else {
							$theme_id = $wpdb -> get_var($themeidquery);
							$this -> set_cache($query_hash, $theme_id);
						}
					} else {
						$themeidquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `def` = 'Y' LIMIT 1";
						
						$query_hash = md5($themeidquery);
						if ($ob_theme_id = $this -> get_cache($query_hash)) {
							$theme_id = $ob_theme_id;
						} else {
							$theme_id = $wpdb -> get_var($themeidquery);
							$this -> set_cache($query_hash, $theme_id);
						}
					}
					
					$newsearch = array(
						"/\[" . $this -> pre . "email\]/", 
						'/\[' . $this -> pre . 'field name="email"\]/',
						"/\[" . $this -> pre . "unsubscribe\]/", 
						"/\[" . $this -> pre . "unsubscribeurl\]/", 
						"/\[" . $this -> pre . "unsubscribeall\]/",
						"/\[" . $this -> pre . "blogname\]/", 
						"/\[" . $this -> pre . "siteurl\]/", 
						"/\[" . $this -> pre . "activate\]/", 
						"/\[newsletters_activate_url\]/", 
						"/\[" . $this -> pre . "manage\]/", 
						"/\[newsletters_print\]/",
						"/\[" . $this -> pre . "online\]/", 
						"/\[newsletters_online_url\]/",
						"/\[" . $this -> pre . "track\]/", 
						"/\[" . $this -> pre . "mailinglist\]/",
						"/\[" . $this -> pre . "subject\]/",
						"/\[" . $this -> pre . "historyid\]/",
						"/\[" . $this -> pre . "unsubscribecomments\]/",
						"/\[" . $this -> pre . "bouncecount\]/",
						"/\[" . $this -> pre . "customfields\]/",
						"/\[newsletters_resubscribe\]/",
						"/\[newsletters_authenticate\]/",
					);
					
					$newsearch = apply_filters('newsletters_processvariables_search', $newsearch, $subscriber);
					
					$newreplace = array(
						$subscriber -> email, 
						$subscriber -> email,
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, false), 
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, false, true),
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, true),
						get_bloginfo('name'), 
						home_url(), 
						$this -> gen_activation_link($subscriber, $theme_id), 
						$this -> gen_activation_link($subscriber, $theme_id, true),
						$this -> gen_manage_link($subscriber, $theme_id, $history_id), 
						$this -> gen_online_link($subscriber, false, $history_id, false, $theme_id, true),
						$this -> gen_online_link($subscriber, false, $history_id, false, $theme_id, false),
						$this -> gen_online_link($subscriber, false, $history_id, true, $theme_id, false), 
						$this -> gen_tracking_link($eunique), 
						$this -> gen_mailinglist_names(((empty($history)) ? $subscriber : $history)),
						//stripslashes(str_replace("$", "&#36;", $subject)),
						(preg_replace('/\$(\d)/', '\\\$$1', stripslashes($subject))),
						$history_id,
						$this -> gen_unsubscribe_comments(),
						$subscriber -> bouncecount,
						$this -> output_custom_fields($subscriber),
						$this -> gen_resubscribe_link($subscriber),
						$this -> gen_authenticate_link($subscriber),
					);
					
					$newreplace = apply_filters('newsletters_processvariables_replace', $newreplace, $subscriber);
					
					// Default subscriber fields
					if (!empty($Subscriber -> table_fields)) {
						foreach ($Subscriber -> table_fields as $field => $attributes) {
							$newsearch[$field] = '/\[' . $this -> pre . 'field name="' . $field . '"\]/';
							$newreplace[$field] = $subscriber -> {$field};
						}
					}
					
					$subject = preg_replace($newsearch, $newreplace, stripslashes($subject));
					$subject = apply_filters('newsletters_process_set_variables_subscriber_subject', $subject, $subscriber);
					$message = preg_replace($newsearch, $newreplace, stripslashes($message));
					$message = apply_filters('newsletters_process_set_variables_subscriber_message', $message, $subscriber);
					
					$this -> replace_subscriber = $subscriber;
					// get regular expression on [wpmlfield...] and [newsletters_field...]
					$pattern = get_shortcode_regex(array("wpmlfield", "newsletters_field"));
					$message = preg_replace_callback("/" . $pattern . "/si", array($this, "replace_custom_field"), $message);
					$message = htmlspecialchars_decode($message, ENT_NOQUOTES);
					$subject = preg_replace_callback("/" . $pattern . "/si", array($this, "replace_custom_field"), $subject);
					$subject = htmlspecialchars_decode($subject, ENT_NOQUOTES);	
					
					$this -> replace_subscriber = false;				
				// Process shortcodes from user
				} elseif (!empty($user)) {					
					if (!empty($history_id)) {
						$subject = $this -> History() -> field('subject', array('id' => $history_id));
						$post_id = $this -> History() -> field('post_id', array('id' => $history_id));
						if (!empty($post_id) && $getpost = get_post($post_id)) {
							global $post;
							$post = $getpost;
						}
						
						$themeidquery = "SELECT `theme_id` FROM `" . $wpdb -> prefix . $this -> History() -> table . "` WHERE `id` = '" . $history_id . "' LIMIT 1";
						
						$query_hash = md5($themeidquery);
						if ($ob_theme_id = $this -> get_cache($query_hash)) {
							$theme_id = $ob_theme_id;
						} else {
							$theme_id = $wpdb -> get_var($themeidquery);
							$this -> set_cache($query_hash, $theme_id);
						}
					} else {
						$themeidquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `def` = 'Y' LIMIT 1";
						
						$query_hash = md5($themeidquery);
						if ($ob_theme_id = $this -> get_cache($query_hash)) {
							$theme_id = $ob_theme_id;
						} else {
							$theme_id = $wpdb -> get_var($themeidquery);
							$this -> set_cache($query_hash, $theme_id);
						}
					}
					
					$newsearch = array(
						"/\[" . $this -> pre . "email\]/", 
						'/\[' . $this -> pre . 'field name="email"\]/',
						"/\[" . $this -> pre . "unsubscribe\]/", 
						"/\[" . $this -> pre . "unsubscribeurl\]/", 
						"/\[" . $this -> pre . "unsubscribeall\]/",
						"/\[" . $this -> pre . "blogname\]/", 
						"/\[" . $this -> pre . "siteurl\]/", 
						"/\[" . $this -> pre . "activate\]/", 
						"/\[" . $this -> pre . "manage\]/", 
						"/\[" . $this -> pre . "online\]/", 
						"/\[" . $this -> pre . "track\]/", 
						"/\[" . $this -> pre . "mailinglist\]/",
						"/\[" . $this -> pre . "subject\]/",
						"/\[" . $this -> pre . "historyid\]/",
						"/\[" . $this -> pre . "unsubscribecomments\]/",
						"/\[" . $this -> pre . "bouncecount\]/",
						"/\[" . $this -> pre . "customfields\]/",
					);
					
					$newreplace = array(
						$user -> user_email, 
						$user -> user_email,
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, false), 
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, false, true),
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, true),
						get_bloginfo('name'), 
						home_url(), 
						"", 
						get_edit_user_link($user -> ID), 
						$this -> gen_online_link(false, $user, $history_id, false, $theme_id), 
						$this -> gen_tracking_link($eunique), 
						$this -> gen_role_names($user),
						stripslashes($subject),
						$history_id,
						$this -> gen_unsubscribe_comments(),
						$subscriber -> bouncecount,
						"",
					);
					
					$subject = preg_replace($newsearch, $newreplace, stripslashes($subject));
					$subject = apply_filters('newsletters_process_set_variables_user_subject', $subject, $user);
					$message = preg_replace($newsearch, $newreplace, stripslashes($message));
					$message = apply_filters('newsletters_process_set_variables_user_message', $message, $user);
					
					$this -> replace_user = $user;
					$pattern = get_shortcode_regex(array("wpmlfield", "newsletters_field"));
					$message = preg_replace_callback("/" . $pattern . "/si", array($this, "replace_custom_field"), $message);
					$message = htmlspecialchars_decode($message, ENT_NOQUOTES);
					$subject = preg_replace_callback("/" . $pattern . "/si", array($this, "replace_custom_field"), $subject);
					$subject = htmlspecialchars_decode($subject, ENT_NOQUOTES);	
					
					$this -> replace_user = false;
				}
			}
			
			if (!empty($issubject) && $issubject == true) {	
				$subject = do_shortcode($subject);
				$return = $subject;
			} else {
				$message = do_shortcode($message);
				$return = $message;
			}
			
			//** Bit.ly and Click tracking					
			$pattern = '/<a[^>]*?href=[\'"](.*?)[\'"][^>]*?>(.*?)<\/a>/si';				
			if (preg_match_all($pattern, $return, $regs)) {				
				$return = apply_filters('newsletters_emailbody_links', $return, $history_id, $regs);
				$shortlinks = $this -> get_option('shortlinks');
			
				/* Bit.ly if shortlinks are enabled */
				if (!empty($shortlinks) && $shortlinks == "Y") {													
					if (!empty($regs[1])) {
						$results = $regs[1];
						foreach($results as $k => $v) {														
							if (apply_filters('wpml_bitlink_loop', true, $v, $regs)) {
								$bitlink = $this -> make_bitly_url($v);								
								if (!empty($bitlink)) {									
									$pattern = '/[\'"](' . preg_quote($v, '/') . ')[\'"]/si';									
									$return = preg_replace($pattern, '"' . $bitlink . '"', $return);
									$regs[1][$k] = $bitlink;
								}
							}
						}
					}
				}
			
				/* Click Tracking */
				if ($this -> get_option('clicktrack') == "Y") {					
					if (!empty($regs[1])) {
						$results = $regs[1];
						foreach ($results as $rkey => $result) {
							if (apply_filters('wpml_hashlink_loop', true, $result, $regs)) {								
								$hashlink = $this -> hashlink($result, $history_id, $subscriber -> id, false);								
								$pattern = '/[\'"](' . preg_quote($result, '/') . ')[\'"]/si';									
								$return = preg_replace($pattern, '"' . $hashlink . '"', $return);
							}
						}
					}
				}	
			}
			
			// Replace video URLs
			$videoembed = $this -> get_option('videoembed');
			if (!empty($videoembed)) {
				require_once(ABSPATH . WPINC . DS . 'class-oembed.php');
				$embed = new WP_oEmbed();
				
				$patterns = array();
				if (!empty($embed -> providers)) {
					foreach ($embed -> providers as $regex => $data) {		
						if (empty($data[1])) {
							$regex = '#' . str_replace( '___wildcard___', '(.+)', preg_quote( str_replace( '*', '___wildcard___', $regex ), '#' ) ) . '#i';
							$regex = preg_replace( '|^#http\\\://|', '#https?\://', $regex);
						}
						
						$patterns[] = $regex;
					}
				}
				
				$return = preg_replace_callback($patterns, array($this, 'autoembed_callback'), $return);
			}
				
			return $return;
		}
		
		function replace_custom_field($matches = null) {
			global $Db, $Field, $Html;
			
			$subscriber = $this -> replace_subscriber;
			$user = $this -> replace_user;
			
			if (!empty($matches)) {
				$atts = shortcode_parse_atts($matches['3']);	
								
				if (!empty($atts['name'])) {					
					$Db -> model = $Field -> model;
					if ($field = $Db -> find(array('slug' => $atts['name']))) {						
						$fieldoptions = $field -> newfieldoptions;
						
						if (!empty($subscriber)) {
							switch ($field -> type) {
								case 'pre_country'		:
									$shortcode_replace = $this -> Country() -> field('value', array('id' => $subscriber -> {$field -> slug}));
									break;
								case 'pre_date'			:
									$date = @unserialize($subscriber -> {$field -> slug});
									if (!empty($date) && is_array($date)) {
										$shortcode_replace = $date['y'] . '-' . $date['m'] . '-' . $date['d'];
									} else {
										$shortcode_replace = date_i18n(get_option('date_format'), strtotime($subscriber -> {$field -> slug}));
									}
									break;
								case 'pre_gender'		:
									$shortcode_replace = $Html -> gender($subscriber -> {$field -> slug});
									break;
								case 'checkbox'			:
									$supoptions = maybe_unserialize($subscriber -> {$field -> slug});
									if (!empty($supoptions) && is_array($supoptions)) {
										$replace = "";
										foreach ($supoptions as $supopt) {	
											$replace .= '&raquo; ' . __($fieldoptions[$supopt]) . "\r\n";
										}
										$shortcode_replace = $replace;
									} else {
										$shortcode_replace = __('none', $this -> plugin_name);
									}
									break;
								case 'radio'			:
								case 'select'			:
									$value = $subscriber -> {$field -> slug};									
									$shortcode_replace = __($fieldoptions[$value]);
									break;
								case 'special'			:
									switch ($field -> slug) {
										case 'list'			:
											$shortcode_replace = $this -> gen_mailinglist_names($subscriber);	
											break;
									}
									break;
								default					:
									$value = $subscriber -> {$field -> slug};
									if (!empty($value)) {
										if (($varray = @unserialize($value)) !== false) {
											$subscriber -> {$field -> slug} = '';
											$newline = (empty($subscriber -> format) || $subscriber -> format == "html") ? "<br/>" : "\r\n";
											
											foreach ($varray as $vkey => $vval) {
												$subscriber -> {$field -> slug} .= '&raquo; ' . __($vval) . $newline;
											}
										} else {
											if (!empty($field -> type) && $field -> type == "textarea") {
												$subscriber -> {$field -> slug} = wpautop($subscriber -> {$field -> slug});
											}
										}
									}
								
									$shortcode_replace = $subscriber -> {$field -> slug};
									break;
							}
						
							return $shortcode_replace;
						} elseif (!empty($user)) {	
							$importusersfields = $this -> get_option('importusersfields');
							$importusersfieldspre = $this -> get_option('importusersfieldspre');
													
							if (!empty($importusersfieldspre[$field -> id])) {
								if (!empty($user -> {$importusersfieldspre[$field -> id]})) {
									$shortcode_replace = $user -> {$importusersfieldspre[$field -> id]};
								}
							} elseif (!empty($importusersfields[$field -> id])) {
								if (!empty($user -> {$importusersfields[$field -> id]})) {
									$shortcode_replace = $user -> {$importusersfields[$field -> id]};
								}
							}
							
							return $shortcode_replace;
						}
					} else {
						if (!empty($subscriber -> {$atts['name']})) {
							return stripslashes($subscriber -> {$atts['name']});
						}
					}
				}
			}
			
			return false;
		}
		
		function gen_authenticate_link($subscriber = null) {
			global $Html;
			$link = false;
			
			if (!empty($subscriber)) {
				$subscriberauth = $this -> gen_auth($subscriber -> id);
				
				$url = $Html -> retainquery('method=loginauth&email=' . $subscriber -> email . '&subscriberauth=' . $subscriberauth, $this -> get_managementpost(true));
				$text = __($this -> get_option('authenticatelinktext'));
				if (empty($text)) $text = __('Authenticate now', $this -> plugin_name);
				$link = '<a href="' . $url . '">' . esc_attr(stripslashes($text)) . '</a>';
			}
			
			return $link;
		}
		
		function gen_unsubscribe_comments() {
			/* Unsubscribe Comments */
			$unsubscribecomments = __('No feedback was provided by the subscriber.', $this -> plugin_name);
			if (!empty($_POST[$this -> pre . 'comments'])) {
				$unsubscribecomments = "";
				$unsubscribecomments .= __('Comments:', $this -> plugin_name) . "\r\n";
				$unsubscribecomments .= "------------------------------------" . "\r\n";
				$unsubscribecomments .= stripslashes($_POST[$this -> pre . 'comments']) . "\r\n";
				$unsubscribecomments .= "------------------------------------" . "\r\n";
			}
			
			return wpautop($unsubscribecomments);
		}
		
		function subscription_confirm($subscriber = array()) {
			global $wpdb, $Db, $Subscriber, $Mailinglist, $SubscribersList;
			
			if (!empty($subscriber)) {			
				if (!empty($_POST['list_id'])) {
					$subscriber -> mailinglists = $_POST['list_id'];	
				} elseif (!empty($subscriber -> mailinglists)) {
					//do nothing, it's ready
				} else {
					$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, false, false, "N");
				}
			
				if ($this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {			
					if ($this -> get_option('activationemails') == "multiple") {
						foreach ($subscriber -> mailinglists as $list_id) {
							$isactive = $SubscribersList -> field('active', array('subscriber_id' => $subscriber -> id, 'list_id' => $list_id));
							$subscriber -> mailinglist_id = $list_id;
							$subscriber -> mailinglists = array($list_id);
							$mailinglist = $Mailinglist -> get($list_id, false);
						
							if ($isactive == "N") {
								if (empty($mailinglist -> doubleopt) || $mailinglist -> doubleopt == "Y") {
									if ($this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {									
										$subject = stripslashes($this -> et_subject('confirm'));
										$fullbody = $this -> et_message('confirm', $subscriber);
										$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('confirm'), false, $fullbody);
										$this -> execute_mail($subscriber, false, $subject, $message, false, false, false, false, "confirmation");
									}
								} else {
									$Db -> model = $SubscribersList -> model;
									$Db -> save_field('active', "Y", array('subscriber_id' => $subscriber -> id, 'list_id' => $list_id));
								}
							}
						}
					} else {	
						foreach ($subscriber -> mailinglists as $lkey => $list_id) {
							$mailinglist = $Mailinglist -> get($list_id, false);
							if (!empty($mailinglist -> doubleopt) && $mailinglist -> doubleopt == "N") {
								unset($subscriber -> mailinglists[$lkey]);
								$Db -> model = $SubscribersList -> model;
								$Db -> save_field('active', "Y", array('subscriber_id' => $subscriber -> id, 'list_id' => $list_id));
							}
						}
							
						if (!empty($subscriber -> mailinglists)) {		
							$subject = stripslashes($this -> et_subject('confirm'));
							$fullbody = $this -> et_message('confirm', $subscriber);
							$message = $this -> render_email(false, array('subscriber' => $subscriber), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('confirm'), false, $fullbody);
							$this -> execute_mail($subscriber, false, $subject, $message, false, false, false, false, "confirmation");
						}
					}
					
					return true;
				}
			}
			
			return false;
		}
		
		function admin_subscription_notification($subscriber = array()) {	
			global $wpdb, $Mailinglist;
		
			if (!empty($subscriber)) {
				if ($this -> get_option('adminemailonsubscription') == "Y") {		
					
					$emailsused = array();
					
					$adminemail = $this -> get_option('adminemail');	// main administrator email
					$to = (object) array('email' => $adminemail);
					$subject = stripslashes($this -> et_subject('subscribe', $subscriber));
					$fullbody = $this -> et_message('subscribe', $subscriber);					
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('subscribe'), false, $fullbody);					
					
					if (strpos($adminemail, ",") !== false) {
						$adminemails = explode(",", $adminemail);
						foreach ($adminemails as $adminemail) {
							if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
								$to -> email = $adminemail;
								$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "subscription");
								$emailsused[] = $adminemail;
							}
						}
					} else {
						$to -> email = $adminemail;
						$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "subscription");
					}
					
					if (!empty($subscriber -> mailinglists)) {
						foreach ($subscriber -> mailinglists as $mailinglist_id) {
							$adminemailquery = "SELECT `adminemail` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $mailinglist_id . "'";
							if ($email = $wpdb -> get_var($adminemailquery)) {
								if (!empty($email) && $email != $adminemail) {		
									if (empty($emailsused) || !in_array($email, $emailsused)) {							
										$to = (object) array('email' => $email);
										$subject = stripslashes($this -> et_subject('subscribe', $subscriber));
										$fullbody = $this -> et_message('subscribe', $subscriber);					
										$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('subscribe'), false, $fullbody);
										$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "subscription");
										$emailsused[] = $email;
									}
								}
							}
						}
					}
					
					return true;
				}
			}
			
			return false;
		}
		
		function user_unsubscription_notification($subscriber = null, $mailinglists = null) {
			global $wpdb, $Mailinglist, $Subscriber;
			$unsubscribe_usernotification = $this -> get_option('unsubscribe_usernotification');
			
			if (!empty($subscriber) && !empty($mailinglists)) {
				if (!empty($unsubscribe_usernotification)) {
					$subject = stripslashes($this -> et_subject('unsubscribeuser', $subscriber));
					$fullbody = $this -> et_message('unsubscribeuser', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglists' => $mailinglists), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('unsubscribeuser'), false, $fullbody);
					
					if ($this -> execute_mail($subscriber, false, $subject, $message, false, false, false, false, "unsubscription")) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		function admin_unsubscription_notification($subscriber = array(), $mailinglist = array()) {
			global $wpdb, $Mailinglist, $Subscriber;
			
			if (!empty($subscriber) && !empty($mailinglist)) {			
				if ($this -> get_option('adminemailonunsubscription') == "Y") {	
					$emailsused = array();								
					$adminemail = $this -> get_option('adminemail');	// main administrator email
					
					if (!empty($subscriber -> mailinglists)) {
						foreach ($subscriber -> mailinglists as $mailinglist_id) {
							$adminemailquery = "SELECT `adminemail` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $mailinglist_id . "'";
							if ($email = $wpdb -> get_var($adminemailquery)) {
								if (!empty($email) && $email != $adminemail) {
									if (empty($emailsused) || !in_array($email, $emailsused)) {
										$to = (object) array('email' => $email);
										$subject = stripslashes($this -> et_subject('unsubscribe', $subscriber));
										$fullbody = $this -> et_message('unsubscribe', $subscriber);					
										$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('subscribe'), false, $fullbody);
										$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "unsubscription");
										$emailsused[] = $email;
									}
								}
							}
						}
					}
					
					if (is_array($mailinglist)) {
						$subscriber -> mailinglists = $mailinglist;
					} else {
						$subscriber -> mailinglists = array($mailinglist);
					}
					
					$to = new stdClass();
					$to -> id = $Subscriber -> admin_subscriber_id();
					$to -> email = $adminemail;
					$subject = stripslashes($this -> et_subject('unsubscribe', $subscriber));
					$fullbody = $this -> et_message('unsubscribe', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('unsubscribe'), false, $fullbody);
					
					if (strpos($adminemail, ",") !== false) {
						$adminemails = explode(",", $adminemail);
						foreach ($adminemails as $adminemail) {
							if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
								$to -> email = $adminemail;
								$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "unsubscription");
								$emailsused[] = $adminemail;
							}
						}
						
						return true;
					} else {
						$to -> email = $adminemail;
						$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "unsubscription");
						
						return true;
					}
				}
			}
			
			return false;
		}
		
		function admin_bounce_notification($subscriber = array()) {	
			if ($this -> get_option('adminemailonbounce') == "Y") {		
				if (!empty($subscriber)) {		
					$emailsused = array();	
					$adminemail = $this -> get_option('adminemail');
					$subject = stripslashes($this -> et_subject('bounce', $subscriber));
					$fullbody = $this -> et_message('bounce', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('bounce'), false, $fullbody);
					
					if (strpos($adminemail, ",") !== false) {
						$adminemails = explode(",", $adminemail);
						foreach ($adminemails as $adminemail) {
							if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
								$to -> email = $adminemail;
								$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "bounce");
								$emailsused[] = $adminemail;
							}
						}
					} else {
						$to -> email = $adminemail;
						$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "bounce");
					}
					
					return true;
				}
			}
			
			return false;
		}
		
		function strip_title($title = null) {
			global $Html;
			return $Html -> sanitize($title, "_");
			
			return false;
		}
		
		function replace_history($match = array()) {
			if (!empty($match)) {
				global $Db;
				
				if ($emails = $this -> History() -> find_all(array('sent' => "> 0"), false, array('modified', "DESC"))) {			
					$content = '';
				
					foreach ($emails as $email) {
						ob_start();
						
						?>
						
						<h3><a href="<?php echo get_option('home'); ?>?<?php echo $this -> pre; ?>method=newsletter&amp;id=<?php echo $email -> id; ?>" title="<?php echo $email -> subject; ?>"><?php echo $email -> subject; ?></a></h3>
						<div><small><?php _e('Sent on', $this -> plugin_name); ?> : <?php echo $email -> modified; ?></small></div>
						<?php echo $this -> strip_set_variables($email -> message); ?>
						
						<?php
						
						$content .= ob_get_clean();
					}
					
					$content = wpautop($content);
					return $content;
				}
			}
			
			return false;
		}
		
		function replace_meta($matches = array()) {
			if (!empty($matches[0])) {			
				if (preg_match("/" . $this -> pre . "meta\_([0-9]*)/i", $matches[0], $matches2)) {
					if (!empty($matches2[1])) {
						global $post_ID;
						$oldpostid = $post_ID;
						$post_ID = $matches2[1];
						
						ob_start();
						the_meta();
						$meta = ob_get_clean();
						
						$post_ID = $oldpostid;
						return $meta;
					}
				}
			}
			
			return false;
		}
		
		function remove_server_limits() {
			if (!ini_get('safe_mode')) {
				@set_time_limit(0);
				@ini_set('memory_limit', -1);
				@ini_set('upload_max_filesize', '128M');
				@ini_set('post_max_size', '1024M');
				@ini_set('max_execution_time', 3000);
				@ini_set('max_input_time', 3000);
				return true;
			}
	
			return false;
		}
		
		function set_time_limit($time = 0) {
			if (ini_get('max_execution_time')) {
				ini_set('max_execution_time', 0);
			}
	
			//ensure that "safe_mode" is turned off
			if (!ini_get('safe_mode')) {		
				//check if "set_time_limit" is available
				if (ini_get('set_time_limit')) {
					//set the "max_execution_time" to unlimited
					set_time_limit(0);
				}
			}
		}
		
		function phpmailer_messageid() {
			$messageid = "<";
			$messageid .= md5(uniqid(time()));
			$messageid .= "@";
			$messageid .= $_SERVER['SERVER_NAME'];
			$messageid .= ">";
			return $messageid;
		}
		
		function inlinestyles($html = null) {
			global $newsletters_plaintext;
			
			if (!empty($newsletters_plaintext)) return $html;
			
			$inlinestyles = $this -> get_option('inlinestyles');
			
			if (!empty($inlinestyles)) {
				$url = "http://premailer.dialect.ca/api/0.1/documents";
				
				// Remove media query CSS for reinsert later
				
				
				$doc = new DOMDocument();
				$doc -> loadHTML($html);				
				$stylestoadd = array();				
				foreach ($doc -> getElementsByTagName('style') as $style) {
					if ($style -> getAttribute('data-premailer') === "ignore") {
						$style -> parentNode -> removeChild($style);
						$stylestoadd[] = $style -> nodeValue;
					}
				}				
				$html = $doc -> saveHTML();
				
				$postfields = array(
					'html'						=>	$html,
					'adapter'					=>	'hpricot', //nokogiri
					'preserve_styles'			=>	true,
					'remove_ids'				=>	false,
					'remove_classes'			=>	false,
					'remove_comments'			=>	false,
				);
				
				$postfields = apply_filters('newsletters_inlinestyles_options', $postfields);
				
				if (function_exists('curl_init') && $ch = curl_init($url)) {
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);	
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_TIMEOUT, 60);
					curl_setopt($ch, CURLOPT_HEADER, false);
					$result = curl_exec($ch);
					
					curl_close($ch);					
					$result = json_decode($result);
					
					if (!empty($result -> status) && $result -> status == "201") {
						$remote = wp_remote_get(trim($result -> documents -> html), array('timeout' => 120));
						if (!is_wp_error($remote)) {
							$html = trim(html_entity_decode(urldecode($remote['body'])));
							
							$doc = new DOMDocument();
							$doc -> loadHTML($html);
							$head = $doc -> getElementsByTagName('head') -> item(0);
							foreach ($stylestoadd as $styletoadd) {
								$new_elm = $doc -> createElement('style', $styletoadd);
								$elm_type_attr = $doc -> createAttribute('type');
								$elm_type_attr -> value = 'text/css';
								$new_elm -> appendChild($elm_type_attr);
								$elm_data_attr = $doc -> createAttribute('data-premailer');
								$elm_data_attr -> value = 'ignore';
								$new_elm -> appendChild($elm_data_attr);
								$head -> appendChild($new_elm);
							}
							$html = $doc -> saveHTML();							
						} else {
							$this -> log_error($remote -> get_error_message());
						}
					} else {
						$this -> log_error($result -> status . ' - ' . $result -> message);
					}
				}
			}
			
			return apply_filters('newsletters_inlinestyles_html', $html);
		}
		
		function execute_mail($subscriber = null, $user = null, $subject = null, $message = null, $attachments = null, $history_id = null, $eunique = null, $shortlinks = true, $emailtype = "newsletter") {
			global $wpdb, $Db, $Html, $Email, $phpmailer, $Mailinglist, $Subscriber, 
			$SubscribersList, $orig_message, $wpml_message, $wpml_textmessage, $fromwpml, $newsletters_plaintext;
			
			$sent = false;
			$fromwpml = true;
		
			if (empty($subscriber) && empty($user)) { $error[] = __("No subscriber specified", $this -> plugin_name); }
			if (empty($subject)) { $error[] = __('No subject specified', $this -> plugin_name); }
			if (empty($message)) { $error[] = __('No message specified', $this -> plugin_name); }
			
			global $wpdb;
			if (!empty($history_id)) { 
				$query = "SELECT `from`, `fromname`, `text`, `mailinglists` FROM `" . $wpdb -> prefix . $this -> History() -> table . "` WHERE `id` = '" . $history_id . "'";
				$his = $wpdb -> get_row($query);
				$history = stripslashes_deep($his); 
			}
			
			$smtpfrom = (empty($history -> from)) ? __($this -> get_option('smtpfrom')) : $history -> from;
			$smtpfromname = (empty($history -> fromname)) ? __($this -> get_option('smtpfromname')) : $history -> fromname;
			
			$validationdata = array('subscriber' => $subscriber, 'user' => $user, 'subject' => $subject, 'message' => $message, 'history_id' => $history_id);
			$error = apply_filters($this -> pre . '_sendmail_validation', $error, $validationdata);
			
			if (!empty($attachments) && $attachments != false) {
				$attachments = maybe_unserialize($attachments);
			}
			
			if (empty($error)) {
				$Db -> model = $Email -> model;		
				
				$message = str_replace("[wpmlsubject]", $subject, $message);
				
				$subject = do_shortcode($subject);
				$message = do_shortcode($message);
				$wpml_textmessage = do_shortcode($wpml_textmessage);
				
				$subject = $this -> process_set_variables($subscriber, $user, stripslashes($subject), $history_id, $eunique, true);			
				$message = $this -> process_set_variables($subscriber, $user, stripslashes($message), $history_id, $eunique);
				$message = apply_filters('newsletters_execute_mail_message', $message);
				$wpml_textmessage = $this -> process_set_variables($subscriber, $user, stripslashes($wpml_textmessage), $history_id, $eunique);
				$wpml_textmessage = apply_filters('newsletters_execute_mail_textmessage', $wpml_textmessage);
				
				if (!empty($subscriber -> id)) {
					$Subscriber -> inc_sent($subscriber -> id);
				}
				
				if (!empty($subscriber -> mailinglists)) {
					foreach ($subscriber -> mailinglists as $mailinglist_id) {
						$query = "SELECT `paid` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $mailinglist_id . "' LIMIT 1";
						$paid = $wpdb -> get_var($query);
						
						if (!empty($paid) && $paid == "Y") {
							$query = "UPDATE `" . $wpdb -> prefix . $SubscribersList -> table . "` SET `paid_sent` = (`paid_sent` + 1) WHERE `subscriber_id` = '" . $subscriber -> id . "' AND `list_id` = '" . $mailinglist_id . "' LIMIT 1";
							$wpdb -> query($query);
						}
					}
				}
				
				// Should an AltBody be generated?
				// This is used with multi-mime emails are turned on
				$altbody = false;
				if ($this -> get_option('multimime') == "Y" && empty($newsletters_plaintext)) {
					if (!empty($history -> text)) {
						$altbody = $history -> text;	
					} else {						
						if (version_compare(PHP_VERSION, '5.3.2') >= 0) {	
							require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
							$html2text = new Html2Text();
							
							if (!empty($wpml_textmessage)) {
								$altbody = $html2text -> convert($wpml_textmessage);
							}
						}
					}
				}
				
				$to = false;
				if (!empty($subscriber)) {
					$to = $subscriber -> email;
				} elseif (!empty($user)) {
					$to = $user -> user_email;
				}
				
				$mailtype = $this -> get_option('mailtype');				
				$mailpriority = $this -> get_option('mailpriority');
	
				global $newsletters_presend, $newsletters_emailraw;
				if ($mailtype == "smtp" || $mailtype == "gmail" || (!empty($newsletters_presend) && $newsletters_presend == true)) {
					if (!is_object($phpmailer)) {
						require_once(dirname(__FILE__) . DS . 'vendors' . DS . 'class.phpmailer.php');							
						$phpmailer = new PHPMailer();
					}
					
					//clear all recipients
					$phpmailer -> ClearAddresses();
					$phpmailer -> ClearAllRecipients();
					$phpmailer -> ClearCCs();
					$phpmailer -> ClearBCCs();
					$phpmailer -> ClearAttachments();
					$phpmailer -> ClearReplyTos();
					$phpmailer -> ClearCustomHeaders();
					
					//set the language
					$phpmailer_language = $this -> plugin_base() . DS . 'vendors' . DS . 'phpmailer-language';
					$phpmailer -> SetLanguage('en', $phpmailer_language);
				
					$phpmailer -> IsSMTP();
					$phpmailer -> Host = $this -> get_option('smtphost');
					$phpmailer -> Port = $this -> get_option('smtpport');
					$phpmailer -> SMTPKeepAlive = true;
					
					$smtpsecure = $this -> get_option('smtpsecure');
					if (!empty($smtpsecure) && $smtpsecure != "N") {
						$phpmailer -> SMTPSecure = $smtpsecure;
					}
					
					$phpmailer -> SMTPAutoTLS = false;
					
					if ($this -> debugging) {
						$phpmailer -> SMTPDebug = true;
					}
					
					if ($this -> get_option('smtpauth') == "Y") {
						$phpmailer -> SMTPAuth = true;
						$phpmailer -> Username = $this -> get_option('smtpuser');
						$phpmailer -> Password = $this -> get_option('smtppass');
					}
					
					//DKIM-Signature (DomainKeys Identified Mail)
					if ($this -> get_option('dkim') == "Y") {
						$phpmailer -> DKIM_identity = $smtpfrom;
						$phpmailer -> DKIM_private = $this -> get_option('dkim_private');
						$phpmailer -> DKIM_domain = $this -> get_option('dkim_domain');
						$phpmailer -> DKIM_selector = $this -> get_option('dkim_selector');
					}
					
					if (!empty($attachments) && $attachments != false) {						
						if (is_array($attachments)) {							
							foreach ($attachments as $attachment) {								
								$phpmailer -> AddAttachment($attachment['filename'], $attachment['title']);	
							}
						} else {
							$phpmailer -> AddAttachment($attachments['filename'], $attachments['title']);
						}
					}
					
					//set the Charset to that of Wordpress
					$phpmailer -> CharSet = get_option('blog_charset');
					$phpmailer -> SetFrom($smtpfrom, $smtpfromname);
					
					$phpmailer -> Sender = $this -> get_option('bounceemail');
					
					if (!empty($subscriber)) {
						$to = $subscriber -> email;
					} elseif (!empty($user)) {
						$to = $user -> user_email;
					}
					
					$phpmailer -> AddCustomHeader('Precedence', "bulk");
					$phpmailer -> AddCustomHeader('List-Unsubscribe', $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true));
					
					$phpmailer -> AddAddress($to);
					$phpmailer -> AddReplyTo($smtpfrom, $smtpfromname);
					
					// Should the Reply-To header be different?
					$replytodifferent = $this -> get_option('replytodifferent');
					if (!empty($replytodifferent)) {
						$smtpreply = $this -> get_option('smtpreply');
						$phpmailer -> AddReplyTo($smtpreply, $smtpfromname);
					}
	
					/*$htmlformat = (empty($subscriber -> format) || $subscriber -> format == "html") ? true : false;
					$phpmailer -> IsHTML($htmlformat);
					
					if (empty($subscriber -> format) || $subscriber -> format == "html") {
						$phpmailer -> IsHTML(true);
					}*/
					
					if (!empty($newsletters_plaintext)) {
						$phpmailer -> ContentType = "text/plain";
						$phpmailer -> IsHTML(false);
						$message = strip_tags($message);
					} else {
						$phpmailer -> ContentType = "text/html";
						$phpmailer -> IsHTML(true);
					}
					
					$phpmailer -> Subject = stripslashes($subject);
					$phpmailer -> Body = $this -> inlinestyles(apply_filters($this -> pre . '_send_body', stripslashes($message), $phpmailer, $history_id));
						
					if (!empty($altbody)) {	
						$phpmailer -> AltBody = $altbody;	
					}
					
					if (!empty($mailpriority) && $mailpriority != 3) {
						$phpmailer -> Priority = $mailpriority; //set the email priority					
					}
					$phpmailer -> WordWrap = 0;
					$phpmailer -> Encoding = $this -> get_option('emailencoding');
					$phpmailer -> MessageID = $this -> phpmailer_messageid();
					
					global $newsletters_presend, $newsletters_emailraw;
					if (!empty($newsletters_presend) && $newsletters_presend == true) {
						$phpmailer -> addCustomHeader('Received', 'by testing');
						$phpmailer -> PreSend();
						//$header = $phpmailer -> MIMEHeader;
						//$body = $phpmailer -> MIMEBody;
						//$emailraw = $header . $body;
						$emailraw = $phpmailer -> getSentMIMEMessage();
						$newsletters_emailraw = $emailraw;
						return $emailraw;
					}
					
					if ($phpmailer -> Send()) {
						$sent = true;
					} else {					
						global $mailerrors;
						$mailerrors = $phpmailer -> ErrorInfo;
						return false;	
					}
				} elseif ($mailtype == "api") {
					$mailapi = $this -> get_option('mailapi');
					
					$data = array(
						'api'			=>	$mailapi,
						'to'			=>	$to,
						'from'			=>	$smtpfrom,
						'fromname'		=>	$smtpfromname,
						'replyto'		=>	$smtpreply,
						'subject'		=>	$subject,
						'html'			=>	$message,
						'text'			=>	$altbody,
						'attachments'	=>	$attachments,
						'mailpriority'	=>	$mailpriority,
					);
					
					do_action('newsletters_sendmail_api', $data);
					
					switch ($mailapi) {
						case 'mailgun'						:							
							require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
							
							$mailgun_apikey = $this -> get_option('mailapi_mailgun_apikey');
							$mailgun_domain = $this -> get_option('mailapi_mailgun_domain');
							
							$mg = new Mailgun\Mailgun($mailgun_apikey);
							
							try {
								
								$data = array(
									'from'				=>	$smtpfromname . '<' . $smtpfrom . '>',
									'to'				=>	$to,
									'subject'			=>	stripslashes($subject),
									'text'				=>	$altbody,
									'html'				=>	((empty($newsletters_plaintext)) ? $this -> inlinestyles(apply_filters($this -> pre . '_send_body', stripslashes($message), $phpmailer, $history_id, 'mailgun', $mg)) : null),
									'attachment'		=>	array(),
								);
								
								$data['h:Precedence'] = "bulk";
								$data['h:List-Unsubscribe'] = $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true);
								
								$files = array();
								
								if (!empty($attachments) && $attachments != false) {						
									if (is_array($attachments)) {							
										foreach ($attachments as $attachment) {	
											$files['attachment'][] = $attachment['filename'];
										}
									} else {
										$files['attachment'][] = $attachment['filename'];
									}
								}
								
								$data = apply_filters('newsletters_mailapi_mailgun_data', $data);
								$files = apply_filters('newsletters_mailapi_mailgun_files', $files);
								
								$result = $mg -> sendMessage($mailgun_domain, $data, $files);
								
								$sent = true;
							} catch (Exception $e) {
								global $mailerrors;
								$mailerrors = $e -> getMessage();
								return false;
							}							
							break;
						case 'mailjet'						:
						
							break;
						case 'amazonses'					:
							
							$mailapi_amazonses_key = $this -> get_option('mailapi_amazonses_key');
							$mailapi_amazonses_secret = $this -> get_option('mailapi_amazonses_secret');
							$mailapi_amazonses_region = $this -> get_option('mailapi_amazonses_region');
						
							require_once($this -> plugin_base() . DS . 'vendors' . DS . 'amazonses' . DS . 'SimpleEmailService.php');
							
							global $sesmessage;
							$sesmessage = new SimpleEmailServiceMessage();
							$sesmessage -> addTo($to);
							$sesmessage -> setFrom($smtpfromname . '<' . $smtpfrom . '>');
							$sesmessage -> setSubject(stripslashes($subject));
							$htmlbody = ((empty($newsletters_plaintext)) ? $this -> inlinestyles(apply_filters($this -> pre . '_send_body', stripslashes($message), $phpmailer, $history_id, 'amazonses', $sesmessage)) : null);
							$sesmessage -> setMessageFromString($altbody, $htmlbody);
							
							if (!empty($attachments) && $attachments != false) {						
								if (is_array($attachments)) {							
									foreach ($attachments as $attachment) {	
										$sesmessage -> addAttachmentFromFile($attachment['title'], $attachment['filename']);						
									}
								} else {
									$sesmessage -> addAttachmentFromFile($attachment['title'], $attachment['filename']);
								}
							}
							
							$sesmessage -> addCustomHeader("Precedence: bulk");
							$sesmessage -> addCustomHeader("List-Unsubscribe: " . $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true));
							
							do_action('newsletters_mailapi_amazonses_beforesend', $sesmessage);
							
							$ses = new SimpleEmailService($mailapi_amazonses_key, $mailapi_amazonses_secret, 'email.' . $mailapi_amazonses_region . '.amazonaws.com');
							$result = $ses -> sendEmail($sesmessage);
							
							if (!empty($result)) {
								
								$sent = true;
							} else {
								return false;
							}
						
							break;
						case 'sendgrid'						:
							require_once $this -> plugin_base() . DS . 'vendors' . DS . 'mailapis' . DS . 'sendgrid' . DS . 'sendgrid-php.php';							
							$sendgrid_apikey = $this -> get_option('mailapi_sendgrid_apikey');
							
							$sendgrid = new SendGrid($sendgrid_apikey);
							
							global $sendgridemail;
							$sendgridemail    = new SendGrid\Email();
							
							$sendgridemail -> addTo($to);
							$sendgridemail -> setFrom($smtpfrom);
							$sendgridemail -> setFromName($smtpfromname);
							$sendgridemail -> setSubject(stripslashes($subject));
							
							if (empty($newsletters_plaintext)) {
								$html = $this -> inlinestyles(apply_filters($this -> pre . '_send_body', stripslashes($message), $phpmailer, $history_id, 'sendgrid', $sendgridemail));
								$sendgridemail -> setHtml($html);
								$sendgridemail -> setText($altbody);
							} else {
								$sendgridemail -> setText($altbody);
							}
							
							if (!empty($attachments) && $attachments != false) {						
								if (is_array($attachments)) {							
									foreach ($attachments as $attachment) {								
										$sendgridemail -> addAttachment($attachment['filename'], $attachment['title']);
									}
								} else {
									$sendgridemail -> addAttachment($attachment['filename'], $attachment['title']);
								}
							}
							
							do_action('newsletters_mailapi_sendgrid_beforesend', $sendgridemail);
							
							$replytodifferent = $this -> get_option('replytodifferent');
							if (!empty($replytodifferent)) {
								$smtpreply = $this -> get_option('smtpreply');
								$sendgridemail -> setReplyTo($smtpreply);
							}
							
							$sendgridemail -> addHeader('Precedence', "bulk");
							$sendgridemail -> addHeader('List-Unsubscribe', $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true));
							
							try {
								$result = $sendgrid -> send($sendgridemail);
								$sent = true;
							} catch(\SendGrid\Exception $e) {
							    global $mailerrors;
							    $errors = $e -> getErrors();
							    $mailerrors = $errors[0];
							    return false;
							}
						
							break;
						case 'mandrill'						:
						
							$headers['Precedence'] = "bulk";
							$headers['List-Unsubscribe'] = $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true);
							
							$mailpriority = $this -> get_option('mailpriority');
						
							try {								
								require_once($this -> plugin_base() . DS . 'vendors' . DS . 'mailapis' . DS . 'mandrill' . DS . 'Mandrill.php');
								$mailapi_mandrill_key = $this -> get_option('mailapi_mandrill_key');
							    $mandrill = new Mandrill($mailapi_mandrill_key);
							    
							    global $mandrillmessage;
							    
							    $mandrillmessage = array(
							        'html' => ((empty($newsletters_plaintext)) ? $this -> inlinestyles(apply_filters($this -> pre . '_send_body', stripslashes($message), $phpmailer, $history_id, 'mandrill', $mandrill)) : null),
							        'text' => $altbody,
							        'subject' => stripslashes($subject),
							        'from_email' => $smtpfrom,
							        'from_name' => $smtpfromname,
							        'to' => array(
							            array(
							                'email' => $to,
							                //'name' => 'Recipient Name',
							                'type' => 'to'
							            )
							        ),
							        'headers' => $headers,
							        'important' => ((!empty($mailpriority) && $mailpriority == 1) ? true : false),
							        'track_opens' => null,
							        'track_clicks' => null,
							        'auto_text' => null,
							        'auto_html' => null,
							        'inline_css' => null,
							        'url_strip_qs' => null,
							        'preserve_recipients' => null,
							        'view_content_link' => null,
							        //'bcc_address' => 'message.bcc_address@example.com',
							        'tracking_domain' => null,
							        'signing_domain' => null,
							        'return_path_domain' => null,
							        //'merge' => true,
							        //'merge_language' => 'mailchimp',
							        /*'global_merge_vars' => array(
							            array(
							                'name' => 'merge1',
							                'content' => 'merge1 content'
							            )
							        ),
							        'merge_vars' => array(
							            array(
							                'rcpt' => 'recipient.email@example.com',
							                'vars' => array(
							                    array(
							                        'name' => 'merge2',
							                        'content' => 'merge2 content'
							                    )
							                )
							            )
							        ),
							        'tags' => array('password-resets'),*/
							        //'subaccount' => ,
							        /*'google_analytics_domains' => array('example.com'),
							        'google_analytics_campaign' => 'message.from_email@example.com',
							        'metadata' => array('website' => 'www.example.com'),
							        'recipient_metadata' => array(
							            array(
							                'rcpt' => 'recipient.email@example.com',
							                'values' => array('user_id' => 123456)
							            )
							        ),
							        'attachments' => array(
							            array(
							                'type' => 'text/plain',
							                'name' => 'myfile.txt',
							                'content' => 'ZXhhbXBsZSBmaWxl'
							            )
							        ),
							        'images' => array(
							            array(
							                'type' => 'image/png',
							                'name' => 'IMAGECID',
							                'content' => 'ZXhhbXBsZSBmaWxl'
							            )
							        )*/
							    );
							    
							    $mandrillmessage = apply_filters('newsletters_mailapi_mandrill_messagearray', $mandrillmessage);
							    
							    $subaccount = $this -> get_option('mailapi_mandrill_subaccount');
							    if (!empty($subaccount)) {
								    $mandrillmessage['subaccount'] = $subaccount;
							    }
							    
							    if (!empty($attachments) && $attachments != false) {
								    $mandrillmessage['attachments'] = array();
								    						
									if (is_array($attachments)) {							
										foreach ($attachments as $attachment) {																			
											$filetype = wp_check_filetype($attachment['filename']);
											
											$mandrillmessage['attachments'][] = array(
												'type'				=>	$filetype['type'],
												'name'				=>	$attachment['title'],
												'content'			=>	base64_encode(file_get_contents($attachment['filename'])),
											);
										}
									} else {										
										$filetype = wp_check_filetype($attachment['filename']);
											
										$mandrillmessage['attachments'][] = array(
											'type'				=>	$filetype['type'],
											'name'				=>	$attachment['title'],
											'content'			=>	base64_encode(file_get_contents($attachment['filename'])),
										);
									}
								}
								
								/*$mandrillmessage['headers'] = array(
									'Content-Type'				=>	"text/plain",
								);*/
							    
							    $async = true;
							    //$ip_pool = 'Main Pool';
							    $ip_pool = $this -> get_option('mailapi_mandrill_ippool');
							    //$send_at = 'example send_at';
							    $send_at = null;
							    $result = $mandrill -> messages -> send($mandrillmessage, $async, $ip_pool, $send_at);
							    
							    
							    if (!empty($result[0]['status']) && ($result[0]['status'] == "sent" || $result[0]['status'] == "queued")) {
							    	$sent = true;
							    }
							    
							    /*
							    Array
							    (
							        [0] => Array
							            (
							                [email] => recipient.email@example.com
							                [status] => sent
							                [reject_reason] => hard-bounce
							                [_id] => abc123abc123abc123abc123abc123
							            )
							    
							    )
							    */
							} catch(Mandrill_Error $e) {
							    // Mandrill errors are thrown as exceptions
							    //echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
							    // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
							    //throw $e;
							    
							    global $mailerrors;
							    $mailerrors = $e -> getMessage();
							    return false;
							}
							break;
					}
				} else {
					if (!empty($subscriber)) {
						$to = $subscriber -> email;					
					} elseif (!empty($user)) {
						$to = $user -> user_email;	
					}
					
					$content_type = (!empty($newsletters_plaintext)) ? 'text/plain' : 'text/html';
					
					$subject = stripslashes($subject);
					$message = stripslashes($message);					
					$headers = '';
					$headers .= 'Content-Type: ' . $content_type . '; charset="UTF-8"' . "\r\n";
					$headers .= 'From: ' . $smtpfromname . ' <' . $smtpfrom . '>' . "\r\n";	
					
					$atts = array();
					if (!empty($attachments) && is_array($attachments)) {						
						foreach ($attachments as $attachment) {
							$atts[] = $attachment['filename'];
						}
					}
					
					global $wpml_message, $wpmlhistory_id;
					$wpml_message = $message;
					$wpmlhistory_id = $history_id;
					
					if ($result = wp_mail($to, $subject, $message, $headers, $atts)) {
						$sent = true;
					} else {
						global $mailerrors, $phpmailer;
						$mailerrors = $phpmailer -> ErrorInfo;
						
						return false;
					}
				}
				
				if (!empty($sent) && $sent == true) {	
					global $phpmailer;
					$this -> delete_all_cache('all');
					
					$history_lists = maybe_unserialize($history -> mailinglists);
					$includeonly = (empty($history_lists)) ? $subscriber -> mailinglists : $history_lists;
					$subscriber_lists = maybe_unserialize($Subscriber -> mailinglists($subscriber -> id, $includeonly, false, false));
					$e_mailinglists = (empty($subscriber_lists)) ? $history_lists : $subscriber_lists;
						
					$e_data = array(
						'eunique'				=>	$eunique,
						'subscriber_id'			=>	(!empty($subscriber) ? $subscriber -> id : 0),
						'user_id'				=>	(!empty($user) ? $user -> ID : 0),
						'mailinglist_id'		=>	(!empty($subscriber -> mailinglist_id) ? $subscriber -> mailinglist_id : ''),
						'mailinglists'			=>	maybe_serialize($e_mailinglists),
						'history_id'			=>	$history_id,
						'type'					=>	$emailtype,
						'read'					=>	"N",
						'status'				=>	"sent",
						'messageid'				=>	$phpmailer -> MessageID,
					);
					
					$Db -> model = $Email -> model;
					
					if (!$Db -> save($e_data, true)) {
						$this -> log_error('Email could not be saved: ' . implode("; ", $Email -> errors));
					}
				}
			}
			
			return $sent;
		}
		
		function log_error($error = null) {
			$debugging = get_option('tridebugging');
			$this -> debugging = (empty($debugging)) ? $this -> debugging : true;
			
			if (!empty($error) && $this -> debugging == true) {
				error_log(date_i18n('[Y-m-d H:i:s] ') . $error . PHP_EOL, 3, NEWSLETTERS_LOG_FILE);
				
				return true;
			}
			
			return false;
		}
		
		/**
		 * Prints a variable or an array encapsulated in PRE tags
		 * Creates an easy to read hierarchy/structure
		 *
		 * @param ARRAY/STRING
		 * @return BOOLEAN
		 */
		function debug($var = array(), $output = true, $specialchars = false) {
			if ($output == false) { ob_start(); }
			$debugging = get_option('tridebugging');
			$this -> debugging = (empty($debugging)) ? $this -> debugging : true;
	
			if ($this -> debugging == true) {
				if (!empty($specialchars) && $specialchars == true) {
					echo '<pre>' . print_r(htmlspecialchars($var), true) . '</pre>';
				} else {
					echo '<pre>' . print_r($var, true) . '</pre>';
				}
			}
			
			if ($output == false) {
				$debug = ob_get_clean();
				ob_end_clean();
				return $debug;
			}
			
			return true;
		}
		
		function debug_trace($var = null) {
			$this -> debug($var);
			$this -> debug(array_reverse(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)));
		}
		
		function add_option($name = null, $value = null) {
			global $wpml_add_option_count;
			$value = apply_filters('newsletters_add_option', $value, $name);
			if (add_option($this -> pre . $name, $value)) {
				$wpml_add_option_count++;
				return true;
			}
			
			return false;
		}
		
		function update_option($name = null, $value = null) {
			$value = apply_filters('newsletters_update_option', $value, $name);
			if (update_option($this -> pre . $name, $value)) {
				return true;
			}
			
			return false;
		}
		
		function get_managementpost($permalink = false, $autocreate = false, $language = null) {		
			global $wpdb, $wp_rewrite, $newsletters_managementpost_error;
			$user_id = get_current_user_id();
			require_once(ABSPATH . WPINC . DS . 'rewrite.php');
			if (!is_object($wp_rewrite)) { $wp_rewrite = new WP_Rewrite(); }
			
			if (empty($language)) {
				$managementpost = __(get_option($this -> pre . 'managementpost'));
			} else {
				$managementpost = $this -> language_use($language, $this -> get_option('managementpost'));
			}
			$query = "SELECT `ID` FROM `" . $wpdb -> posts . "` WHERE `ID` = '" . $managementpost . "' AND `post_status` = 'publish'";
			
			$query_hash = md5($query);
			if ($ob_post = $this -> get_cache($query_hash)) {
				$post = $ob_post;
			} else {
				$post = $wpdb -> get_row($query);
				$this -> set_cache($query_hash, $post);
			}
			
			if (empty($managementpost) || !$post) {
				if ($autocreate == true) {
					$postdata = array(
						'post_title'			=>	__('Manage Subscriptions', $this -> plugin_name),
						'post_content'			=>	__('[newsletters_management]', $this -> plugin_name),
						'post_type'				=>	"page",
						'post_status'			=>	"publish",
						'post_author'			=>	$user_id,
						'comment_status'		=>	"closed",
						'ping_status'			=>	"closed",
					);
					
					$post_id = wp_insert_post($postdata);
					update_option($this -> pre . 'managementpost', $post_id);
					
					if ($permalink == true) {
						return get_permalink($post_id);	
					} else {
						return $post_id;
					}
				} else {
					if (is_admin() && $this -> ci_serial_valid()) {
						if (!$newsletters_managementpost_error) {
							$error = sprintf(__('Newsletter plugin subscriber management post/page does not exist %s', $this -> plugin_name), '<a href="' . admin_url('admin.php') . '?page=' . $this -> sections -> settings . '&method=managementpost" class="button button-secondary button-small">' . __('please create it now', $this -> plugin_name) . '</a>');
							$this -> render_error($error);
							$newsletters_managementpost_error = true;
						}
					}	
				}
			} else {
				if ($permalink == true) {
					return get_permalink($post -> ID);	
				} else {
					return $post -> ID;	
				}
			}
			
			return false;
		}
		
		function get_imagespost() {
			global $wpdb;
			$user_id = get_current_user_id();
			
			$imagespost = get_option($this -> pre . 'imagespost');
			$query = "SELECT `ID` FROM `" . $wpdb -> posts . "` WHERE `ID` = '" . $imagespost . "'";
			
			$query_hash = md5($query);
			if ($ob_post = $this -> get_cache($query_hash)) {
				$post = $ob_post;
			} else {
				$post = $wpdb -> get_row($query);
				$this -> set_cache($query_hash, $post);
			}
			
			if (empty($imagespost) || !$post) {
				$postdata = array(
					'post_title'			=>	__('Newsletter Images (do not remove)', $this -> plugin_name),
					'post_content'			=>	__('This is a placeholder for the Newsletter plugin images. You may edit and reuse this post but do not remove it.', $this -> plugin_name),
					'post_type'				=>	"post",
					'post_status'			=>	"draft",
					'post_author'			=>	$user_id,
				);
				
				$post_id = wp_insert_post($postdata);
				update_option($this -> pre . 'imagespost', $post_id);
				return $post_id;
			} else {
				return $post -> ID;	
			}
			
			return false;
		}
		
		function get_option($name = null, $stripslashes = true) {
			switch ($name) {
				case 'imagespost'			:
					if ($imagespost = $this -> get_imagespost()) {
						$this -> update_option('imagespost', $imagespost);
					}
					break;	
			}
			
			if ($option = $this -> get_cache($name, 'option')) {			
				return $option;
			}
			
			if ($option = get_option($this -> pre . $name)) {
				if (maybe_unserialize($option) !== false) {
					$option = maybe_unserialize($option);
				}
				
				if ($stripslashes == true) {
					$option = stripslashes_deep($option);
				}
				
				${'newsletters_option_' . $name} = $option;
				$option = apply_filters('newsletters_get_option', $option, $name);
				$this -> set_cache($name, $option, 'option');
				return $option;
			}
			
			return false;
		}
		
		function delete_option($name = null) {
			if (!empty($name)) {
				if (delete_option($this -> pre . $name)) {
					return true;
				}
			}
			
			return false;
		}
		
		function ajax_latestposts_settings() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			$this -> render('metaboxes' . DS . 'settings-latestposts', false, true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_latestposts_delete() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			if (!empty($_GET['id'])) {
				if ($this -> Latestpostssubscription() -> delete($_GET['id'])) {
					echo 'success';
				}
			}
			
			exit();
			die();
		}
		
		function ajax_load_new_editor() {
			define('DOING_AJAX', true);
			
			wp_enqueue_script('jquery');
			//meta boxes
			wp_enqueue_script('common', false, false, false, true);
			wp_enqueue_script('wp-lists', false, false, false, true);
			wp_enqueue_script('postbox', false, false, false, true);			
			//editor
			wp_enqueue_script('editor', false, false, false, true);
			wp_enqueue_script('quicktags', false, false, false, true);
			wp_enqueue_script('wplink', false, false, false, true);
			wp_enqueue_script('wpdialogs-popup', false, false, false, true);
			wp_enqueue_style('wp-jquery-ui-dialog', false, false, false, true);
			wp_enqueue_script('word-count', false, false, false, true);
			wp_enqueue_script('media-upload', false, false, false, true);
			wp_admin_css();
			wp_enqueue_script('utils', false, false, false, true);
			
			?>
			
			<div class="postbox" id="contentareabox<?php echo $_REQUEST['contentarea']; ?>">
				<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php echo __('Content Area', $this -> plugin_name); ?> <?php echo $_REQUEST['contentarea']; ?></span></h3>
					<div class="inside">
						
					<?php 
					
					wp_editor("", 'contentarea' . $_REQUEST['contentarea'], array(
						'textarea_name'				=>	'contentarea[' . $_REQUEST['contentarea'] . ']',
					));
					
					?>
				
					<table id="post-status-info" cellpadding="0" cellspacing="0">
						<tbody>
							<tr>
								<td id="wp-word-count">
									<span id="word-count"><code>[newsletters_content id="<?php echo $_REQUEST['contentarea']; ?>"]</code></span>
								</td>
								<td class="autosave-info">
									<span id="autosave" style="display:none;"></span>
								</td>
							</tr>
						</tbody>
					</table>
					<p><a href="" onclick="if (confirm('<?php echo __('Are you sure you want to remove this content area?', $this -> plugin_name); ?>')) { deletecontentarea('<?php echo $_REQUEST['contentarea']; ?>', ''); } return false;" class="button button-secondary"><?php _e('Delete', $this -> plugin_name); ?></a></p>
				</div>
			</div>
	
			<?php
			
			exit();
			die();
		}
		
		function ajax_latestposts_changestatus() {
			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			$success = false;
			
			if (!empty($_POST['id']) && !empty($_POST['status'])) {
				if ($this -> Latestpostssubscription() -> save_field('status', $_POST['status'], array('id' => $_POST['id']))) {
					$success = __('Status has been changed', $this -> plugin_name);
				}
			}
			
			$this -> render('metaboxes' . DS . 'settings-latestposts', array('success' => $success), true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_latestposts_save() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$ajax = false;
			$success = false;
			
			if (!empty($_POST)) {
				$ajax = true;
				
				foreach ($_POST as $pkey => $pval) {
					if (!empty($pval) && is_array($pval)) {
						$_POST[$pkey] = maybe_serialize($pval);
					}
				}
								
				if ($this -> Latestpostssubscription() -> save($_POST)) {
					$success = true;
				} else {
					$success = false;
				}
				
				$latestpostssubscription = $this -> Latestpostssubscription() -> data;
			} else {
				if (!empty($_GET['id'])) {
					$latestpostssubscription = $this -> Latestpostssubscription() -> find(array('id' => $_GET['id']));
				}
			}
			
			$this -> render('latestposts-save', array('latestpostssubscription' => $latestpostssubscription, 'errors' => $this -> Latestpostssubscription() -> errors, 'success' => $success, 'ajax' => $ajax), true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_latestposts_preview() {	
			define('DOING_AJAX', true);
	    	define('SHORTINIT', true);
	    	
	    	if ($content = $this -> latestposts_hook($_GET['id'], true)) {
				echo $content;
			} else {
				echo __('Latest posts preview could not load', $this -> plugin_name);
			}
			
			exit(); die();
		}
		
		function ajax_lps_posts() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
		
			global $Db;
			
			$conditions = array();
			if (!empty($_GET['id'])) {
				$conditions['lps_id'] = $_GET['id'];
				$latestpostssubscription = $this -> Latestpostssubscription() -> find(array('id' => $_GET['id']));
			}
			
			$posts = $this -> Latestpost() -> find_all($conditions, null, array('created', "DESC"));
			
			$this -> render('posts', array('posts' => $posts, 'latestpostssubscription' => $latestpostssubscription), true, 'admin');
			
			exit(); die();
		}
		
		function ajax_delete_lps_post() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			global $Db;
		
			if (!empty($_POST['id'])) {
				$this -> Latestpost() -> delete($_POST['id']);
			}
			
			exit(); die();
		}
		
		function get_latestposts_used($latestpostssubscription = null) {
			global $wpdb, $Db;
			
			if (!empty($latestpostssubscription)) {
				$count = $this -> Latestpost() -> count(array('lps_id' => $latestpostssubscription -> id));
				return $count;
			}
			
			return 0;
		}
		
		function get_latestposts($latestpostssubscription = null) {
			global $wpdb, $post, $Db, $Html, $Mailinglist, $Queue, $Subscriber, $SubscribersList;
			$post_criteria = false;
		
			if (!empty($latestpostssubscription)) {
				$exclude = array();
				if (!empty($latestpostssubscription -> exclude)) {
					if (($exclude = @explode(",", $latestpostssubscription -> exclude)) !== false) {
						//exclude array exists…
						foreach ($exclude as $exkey => $exval) {
							$exclude[$exkey] = trim($exval);
						}
					}
				}
				
				$order = (!empty($latestpostssubscription -> order)) ? $latestpostssubscription -> order : "DESC";
				$orderby = (!empty($latestpostssubscription -> orderby)) ? $latestpostssubscription -> orderby : "date";
						
				$post_criteria = array(
					'numberposts'			=>	$latestpostssubscription -> number,
					'category'				=>	@implode(",", maybe_unserialize($latestpostssubscription -> categories)),
					'orderby'				=>	$orderby,
					'order'					=>	$order,
					'exclude'				=>	$exclude,
					'post_type'				=>	"post",
					'post_status'			=>	"publish",
				);
				
				if (!empty($latestpostssubscription -> takefrom) && $latestpostssubscription -> takefrom == "posttypes") {
					$post_criteria['category'] = 0;
					$post_criteria['post_type'] = maybe_unserialize($latestpostssubscription -> posttypes);
				}
				
				$latestpostsquery = "SELECT id, post_id FROM " . $wpdb -> prefix . $this -> Latestpost() -> table . " WHERE `lps_id` = '" . $latestpostssubscription -> id . "'";
				$latestposts = $wpdb -> get_results($latestpostsquery);
				
				if (!empty($latestposts)) {
					foreach ($latestposts as $latestpost) {
						if (!empty($post_criteria['exclude'])) {
							$post_criteria['exclude'][] = $latestpost -> post_id;
						} else {
							$post_criteria['exclude'][] = $latestpost -> post_id;
						}
					}
				}
				
				$olderthanquery = "SELECT ID FROM " . $wpdb -> posts . " WHERE post_date < '" . date_i18n("Y-m-d H:i:s", strtotime($latestpostssubscription -> olderthan)) . "'";
				$olderthan = $wpdb -> get_results($olderthanquery);
				
				if (!empty($olderthan)) {
					foreach ($olderthan as $olderthanpost) {
						$post_criteria['exclude'][] = $olderthanpost -> ID;
					}
				}
				
				$post_criteria['suppress_filters'] = 0;
			}
				
			return apply_filters('newsletters_latest_posts_criteria', $post_criteria);
		}
		
		function updating_plugin() {
			if (!is_admin()) return;
			
			$dbversion = $this -> get_option('dbversion');
			if (empty($dbversion)) {
				$this -> add_option('dbversion', $this -> dbversion);
			}
		
			if (!$this -> get_option('version')) {
				$this -> add_option('version', $this -> version);
				$this -> update_options();
				return;
			}
			
			$cur_dbversion = $this -> get_option('dbversion');
			if (version_compare($cur_dbversion, $this -> dbversion) < 0) {
				$this -> update_option('showmessage_dbupdate', true);
			}
	
			$cur_version = $this -> get_option('version');
			$version = $this -> version;
	
			if (version_compare($this -> version, $cur_version) === 1) {				
				if (version_compare("3.8.4", $cur_version) === 1) {											
					if (!empty($this -> classes)) {
						global $wpdb;
						$this -> update_options();
						$this -> initialize_classes();
						
						foreach ($this -> classes as $class_name) {
							global ${$class_name};
							
							$query = "ALTER TABLE `" . $wpdb -> prefix . "" . $this -> pre . "" . ${$class_name} -> controller . "` ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=UTF8 COLLATE=utf8_general_ci;";
							$wpdb -> query($query);
							
							if (!empty(${$class_name} -> tv_fields)) {									
								foreach (${$class_name} -> tv_fields as $table_field_name => $table_field_attributes) {
									if (!empty($table_field_name) && $table_field_name != "key") {
										if (!preg_match("/(INT|DATETIME)/si", $table_field_attributes[0])) {
											$query = "ALTER TABLE `" . $wpdb -> prefix . "" . $this -> pre . "" . ${$class_name} -> controller . "` CHANGE `" . $table_field_name . "` `" . $table_field_name . "` " . $table_field_attributes[0] . " CHARACTER SET utf8 COLLATE utf8_general_ci " . $table_field_attributes[1] . ";";		
											$wpdb -> query($query);
										}
									}
								}
							}
						}
						
						global $wpdb, $Theme;
						$wpdb -> flush();
						
						$db_queries = array(
							"ALTER TABLE `" . $wpdb -> prefix . "" . $this -> History() -> table . "` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
							"ALTER TABLE `" . $wpdb -> prefix . "" . $this -> Template() -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
							"ALTER TABLE `" . $wpdb -> prefix . "" . $Theme -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						);
						
						foreach ($db_queries as $db_query) {
							$wpdb -> query($db_query);
						}
					}
					
					$version = "3.8.4";
				} elseif (version_compare("3.8.4.1", $cur_version) === 1) {
					$this -> update_options();
					$version = "3.8.4.1";	
				} elseif (version_compare("3.8.5.1", $cur_version) === 1) {
					$this -> update_options();
					$version = "3.8.5.1";	
				} elseif (version_compare("3.8.6", $cur_version) === 1) {										
					$this -> update_options();				
					$this -> update_option('scheduling', 'Y');
					$this -> update_option('scheduleinterval', "2minutes");
					$this -> update_option('cronrunning', "N");
					$this -> update_option('schedulenotify', "N");
					$this -> update_option('emailsperinterval', 20);
					$this -> scheduling();
					
					/* Currencies */
					global $currencies;
					require_once $this -> plugin_base() . DS . 'includes' . DS . 'currencies.php';
					$this -> update_option('currencies', $currencies);
					
					/* Permissions */
					$permissions = maybe_unserialize($this -> get_option('permissions'));
					$permissions['autoresponders'] = 10;
					$this -> update_option('permissions', $permissions);
					
					$this -> update_option('sendnewsletteronsubscribe', "N");
					$version = "3.8.6";	
				} elseif (version_compare("3.8.7", $cur_version) === 1) {	
					$this -> update_options();
							
					$permissions = maybe_unserialize($this -> get_option('permissions'));
					$permissions['groups'] = "10";
					$this -> update_option('permissions', $permissions);
					$this -> get_managementpost();	
					
					global $wpdb, $Theme, $Queue;
					$wpdb -> flush();
					
					$db_queries = array(
						"ALTER TABLE `" . $wpdb -> prefix . "" . $this -> History() -> table . "` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . "" . $Queue -> table . "` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . "" . $this -> Template() -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . "" . $Theme -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . $this -> Autoresponderemail() -> table . "` ADD UNIQUE INDEX (subscriber_id, autoresponder_id)",
					);
					
					foreach ($db_queries as $db_query) {
						$wpdb -> query($db_query);
					}
								
					$version = '3.8.7';	
				} elseif (version_compare("3.8.7.2", $cur_version) === 1) {
					$this -> update_options();
					$version = '3.8.7.2';
				}
				
				if (version_compare($cur_version, "3.8.9.2") < 0) {				
					$this -> update_options();
					$this -> initialize_default_themes();
					
					global $wpdb, $Db, $Theme;
					$themesquery = "SELECT * FROM " . $wpdb -> prefix . $Theme -> table . "";
					if ($themes = $wpdb -> get_results($themesquery)) {
						foreach ($themes as $theme) {
							$newcontent = "";
							ob_start();
							echo do_shortcode(stripslashes($theme -> content));
							$newcontent = ob_get_clean();
							
							$themequery = "UPDATE `" . $wpdb -> prefix . $Theme -> table . "` SET `content` = '" . esc_sql($newcontent) . "' WHERE `id` = '" . $theme -> id . "'";
							$wpdb -> query($themequery);
						}
					}
					
					$version = "3.8.9.2";
				}
				
				if (version_compare($cur_version, "3.8.9.4") < 0) {
					global $wpdb, $Mailinglist;			
					$this -> update_options();
					
					$intervals = array(
						'daily'			=>	__('Daily', $this -> plugin_name),
						'weekly'		=>	__('Weekly', $this -> plugin_name),
						'monthly'		=>	__('Monthly', $this -> plugin_name),
						'2months'		=>	__('Every Two Months', $this -> plugin_name),
						'3months'		=>	__('Every Three Months', $this -> plugin_name),
						'biannually'	=>	__('Twice Yearly (Six Months)', $this -> plugin_name),
						'9months'		=>	__('Every Nine Months', $this -> plugin_name),
						'yearly'		=>	__('Yearly', $this -> plugin_name),
						'once'			=>	__('Once Off', $this -> plugin_name),
					);
					
					$this -> update_option('intervals', $intervals);
					
					$query = "ALTER TABLE `" . $wpdb -> prefix . $Mailinglist -> table . "` CHANGE `interval` `interval` ENUM('daily', 'weekly', 'monthly', '2months', '3months', 'biannually', '9months', 'yearly', 'once') NOT NULL DEFAULT 'monthly';";
					$wpdb -> query($query);
					
					$version = "3.8.9.4";
				}
				
				if (version_compare($cur_version, "3.9") < 0) {						
					$this -> update_options();				
					$this -> initialize_default_themes();
					
					global $wpdb, $Db, $Theme;
					$themesquery = "SELECT * FROM " . $wpdb -> prefix . $Theme -> table . "";
					if ($themes = $wpdb -> get_results($themesquery)) {
						foreach ($themes as $theme) {
							$newcontent = "";
							ob_start();
							$newcontent = ob_get_clean();
							$themequery = "UPDATE `" . $wpdb -> prefix . $Theme -> table . "` SET `content` = '" . esc_sql($newcontent) . "' WHERE `id` = '" . $theme -> id . "'";						
							//$wpdb -> query($themequery);
						}
					}
					
					$version = "3.9";
				}
				
				if (version_compare($cur_version, "3.9.4") < 0) {
					global $wpdb;
					$this -> update_options();
					
					/* convert database tables to MyISAM */
					if (!empty($this -> tablenames)) {
						foreach ($this -> tablenames as $tablename) {					
							$query = "ALTER TABLE `" . $tablename . "` ENGINE=MyISAM;";
							$wpdb -> query($query);
						}
					}
					
					/* Auto import WordPress users */
					if ($importuserslist = $this -> get_option('importuserslist')) {
						$this -> update_option('importuserslists', array($importuserslist));
					}
					
					$version = "3.9.4";
				}
				
				if (version_compare($cur_version, "3.9.9") < 0) {				
					global $wpdb, $Queue, $Field;
					$this -> update_options();
					$wpdb -> query("ALTER TABLE `" . $wpdb -> prefix . $Field -> table . "` CHANGE `type` `type` VARCHAR(255) NOT NULL DEFAULT 'text'");
					$version = "3.9.9";
				}
				
				if (version_compare($cur_version, "4.4.4") < 0) {
					$this -> update_options();

					$latestposts = $this -> get_option('latestposts');
					if (!empty($latestposts) && $latestposts == "Y") {					
						$latestpostssubscription = array(
							'subject'				=>	$this -> get_option('latestposts_subject'),
							'number'				=>	$this -> get_option('latestposts_number'),
							'language'				=>	$this -> get_option('latestposts_language'),
							'takefrom'				=>	$this -> get_option('latestposts_takefrom'),
							'posttypes'				=>	maybe_serialize($this -> get_option('latestposts_posttypes')),
							'categories'			=>	maybe_serialize($this -> get_option('latestposts_categories')),
							'groupbycategory'		=>	$this -> get_option('latestposts_groupbycategory'),
							'exclude'				=>	$this -> get_option('latestposts_exclude'),
							'order'					=>	$this -> get_option('latestposts_order'),
							'orderby'				=>	$this -> get_option('latestposts_orderby'),
							'olderthan'				=>	$this -> get_option('latestposts_olderthan'),
							'lists'					=>	maybe_serialize($this -> get_option('latestposts_lists')),
							'startdate'				=>	$this -> get_option('latestposts_startdate'),
							'interval'				=>	$this -> get_option('latestposts_interval'),
							'theme_id'				=>	$this -> get_option('latestposts_theme'),
						);
						
						$this -> Latestpostssubscription() -> save($latestpostssubscription);
						$this -> latestposts_scheduling($latestpostssubscription['interval'], $latestpostssubscription['startdate'], array((int) $this -> Latestpostssubscription() -> insertid));
					}
					
					// Set the 'rel_id' field on fieldslists table as AUTO_INCREMENT
					global $wpdb, $Db, $FieldsList;
					$query = "ALTER TABLE " . $wpdb -> prefix . $this -> Latestpost() -> table . " CHANGE `post_id` `post_id` INT(11) NOT NULL DEFAULT '0'";
					$wpdb -> query($query);
					$query = "ALTER TABLE " . $wpdb -> prefix . $FieldsList -> table . " CHANGE `rel_id` `rel_id` INT(11) NOT NULL AUTO_INCREMENT";
					$wpdb -> query($query);
					
					$version = '4.4.4';
				}
				
				if (version_compare($cur_version, "4.4.6.1") < 0) {
					global $wpdb, $Db;
					
					$this -> update_options();
					
					$query = "ALTER TABLE `" . $wpdb -> prefix . $this -> Latestpost() -> table . "` DROP INDEX `post_id`";
					$wpdb -> query($query);
					
					$version = '4.4.6.1';
				}
				
				if (version_compare($cur_version, "4.5.4.2") < 0) {
					global $wpdb;
					$this -> update_options();
					
					//update the theme folder to default
					$this -> update_option('theme_folder', "default");
					$this -> theme_folder_functions();
					include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');
					$stylesdone = false;
					$scriptsdone = false;
					
					if (!empty($defaultstyles)) {
						$loadstyles = array();
						foreach ($defaultstyles as $handle => $style) {
							$loadstyles[] = $handle;
						}
						$this -> update_option('loadstyles', $loadstyles);
						$stylesdone = true;
					}
					if (!empty($defaultscripts)) {
						$loadscripts = array();
						foreach ($defaultscripts as $handle => $script) {
							$loadscripts[] = $handle;
						}
						$this -> update_option('loadscripts', $loadscripts);
						$scriptsdone = true;
					}
					
					if (!empty($stylesdone) && !empty($scriptsdone)) {
						// all done, update the version
						$version = '4.5.4.2';	
					}
				}
				
				if (version_compare($cur_version, "4.5.5.4.4") < 0) { 
					global $wpdb, $Subscriber, $SubscribersList;
					$this -> update_options();
					
					// Change of 'bootstrap' to 'newsletters-bootstrap' handle
					$this -> theme_folder_functions();
					include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');
					$loadstyles = $this -> get_option('loadstyles');
					if (!empty($loadstyles) && in_array('bootstrap', $loadstyles)) {
						$loadstyles[] = 'newsletters-bootstrap';
						$this -> update_option('loadstyles', $loadstyles);
					}
					
					/* Unique 'email' column stuff */
					$subscribers_table = $wpdb -> prefix . $Subscriber -> table;
					$subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;
					// Delete duplicate subscribers and only keep the one with the smallest ID value
					$query = "DELETE n1 FROM `" . $subscribers_table . "` n1, `" . $subscribers_table . "` n2 WHERE n1.id > n2.id AND n1.email = n2.email";
					$wpdb -> query($query);
					// Remove empty associations
					$query = "DELETE FROM `" . $subscriberslists_table . "` WHERE `subscriber_id` NOT IN (SELECT `id` FROM `" . $subscribers_table . "`)";
					$wpdb -> query($query);
					// Make the 'email' column a unique index/key to prevent duplicates
					$query = "ALTER TABLE  `" . $subscribers_table . "` ADD UNIQUE KEY `email_unique` (`email`)";
					$wpdb -> query($query);
					
					$version = '4.5.5.4.4';
				}
				
				if (version_compare($cur_version, "4.5.5.9") < 0) {
					$this -> update_options();
					
					$loadscripts = $this -> get_option('loadscripts');
					$loadscripts[] = 'jquery-form';
					$this -> update_option('loadscripts', $loadscripts);
					
					$version = "4.5.5.9";	
				}
				
				if (version_compare($cur_version, "4.6.3") < 0) { 
					$this -> update_options();					
					
					$paymentmethod = $this -> get_option('paymentmethod');
					$this -> update_option('paymentmethod', array($paymentmethod));
					
					// Update the "Posts" and "Latest Posts" templates
					include($this -> plugin_base() . DS . 'includes' . DS . 'email-templates.php');
					
					$current_posts = $this -> get_option('etmessage_posts');
					if (!preg_match("/(newsletters_post_anchor)/si", $current_posts)) {
						$this -> update_option('etmessage_posts_backup', $current_posts);
						$this -> update_option('etmessage_posts', $email_templates['posts']['message']);
					}
					
					$current_latestposts = $this -> get_option('etmessage_latestposts');
					if (!preg_match("/(newsletters_post_anchor)/si", $current_latestposts)) {						
						$this -> update_option('etmessage_latestposts_backup', $current_latestposts);
						$this -> update_option('etmessage_latestposts', $email_templates['latestposts']['message']);
					}
					
					$version = "4.6.3";	
				}
			
				//the current version is older.
				//lets update the database
				$this -> update_option('version', $version);
			}	
		}
		
		function update_options() {			
			if (!is_admin()) return;
			$this -> check_tables();
			$this -> get_managementpost(false, true);
			
			global $wpml_add_option_count, $wpdb, $Field, $Theme;
			$wpml_add_option_count = 0;
					
			$options = array();	
			$options['defaulttemplate'] = true;
			$options['old_folder_name'] = "newsletters-lite";
			$options['screenoptions_subscribers_custom'] = array('gravatars', 'device');		
			$options['managementloginsubject'] = __('Authenticate Subscriber Account', $this -> plugin_name);
			$options['authenticatelinktext'] = __('Authenticate now', $this -> plugin_name);
			$options['managementauthtype'] = 3;
			$options['managementallowemailchange'] = "Y";
			$options['managementformatchange'] = "Y";
			$options['managementallownewsubscribes'] = "Y";
			$options['managementshowsubscriptions'] = "Y";
			$options['managementcustomfields'] = "Y"; 
			$options['cookieformat'] = "D, j M Y H:i:s";
			$options['defaultlistcreated'] = "N";
			$options['subscriptionmessage'] = __('Subscription Successful', $this -> plugin_name);
			$options['sendingprogress'] = "N";
			$options['createpreview'] = "Y";
			$options['createspamscore'] = "Y";
			$options['emailencoding'] = "8bit";
			$options['clicktrack'] = "Y";
			$options['shortlinks'] = "N";
			$options['theme_folder'] = "default2";
			$options['theme_usestyle'] = "Y";
			$options['customcss'] = "N";
			$options['multimime'] = "N"; //should multi mime (text/html) emails be sent?
			$options['videoembed'] = true;
			$options['mailtype'] = 'mail';
			$options['smtphost'] = 'mail.domain.com';
			$options['smtpport'] = 25;
			$options['smtpsecure'] = "N";
			$options['smtpauth'] = 'N';
			$options['smtpuser'] = __('username', $this -> plugin_name);
			$options['smtppass'] = __('password', $this -> plugin_name);
			$options['adminemail'] = get_option('admin_email');
			$options['smtpfrom'] = get_option('admin_email');
			$options['smtpreply'] = get_option('admin_email');
			$options['smtpfromname'] = get_option('blogname');
			$options['dkim'] = "N";
			$options['dkim_domain'] = "domain.com";
			$options['dkim_selector'] = "newsletters";
			$options['tracking'] = "Y";
			$options['tracking_image'] = "invisible";
			$options['servertype'] = 'cpanel';
	        $options['mailpriority'] = 3; //set the mail priority to "Normal"
			$options['unsubscribeondelete'] = 'N';
			$options['unsubscribeemails'] = "single";
			$options['unsubscribeconfirmation'] = "Y";
			$options['unsubscribecomments'] = "Y";
			$options['registercheckbox'] = 'Y';
			$options['registerformlabel'] = __('Receive news updates via email from this site', $this -> plugin_name);		
			$options['checkboxon'] = 'N';
			$options['autosubscribelist'] = array(1);
			$options['sendonpublish'] = 'Y';
			$options['sendonpublishef'] = 'ep';
			$options['sendonpublishexcerptlength'] = 250;
			$options['sendonpublishunsubscribe'] = 'Y';
			$options['resubscribe'] = 1;
			$options['resubscribetext'] = __('resubscribe', $this -> plugin_name);
			$options['unsubscribetext'] = __('Unsubscribe from this newsletter', $this -> plugin_name);
			$options['unsubscribealltext'] = __('Unsubscribe from all emails', $this -> plugin_name);
			$options['unsubscribedelete'] = "N";
			$options['adminemailonsubscription'] = 'Y';
			$options['adminemailonunsubscription'] = 'Y';
			$options['activationlinktext'] = __('Confirm Subscription', $this -> plugin_name);
			$options['customactivateredirect'] = "N";
			$options['activateredirecturl'] = home_url();
			$options['managelinktext'] = __('Manage Subscriptions', $this -> plugin_name);
			$options['onlinelinktext'] = __('View in your browser', $this -> plugin_name);
			$options['printlinktext'] = __('Print Email', $this -> plugin_name);
			$options['scheduling'] = 'Y';
			$options['schedulecrontype'] = "wp";
			$options['scheduleinterval'] = "2minutes";
			$options['cronrunning'] = "N";
			$options['schedulenotify'] = "N";
			$options['queuesendorder'] = "ASC";
			$options['queuesendorderby'] = "created";
			$options['emailsperinterval'] = 20;
			$options['autoresponderscheduling'] = "hourly";
			$options['tinymcebtn'] = "Y";
			$options['sendasnewsletterbox'] = "Y";
			$options['subscriberegister'] = "N";
			$options['custompostslug'] = "newsletter";
			$options['importusers'] = "N";
			$options['importusersscheduling'] = "hourly";
			$options['importuserslists'] = array(1);
			$options['importusersrequireactivate'] = "N";
			$options['subscriptions'] = "Y";
			$options['paidsubscriptionredirect'] = "Y";
			$options['rssfeed'] = "N";
			$options['deleteonbounce'] = 'Y';
	        $options['bouncecount'] = 3;
			$options['adminemailonbounce'] = 'Y';
			$options['bounceemail'] = get_option('admin_email');
	        $options['bouncemethod'] = "cgi";
	        $options['bouncepop_interval'] = "3600";
	        $options['bouncepop_host'] = "localhost";
	        $options['bouncepop_user'] = "bounce@domain.com";
	        $options['bouncepop_pass'] = "mailboxpassword";
	        $options['bouncepop_port'] = "110";
			$options['subscriberexistsredirect'] = "management";
			$options['subscriberexistsmessage'] = __('You are already subscribed, redirecting to the management page...', $this -> plugin_name);
			$options['subscriberexistsredirecturl'] = get_permalink($this -> get_managementpost());
			$options['requireactivate'] = 'Y';	
			$options['activateaction'] = "none";
			$options['activatereminder'] = 3;
			$options['activatedelete'] = 7;
			$options['activationemails'] = "single";
			$options['tcodemo'] = 'N';
			$options['tcovendorid'] = '123456';
			$options['tcosecret'] = __('secretstring', $this -> plugin_name);
			$options['tcoaccount'] = "live";
			$options['adminordernotify'] = 'Y';
			$options['subscriberedirect'] = "N";
			$options['subscriberedirecturl'] = $this -> get_managementpost(true);
			$options['paymentmethod'] = array('2co', 'paypal');
			$options['csvdelimiter'] = ",";
			$options['captcha_type'] = ($this -> is_plugin_active('captcha')) ? 'rsc' : 'none';
			$options['recaptcha_theme'] = "light";
			$options['recaptcha_language'] = "en";
			$options['recaptcha_customcss'] = '.recaptcha_widget { margin: 10px 0 15px 0; }
			.recaptcha_widget .recaptcha_image { margin: 10px 0 5px 0; }
			.recaptcha_widget .recaptcha_image img { width: 250px; box-shadow: none; }
			.recaptcha_widget .recaptcha_links { font-size: 85%; }
			.recaptcha_widget .recaptcha_response { }';
			$options['captcha_rgb'] = array(255, 255, 255);
			$options['captcha_bg'] = "#FFFFFF";
			$options['captcha_fg'] = array(0, 0, 0);
			$options['farbtastic_fg'] = "#000000";
			$options['captcha_size'] = array('w' => 72, 'h' => 24);
			$options['captcha_chars'] = "4";
			$options['captcha_font'] = "14";
			$options['captchainterval'] = "hourly";
			$this -> captchacleanup_scheduling();
			$options['commentformcheckbox'] = "Y";
			$options['commentformlabel'] = __('Receive news updates via email from this site', $this -> plugin_name);
			$options['commentformautocheck'] = "N";
			$options['commentformlist'] = "1";
			$options['excerpt_settings'] = 1;
			$options['excerpt_length'] = 55;
			$options['excerpt_more'] = __('Read more', $this -> plugin_name);
			$options['latestposts'] = "N";
			$options['latestposts_subject'] = __('Latest Posts from', $this -> plugin_name) . ' ' . get_bloginfo('name');
			$options['latestposts_number'] = 10;
			$options['latestposts_language'] = "en";
			$options['latestposts_categories'] = false;
			$options['latestposts_groupbycategory'] = "Y";
			$options['latestposts_exclude'] = "";
			$options['latestposts_order'] = "DESC";
			$options['latestposts_orderby'] = "post_date";
			global $wpdb;
			$olderthanquery = "SELECT post_date FROM " . $wpdb -> posts . " ORDER BY post_date ASC LIMIT 1";
			$olderthan = $wpdb -> get_var($olderthanquery);
			$options['latestposts_olderthan'] = date_i18n("Y-m-d H:i:s", strtotime($olderthan));
			$options['latestposts_startdate'] = date_i18n("Y-m-d H:i:s", time());
			$options['latestposts_lists'] = false;
			$options['latestposts_interval'] = "weekly";
			$options['latestposts_time'] = "00:00:00";
			$options['latestposts_theme'] = false;
			$options['paypalemail'] = get_option('admin_email');
			$options['paypalsubscriptions'] = "N";
			$options['paypalsandbox'] = "N";
			$options['paypalliveurl'] = "https://www.paypal.com/cgi-bin/webscr";
			$options['paypalsandurl'] = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			$options['countriesinserted'] = "N";
			$options['generalredirect'] = $this -> get_managementpost(true);
			$options['offsitetitle'] = get_bloginfo('name');
			$options['offsitelist'] = 'checkboxes';
			$options['offsitewidth'] = 400;
			$options['offsiteheight'] = 300;
			$options['offsitebutton'] = __('Subscribe Now', $this -> plugin_name);
			$options['currency'] = 'USD';
			include $this -> plugin_base() . DS . 'includes' . DS . 'currencies.php';
			$options['currencies'] = $currencies;
			
			$intervals = array(
				'daily'			=>	__('Daily', $this -> plugin_name),
				'weekly'		=>	__('Weekly', $this -> plugin_name),
				'monthly'		=>	__('Monthly', $this -> plugin_name),
				'2months'		=>	__('Every Two Months', $this -> plugin_name),
				'3months'		=>	__('Every Three Months', $this -> plugin_name),
				'biannually'	=>	__('Twice Yearly (Six Months)', $this -> plugin_name),
				'9months'		=>	__('Every Nine Months', $this -> plugin_name),
				'yearly'		=>	__('Yearly', $this -> plugin_name),
				'once'			=>	__('Once Off', $this -> plugin_name),
			);
			
			$options['intervals'] = maybe_serialize($intervals);
			
			$embed = array(
				'acknowledgement'		=>	__('Thank you for subscribing.', $this -> plugin_name),			//default acknowledgement message
				'subtitle'				=>	__('Subscribe to our newsletter.', $this -> plugin_name),		//subtitle of the subscription form
				'subscribeagain'		=>	"N",															//show a "Subscribe again" link?
				'ajax'					=>	"N",															//turn on Ajax features?
				'button'				=>	__('Subscribe Now', $this -> plugin_name),						//button text
				'scroll'				=>	"Y",															//scroll to the subscription form?
				'captcha'				=>	"N",															//security captcha image?
			);
			
			if ($this -> language_do()) {				
				foreach ($embed as $ekey => $eval) {
					$embed[$ekey][$this -> language_default()] = $eval;	
				}
			}
			
			$options['embed'] = $embed;
			
			$poststatuses = array(
				'publish'			=>	__('Published', $this -> plugin_name),
				'pending'			=>	__('Pending', $this -> plugin_name),
				'draft'				=>	__('Draft', $this -> plugin_name),
				'private'			=>	__('Private', $this -> plugin_name)
			);
			
			$options['poststatuses'] = $poststatuses;
			
			// API Stuff
			$options['api_key'] = strtoupper(md5($_SERVER['SERVER_NAME']));
			
			foreach ($options as $okey => $oval) {
				$this -> add_option($okey, $oval);
			}
			
			$this -> theme_folder_functions();
			// Styles & Scripts
			include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');			
			if (!empty($defaultstyles)) {
				$loadstyles = array();
				foreach ($defaultstyles as $handle => $style) {
					$loadstyles[] = $handle;
				}
				$this -> add_option('loadstyles', $loadstyles);
			}
			if (!empty($defaultscripts)) {
				$loadscripts = array();
				foreach ($defaultscripts as $handle => $script) {
					$loadscripts[] = $handle;
				}
				$this -> add_option('loadscripts', $loadscripts);
			}
			
			// Scheduled tasks
			$ratereview_scheduled = $this -> get_option('ratereview_scheduled');
			if (empty($ratereview_scheduled)) {
				wp_schedule_single_event(strtotime("+7 day"), 'newsletters_ratereviewhook', array(7));
				wp_schedule_single_event(strtotime("+14 day"), 'newsletters_ratereviewhook', array(14));
				wp_schedule_single_event(strtotime("+30 day"), 'newsletters_ratereviewhook', array(30));
				wp_schedule_single_event(strtotime("+30 day"), 'newsletters_ratereviewhook', array(60));
				$this -> update_option('ratereview_scheduled', true);
			}
			
			$this -> get_imagespost();		
			$this -> get_managementpost();
			
			$themesquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` LIMIT 1";
			$themes = $wpdb -> get_results($themesquery);
			if (empty($themes)) { $this -> initialize_default_themes(); }
			
			$permissions = $this -> get_option('permissions');
			if (empty($permissions)) { $this -> init_roles(); }
			$this -> check_roles();
			
			$this -> scheduling();
			$this -> optimize_scheduling();
			$this -> emailarchive_scheduling();
			$this -> autoresponder_scheduling();
			$this -> init_fieldtypes();
			$this -> predefined_templates();
			
			$Field -> check_default_fields();			
			return $wpml_add_option_count;
		}
		
		function predefined_templates() {
			require_once $this -> plugin_base() . DS . 'includes' . DS . 'email-templates.php';
			
			if (!empty($email_templates)) {
				foreach ($email_templates as $etk => $email_template) {			
					$this -> add_option('etsubject_' . $etk, $email_template['subject']);
					$this -> add_option('etmessage_' . $etk, $email_template['message']);
				}
			}
		}
		
		function init_fieldtypes() {
			global $wpdb;
		
			$fieldtypes = array(
				'hidden'		=> 	__('Hidden', $this -> plugin_name),
				'special'		=>	__('Special', $this -> plugin_name),
				'text'			=>	__('Text Field', $this -> plugin_name),
				'textarea'		=>	__('Text Area', $this -> plugin_name),
				'select'		=>	__('Select Drop Down', $this -> plugin_name),
				'radio'			=>	__('Radio Buttons', $this -> plugin_name),
				'checkbox'		=>	__('Checkboxes', $this -> plugin_name),
				'file'			=>	__('File Upload', $this -> plugin_name),
				'pre_country'	=>	__('Predefined : Country Select', $this -> plugin_name),
				'pre_date'		=>	__('Predefined : Date Picker (YYYY-MM-DD)', $this -> plugin_name),
				'pre_gender'	=>	__('Predefined : Gender', $this -> plugin_name),
			);
			
			$this -> update_option('fieldtypes', $fieldtypes);
			
			$fieldtypesquery = "ALTER TABLE `" . $wpdb -> prefix . "wpmlfields` CHANGE `type` `type` VARCHAR(255) NOT NULL DEFAULT 'text';";
			$wpdb -> query($fieldtypesquery);
			return true;
		}
		
		function check_roles() {
			global $wp_roles;
			$permissions = $this -> get_option('permissions');
			
			if (empty($permissions) || !is_array($permissions)) {
				$permissions = array();
			}
			
			if ($role = get_role('administrator')) {		
				if (!empty($this -> sections)) {			
					foreach ($this -> sections as $section_key => $section_menu) {																		
						if (empty($role -> capabilities['newsletters_' . $section_key])) {
							$role -> add_cap('newsletters_' . $section_key);
							
							if (empty($permissions[$section_key]) || !is_array($permissions[$section_key])) {
								$permissions[$section_key] = array();
							}
							
							$permissions[$section_key][] = 'administrator';
						}
					}
				}
				
				if (!empty($this -> blocks)) {
					foreach ($this -> blocks as $block) {
						$role -> add_cap($block);
						$permissions[$block][] = 'administrator';
					}
				}
				
				$this -> update_option('permissions', $permissions);
			}
			
			return false;		
		}
		
		function init_roles($sections = null) {
			global $wp_roles;
			$sections = $this -> sections;
		
			/* Get the administrator role. */
			$role = get_role('administrator');
	
			/* If the administrator role exists, add required capabilities for the plugin. */
			if (!empty($role)) {
				if (!empty($sections)) {			
					foreach ($sections as $section_key => $section_menu) {
						$role -> add_cap('newsletters_' . $section_key);
					}
				}
				
				if (!empty($this -> blocks)) {
					foreach ($this -> blocks as $block) {
						$role -> add_cap($block);
					}
				}
			} elseif (empty($role) && !is_multisite()) {
				$newrolecapabilities = array();
				$newrolecapabilities[] = 'read';
			
				if (!empty($sections)) {
					foreach ($sections as $section_key => $section_menu) {
						$newrolecapabilities[] = 'newsletters_' . $section_key;
					}
				}
				
				if (!empty($this -> blocks)) {
					foreach ($this -> blocks as $block) {
						$newrolecapabilities[] = $block;
					}
				}
	
				add_role(
					'newsletters',
					_e('Newsletters Manager', $this -> plugin_name),
					$newrolecapabilities
				);
			}
			
			if (!empty($sections)) {
				$permissions = array();
			
				foreach ($sections as $section_key => $section_menu) {
					$wp_roles -> add_cap('administrator', 'newsletters_' . $section_key);
					$permissions[$section_key][] = 'administrator';
				}
				
				foreach ($this -> blocks as $block) {
					$wp_roles -> add_cap('administrator', $block);
					$permissions[$block][] = 'administrator';
				}
				
				$this -> update_option('permissions', $permissions);
			}
		}
		
		function list_exists($list_id = null) {
			if (!empty($list_id)) {		
				if ($Mailinglist -> list_exists($list_id)) {
					return true;
				}
			}
			
			return false;
		}
		
		function array_to_object($array = array()) {
			if (!empty($array)) {
				return (object) $array;
			}
			
			return false;
		}
		
		function truncatetext($text = null, $start = 0, $end = 0, $append = '...') {
			return substr($text, $start, $end) . $append;
		}
		
		function gen_date($format = "Y-m-d H:i:s", $time = null) {
			$newtime = (empty($time)) ? time() : $time;
			return date_i18n($format, $newtime);
		}
		
		function override_mce_options($initArray = null) {			
			if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {							
				$opts = '*[*]';
			    $initArray['valid_elements'] = $opts;
			    $initArray['extended_valid_elements'] = $opts;
			    //$initArray['entities'] = "169,copy,174,reg,8482,trade";
			    //$initArray['entity_encoding'] = "raw";
			    //$initArray['verify_html'] = 0;
			    //$initArray['cleanup'] = 0;
			    //$initArray['cleanup_on_startup'] = 0;
			    //$initArray['plugins'] .= ",fullpage";
			    //$initArray['validate_children'] = 0;
			    //$initArray['valid_children'] = $opts;
			}
			    
			return $initArray;
		}
		
		function the_content($content = null) {
			if (!is_admin()) {
				$showpostattachments = $this -> get_option('showpostattachments');
				if (!empty($showpostattachments)) {
					global $post, $Db;
					if ($history = $this -> History() -> find(array('post_id' => $post -> ID))) {
						if (!empty($history -> attachments)) {
							$post_attachments = $this -> render('post-attachments', array('attachments' => $history -> attachments), false, 'default');
							$content .= $post_attachments;
						}
					}
				}
			}
			
			return $content;
		}
		
		function stripext($filename = null, $return = 'ext') {
			if (!empty($filename)) { 
				//$extArray = split("[/\\.]", $filename); 
				
				if ($return == 'ext') {
					//$p = count($extArray) - 1; 
					//$extension = $extArray[$p]; 
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					return $extension;
				} else {
					///$p = count($extArray) - 2;
					//$filename = $extArray[$p];
					$file = pathinfo($filename, PATHINFO_FILENAME);
					return $file;
				}
			}
			
			return false;
		}
		
		function bounce($email = null, $type = 'cgi', $status = null) {
			global $wpdb, $Bounce, $Email, $Db, $Subscriber, $SubscribersList;
	
	        $deleted_subscribers = 0;
	        $deleted_emails = 0;
	        
	        $deleteonbounce = $this -> get_option('deleteonbounce');
	        $bouncecount = $this -> get_option('bouncecount');
	
	        switch ($type) {
	            case 'cgi'              :
	        		if (!empty($email)) {
	        			$email = urldecode($email);
	        			preg_match_all("/[<](.*)[>]/i", $email, $matches);
	
	        			if ($this -> get_option('servertype') == "plesk") {
	        				$email = trim($matches[1][0]);
	        			} else {
	        				$email = trim($matches[1][2]);
	        			}
	
	        			$Db -> model = $Subscriber -> model;
	        			if ($subscriber = $Db -> find(array('email' => $email))) {                           	                        
	                        $Db -> model = $Subscriber -> model;
	                        if (!empty($deleteonbounce) && $deleteonbounce == "Y" && (empty($bouncecount) || $bouncecount == 1 || $bouncecount < 1 || (($subscriber -> bouncecount + 1) >= $bouncecount))) {
		                        $Db -> delete($subscriber -> id);
		                        $deleted_subscribers++;
	                        } else {
		                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
	                        }
	                        
	                        $bouncedata = array('email' => $subscriber -> email);
	                        $Bounce -> save($bouncedata);
	                        
	                        do_action('newsletters_subscriber_bounce', $subscriber -> id, ($subscriber -> bouncecount + 1), false);
	                        
	                        //send a notification to the administrator
	                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
	                        $this -> admin_bounce_notification($subscriber);
	                        
	                        return true;
	                    }
	        		}
	
	        		return false;
	                break;
	            case 'pop'					:
	            	require_once($this -> plugin_base() . DS . 'vendors' . DS . 'bounce' . DS . 'bounce_driver.class.php');
	            	$bouncehandler = new BounceHandler();
	            
					require_once($this -> plugin_base() . DS . 'vendors' . DS . 'class.pop3_new.php');
					$pop_host = $this -> get_option('bouncepop_host');
					$pop_port = $this -> get_option('bouncepop_port');
					$pop_user = $this -> get_option('bouncepop_user');
					$pop_pass = $this -> get_option('bouncepop_pass');
					$pop3 = new POP3();
					
					if ($pop3 -> connect($pop_host, $pop_port)) {
						if ($pop3 -> user($pop_user)) {
							$count = $pop3 -> pass($pop_pass);
							
							if (!empty($count) && $count > 0) {
								for ($m = 1; $m <= $count; $m++) {
									$message = $pop3 -> get($m);
									$message_array = $pop3 -> get($m, "array");									
									$the_facts = $bouncehandler -> get_the_facts($message);
									
									foreach ($message_array as $mkey => $mval) {
										if (preg_match("/^Message\-ID\:(.*)/s", $mval, $matches)) {
											$messageid_array = explode(": ", $message_array[$mkey]);
											$messageid = trim($messageid_array[1]);
											
											if (!empty($messageid)) {
												$Db -> model = $Email -> model;
												$bouncedemail = $Db -> find(array('messageid' => $messageid));
												break;
											}
										}	
									}
									
									if (!empty($the_facts[0]['recipient']) && !empty($the_facts[0]['action'])) {
				                        if ($the_facts[0]['action'] == "failed") {
					                        $email = trim($the_facts[0]['recipient']);
					                        $status = trim($the_facts[0]['status']);
					                        
					                        include($this -> plugin_base() . DS . 'vendors' . DS . 'bounce' . DS . 'bounce_responses.php');
											$status_message = $bouncehandler -> fetch_status_messages(trim($the_facts[0]['status']));
											if (!empty($status_message)) {
												$status .= ' - ' . $status_message[0][0] . ', ' . $status_message[1][0];
											}
					                        
					                 		$Db -> model = $Subscriber -> model;	                                
			                                if ($subscriber = $Db -> find(array('email' => $email))) {	 
			                                	$subscriber_id = $subscriber -> id;                               
			                                    
			                                    $Db -> model = $Subscriber -> model;
						                        if (!empty($deleteonbounce) && $deleteonbounce == "Y" && (empty($bouncecount) || $bouncecount == 1 || $bouncecount < 1 || (($subscriber -> bouncecount + 1) >= $bouncecount))) {
							                        $Db -> delete($subscriber -> id);
							                        $deleted_subscribers++;
						                        } else {
							                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
						                        }
			                                    
			                                    $bouncedata = array(
			                                    	'email' 		=> 	$subscriber -> email, 
			                                    	'status' 		=> 	$status, 
			                                    	'history_id' 	=> 	$bouncedemail -> history_id
			                                    );
			                                    
			                                    $Bounce -> save($bouncedata);
			                                    
			                                    do_action('newsletters_subscriber_bounce', $subscriber -> id, ($subscriber -> bouncecount + 1), $bouncedemail -> history_id);
			                                    
			                                    $Db -> model = $Email -> model;
			                                    $Db -> save_field('bounced', "Y", array('id' => $bouncedemail -> id));
			                                    
			                                    //send a notification to the administrator
			                                    $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
				                                $this -> admin_bounce_notification($subscriber);
				                                
				                                if (!$pop3 -> delete($m)) {
					                            	$this -> log_error($pop3 -> ERROR);    
				                                }
				                                
				                                $deleted_emails++;  
			                                }     
				                        }
			                        }
								}
								
								$pop3 -> quit();
								return array($deleted_subscribers, $deleted_emails);
							} else {
								if (false === $count) {
									wp_die($pop3 -> ERROR);
								} elseif ($count === 0) {
									wp_die(__('There are no emails in the mailbox', $this -> plugin_name));
								}
							}
						} else {
							wp_die($pop3 -> ERROR);
						}
					} else {
						wp_die($pop3 -> ERROR);
					}
	            	break;
	            case 'sns'				:
	            	$Db -> model = $Subscriber -> model;	                                
                    if ($subscriber = $Db -> find(array('email' => $email))) {	 
                    	$subscriber_id = $subscriber -> id;                               
                        
                        $Db -> model = $Subscriber -> model;
                        if (!empty($deleteonbounce) && $deleteonbounce == "Y" && (empty($bouncecount) || $bouncecount == 1 || $bouncecount < 1 || (($subscriber -> bouncecount + 1) >= $bouncecount))) {
	                        $Db -> delete($subscriber -> id);
	                        $deleted_subscribers++;
                        } else {
	                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
                        }
                        
                        $bouncedata = array(
                        	'email' 		=> 	$subscriber -> email,
                        	'status'		=>	$status,
                        );
                        
                        $Bounce -> save($bouncedata);
                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
                        $this -> admin_bounce_notification($subscriber);
                        $deleted_emails++;  
                    }
	            	
	            	return array($deleted_subscribers, $deleted_emails);
	            	break;
	            case 'mandrill-bounce'							:
	            	$Db -> model = $Subscriber -> model;	                                
                    if ($subscriber = $Db -> find(array('email' => $email))) {	 
                    	$subscriber_id = $subscriber -> id;                               
                        
                        $Db -> model = $Subscriber -> model;
                        if (!empty($deleteonbounce) && $deleteonbounce == "Y" && (empty($bouncecount) || $bouncecount == 1 || $bouncecount < 1 || (($subscriber -> bouncecount + 1) >= $bouncecount))) {
	                        $Db -> delete($subscriber -> id);
	                        $deleted_subscribers++;
                        } else {
	                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
                        }
                        
                        $bouncedata = array(
                        	'email' 			=> 	$subscriber -> email,
                        	'status'			=>	$status,
                        );
                        
                        $Bounce -> save($bouncedata);
                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
                        $this -> admin_bounce_notification($subscriber);
                        $deleted_emails++;  
                    }
	            	
	            	return array($deleted_subscribers, $deleted_emails);
	            	break;
	            case 'mandrill-delete'				:
	            	$Db -> model = $Subscriber -> model;	                                
                    if ($subscriber = $Db -> find(array('email' => $email))) {	 
                    	$subscriber_id = $subscriber -> id;
                        
                        $Db -> model = $Subscriber -> model;
                        $Db -> delete($subscriber -> id);
                        $deleted_subscribers++;
                        
                        $bouncedata = array(
                        	'email' 			=> 	$subscriber -> email,
                        	'status'			=>	$status,
                        );
                        
                        $Bounce -> save($bouncedata);
                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
                        $this -> admin_bounce_notification($subscriber);
                        $deleted_emails++;  
                    }
	            	
	            	return array($deleted_subscribers, $deleted_emails);
	            	break;
	        }
		}
		
		function add_action($action = null, $function = null, $priority = 10, $params = 1) {
			add_action($action, array($this, $function == '' ? $action : $function), $priority, $params);
		}
		
		function remove_action($action = null, $function = null) {
			remove_action($action, array($this, $function));
		}
		
		function add_filter($filter = null, $function = null, $priority = 10, $params = 1) {
			add_filter($filter, array($this, $function == '' ? $filter : $function), $priority, $params);
		}
		
		function plugin_base() {
			return rtrim(dirname(__FILE__), '/');
		}
		
		function url() {
			$url = rtrim(plugins_url(false, __FILE__));
			return $url;
		}
		
		function redirect($location = null, $msgtype = null, $message = null, $jsredirect = false) {
			global $Html;
			$url = $location;
	
			if (!empty($msgtype)) {		
				if (is_admin()) {
					if ($msgtype == "message") {
						$url = $Html -> retainquery($this -> pre . 'updated=true', $url);
					} elseif ($msgtype == "error") {
						$url = $Html -> retainquery($this -> pre . 'error=true', $url);
					}
				} else {
					if ($msgtype == "success") {
						$url = $Html -> retainquery('updated=1&success=' . $message, $url);
					} elseif ($msgtype == "error") {
						$url = $Html -> retainquery('updated=1&error=' . $message, $url);
					}
				}
			}
			
			if (!empty($message) && is_admin()) {
				$message = rawurlencode($message);
				$url = $Html -> retainquery($this -> pre . 'message=' . ($message), $url);
			}
			
			if (headers_sent() || $jsredirect == true) {
				?>
				
				<script type="text/javascript">
				window.location.href = '<?php echo addslashes($url); ?>';
				</script>
				
				<?php
			} else {			
				header("Location: " . $url . "");
				exit();	
			}
		}
		
		function render_error($message, $vars = array()) {
			//$message = esc_html($message); TO DO
			if (!empty($message) && is_numeric($message)) {
				include $this -> plugin_base() . DS . 'includes' . DS . 'messages.php';
				if (!empty($messages[$message])) {
					$message = vsprintf($messages[$message], $vars);
				}
			}
			
			$this -> render_admin('error-top', array('message' => $message));
		}
		
		function render_message($message, $vars = array(), $dismissable = true) {			
			//$message = esc_html($message); TO DO
			if (!empty($message) && is_numeric($message)) {
				include($this -> plugin_base() . DS . 'includes' . DS . 'messages.php');
				if (!empty($messages[$message])) {
					$message = vsprintf($messages[$message], $vars);
				}
			}
			
			$this -> render_admin('message', array('message' => $message, 'dismissable' => $dismissable));
		}
		
		function et_subject($type = null, $subscriber = null, $language = null) {
			$subject = false;
		
			if (!empty($type)) {
				$subject = __($this -> get_option('etsubject_' . $type));
			}
		
			$subject = $this -> process_set_variables($subscriber, $user, $subject, false, false, true);
			return $subject;
		}
		
		function et_template($type = null, $subscriber = null, $language = null) {
			$theme_id = $this -> default_theme_id('system');
			
			if (!empty($type)) {
				$configured_theme_id = __($this -> get_option('ettemplate_' . $type));
				
				if (!empty($configured_theme_id)) {
					$theme_id = $configured_theme_id;	
				}
			}
			
			return $theme_id;
		}
		
		function et_message($type = null, $subscriber = null, $language = null) {
			$message = false;
			
			if (!empty($type)) {
				$template = $this -> get_option('etmessage_' . $type);			
				
				switch ($type) {
					case 'posts'				:
					case 'latestposts'			:
					case 'sendas'				:
						if (!empty($language) && $this -> language_do()) {
							$message = $this -> language_use($language, $template, false);
						} else {
							$message = __($template);	
						}
						break;
					default 					:
						if (!empty($language) && $this -> language_do()) {
							$message = wpautop($this -> language_use($language, $template, false));	
						} else {
							$message = wpautop(__($template));
						}
						
						$message = $this -> process_set_variables($subscriber, $user, $message, false, false, true);
					break;
				}
			}
		
			return $message;
		}
		
		function get_themefolders() {
			$dir = $this -> plugin_base() . DS . 'views' . DS;
			$themefolders = array();
			
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						$filetype = filetype($dir . $file);
						if (!empty($filetype) && $filetype == "dir") {
							if ($file != "admin" && $file != "email" && $file != "." && $file != "..") {
								$themefolders[] = $file;
							}
						}
					}
					
					closedir($dh);	
				}
			}
			
			return $themefolders;
		}
		
		function render($file = null, $params = array(), $output = true, $folder = 'default', $extension = null) {	
			$this -> plugin_name = basename(dirname(__FILE__));
			$this -> sections = apply_filters('newsletters_sections', (object) $this -> sections);
		
			if (!empty($file)) {				
				$filename = $file . '.php';
				
				if (!empty($folder) && $folder != "admin") {
					$theme_folder = $this -> get_option('theme_folder');
					$folder = (!empty($theme_folder)) ? $theme_folder : $folder;
					
					$template_url = get_stylesheet_directory_uri();
					$theme_path = get_stylesheet_directory();
					$full_path = $theme_path . DS . 'newsletters' . DS . $filename;
					
					if (!empty($theme_path) && file_exists($full_path)) {
						$folder = $theme_path . DS . 'newsletters';
						$theme_serve = true;
					}
				}
				
				if (!empty($extension)) {				
					if ($extensions = $this -> get_extensions()) {					
						foreach ($extensions as $ext) {
							if ($extension == $ext['slug']) {
								$extension_folder = $ext['plugin_name'];
							}
						}
					}
					
					$filepath = dirname(plugin_dir_path(__FILE__)) . DS . $extension_folder . DS;
				} else {					
					if (empty($theme_serve)) {
						$filepath = $this -> plugin_base() . DS . 'views' . DS . $folder . DS;
					} else {
						$filepath = $folder . DS;
					}
				}
				
				$filefull = $filepath . $filename;
				
				if (!empty($params)) {
					foreach ($params as $key => $val) {
						${$key} = $val;
					}
				}
				
				if (file_exists($filefull)) {					
					ob_start();
					
					if (!empty($this -> classes)) {
						foreach ($this -> classes as $class) {
							global ${$class};
						}
					}
					
					if (!empty($this -> helpers)) {
						foreach ($this -> helpers as $helper) {
							global ${$helper};
						}
					}
				
					include($filefull);
					
					$data = ob_get_clean();
					$data = apply_filters('newsletters_render', $data, $file, $params, $output);
					
					if ($output == false) {
						return $data;
					}
					
					echo $data;
					return true;
				} else {
					echo sprintf(__('Rendering of %s has failed!', $this -> plugin_name), '"' . $filefull . '"');
				}
			} else {
				echo __('No file was specified for rendering', $this -> plugin_name);
			}
		}
		
		function has_child_theme_folder() {
			$theme_path = get_stylesheet_directory();
			$full_path = $theme_path . DS . 'newsletters';
			
			if (file_exists($full_path)) {
				return true;
			}
			
			return false;
		}
		
		function active_theme_folder() {
			$theme_folder = $this -> get_option('theme_folder');
			$theme_folder = (!empty($theme_folder)) ? $theme_folder : 'default';
			
			if ($this -> has_child_theme_folder()) {
				$theme_path = get_stylesheet_directory();
				$theme_folder = $theme_path . DS . 'checkout' . DS;
			} else {
				$theme_folder = $this -> plugin_base() . DS . 'views' . DS . $theme_folder . DS;
			}
			
			return $theme_folder;
		}
		
		function theme_folder_functions() {
			if ($theme_folder = $this -> active_theme_folder()) {
				$functions_path = $theme_folder . 'functions.php';
				
				$theme_folder_option = $this -> get_option('theme_folder');
				$functions_path_original = $this -> plugin_base() . DS . 'views' . DS . $theme_folder_option . DS . 'functions.php';
				
				if (file_exists($functions_path)) {
					require_once($functions_path);
					
					return true;
				} elseif (file_exists($functions_path_original)) {
					require_once($functions_path_original);
					
					return true;
				}
			}
			
			return false;
		}
		
		function render_url($file = null, $folder = 'admin', $extension = null) {	
			$this -> plugin_name = basename(dirname(__FILE__));
		
			if (!empty($file)) {		
				if (!empty($folder) && $folder != "admin") {
					$theme_folder = $this -> get_option('theme_folder');
					$folder = (!empty($theme_folder)) ? $theme_folder : $folder;
					$folderurl = plugins_url() . '/' . $this -> plugin_name . '/views/' . $folder . '/';
				
					$template_url = get_stylesheet_directory_uri();
					$theme_path = get_stylesheet_directory();
					$full_path = $theme_path . DS . 'newsletters' . DS . $file;
					
					if (!empty($theme_path) && file_exists($full_path)) {
						$folderurl = $template_url . '/newsletters/';
					}
				} else {
					$folderurl = plugins_url() . '/' . $this -> plugin_name . '/';
				}
				
				$url = $folderurl . $file;				
				return $url;
			}
			
			return false;
		}
		
		function default_theme_id($type = "sending") {
			global $Db, $Theme;
			$Db -> model = $Theme -> model;
			$theme_id = 0;
			
			switch ($type) {
				case 'system'			:
					if ($theme = $Db -> find(array('defsystem' => "Y"))) {
						return $theme -> id;
					}
					break;
				case 'sending'			:
				default 				:
					if ($theme = $Db -> find(array('def' => "Y"))) {
						return $theme -> id;
					}
					break;
			}
			
			return $theme_id;
		}
		
		function make_bitly_url($url = null, $format = 'txt') {		
			if (!preg_match("/(manage\-subscriptions|loginauth|wpml|wpmlmethod|jpg|png|gif|jpeg|bmp|wpmltrack|wpmllink)/si", $url)) {			
				if (preg_match("/^http\:\/\//si", $url) || preg_match("/^https\:\/\//si", $url)) {
					$login = $this -> get_option('shortlinkLogin');
					$appkey = $this -> get_option('shortlinkAPI');
					$bitly = 'http://api.bit.ly/v3/shorten?longUrl=' . urlencode($url) . '&login=' . $login . '&apiKey=' . $appkey . '&format=' . $format;
					$bitly = apply_filters('newsletters_bitly_url', $bitly);
					
					$result = wp_remote_get($bitly, array('timeout' => 120));
					if (!is_wp_error($result)) {
						$body = trim($result['body']);
						
						if (filter_var($body, FILTER_VALIDATE_URL) !== FALSE) {
							$bitlink = $body;
							return $bitlink;
						}
					}
				}
			}
			
			return $url;
		}
		
		function hashlink($link = null, $history_id = null, $subscriber_id = null, $user_id = null) {
			global $Html, $wpmlLink;
			$hashlink = $link;
		
			if (!empty($link)) {			
				if (!preg_match("/(manage\-subscriptions|loginauth|wpml|wpmlmethod|jpg|png|gif|jpeg|bmp|wpmltrack|wpmllink)/si", $link)) {
					if (preg_match("/^http\:\/\//si", $link) || preg_match("/^https\:\/\//si", $link)) {					
						$hash = md5($link);
						
						$queryargs = array();
						$queryargs[$this -> pre . 'link'] = $hash;
						$queryargs['history_id'] = $history_id;
						
						if (!empty($subscriber_id)) $queryargs['subscriber_id'] = $subscriber_id;
						if (!empty($user_id)) $queryargs['user_id'] = $user_id;
						
						$queryargs = apply_filters('newsletters_hashlink_queryargs', $queryargs, $link, $hash, $history_id);
						
						//$hashlink = $Html -> retainquery($query, home_url());
						$hashlink = add_query_arg($queryargs, home_url());
						
						if (!$curlink = $this -> Link() -> find(array('hash' => $hash))) {
							$link_data = array(
								'link'			=>	$link,
								'hash'			=>	$hash,
							);	
							
							$this -> Link() -> save($link_data, true);
						}
					}
				}
			}
			
			return apply_filters('newsletters_hashlink', $hashlink, $link, $history_id, $subscriber_id, $user_id);
		}
		
		function admin_footer_text($text = null) {			
			if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {
				$plugin = '<a href="http://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">Tribulant Newsletters</a>';
				
				$stars = '<a href="https://wordpress.org/support/view/plugin-reviews/newsletters-lite?rate=5#postform" target="_blank"><span class="newsletters_footer_rating">
		          <span class="star"></span><span class="star"></span><span class="star"></span><span class="star"></span><span class="star"></span>
		        </span></a>';
	        
	        	$stars .= '<style type="text/css">
	        	.newsletters_footer_rating {
				    unicode-bidi: bidi-override;
				    direction: rtl;
				    font-size: 16px;
				}
				
				.newsletters_footer_rating span.star {
				    font-family: FontAwesome !important;
				    font-weight: normal;
				    font-style: normal;
				    display: inline-block;
				}
				
				.newsletters_footer_rating span.star:before,
				.newsletters_footer_rating span.star ~ span.star:before {
					font-family: FontAwesome;
				    content: "\f005";
				    color: #e3cf7a;
				    padding-right: 2px;
				}
	        	</style>';
				
				$newsletters_text = '</p><br class="clear" /><p class="alignleft">' . sprintf(__('If you like %s, please leave us a %s rating on WordPress.org. Thank you in advance!', $this -> plugin_name), $plugin, $stars) . '';
				$text .= $newsletters_text;
			}
			
			return $text;
		}
		
		function render_email($file = null, $params = array(), $output = false, $html = true, $renderht = true, $theme_id = 0, $shortlinks = true, $fullbody = false) {						
			global $newsletters_history_id, $newsletters_plaintext;
			$this -> plugin_name = basename(dirname(__FILE__));
		
			if (!empty($file) || !empty($fullbody)) {
				$defaulttemplate = $this -> get_option('defaulttemplate');
				if (!empty($defaulttemplate)) {
					$head = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'head-default.php';
					$foot = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'foot-default.php';
				} else {
					$head = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'head.php';
					$foot = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'foot.php';
				}
				
				/* Go through the parameters */				
				if (!empty($params)) {					
					foreach ($params as $pkey => $pval) {
						${$pkey} = $pval;
						
						switch ($pkey) {
							case 'subscriber'	:
								global $current_subscriber;
								$current_subscriber = $subscriber;
								break;
							case 'message'		:
								global $orig_message;
								$orig_message = stripslashes($pval);
								break;
						}
					}
				}
				
				if (!empty($this -> classes)) {
					foreach ($this -> classes as $class) {
						global ${$class};
					}
				}
				
				if (!empty($this -> helpers)) {
					foreach ($this -> helpers as $helper) {
						global ${$helper};
					}
				}
	
				/* Head */			
				if (true || $html == true) {
					if ($renderht == true && file_exists($head)) { 
						if ($output == false) { ob_start(); }
						include($head); 
						if ($output == false) { $head = ob_get_clean(); }
					} else {
						$head = "";
					}
				}
				
				/** Body Part BEG **/
				if (empty($fullbody)) {
					$filefull = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . $file . '.php';
					
					if (file_exists($filefull)) {					
						if ($output == false) { ob_start(); }
						
						include($filefull);
						if ($output == false) { $body = ob_get_clean(); }
						
						if ($output == false && (true || $html == true)) {
							$body = wpautop($body);
						}
					}
				} else {
					$body = "";
					ob_start();
					echo wpautop(stripslashes($fullbody));
					$body = ob_get_clean();
				}
				
				$bodyonly = $body;
				/** Body Part END **/
				
				/* Foot */
				if (true || $html == true) {
					if ($renderht == true && file_exists($foot)) { 
						if ($output == false) { ob_start(); }
						include($foot); 
						if ($output == false) { $foot = ob_get_clean(); }
					} else {
						$foot = "";
					}
				}
				
				if (!empty($history_id)) {
					$this -> history_id = $newsletters_history_id = $history_id;
					$history = $this -> History() -> get($history_id);
					
					if (!empty($history -> language)) {
						$this -> language_set($history -> language);
					}
				}
				
				/*$eunique = (empty($user)) ? 
				md5($subscriber -> id . $subscriber -> mailinglist_id . $email -> history_id . date_i18n("YmdH")) : 
				md5($user -> ID . $user -> roles[0] . $email -> history_id . date_i18n("YmdH"));*/
				
				//$body = $this -> process_set_variables($subscriber, $user, stripslashes($body), $history_id, $eunique);
				
				//pass the $body through the shortcodes
				//$body = do_shortcode(stripslashes($body));
				//$body = str_replace("$", "&#36;", $body);
				$body = preg_replace('/\$(\d)/', '\\\$$1', $body);
				
				$themeintextversion = $this -> get_option('themeintextversion');
				if (empty($themeintextversion)) {
					global $wpml_textmessage;
					$wpml_textmessage = $body;
				}
				
				if (empty($output) || $output == false) {																				
					if (true || $html == true) {						
						global $Db, $Theme;
						$Db -> model = $Theme -> model;						
						if (!empty($theme_id) && $theme = $Db -> find(array('id' => $theme_id))) {
							$theme_content = "";							
							ob_start();					
							echo do_shortcode(stripslashes($theme -> content));
							$theme_content = ob_get_clean();
							$theme_content = apply_filters('newsletters_theme_before_wpmlcontent_replace', $theme_content);
							
							$body = '<div class="newsletters_content">' . apply_filters($this -> pre . '_wpmlcontent_before_replace', $body) . '</div>';							
							$body = preg_replace("/\[wpmlcontent\]/si", $body, $theme_content);
						} else {
							// No theme, load default
							$body = do_shortcode($head) . $body . do_shortcode($foot);
						}
						
						// Parse the content areas
						$pattern = "/\[(\[?)(newsletters_content)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s";
						$body = preg_replace_callback($pattern, array($this, 'newsletters_content'), $body);
						$body = htmlspecialchars_decode($body, ENT_NOQUOTES);
						$body = apply_filters($this -> pre . '_wpmlcontent_after_replace', $body);
						
						// Remove width/height attributes from images
						$body = $this -> remove_width_height_attr($body);
					}
					
					/*$preheader = 'This is my custom preheader text that will show up in the auto preview on the email client to capture the attention of the reader reading the email.';
					
					$doc = new DOMDocument();
					$doc -> loadHTML($body);
					$body = $doc -> getElementsByTagName('body') -> item(0);
					$intro = $doc -> createElement('span', $preheader);
					$intro -> setAttribute('style', "display:none !important; visibility:hidden; opacity:0; color:transparent; height:0; width:0;");
					$body -> insertBefore($intro, $body -> firstChild);
					$body = $doc -> saveHTML();*/
					
					if (!empty($themeintextversion)) {
						global $wpml_textmessage;
						$wpml_textmessage = $body;
					}
					
					global $newsletters_plaintext;
					$newsletters_plaintext = false;
					if (empty($html) || $html != true || (!empty($history -> format) && $history -> format == "text")) {		
						if (version_compare(PHP_VERSION, '5.3.2') >= 0) {										
							require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
		    				$html2text = new Html2Text();
							$body = $html2text -> convert($wpml_textmessage);
							$newsletters_plaintext = true;
						}
					} else {
						$newsletters_plaintext = false;
					}
								
					return $body;
				} else {								
					return true;
				}
			}
			
			return false;
		}
		
		function remove_width_height_attr($html = null) {
			$remove_width_height_attr = $this -> get_option('remove_width_height_attr');
			
			if (!empty($remove_width_height_attr)) {
				$dom = new DOMDocument();
				$dom -> loadHTML($html);
				
				foreach ($dom -> getElementsByTagName('img') as $img) {				
					$img -> removeAttribute('width');
					$img -> removeAttribute('height');
				}
				
				$html = $dom -> saveHTML();
				$html = trim(preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $html));
			}
			
			return $html;
		}
		
		function newsletters_content($matches = null) {
			$output = "";		
			if (!empty($matches)) {
				$atts = shortcode_parse_atts($matches['3']);				
				if (!empty($this -> history_id) && !empty($atts['id'])) {					
					//if (is_object($this -> Content)) {						
						if ($contentarea = $this -> Content() -> find(array('number' => $atts['id'], 'history_id' => $this -> history_id))) {							
							$output = wpautop(do_shortcode(stripslashes($contentarea -> content)));
						}
					//}
				}
			}
			
			return $output;
		}
		
		function check_uploaddir() {
			if (!is_admin()) return;
		
			global $uploaddir, $Html;
			$uploaddir = $Html -> uploads_path() . '/' . $this -> plugin_name . '/';
			
			if (file_exists($uploaddir)) {
				/* Export subscribers folder */
				$exportdir = $uploaddir . 'export' . DS;
				if (!file_exists($exportdir)) {
					@mkdir($exportdir, 0777);
					@chmod($exportdir, 0777);
				} else {
					$exportindex = $exportdir . 'index.php';
					$exportindexcontent = "<?php /* Silence */ ?>";
					$exporthtaccess = $exportdir . '.htaccess';
					$exporthtaccesscontent = "order allow,deny\r\ndeny from all\r\n\r\nOptions All -Indexes";
					if (!file_exists($exportindex) && $fh = fopen($exportindex, "w")) { fwrite($fh, $exportindexcontent); fclose($fh); }
					if (!file_exists($exporthtaccess) && $fh = fopen($exporthtaccess, "w")) { fwrite($fh, $exporthtaccesscontent); fclose($fh); }
				}
			
				/* Embedded images folder */
				if ($this -> is_plugin_active('embedimages')) {
					$embedimagesdir = $uploaddir . 'embedimages' . DS;
					if (!file_exists($embedimagesdir)) {
						@mkdir($embedimagesdir, 0777);
						@chmod($embedimagesdir, 0777);
					}
				}
			} else {
				@mkdir($uploaddir, 0777);
				@chmod($uploaddir, 0777);
			}
		}
		
		function render_admin($file = null, $params = array(), $output = true) {	
			$this -> plugin_name = basename(dirname(__FILE__));
		
			if (!empty($file)) {
				$filefull = $this -> plugin_base() . DS . 'views' . DS . 'admin' . DS . $file . '.php';
			
				if (!empty($params)) {
					foreach ($params as $key => $val) {
						${$key} = $val;
					}
				}
			
				if (file_exists($filefull)) {
					if ($output == false) {
						ob_start();
					}
					
					if (!empty($this -> classes)) {
						foreach ($this -> classes as $class) {
							global ${$class};
						}
					}
					
					if (!empty($this -> helpers)) {
						foreach ($this -> helpers as $helper) {
							global ${$helper};
						}
					}
				
					include($filefull);
					
					if ($output == false) {
						$data = ob_get_clean();
						return $data;
					} else {
						return true;
					}
				}
			}
			
			return false;
		}
		
		function extension_vendor($name = null) {
			if (!empty($name)) {
				switch ($name) {
					/* Embedded Images */
					case 'embedimages'						:
						$filepath = 'newsletters-embedimages' . DS . 'embedimages.php';
						break;
				}
				
				$filefull = dirname(plugin_dir_path(__FILE__)) . DS . $filepath;
				
				if (file_exists($filefull)) {
					
					require_once $filefull;
					$class = $this -> pre . $name;
					
					if (class_exists($class)) {
						${$name} = new $class;
						return ${$name};	
					}
				}
			}
		}
		
		function get_extensions() {
			include $this -> plugin_base() . DS . 'includes' . DS . 'extensions.php';
			$extensions = apply_filters($this -> pre . '_extensions_list', $extensions);
			$this -> extensions = $extensions;
			
			if (!empty($extensions) && is_array($extensions)) {
				$titles = array();
				foreach ($extensions as $extension) {
					$titles[] = $extension['name'];
				}
				
				array_multisort($titles, SORT_ASC, $extensions);
				return $extensions;
			}
			
			return false;
		}
		
		function is_plugin_active($name = null, $orinactive = false) {
			if (!empty($name)) {
				global $Html;
				$slug = $Html -> sanitize($name);
				
				$hash = ($slug . $orinactive);
				if ($ob_active = $this -> get_cache($hash, 'pluginactive')) {
					return $ob_active;
					$active = (!empty($ob_active) && $ob_active == "Y") ? true : false;
					return $active;	
				}
			
				require_once ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'plugin.php';
				
				if ($extensions = $this -> get_extensions()) {			
					foreach ($extensions as $extension) {
						if ($name == $extension['slug']) {
							$path = $extension['plugin_name'] . DS . $extension['plugin_file'];
						}
					}
				}
	
				if (empty($path)) {			
					switch ($name) {
						case 'embedimages'							:
							$path = 'newsletters-embedimages' . DS . 'embedimages.php';
							break;
						case 'newsletters-cforms'					:
							$path = 'newsletters-cforms' . DS . 'cforms.php';
							break;
						case 'qtranslate'							:
							$path = 'qtranslate' . DS . 'qtranslate.php';
							break;
						case 'qtranslate-x'							:
							$path = 'qtranslate-x' . DS . 'qtranslate.php';
							break;
						case 'wpml'									:
							$path = 'sitepress-multilingual-cms' . DS . 'sitepress.php';
							break;
						case 'captcha'								:
							$path = 'really-simple-captcha' . DS . 'really-simple-captcha.php';
							break;
					}
				}
				
				if (!empty($path)) $path2 = str_replace("\\", "/", $path);
				
				if (!empty($name) && $name == "qtranslate") {
					$path2 = 'mqtranslate' . DS . 'mqtranslate.php';
				}
				
				if (!empty($path)) {
					$plugins = get_plugins();
					
					if (!empty($plugins)) {
						if (array_key_exists($path, $plugins) || array_key_exists($path2, $plugins)) {						
							/* Let's see if the plugin is installed and activated */
							if (is_plugin_active(plugin_basename($path)) ||
								is_plugin_active(plugin_basename($path2))) {
								
								$this -> set_cache($hash, true, 'pluginactive');
								
								return true;
							}
							
							/* Maybe the plugin is installed but just not activated? */
							if (!empty($orinactive) && $orinactive == true) {							
								if (is_plugin_inactive(plugin_basename($path)) ||
									is_plugin_inactive(plugin_basename($path2))) {									
									$this -> set_cache($hash, true, 'pluginactive');
									
									return true;	
								}
							}	
						}
					}
				}
			}
			
			$this -> set_cache($hash, false, 'pluginactive');
			return false;
		}
		
		// Make sure that all metaboxes (fields) are closed when the form builder is loaded
		function closed_meta_boxes_form($closed = null) {							
			if (!empty($_GET['id'])) {
				if ($form = $this -> Subscribeform() -> find(array('id' => $_GET['id']))) {
					$closed = array();
					
					if (!empty($form -> form_fields)) {
						foreach ($form -> form_fields as $form_field) {							
							$id = 'newsletters_forms_field_' . $form_field -> field_id;
							if (empty($closed) || !in_array($id, $closed)) {
								$closed[] = $id;
							}
						}
					}
				}
			}
		
		    return $closed;
		}
		
		// Reset the meta box order on the save a form page
		// The meta boxes in WordPress are actually used as fields on the form here
		function meta_box_order($order = null, $option = null, $user = null) {			
			if (!empty($order)) {
				$order = false;
			}
			
			return $order;
		}
	
		function use_captcha($status = "Y") {						
			if ($status == 'Y') {			
				$captcha_type = $this -> get_option('captcha_type');
				if (!empty($captcha_type)) {				
					switch ($captcha_type) {
						case 'rsc'				:
							if ($this -> is_plugin_active('captcha')) {
								return "rsc";
							}
							break;
						case 'recaptcha'		:
							return "recaptcha";
							break;
						case 'none'				:
						default 				:
							return false;
							break;
					}
				}
			}
			
			return false;
		}	
	}
}

if (!class_exists('fakemailer')) {
	class fakemailer {
	    public function Send() {
	        throw new phpmailerException('Cancelling mail');
	    }
	}
}

if (!class_exists('phpmailerException' )) {
	/*class phpmailerException extends Exception {
	    public function errorMessage() {
	        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
	        return $errorMsg;
	    }
	}*/
}

?>