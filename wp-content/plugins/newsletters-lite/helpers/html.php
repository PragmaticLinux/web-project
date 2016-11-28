<?php

if (!class_exists('wpmlHtmlHelper')) {
class wpmlHtmlHelper extends wpMailPlugin {

	var $name = 'Html';
	
	function __construct() {
		return true;
	}
	
	public function detectDelimiter($csvFile = null) {
	    $delimiters = array(
	        ';' => 0,
	        ',' => 0,
	        "\t" => 0,
	        "|" => 0
	    );
	
	    $handle = fopen($csvFile, "r");
	    $firstLine = fgets($handle);
	    fclose($handle); 
	    foreach ($delimiters as $delimiter => &$count) {
	        $count = count(str_getcsv($firstLine, $delimiter));
	    }
	
	    return array_search(max($delimiters), $delimiters);
	}
	
	function paymentmethod($pmethod = null) {
		
		if (!empty($pmethod)) {
			switch ($pmethod) {
				case 'paypal'				:
					$paymentmethod = __('PayPal', $this -> plugin_name);
					break;
				case '2co'					:
					$paymentmethod = __('2CheckOut', $this -> plugin_name);
					break;
			}
		}
		
		return apply_filters('newsletters_paymentmethod_title', $paymentmethod);
	}
	
	function get_image_sizes( $size = null) {

        global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

            if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
                
					$sizes[$_size]['title'] = ucfirst($_size);
                    $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                    $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                    $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                    $sizes[ $_size ] = array(
                        	'title'			=>	$_size,
                            'width' 		=> 	$_wp_additional_image_sizes[ $_size ]['width'],
                            'height' 		=> 	$_wp_additional_image_sizes[ $_size ]['height'],
                            'crop' 			=>  $_wp_additional_image_sizes[ $_size ]['crop']
                    );

            }
        }

        // Get only 1 size if found
        if ( $size ) {

                if( isset( $sizes[ $size ] ) ) {
                        return $sizes[ $size ];
                } else {
                        return false;
                }

        }

        return $sizes;
	}
	
	/**
	 * Format array for the datepicker
	 *
	 * WordPress stores the locale information in an array with a alphanumeric index, and
	 * the datepicker wants a numerical index. This function replaces the index with a number
	 */
	function strip_array_indices( $ArrayToStrip ) {
	    foreach( $ArrayToStrip as $objArrayItem) {
	        $NewArray[] =  $objArrayItem;
	    }
	 
	    return( $NewArray );
	}
	
	function bar_chart($id = null, $attributes = array(), $data = array(), $options = array()) {
		
		$default_attributes = array(
			'width'					=>	"100%",
			'height'				=>	"300px",
		);
		
		$attributes = wp_parse_args($attributes, $default_attributes);
		
		$default_options = array(
			'barShowStroke'			=>	false,
			'multiTooltipTemplate'	=>	"\<\%\= datasetLabel \%\>: \<\%\= value \%\>",
			'legendTemplate'		=>	"<ul class=\"\<\%\=name.toLowerCase()\%\>-legend\">\<\% for (var i=0; i<datasets.length; i++){\%\><li><span style=\"background-color:<\%\=datasets[i].fillColor\%\>\"></span>\<\% if(datasets[i].label){ \%\><\%\=datasets[i].label\%\>\<\%}\%\></li>\<\%}\%\></ul><br class=\"clear\" />"
		);
		
		$options = wp_parse_args($options, $default_options);
		
		$this -> render('charts' . DS . 'bar', array('id' => $id, 'attributes' => $attributes, 'data' => $data, 'options' => $options), true, 'admin');
	}
	
	function pie_chart($id = null, $attributes = array(), $data = array(), $options = array()) {
		
		$default_attributes = array(
			'width'					=>	300,
			'height'				=>	300,
		);
		
		$attributes = wp_parse_args($attributes, $default_attributes);
		
		$default_options = array(
			'tooltipTemplate'		=>	"<%if (label){%><%=label%>: <%}%><%=value%>%",
		);
		
		$options = wp_parse_args($options, $default_options);
		
		if (!empty($attributes['width']) && $attributes['width'] < 150) {
			foreach ($data as $dkey => $dval) {
				$data[$dkey]['label'] = substr($dval['label'], 0, 1);
			}
		}
		
		$this -> render('charts' . DS . 'pie', array('id' => $id, 'attributes' => $attributes, 'data' => $data, 'options' => $options), true, 'admin');
	}
	 
	/**
	 * Convert the php date format string to a js date format
	 */
	function date_format_php_to_js( $sFormat ) {
	    switch( $sFormat ) {
	        //Predefined WP date formats
	        case 'F j, Y':
	            return( 'MM dd, yy' );
	            break;
	        case 'Y/m/d':
	            return( 'yy/mm/dd' );
	            break;
	        case 'm/d/Y':
	            return( 'mm/dd/yy' );
	            break;
	        case 'd/m/Y':
	            return( 'dd/mm/yy' );
	            break;
	     }
	}
	
	function is_json($string = null) {
		if (!empty($string)) {
			if (is_string($string) && is_object(json_decode($string))) {
				return true;
			}
		}
		
		return false;
	}
	
	function hidden_type_operator($key = null) {
		$operator = false;
	
		if (!empty($key)) {
			
			switch ($key) {
				case 'post'			:
					$operator = "&#36;_POST";
					break;
				case 'get'			:
					$operator = "&#36;_GET";
					break;
				case 'global'		:
					$operator = "&#36;GLOBALS";
					break;
				case 'cookie'		:
					$operator = "&#36;_COOKIE";
					break;
				case 'session'		:
					$operator = "&#36;_SESSION";
					break;
				case 'server'		:
					$operator = "&#36;_SERVER";
					break;
			}
		}
		
		return $operator;
	}
	
	function fragment_cache($content = null, $object = null, $method = null, $data = null) {
		$output = $content;
		return stripslashes($content);
	
		if (!empty($content)) {				
			if (is_plugin_active(plugin_basename('wp-super-cache/wp-cache.php'))) {			
				//return $content;
			
				//global $wp_cache_config_file, $newsletters_wpsc_cachedata;
				//include $wp_cache_config_file;
				//if (empty($wp_cache_mfunc_enabled)) { wp_cache_replace_line('^ *\$wp_cache_mfunc_enabled', "\$wp_cache_mfunc_enabled = 1;", $wp_cache_config_file); }
				//if (empty($wp_super_cache_late_init)) { wp_cache_replace_line('^ *\$wp_super_cache_late_init', "\$wp_super_cache_late_init = 1;", $wp_cache_config_file); }
				//if (empty($wp_cache_mod_rewrite)) { wp_cache_replace_line('^ *\$wp_cache_mod_rewrite', "\$wp_cache_mod_rewrite = 0;", $wp_cache_config_file); }
			} elseif (is_plugin_active(plugin_basename('w3-total-cache/w3-total-cache.php'))) {													
				$content = stripslashes($content);				
				$output .= '<!--mfunc ' . W3TC_DYNAMIC_SECURITY . ' ?>' . $content . '<?php -->';
				$output .= $content;
				$output .= '<!--/mfunc ' . W3TC_DYNAMIC_SECURITY . ' -->';
			} elseif (is_plugin_active(plugin_basename('quick-cache/quick-cache.php'))) {
				define('QUICK_CACHE_ALLOWED', FALSE);
				$output = $content;
			}
		}
		
		return stripslashes($output);
	}
	
	function wp_has_current_submenu($submenu = false) {
		$menu = false;
		if (!empty($submenu)) {
			if (preg_match("/^newsletters\-([^-]+)?/si", $submenu, $matches)) {
				$menu = $matches[0];
			}
		}
	
		?>
		
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('li#toplevel_page_newsletters').attr('class', "wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_newsletters menu-top-last");
			jQuery('li#toplevel_page_newsletters > a').attr('class', "wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_newsletters menu-top-last");
			<?php if (!empty($menu)) : ?>jQuery('li#toplevel_page_newsletters ul.wp-submenu li a[href="admin.php?page=<?php echo $menu; ?>"]').attr('class', "current").parent().attr('class', "current");<?php endif; ?>
		});
		</script>
		
		<?php
	}
	
	function help($help = null, $content = null, $link = null) {
		if (!empty($help)) {
			ob_start();
			
			$content = (empty($content)) ? '<i class="fa fa-question-circle fa-fw"></i>' : $content;
		
			?>
			
			<span class="wpmlhelp"><a href="<?php echo esc_attr(stripslashes($link)); ?>" <?php if (empty($link)) : ?>onclick="return false;"<?php endif; ?> title="<?php echo esc_attr(stripslashes($help)); ?>"><?php echo stripslashes($content); ?></a></span>
			
			<?php
			
			$html = ob_get_clean();
			return apply_filters('newsletters_help', $html, $content, $link);
		}
	}
	
	function hex2rgb( $colour ) {
	    if ( $colour[0] == '#' ) {
	            $colour = substr( $colour, 1 );
	    }
	    if ( strlen( $colour ) == 6 ) {
	            list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	    } elseif ( strlen( $colour ) == 3 ) {
	            list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	    } else {
	            return false;
	    }
	    $r = hexdec( $r );
	    $g = hexdec( $g );
	    $b = hexdec( $b );
	    
	    return array($r, $g, $b);
	}
	
	function eunique($subscriber = null, $email_id = null, $type = null) {
		if (!empty($subscriber) && (!empty($email_id) || !empty($type))) {
			return md5($subscriber -> id . $subscriber -> mailinglist_id . $email_id . $type . date_i18n("YmdH"));	
		}
	}
	
	function time_difference($time_one = null, $time_two = null, $interval = 'days') {
		$difference = 0;
	
		if (!empty($time_one) && !empty($time_two)) {
			switch ($interval) {
				case 'minutes'				:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60));
					break;
				case 'hours'				:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60 * 60));
					break;
				case 'days'					:
				default						:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60 * 60 * 24));
					break;
				case 'weeks'				:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60 * 60 * 24 * 7));
					break;
				case 'years'				:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60 * 60 * 24 * 7 * 52));
					break;
			}
		}
		
		return $difference;
	}
	
	/*
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 * @author Tristan Jahier
	 */
	function dateformat_PHP_to_jQueryUI($php_format = null) {
	    $SYMBOLS_MATCHING = array(
	        // Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
	        // Week
	        'W' => '',
	        // Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
	        // Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
	        // Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	    );
	    $jqueryui_format = "";
	    $escaping = false;
	    for($i = 0; $i < strlen($php_format); $i++)
	    {
	        $char = $php_format[$i];
	        if($char === '\\') // PHP date format escaping character
	        {
	            $i++;
	            if($escaping) $jqueryui_format .= $php_format[$i];
	            else $jqueryui_format .= '\'' . $php_format[$i];
	            $escaping = true;
	        }
	        else
	        {
	            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
	            if(isset($SYMBOLS_MATCHING[$char]))
	                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
	            else
	                $jqueryui_format .= $char;
	        }
	    }
	    return $jqueryui_format;
	}
	
	function days_difference($date_one = null, $date_two = null) {
		return $this -> time_difference($date_one, $date_two, 'days');
	
		$days = 0;
		
		if (!empty($date_one) && !empty($date_two)) {
			$one = strtotime($date_one);
			$two = strtotime($date_two);			
			$days = floor(($one - $two) / (60 * 60 * 24));
		}
		
		return $days;	
	}
	
	function field_type($type = null, $slug = null) {	
		if (!empty($type)) {
			$fieldtypes = array(
				'special'		=>	__('Special', $this -> plugin_name),
				'text'			=>	__('Text Field', $this -> plugin_name),
				'textarea'		=>	__('Text Area', $this -> plugin_name),
				'select'		=>	__('Select Drop Down', $this -> plugin_name),
				'radio'			=>	__('Radio Buttons', $this -> plugin_name),
				'checkbox'		=>	__('Checkboxes', $this -> plugin_name),
				'file'			=>	__('File Upload', $this -> plugin_name),
				'pre_country'	=>	__('Predefined : Country Select', $this -> plugin_name),
				'pre_date'		=>	__('Predefined : Date Picker', $this -> plugin_name),
				'pre_gender'	=>	__('Predefined : Gender', $this -> plugin_name),
				'hidden'		=>	__('Hidden', $this -> plugin_name),
			);	
			
			switch ($slug) {
				case 'email'				:
					$field_type = __('Email Address', $this -> plugin_name);
					break;
				case 'list'					:
					$field_type = __('Mailing List', $this -> plugin_name);
					break;
				default 					:
					$field_type = $fieldtypes[$type];	
					break;
			}
			
			return $field_type;
		}
	
		return false;	
	}
	
	function uploads_path($dated = false) {
		if ($upload_dir = wp_upload_dir()) {	
			if ($dated) {
				return str_replace("\\", "/", $upload_dir['path']);	
			} else {
				return str_replace("\\", "/", $upload_dir['basedir']);
			}
		}
		
		return str_replace("\\", "/", WP_CONTENT_DIR . '/uploads');
	}
	
	function uploads_subdir() {
		$subdir = '';
	
		if ($upload_dir = wp_upload_dir()) {
			if (!empty($upload_dir['subdir'])) {
				$subdir = $upload_dir['subdir'];
			}
		}
		
		return $subdir;
	}
	
	function uploads_url() {
		if ($upload_dir = wp_upload_dir()) {
			return $upload_dir['baseurl'];
		}
		
		return site_url() . '/wp-content/uploads';
	}
	
	function file_custom_field($value = null, $limit = false, $types = false, $field = null, $removefile = false) {	
		$output = false;
		
		if (!empty($value)) {
			$currentfile = '<p class="newsletters-currentfile">';			
			$currentfile .= '<a class="btn btn-default btn-sm" href="' . esc_attr(stripslashes($value)) . '" target="_blank"><i class="fa fa-paperclip"></i> ' . __('Uploaded file', $this -> plugin_name) . '</a>';			
			
			if (!empty($removefile)) {
				$currentfile .= ' <a href="" class="text-danger" onclick="if (confirm(\'' . __('Are you sure you want to remove this file?', $this -> plugin_name) . '\')) { jQuery(\'#newsletters_oldfile_' . $field -> id . '\').remove(); jQuery(this).parent().remove(); } return false;"><i class="fa fa-times"></i></a>';
			}
			
			$currentfile .= '</p>';
			$output .= $currentfile;
		}

		return $output;
	}
	
	function get_gravatar($email = null, $s = 50, $d = 'mm', $r = 'g', $img = true, $atts = array() ) {
		// Uses WordPress get_avatar() function
		return get_avatar($email, $s, null, false);
	}
	
	function wordpress_usermeta_fields() {
		$usermeta = array(
			'first_name'				=>	__('First Name', $this -> plugin_name),
			'last_name'					=>	__('Last Name', $this -> plugin_name),
			'nickname'					=>	__('Nickname', $this -> plugin_name),
			'description'				=>	__('Biographical Info', $this -> plugin_name),
		);
		
		return $usermeta;
	}
	
	function RoundUptoNearestN($biggs){
       $rounders = (strlen($biggs) - 2) * -1;
       $places = (strlen($biggs) -2);

       $counter = 0;
       while ($counter <= $places) {
           $counter++;
               if($counter == 1) {
               		$holder = $holder . '1'; }
               else {
	                $holder = $holder . '0'; }
       }

       $biggs = $biggs + $holder;
       $biggs = round($biggs, $rounders);
       if ($biggs < 30) { $biggs = 30; } elseif ($biggs < 50) { $biggs = 50; } elseif ($biggs < 80) { $biggs = 80; } elseif ($biggs < 100) { $biggs = 100; }
       return $biggs;
	}
	
	function next_scheduled($hook = null, $args = null) {
		if (!preg_match("/(newsletters)/si", $hook)) {
			$hook = $this -> pre . '_' . $hook;
		}
		
		$args = (empty($args)) ? array() : $args;
	
		if (!empty($hook) && $schedules = wp_get_schedules()) {						
			if ($hookinterval = wp_get_schedule($hook, $args)) {
				if ($hookschedule = wp_next_scheduled($hook, $args)) {				
					return $schedules[$hookinterval]['display'] . '<br/><small><b>' . date_i18n("Y-m-d H:i:s", $hookschedule) . '</b></small>';
				} else {
					return __('This task does not have a next schedule.', $this -> plugin_name);	
				}
			} else {
				return __('No schedule has been set for this task.', $this -> plugin_name);	
			}
		} else {
			return __('No cron schedules are available or no task was specified.', $this -> plugin_name);	
		}
		
		return false;
	}
	
	function attachment_link($attachment = null, $icononly = false, $truncate = 20) {		
		$attachmentfile = "";
		
		if (!empty($attachment['subdir'])) {
			$attachmentfile .= $attachment['subdir'] . '/';
		}
		
		$attachmentfile .= basename($attachment['filename']);
		$attachmentfile = ltrim($attachmentfile, "/");
	
		if (!empty($attachmentfile)) {			
			if ($icononly == false) {
				return '<a class="button" style="text-decoration:none;" target="_blank" href="' . $this -> uploads_url() . '/' . $attachmentfile . '" title="' . basename($attachmentfile) . '"><i class="fa fa-paperclip"></i> ' . $this -> truncate(basename($attachmentfile), $truncate) . '</a>';
			} else {
				return '<a class="button" style="text-decoration:none;" target="_blank" href="' . $this -> uploads_url() . '/' . $attachmentfile . '" title="' . basename($attachmentfile) . '"><i class="fa fa-paperclip"></i></a>';
			}
		}
		
		return false;
	}
	
	function system_email($slug = null) {
		$name = false;
		if (!empty($slug)) {
			switch ($slug) {
				case 'expiration'			:
					$name = __('Expiration', $this -> plugin_name);
					break;
				case 'authentication'		:
					$name = __('Authentication', $this -> plugin_name);
					break;
				case 'testing'				:
					$name = __('Testing', $this -> plugin_name);
					break;
				case 'confirmation'			:
					$name = __('Confirmation', $this -> plugin_name);
					break;
				case 'subscription'			:
					$name = __('Subscription', $this -> plugin_name);
					break;
				case 'unsubscription'		:
					$name = __('Unsubscription', $this -> plugin_name);
					break;
				case 'newsletter'			:
				default  					:
					$name = __('Newsletter', $this -> plugin_name);
					break;
			}
		}
		
		$name = apply_filters('newsletters_system_email', $name, $slug);
		return $name;
	}
	
	function section_name($slug = null) {
		$name = "";
		
		if (!empty($slug)) {
			switch ($slug) {
				case 'welcome'			:
					$name = __('Overview', $this -> plugin_name);
					break;
				case 'submitserial'		:
					$name = __('Submit Serial', $this -> plugin_name);
					break;
				case 'forms'			:
					$name = __('Subscribe Forms', $this -> plugin_name);
					break;
				case 'send'				:
					$name = __('Create Newsletter', $this -> plugin_name);
					break;
				case 'autoresponders'	:
					$name = __('Autoresponders', $this -> plugin_name);
					break;
				case 'autoresponderemails'	:
					$name = __('Autoresponder Emails', $this -> plugin_name);
					break;
				case 'lists'			:
					$name = __('Mailing Lists', $this -> plugin_name);
					break;
				case 'groups'			:
					$name = __('Groups', $this -> plugin_name);
					break;
				case 'subscribers'		:
					$name = __('Subscribers', $this -> plugin_name);
					break;
				case 'fields'			:
					$name = __('Custom Fields', $this -> plugin_name);
					break;
				case 'importexport'		:
					$name = __('Import/Export', $this -> plugin_name);
					break;
				case 'themes'			:
					$name = __('Templates', $this -> plugin_name);
					break;
				case 'templates'		:
					$name = __('Email Snippets', $this -> plugin_name);
					break;
				case 'templates_save'	:
					$name = __('Save Email Snippets', $this -> plugin_name);
					break;
				case 'queue'			:
					$name = __('Email Queue', $this -> plugin_name);
					break;
				case 'history'			:
					$name = __('Sent &amp; Draft Emails', $this -> plugin_name);
					break;
				case 'emails'			:
					$name = __('All Emails', $this -> plugin_name);
					break;
				case 'links'			:
					$name = __('Links', $this -> plugin_name);
					break;
				case 'clicks'			:
					$name = __('Clicks', $this -> plugin_name); 
					break;
				case 'orders'			:
					$name = __('Subscribe Orders', $this -> plugin_name);
					break;
				case 'settings'			:
					$name = __('General Configuration', $this -> plugin_name);
					break;
				case 'settings_subscribers'	:
					$name = __('Subscribers Configuration', $this -> plugin_name);
					break;
				case 'settings_templates'	:
					$name = __('System Emails Configuration', $this -> plugin_name);
					break;
				case 'settings_system'		:
					$name = __('System Configuration', $this -> plugin_name);
					break;
				case 'settings_tasks'		:
					$name = __('Scheduled Tasks', $this -> plugin_name); 
					break;
				case 'settings_updates'		:
					$name = __('Updates', $this -> plugin_name); 
					break;
				case 'settings_api'			:
					$name = __('API', $this -> plugin_name);
					break;
				case 'extensions'			:
					$name = __('Extensions', $this -> plugin_name);
					break;
				case 'extensions_settings'	:
					$name = __('Extensions Settings', $this -> plugin_name);
					break;
				case 'support'				:
					$name = __('Support &amp; Help', $this -> plugin_name);
					break;
				case 'lite_upgrade'			:
					$name = __('Upgrade to PRO', $this -> plugin_name);
					break;
			}
		}
		
		return $name;
	}
	
	function getppt($interval = null) {
		switch ($interval) {
			case 'daily'		:
				$t = "D";
				break;
			case 'weekly'		:
				$t = "W";
				break;
			case 'monthly'		:
			case '2months'		:
			case '3months'		:
			case 'biannually'	:
			case '9months'		:
				$t = "M";
				break;
			case 'yearly'		:
				$t = "Y";
				break;
			default				:
				$t = "D";
				break;
		}
		
		return $t;
	}
	
	function getpptd($interval) {
		switch ($interval) {
			case 'daily'		:
				$d = "1";
				break;
			case 'weekly'		:
				$d = "1";
				break;
			case 'monthly'		:
				$d = "1";
				break;
			case '2months'		:
				$d = "2";
				break;
			case '3months'		:
				$d = "3";
				break;
			case 'biannually'	:
				$d = "6";
				break;
			case '9months'		:
				$d = "9";
				break;
			case 'yearly'		:
				$d = "1";
				break;
			default				:
				$d = "1";
				break;
		}
		
		return $d;
	}

    function priority_val($priority_key) {
        switch ($priority_key) {
            case 1              :
                $priority_val = "High";
                break;
            case 3              :
                $priority_val = "Normal";
                break;
            case 5              :
                $priority_val = "Low";
                break;
            default             :
                $priority_val = "Normal";
                break;
        }

        return $priority_val;
    }
	
	function link($name = null, $href = null, $options = array()) {
		if (!empty($name) || $name == "0") {
			$defaults = array(
				'target' 		=> 	'_self', 
				'title' 		=> 	$name,
				'onclick'		=>	"",
				'class'			=>	"",
			);
			
			$r = wp_parse_args($options, $defaults);
			extract($r, EXTR_SKIP);
				
			ob_start();
			
			?>
			
			<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" onclick="<?php echo $onclick; ?>" title="<?php echo $title; ?>" target="<?php echo $target; ?>"><?php echo $name; ?></a>
			
			<?php
			
			$link = ob_get_clean();
			return $link;
		}
		
		return false;
	}
	
	function reCaptchaErrorMessage($errorCode = null) {
	    $messages = array(
		    'missing-input-secret'		=>	__('The secret parameter is missing.', $this -> plugin_name),
			'invalid-input-secret'		=>	__('The secret parameter is invalid or malformed.', $this -> plugin_name),
			'missing-input-response'	=>	__('Captcha code is missing', $this -> plugin_name),
			'invalid-input-response'	=>	__('Captcha code is invalid.', $this -> plugin_name),
	    );
	    
	    if (!empty($messages[$errorCode])) {
		    return $messages[$errorCode];
	    }
	    
	    return false;
    }
	
	function tabi() {
		global $wpmltabindex;
		if (empty($wpmltabindex) || !$wpmltabindex) { $wpmltabindex = 1; };
		return $wpmltabindex;
	}
	
	function tabindex($optinid = null, $onlynumber = false) {
		global $wpmltabindex;
		
		if (empty($wpmltabindex) || !$wpmltabindex) {
			$wpmltabindex = 1;
		}
		
		$wpmltabindex++;
		$string = $optinid . $wpmltabindex;
		$string = preg_replace("/[^0-9]+/si", "", $string);
		
		if (empty($onlynumber)) {
			$tabindex = 'tabindex="9' . $string . '"';
		} else {
			$tabindex = '9' . $string;
		}
		return $tabindex;
	}
	
	function gen_date($format = "Y-m-d H:i:s", $time = false) {
		if (empty($format)) {
			$format = get_option('date_format'); 
		} 
		
		$this -> set_timezone();
		$newtime = (empty($time)) ? time() : $time;
		return date_i18n($format, $newtime);
	}
	
	function gender($gender = null) {
		switch ($gender) {
			case 'male'			:
				return __('Male', $this -> plugin_name);
				break;
			case 'female'		:
				return __('Female', $this -> plugin_name);
				break;	
		}
	}
	
	function currency() {
		$currency = $this -> get_option('currency');
		$currencies = maybe_unserialize($this -> get_option('currencies'));		
		return $currencies[$currency]['symbol'];
	}
	
	function field_value($name = null, $language = false) {
		$value = "";
		
		if (!empty($name)) {				
			if ($mn = $this -> strip_mn($name)) {
				
				$model = $mn[1];
				$field = $mn[2];
				
				global ${$mn[1]}, $Db;
				
				if (!empty($Db -> {$model} -> data)) {
					if (is_array($Db -> {$model} -> data) && !empty($Db -> {$model} -> data[$model])) {					
						$value = $Db -> {$model} -> data[$model] -> {$field};
					} else {					
						$value = $Db -> {$model} -> data -> {$field};
					}
				} else {
					if (is_array(${$mn[1]} -> data) && !empty(${$mn[1]} -> data[$mn[1]])) {					
						$value = ${$mn[1]} -> data[$mn[1]] -> {$mn[2]};
					} else {					
						$value = ${$mn[1]} -> data -> {$mn[2]};
					}
				}
				
				if ($this -> language_do() && !empty($language)) {					
					if ($mn[2] == "fieldoptions") {												
						$alloptions = maybe_unserialize($value);
						$optionarray = array();
						
						if (!empty($alloptions)) {
							foreach ($alloptions as $alloption) {
								$alloptionsplit = $this -> language_split($alloption);
								$optionarray[] = trim($alloptionsplit[$language]);
							}
						}
						
						return trim(@implode("\r\n", $optionarray));
					} else {						
						return $this -> language_use($language, $value);
					}
				}

				/*if (is_array(${$mn[1]} -> data) && !empty(${$mn[1]} -> data[$mn[1]])) {					
					$value = ${$mn[1]} -> data[$mn[1]] -> {$mn[2]};
				} else {					
					$value = ${$mn[1]} -> data -> {$mn[2]};
				}
				
				if ($this -> language_do() && !empty($language)) {					
					if ($mn[2] == "fieldoptions") {												
						$alloptions = maybe_unserialize($value);
						$optionarray = array();
						
						if (!empty($alloptions)) {
							foreach ($alloptions as $alloption) {
								$alloptionsplit = $this -> language_split($alloption);
								$optionarray[] = trim($alloptionsplit[$language]);
							}
						}
						
						return trim(@implode("\r\n", $optionarray));
					} else {						
						return $this -> language_use($language, $value);
					}
				}*/
			}
		}

        return $value;
	}
	
	function has_field_error($name = null) {
		if (!empty($name)) {
			if ($mn = $this -> strip_mn($name)) {
				global ${$mn[1]}, $Db;
				
				$model = $mn[1];
				$field = $mn[2];
				
				if (!empty($Db -> {$model}() -> errors[$field])) {
					return true;
				} elseif (!empty(${$mn[1]} -> errors[$mn[2]])) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	function field_error($name = null) {
		if (!empty($name)) {		
			if ($mn = $this -> strip_mn($name)) {
				global ${$mn[1]}, $Db;
				
				$model = $mn[1];
				$field = $mn[2];
				
				if (!empty($Db -> {$model}() -> errors[$field])) {
					ob_start();
					echo '<div class="alert alert-danger ui-state-error ui-corner-all"><p><i class="fa fa-exclamation-triangle"></i> ' . $Db -> {$model}() -> errors[$field] . '</p></div>';
					return ob_get_clean();
				} elseif (!empty(${$mn[1]} -> errors[$mn[2]])) {
					ob_start();
					echo '<div class="alert alert-danger ui-state-error ui-corner-all"><p><i class="fa fa-exclamation-triangle"></i> ' . ${$mn[1]} -> errors[$mn[2]] . '</p></div>';
					return ob_get_clean();
				}
			}
		}
		
		return false;
	}
	
	function field_id($name = null) {
		if (!empty($name)) {
			if ($matches = $this -> strip_mn($name)) {
				$id = $matches[1] . '.' . $matches[2];
				return $id;
			}
		}
	
		return false;
	}
	
	function file_upload_error($code = 0) {
		if (!empty($code)) {
			switch ($code) {
				case 1			:
					$error = __('The uploaded file exceeds the PHP upload_max_filesize directive.', $this -> plugin_name);
					break;
				case 2			:
					$error = __('The uploaded file exceeds the max_file_size directive specified in the form.', $this -> plugin_name);
					break;
				case 3			:
					$error = __('The uploaded file was only partially uploaded.', $this -> plugin_name);
					break;
				case 4			:
					$error = __('No file was uploaded.', $this -> plugin_name);
					break;
				case 6			:
					$error = __('Missing a temporary folder.', $this -> plugin_name);
					break;
				case 7			:
					$error = __('Failed to write file to disk.', $this -> plugin_name);
					break;
				case 8			:
					$error = __('A PHP extension stopped the file upload.', $this -> plugin_name);
					break;
				default			:
					$error = __('An error occurred. Please try again.', $this -> plugin_name);
					break;
			}
			
			return $error;
		}
		
		return false;
	}
	
	function sanitize($string = null, $sep = '-') {
		if (!empty($string)) {
			//$string = ereg_replace("[^0-9a-z" . $sep . "]", "", strtolower(str_replace(" ", $sep, $string)));
			$string = strtolower(preg_replace("/[^0-9A-Za-z" . $sep . "]/si", "", str_replace(" ", $sep, $string)));
			$string = preg_replace("/" . $sep . "[" . $sep . "]*/si", $sep, $string);
			
			return $string;
		}
	
		return false;
	}
	
	function strip_mn($name = null) {
		if (!empty($name)) {
			if (preg_match("/(.*?)\[(.*?)\]/si", $name, $matches)) {
				return $matches;
			}
		}
	
		return false;
	}
	
	function truncate($text = null, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {		
		if (is_array($ending)) {
			extract($ending);
		}
		
		if ($considerHtml) {
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}

			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';

			foreach ($lines as $line_matchings) {
				if (!empty($line_matchings[1])) {
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					} elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
					} elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					$truncate .= $line_matchings[1];
				}

				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length > $length) {
					$left = $length - $total_length;
					$entities_length = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}

				if ($total_length >= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}

		if (!$exact) {
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				$truncate = substr($truncate, 0, $spacepos);
			}
		}

		$truncate .= $ending;

		if ($considerHtml) {
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}
	
	function queryString($params, $name = null) {
		$ret = "";
		foreach ($params as $key => $val) {
			if (is_array($val)) {
				if ($name == null) {
					$ret .= $this -> queryString($val, $key);
				} else {
					$ret .= $this -> queryString($val, $name . "[$key]");   
				}
			} else {
				if ($name != null) {
					$ret .= $name . "[$key]" . "=" . $val . "&";
				} else {
					$ret .= $key . "=" . $val . "&";
				}
			}
		}
		
		return rtrim($ret, "&");   
	} 
	
	function retainquery($add = null, $old_url = null, $endslash = true, $onlyquery = false) {
		if (is_array($add)) {
			$add = implode("&", $add);
		}
	
		$url = (empty($old_url)) ? $_SERVER['REQUEST_URI'] : rtrim($old_url, '&');
		$url = rawurldecode($url);
		$url = preg_replace("/\&?wpmlmessage\=(.*)\&?/si", "", $url);
		//$url = preg_replace("/\&?wpmlupdated\=(.*)\&?/si", "", $url);
		
		$urls = @explode("?", $url);
		$add = ltrim($add, '&');
		
		$url_parts = @parse_url($url);
		if (!empty($url_parts['query'])) parse_str($url_parts['query'], $path_parts);
		$add = str_replace("&amp;", "&", $add);
		parse_str($add, $add_parts);
		
		if (empty($path_parts) || !is_array($path_parts)) {
			$path_parts = array();	
		}
			
		if (!empty($add_parts) && is_array($add_parts)) {
			foreach ($add_parts as $addkey => $addvalue) {
				//$path_parts[$addkey] = stripslashes($addvalue);
				$path_parts[$addkey] = $addvalue;
			}
		}

		$querystring = $this -> queryString($path_parts);
		
		//$urls[1] = preg_replace("/[\&|\?]" . $this -> pre . "message\=([0-9a-z-_+]*)/i", "", $urls[1]);
		//$urls[1] = preg_replace("/[\&|\?]page\=/si", "", $urls[1]);
		
		$url = $urls[0];
		$url .= '?';
		
		if (!empty($querystring)) {
			$url .= '&' . $querystring;
			
			if ($onlyquery) {
				return $querystring;
			}
		}
				
		return preg_replace("/\?(\&)?/si", "?", $url);
	}
}
}

?>