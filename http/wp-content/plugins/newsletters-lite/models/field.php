<?php

if (!class_exists('wpmlField')) {
	class wpmlField extends wpMailPlugin {
	
		var $id;
		var $title;
		var $slug;
		var $type = 'text';
		var $options;
		var $required = 'Y';
		var $default;
		var $created = '0000-00-00 00:00:00';
		var $modified = '0000-00-00 00:00:00';
	
		var $insertid;
		var $name = 'wpmlfield';
		var $model = 'Field';
		var $controller = 'fields';	
		var $error = array();
		var $data = array();
	
		var $table_fields = array(
			'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'title'			=>	"VARCHAR(255) NOT NULL DEFAULT ''",
			'caption'		=>	"TEXT NOT NULL",
			'watermark'		=>	"TEXT NOT NULL",
			'slug'			=>	"VARCHAR(100) NOT NULL DEFAULT ''",
			'type'			=>	"VARCHAR(255) NOT NULL DEFAULT 'text'",
			'hidden_type'	=>	"VARCHAR(100) NOT NULL DEFAULT ''",
			'hidden_value'	=>	"TEXT NOT NULL",
			'fieldoptions'	=>	"TEXT NOT NULL",
			'filetypes'		=>	"TEXT NOT NULL",
			'filesizelimit'	=>	"TEXT NOT NULL",
			'required'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'Y'",
			'errormessage'	=>	"TEXT NOT NULL",
			'invalidmessage'	=> "TEXT NOT NULL",
			'display'		=>	"ENUM('always','specific') NOT NULL DEFAULT 'specific'",
			'validation'	=>	"TEXT NOT NULL",
			'regex'			=>	"TEXT NOT NULL",
			'order'			=>	"INT(11) NOT NULL DEFAULT '0'",
			'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'			=>	"PRIMARY KEY (`id`), INDEX(`slug`), INDEX(`type`), INDEX(`required`)"
		);
		
		var $tv_fields = array(
			'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'title'			=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'caption'		=>	array("TEXT", "NOT NULL"),
			'watermark'		=>	array("TEXT", "NOT NULL"),
			'slug'			=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'type'			=>	array("VARCHAR(255)", "NOT NULL DEFAULT 'text'"),
			'hidden_type'	=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'hidden_value'	=>	array("TEXT", "NOT NULL"),
			'fieldoptions'	=>	array("TEXT", "NOT NULL"),
			'filetypes'		=>	array("TEXT", "NOT NULL"),
			'filesizelimit'	=>	array("TEXT", "NOT NULL"),
			'required'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'Y'"),
			'errormessage'	=>	array("TEXT", "NOT NULL"),
			'invalidmessage'	=>	array("TEXT", "NOT NULL"),
			'display'		=>	array("ENUM('always','specific')", "NOT NULL DEFAULT 'specific'"),
			'validation'	=>	array("TEXT", "NOT NULL"),
			'regex'			=>	array("TEXT", "NOT NULL"),
			'order'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'			=>	"PRIMARY KEY (`id`), INDEX(`slug`), INDEX(`type`), INDEX(`required`)"
		);
		
		var $indexes = array('slug', 'type', 'required');
	
		function __construct($data = array()) {
			global $wpdb, $Db, $FieldsList;
			$this -> sections = (object) $this -> sections;
			$this -> table = $this -> pre . $this -> controller;	
		
			if (!empty($data)) {
				foreach ($data as $key => $val) {				
					$data = (object) $data;
					$field_id = $data -> id;
					
					switch ($key) {
						case 'errormessage'		:
							if (empty($val)) {
								$this -> errormessage = sprintf(__('Please fill in %s', $this -> plugin_name), $this -> title);
							} else {
								$this -> errormessage = $val;
							}
							break;
						case 'fieldoptions'		:
							$this -> initialize_classes();
							$this -> newfieldoptions = false;
							if (!empty($field_id)) {			
								if ($fieldoptions = $this -> Option() -> find_all(array('field_id' => $field_id), false, array('order', "ASC"))) {
									if (is_admin() && $_GET['page'] == $this -> sections -> fields) {
										$this -> newfieldoptions = $fieldoptions;
									} else {
										foreach ($fieldoptions as $fieldoption) {
											$this -> newfieldoptions[$fieldoption -> id] = $fieldoption -> value;
										}
									}
								}
							}
							
							$this -> {$key} = $val;
							break;
						case 'regex'			:
							$this -> {$key} = $val;
							break;
						default 				:
							//$this -> {$key} = stripslashes_deep($val);
							$this -> {$key} = $val;
							break;
					}
				}
			}
			
			$Db -> model = $this -> model;
		}
		
		function title_by_slug($slug = null) {
			global $wpdb, $Db;
			
			if (!empty($slug)) {
				$query = "SELECT `title` FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `slug` = '" . $slug . "'";
				
				if ($title = $wpdb -> get_var($query)) {
					return __($title);
				} else {
					switch ($slug) {
						case 'id'					:
							$title = __('ID', $this -> plugin_name);
							break;
						case 'registered'			:
							$title = __('Registered', $this -> plugin_name);
							break;
						case 'ip_address'			:
							$title = __('IP Address', $this -> plugin_name);
							break;
						case 'user_id'				:
							$title = __('User ID', $this -> plugin_name);
							break;
						case 'emailssent'			:
							$title = __('Emails Sent', $this -> plugin_name);
							break;
						case 'format'				:
							$title = __('Format', $this -> plugin_name);
							break;
						case 'cookieauth'			:
							$title = __('Cookie String', $this -> plugin_name);
							break;
						case 'authkey'				:
							$title = __('Auth String', $this -> plugin_name);
							break;
						case 'authinprog'			:
							$title = __('Auth In Progress', $this -> plugin_name);
							break;
						case 'bouncecount'			:
							$title = __('Bounce Count', $this -> plugin_name);
							break;
						case 'mandatory'			:
							$title = __('Mandatory', $this -> plugin_name);
							break;
						case 'device'				:
							$title = __('Device', $this -> plugin_name);
							break;
						case 'created'				:
							$title = __('Created Date', $this -> plugin_name);
							break;
						case 'modified'				:
							$title = __('Modified Date', $this -> plugin_name);
							break;
					}
					
					return $title;
				}
			}
			
			return false;
		}
		
		function check_default_fields() {
			global $Db, $wpdb, $FieldsList;
		
			if (is_admin()) {
				if (!$emailfield = $this -> email_field()) {
					$this -> init_fieldtypes();
				
					$emailfielddata = array(
						$this -> model =>	array(
							'title' 		=> 	__('Email Address', $this -> plugin_name),
							'slug'			=> 	"email",
							'type'			=>	"text",
							'required'		=>	"Y",
							'errormessage'	=>	__('Please fill in your email address', $this -> plugin_name),
							'display'		=>	"always",
							'order'			=>	"0",
						),
					);
					
					$this -> save($emailfielddata);
					$emailfield_id = $this -> insertid;
				} else {
					$emailfield_id = $emailfield -> id;	
				}
				
				$efieldslistquery = "SELECT * FROM " . $wpdb -> prefix . $FieldsList -> table . " WHERE `special` = 'email'";
				
				$query_hash = md5($efieldslistquery);
				if ($ob_efieldslist = $this -> get_cache($query_hash)) {
					$efieldslist = $ob_efieldslist;
				} else {
					$efieldslist = $wpdb -> get_row($efieldslistquery);
					$this -> set_cache($query_hash, $efieldslist);
				}
				
				if (!$efieldslist) {
					$efieldslistdata = array(
						'field_id'				=>	$emailfield_id,
						'list_id'				=>	"0",
						'special'				=>	"email",
					);	
					
					$FieldsList -> save($efieldslistdata);
				}
				
				if (!$listfield = $this -> list_field()) {
					$this -> init_fieldtypes();
				
					$listfielddata = array(
						$this -> model 		=>	array(
							'title'				=>	__('Mailing List', $this -> plugin_name),
							'slug'				=>	"list",
							'type'				=>	"special",
							'required'			=>	"Y",
							'errormessage'		=>	__('Please select a list', $this -> plugin_name),
							'display'			=>	"always",
							'order'				=>	"1",
						)
					);
					
					$this -> save($listfielddata);
					$listfield_id = $this -> insertid;
				} else {
					$listfield_id = $listfield -> id;
				}
				
				$lfieldslistquery = "SELECT * FROM " . $wpdb -> prefix . $FieldsList -> table . " WHERE `special` = 'list'";
				
				$query_hash = md5($lfieldslistquery);
				if ($ob_lfieldslist = $this -> get_cache($query_hash)) {
					$lfieldslist = $ob_lfieldslist;
				} else {
					$lfieldslist = $wpdb -> get_row($lfieldslistquery);
					$this -> set_cache($query_hash, $lfieldslist);
				}
				
				if (!$lfieldslist) {
					$lfieldslistdata = array(
						'field_id'				=>	$listfield_id,
						'list_id'				=>	"0",
						'special'				=>	"list",
					);	
					
					$FieldsList -> save($lfieldslistdata);
				}
			}
			
			return true;
		}
		
		function email_field() {
			global $wpdb;
			
			$emailfieldquery = "SELECT * FROM " . $wpdb -> prefix . $this -> table . " WHERE slug = 'email'";
			
			$query_hash = md5($emailfieldquery);
			if ($ob_emailfield = $this -> get_cache($query_hash)) {
				return $ob_emailfield;
			}
			
			if ($emailfield = $wpdb -> get_row($emailfieldquery)) {
				$emailfield -> error = $emailfield -> errormessage;
				$this -> set_cache($query_hash, $emailfield);
				return $emailfield;
			}
			
			return false;
		}
		
		function email_field_id() {
			if ($emailfield = $this -> email_field()) {
				return $emailfield -> id;
			}
			
			return false;
		}
		
		function list_field() {
			global $wpdb;
			$listfieldquery = "SELECT * FROM " . $wpdb -> prefix . $this -> table . " WHERE slug = 'list'";
			
			$query_hash = md5($listfieldquery);
			if ($ob_listfield = $this -> get_cache($query_hash)) {
				return $ob_listfield;
			}
			
			if ($listfield = $wpdb -> get_row($listfieldquery)) {
				$listfield -> error = $listfield -> errormessage;
				$this -> set_cache($query_hash, $listfield);
				return $listfield;
			}
			
			return false;
		}
		
		function list_field_id() {
			if ($listfield = $this -> list_field()) {
				return $listfield -> id;
			}
			
			return false;
		}
		
		function find($conditions = array()) {
			global $wpdb;
			
			$query = "SELECT * FROM `" . $wpdb -> prefix . "`";
			
			if (!empty($conditions)) {
				$query .= " WHERE";
				$c = 1;
				
				foreach ($conditions as $ckey => $cval) {
					$query .= " `" . $ckey . "` = '" . $cval . "'";
					
					if ($c < count($conditions)) {
						$query .= " AND";
					}
				
					$c++;
				}
			}
			
			$query .= " LIMIT 1";
			
			$query_hash = md5($query);
			if ($ob_field = $this -> get_cache($query_hash)) {
				return $ob_field;
			}
			
			if ($field = $wpdb -> get_row($query)) {
				if (!empty($field)) {
					$data = $this -> init_class('wpmlField', $field);
					$this -> set_cache($query_hash, $data);
					return $data;
				}
			}
			
			return false;
		}
		
		function select($conditions = false) {
			global $Db, $wpdb;		
			$select = array();
			
			$Db -> model = $this -> model;
			if ($fields = $Db -> find_all($conditions, false, array('order', "ASC"))) {
				if (!empty($fields)) {
					foreach ($fields as $field) {
						if ($field -> slug != "email" && $field -> slug != "list") {
							$select[$field -> id] = __($field -> title);
						}
					}
				}
			}
			
			return $select;
		}
		
		function save_field($fieldname = null, $value = null, $field_id = null) {
			global $wpdb;
		
			if (!empty($fieldname)) {
				if ($value != "") {
					if (!empty($field_id)) {					
						if ($field = $this -> get($field_id)) {
							$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `" . $fieldname . "` = '" . $value . "' WHERE `id` = '" . $field_id . "' LIMIT 1";
							
							if ($wpdb -> query($query)) {
								return true;
							}
						}
					}
				}
			}
			
			return false;
		}
		
		function titleslug_exists($title = null) {
			global $Db, $Html;
			$Db -> model = $this -> model;
			
			if (!empty($title)) {
				$slug = $Html -> sanitize($title, "_");
				
				if ($Db -> find(array('slug' => $slug), array('id'), false, false, false)) {
					return true;
				}
			}
		
			return false;	
		}
		
		function slug_exists($slug) {
			global $Db, $Html;
			$Db -> model = $this -> model;
			
			if (!empty($slug)) {			
				if ($Db -> find(array('slug' => $slug), array('id'), false, false, false)) {
					return true;
				}
			}
		
			return false;
		}
		
		function save($data = array(), $validate = true) {
			global $wpdb, $Db, $Html, $Subscriber, $FieldsList;
			
			$defaults = array(
				'fieldoptions'			=>	false,
				'order'					=>	0,
				'created'				=>	$Html -> gen_date(),
				'modified'				=>	$Html -> gen_date(),
			);
			
			$this -> data[$this -> model] = (object) $data[$this -> model];
			
			if ($this -> language_do()) {
				$this -> data[$this -> model] -> title = $this -> language_join($this -> data[$this -> model] -> title, false, true);
				$this -> data[$this -> model] -> caption = $this -> language_join($this -> data[$this -> model] -> caption, false, true);
				$this -> data[$this -> model] -> watermark = $this -> language_join($this -> data[$this -> model] -> watermark, false, true);
				$this -> data[$this -> model] -> errormessage = $this -> language_join($this -> data[$this -> model] -> errormessage, false, true);
			}
			
			if (!empty($this -> data[$this -> model] -> fieldoptions)) {
				$fieldoptions_data = $this -> data[$this -> model] -> fieldoptions;
				$el = $this -> language_getlanguages();
				$language_default = $this -> language_default();
				
				if ($this -> language_do()) {
					$fieldoptions = array();
					$newfieldoptions = array();
					
					//for ($n = 0; $n < count($fieldoptions_data[$language_default]); $n++) {
					foreach ($el as $language) {
						/*if (!empty($fieldoptions_data[$language][$n])) {							
							$newfieldoptions[$n][$language] = stripslashes($fieldoptions_data[$language][$n]);
						}*/
						
						foreach ($fieldoptions_data[$language] as $fieldoptions_id => $fieldoptions_value) {
							if (!empty($fieldoptions_value)) {
								$fieldoptions[$fieldoptions_id][$language] = stripslashes($fieldoptions_value['value']);	
								
								$newfieldoptions[$fieldoptions_id]['id'] = $fieldoptions_value['id'];
								$newfieldoptions[$fieldoptions_id]['value'] = $fieldoptions[$fieldoptions_id];
							}
						}
					}	
					//}
					
					foreach ($fieldoptions as $key => $fieldoption) {
						$fieldoptions[$key] = $this -> language_join($fieldoption);
						$newfieldoptions[$key]['value'] = $this -> language_join($newfieldoptions[$key]['value']);
					}
					
					$this -> data[$this -> model] -> newfieldoptions = $newfieldoptions;
					//$fieldoptions = $newfieldoptions;
				} else {
					$fieldoptions = array();
					$newfieldoptions = array();
					
					foreach ($this -> data[$this -> model] -> fieldoptions as $fkey => $fieldoption) {
						if (!empty($fieldoption['value'])) {
							$fieldoptions[$fkey] = $fieldoption['value'];
							$newfieldoptions[$fkey]['value'] = $fieldoption['value'];
						}
					}
				}
			}
			
			$data = $this -> data;
			$r = wp_parse_args($data[$this -> model], $defaults);
			extract($r, EXTR_SKIP);
		
			if (!empty($data)) {			
				if ($validate == true) {
					if (!empty($id)) {
						$Db -> model = $this -> model;
						$oldfield = $Db -> find(array('id' => $id), false, false, false);	
						if (empty($title)) { $this -> errors['title'] = __('Please fill in a title', $this -> plugin_name); }
						elseif ($oldfield -> slug != "email") {
							if ($Html -> sanitize($title, '_') == "email") { $this -> errors['title'] = __('You cannot create an email custom field.', $this -> plugin_name); }
						}
					} else {
						if (empty($title)) { $this -> errors['title'] = __('Please fill in a title', $this -> plugin_name); }
						elseif ($Html -> sanitize($title, '_') == "email") { $this -> errors['title'] = __('You cannot create an email custom field.', $this -> plugin_name); }
						elseif (strlen($Html -> sanitize($title, '_')) > 64) { $this -> errors['title'] = __('This title is too long, please keep below 64 characters.', $this -> plugin_name); }
					}
					
					include $this -> plugin_base() . DS . 'includes' . DS . 'variables.php';
					
					if (empty($slug)) { $this -> errors['slug'] = __('Please fill in a slug/nicename for this custom field.', $this -> plugin_name); }
					elseif (empty($id) && empty($oldfield) && $this -> slug_exists($slug)) { $this -> errors['slug'] = __('A custom field with this slug already exists, please choose a different one.', $this -> plugin_name); }
					elseif (in_array($slug, $wordpress_reserved_terms)) { $this -> errors['slug'] = sprintf(__('"%s" is a reserved term, please choose something else', $this -> plugin_name), $slug); }
					
					if (empty($required)) { $this -> errors['required'] = __('Please choose a required status', $this -> plugin_name); }
					else {
						if ($required == "Y") {
							if (empty($errormessage)) { $this -> errors['errormessage'] = __('Please fill in an error message', $this -> plugin_name); }
						}
					}
					
					if (empty($display)) { $this -> errors['display'] = __('Please choose the display for this field.', $this -> plugin_name); }
					
					if (empty($type)) { $this -> errors['type'] = __('Please choose a field type', $this -> plugin_name); }
					else {				
						if ($type == "select" || $type == "radio" || $type == "checkbox") {
							if (empty($fieldoptions)) {
								$this -> errors['fieldoptions'] = __('Please fill in some options', $this -> plugin_name);
							} else {							
								$fieldoptions = maybe_serialize($fieldoptions);							
								$this -> data[$this -> model] -> fieldoptions = $fieldoptions;
							}
						} elseif ($type == "hidden") {						
							switch ($hidden_type) {
								case 'predefined'				:
									if (!empty($hidden_value_predefined)) {
										$this -> data[$this -> model] -> hidden_value = $hidden_value = $hidden_value_predefined;
									} else {
										$this -> errors['hidden_value'] = __('Please fill in a value', $this -> plugin_name);
									}
									break;
								case 'custom'					:
									//do nothing...
									break;
								default 						:
									if (empty($hidden_value)) {
										$this -> errors['hidden_value'] = __('Please fill in a value', $this -> plugin_name);
									}	
									break;
							}
						}
					}	
				}
				
				$this -> errors = apply_filters('newsletters_field_validation', $this -> errors, $this -> data[$this -> model]);
				
				if (empty($this -> errors)) {
					$created = $modified = $this -> gen_date();
					
					if (empty($slug)) {
						$slug = $Html -> sanitize($title, '_');
					}
				
					if (!empty($id)) {
						$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `title` = '" . $title . "', `slug` = '" . $slug . "', `display` = '" . $display . "', `required` = '" . $required . "', `errormessage` = '" . $errormessage . "', `validation` = '" . $validation . "', `regex` = '" . $regex . "', `type` = '" . $type . "', `hidden_type` = '" . $hidden_type . "', `hidden_value` = '" . $hidden_value . "', `filetypes` = '" . $filetypes . "', `filesizelimit` = '" . $filesizelimit . "', `fieldoptions` = '" . esc_sql($fieldoptions) . "', `modified` = '" . $modified . "', `caption` = '" . $caption . "', `watermark` = '" . $watermark . "' WHERE `id` = '" . $id . "' LIMIT 1;";
						$field_old = $this -> get($id);
					} else {
						$query1 = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table . "` (";
						$query2 = "";
						$c = 1;
						
						$oldkeyattr = $this -> table_fields['key'];
						unset($this -> table_fields['key']);
						$oldidattr = $this -> table_fields['id'];
						unset($this -> table_fields['id']);
						
						foreach (array_keys($this -> table_fields) as $field) {
							if (!empty(${$field}) && ${$field} != "0") {
								$query1 .= "`" . $field . "`";
								$query2 .= "'" . esc_sql(${$field}) . "'";
								
								if ($c < count($this -> table_fields)) {
									$query1 .= ", ";
									$query2 .= ", ";
								}
							}
							
							$c++;
						}
						
						$this -> table_fields['id'] = $oldidattr;
						$this -> table_fields['key'] = $oldkeyattr;
						
						$query1 .= ") VALUES (";
						$query = $query1 . $query2 . ");";
					}
	
					if ($wpdb -> query($query)) {										
						$this -> insertid = (empty($id)) ? $wpdb -> insert_id : $id;
						$field_id = $this -> insertid;
						
						if (!empty($Subscriber -> table_fields[$slug])) {
							$attributes = $Subscriber -> table_fields[$slug];
						} elseif (!empty($type) && $type == "pre_date") {
							$attributes = "DATE NOT NULL DEFAULT '0000-00-00'";
						} else {
							$attributes = "TEXT NOT NULL";	
						}
	
						if (!empty($id)) {
							$FieldsList -> delete_all(array('field_id' => $this -> insertid));
							$this -> change_field($Subscriber -> table, $field_old -> slug, $slug, $attributes);
						} else {
							$this -> insert_id = $data['id'] = $wpdb -> insert_id;						
							$this -> add_field($Subscriber -> table, $slug, $attributes);
						}
						
						// Field options
						if (!empty($newfieldoptions)) {
							$this -> initialize_classes();
							
							$fieldoptions_order = array_flip(explode(",", $_POST['Field']['fieldoptions_order']));
							unset($fieldoptions_order[0]);
							
							$o = (count($fieldoptions_order) - 1);
							foreach ($newfieldoptions as $newfieldoption) {
								if (!empty($newfieldoption['value'])) {
									$newfieldoption_data = array(
										//'order'						=>	$o,
										'value'						=>	$newfieldoption['value'],
										'field_id'					=>	$field_id,
									);
									
									if (!empty($newfieldoption['id'])) {
										$newfieldoption_data['id'] = $newfieldoption['id'];
										$newfieldoption_data['order'] = $fieldoptions_order[$newfieldoption['id']];
									} else {
										$newfieldoption_data['order'] = $o;
										$o++;
									}
									
									$Db -> model = $this -> Option() -> model;
									$this -> Option() -> save($newfieldoption_data);								
									$this -> Option() -> errors = false;
								}
							}
						}
						
						if ($display == "always") {
							$Db -> model = $FieldsList -> model;
							$Db -> delete_all(array('field_id' => $this -> insertid));
							
							$fl_data = array('field_id' => $this -> insertid, 'list_id' => "0");
							$FieldsList -> save($fl_data, false); 
						} else {
							if (!empty($mailinglists)) {					
								foreach ($mailinglists as $mailinglist_id) {
									$fl_data = array('field_id' => $this -> insertid, 'list_id' => $mailinglist_id);
									$FieldsList -> save($fl_data, false);
								}
							} else {
								$fl_data = array('field_id' => $this -> insertid, 'list_id' => "0");
								$FieldsList -> save($fl_data, false);
							}
						}
						
						do_action($this -> pre . '_wpml_field_saved', $this -> insertid, $data);
						
						return true;
					}
				}
			}
			
			return false;
		}
		
		function validate_optin($data = array(), $type = 'subscribe') {	
			global $wpdb, $wpcoDb, $Field, $FieldsList, $Html;
			include $this -> plugin_base() . DS . 'includes' . DS . 'variables.php';
				
			if (empty($data['form_id'])) {
				if ($fields = $FieldsList -> fields_by_list($data['list_id'], "order", "ASC", true, true)) {												
					if (!empty($fields)) {				
						foreach ($fields as $field) {																			
							if ($field -> required == "Y") {													
								if (empty($field -> errormessage)) {
									$field -> errormessage = __('Please fill in ', $this -> plugin_name) . $field -> title;
								}
								
								switch ($field -> type) {
									case 'file'				:														
										if (empty($_FILES[$field -> slug]['name']) && !empty($_POST['oldfiles'][$field -> slug])) {
											$data[$field -> slug] = $_POST['oldfiles'][$field -> slug];
										} else {
											if (empty($_FILES[$field -> slug]['name'])) {
												$this -> errors[$field -> slug] = __($field -> errormessage);
											} elseif (!empty($_FILES[$field -> slug]['error']) && $_FILES[$field -> slug]['error'] > 0) {
												$this -> errors[$field -> slug] = $Html -> file_upload_error($_FILES[$field -> slug]['error']);	
											}	
										}
										break;
									case 'pre_date'			:															
										//if (empty($data[$field -> slug]['y']) || empty($data[$field -> slug]['m']) || empty($data[$field -> slug]['d'])) {
										if (empty($data[$field -> slug])) {
											$this -> errors[$field -> slug] = __($field -> errormessage);
										}
									case 'special'			:
										switch ($field -> slug) {
											case 'list'				:
												if (empty($data['list_id']) && $type == "subscribe") {
													$this -> errors['list_id'] = __($field -> errormessage);
												}
												break;
										}
										break;
									default					:														
										if (empty($field -> validation) || $field -> validation == "notempty") {
											if (empty($data[$field -> slug])) {
												$this -> errors[$field -> slug] = __($field -> errormessage);
											}
										} else {
											if (!empty($field -> validation)) {									
												$regex = ($field -> validation == "custom") ? $field -> regex : $validation_rules[$field -> validation]['regex'];										
												if (!preg_match($regex, $data[$field -> slug])) {
													$this -> errors[$field -> slug] = __($field -> errormessage);
												}
											}
										}
										break;
								}
							}
							
							if (!empty($field -> type) && $field -> type == "file") {						
								if (empty($_FILES[$field -> slug]['name']) && !empty($data['oldfiles'][$field -> slug])) {
									$data[$field -> slug] = $data['oldfiles'][$field -> slug];
								} elseif (!empty($_FILES[$field -> slug]['name'])) {
									if (!function_exists('wp_handle_upload')){
								        require_once(ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'file.php');
								    }
								    
								    $upload_overrides = array('test_form' => false);									
									$uploadedfile = $_FILES[$field -> slug];									
									$file_info = wp_handle_upload($uploadedfile, $upload_overrides);
									
									if ($file_info && empty($file_info['error'])) {
										$data[$field -> slug] = $file_info['url'];
									} else {
										$this -> errors[$field -> slug] = $file_info['error'];
									}
								}
							}
							
							if (!empty($field -> type) && $field -> type == "pre_date") {
								if (!empty($data[$field -> slug])) {
									$data[$field -> slug] = date_i18n("Y-m-d", strtotime($data[$field -> slug]));
								}
							}
						}
					}
				}
			} elseif (!empty($data['form_id'])) {
				if ($form = $this -> Subscribeform() -> find(array('id' => $data['form_id']))) {
					if (!empty($form -> form_fields)) {
						foreach ($form -> form_fields as $form_field) {
							if ($field = $this -> get($form_field -> field_id)) {								
								if (empty($form_field -> errormessage)) {
									$form_field -> errormessage = $field -> errormessage;
								}
								
								switch ($field -> type) {
									case 'file'						:									
										if (!empty($_FILES[$field -> slug]['name'])) {							
											if (!function_exists('wp_handle_upload')){
										        require_once(ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'file.php');
										    }
										    
										    $upload_overrides = array('test_form' => false);									
											$uploadedfile = $_FILES[$field -> slug];									
											$file_info = wp_handle_upload($uploadedfile, $upload_overrides);
											
											if ($file_info && empty($file_info['error'])) {
												$data[$field -> slug] = $file_info['url'];
											} else {
												$this -> errors[$field -> slug] = $file_info['error'];
											}
										} elseif (!empty($_POST['oldfiles'][$field -> slug])) {
											$data[$field -> slug] = $_POST['oldfiles'][$field -> slug];
										} else {
											$this -> errors[$field -> slug] = __($form_field -> errormessage);
										}
										break;
									case 'special'					:
										if (!empty($form_field -> required)) {
											switch ($field -> slug) {
												case 'list'				:
													if (empty($data['list_id']) && $type == "subscribe") {
														$this -> errors[$field -> slug] = __($form_field -> errormessage);
													}
													break;
											}
										}
										break;
									default 						:
										if (!empty($form_field -> required)) {
											if (!empty($field -> validation) && $field -> validation == "notempty") {
												if (empty($data[$field -> slug])) {
													$this -> errors[$field -> slug] = __($form_field -> errormessage);
												}
											} else {
												if (!empty($field -> validation)) {									
													$regex = ($field -> validation == "custom") ? $field -> regex : $validation_rules[$field -> validation]['regex'];										
													if (!preg_match($regex, $data[$field -> slug])) {
														$this -> errors[$field -> slug] = __($field -> errormessage);
													}
												}
											}
										}
										break;
								}		
							}
						}
					}
				}
			}
			
			$data = apply_filters('newsletters_field_validate_optin', $data, $this -> errors);
			
			return $data;
			//return $this -> errors;
		}
		
		function delete($field_id = null) {
			global $wpdb, $Db, $Subscriber, $FieldsList;
			
			if (!empty($field_id)) {		
				$oldmodel = $Db -> model;
				$Db -> model = $this -> model;
				
				if ($field = $Db -> find(array('id' => $field_id))) {			
					if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . $field_id . "' LIMIT 1")) {					
						$this -> delete_field($Subscriber -> table, $field -> slug);					
						$FieldsList -> delete_all(array('field_id' => $field_id));
						return true;
					}
				}
			}
			
			return false;
		}
		
		/**
		 * Removes multiple fields by ID.
		 * @param $array ARRAY An array of field record IDs
		 * @return BOOLEAN Either true or false based on the outcome
		 *
		 **/
		function delete_array($array = array()) {
			global $wpdb;
				
			if (!empty($array)) {
				foreach ($array as $field_id) {
					$fieldquery = "SELECT * FROM " . $wpdb -> prefix . $this -> table . " WHERE id = '" . $field_id . "'";
					if ($field = $wpdb -> get_row($fieldquery)) {
						if ($field -> slug != "email") {
							$this -> delete($field_id);
						}
					}
				}
				
				return true;
			}
			
			return false;
		}
		
		function get($field_id = null) {
			global $wpdb;
		
			if (!empty($field_id)) {
				$query = "SELECT * FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `id` = '" . $field_id . "' LIMIT 1";
				
				$query_hash = md5($query);
				if ($ob_field = $this -> get_cache($query_hash)) {
					return $ob_field;
				}
			
				if ($field = $wpdb -> get_row($query)) {
					$this -> data = (array) $this -> data;
					$this -> data[$this -> model] = $this -> init_class($this -> model, $field);
					$this -> set_cache($query_hash, $this -> data[$this -> model]);
					return $this -> data[$this -> model];
				}
			}
			
			return false;
		}
		
		function get_all($fields = array()) {
			global $wpdb;
			
			$fields = (empty($fields)) ? "*" : $fields;
			
			if ($fields != "*") {
				if (is_array($fields)) {
					$selectfields = "";
					$i = 1;
					
					foreach ($fields as $field) {
						$selectfields .= "`" . $field . "`";
						
						if ($i < count($fields)) {
							$selectfields .= ", ";
						}
						
						$i++;
					}
				} else {
					$selectfields = "*";
				}
			} else {
				$selectfields = "*";
			}
			
			$query = "SELECT " . $selectfields . " FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `slug` != 'email' AND `slug` != 'list' ORDER BY `order` ASC";
			
			$query_hash = md5($query);
			if ($ob_fields = $this -> get_cache($query_hash)) {
				return $ob_fields;	
			}
			
			if ($fields = $wpdb -> get_results($query)) {
				if (!empty($fields)) {
					$data = array();
				
					foreach ($fields as $field) {
						$data[] = $this -> init_class($this -> model, $field);
					}
					
					$this -> set_cache($query_hash, $data);
					return $data;
				}
			}
			
			return false;
		}
	}
}

?>