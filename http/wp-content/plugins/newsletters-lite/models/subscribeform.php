<?php

if (!class_exists('wpmlSubscribeform')) {
	class wpmlSubscribeform extends wpmlDbHelper {
		var $model = 'Subscribeform';
		var $controller = 'forms';
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'title'					=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'ajax'					=>	array("INT(1)", "NOT NULL DEFAULT '0'"),
			'buttontext'			=>	array("TEXT", "NOT NULL"),
			'confirmationtype'		=>	array("VARCHAR(100)", "NOT NULL DEFAULT 'message'"),
			'confirmation_message'	=>	array("TEXT", "NOT NULL"),
			'confirmation_redirect'	=>	array("TEXT", "NOT NULL"),
			'captcha'				=>	array("INT(1)", "NOT NULL DEFAULT '0'"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`title`)"
		);
		
		var $indexes = array('title');
		
		function __construct($data = null) {
			$this -> table = $this -> pre . $this -> controller;
			
			foreach ($this -> tv_fields as $field => $attributes) {
				if (is_array($attributes)) {
					$this -> fields[$field] = implode(" ", $attributes);
				} else {
					$this -> fields[$field] = $attributes;
				}
			}
			
			if (!empty($data)) {
				foreach ($data as $dkey => $dval) {
					$this -> {$dkey} = stripslashes_deep($dval);
					
					switch ($dkey) {
						case 'buttontext'					:
							if (empty($dval)) {
								$this -> {$dkey} = __('Subscribe', $this -> plugin_name);
							}
							break;
					}
				}
				
				if (!empty($this -> id)) {
					$this -> form_fields = $this -> FieldsForm() -> find_all(array('form_id' => $this -> id), false, array('order', "ASC"));
				}
			}
			
			return $this;
		}
		
		function defaults() {
			global $Html;
			
			$defaults = array(
				'created'			=>	$Html -> gen_date(),
				'modified'			=>	$Html -> gen_date(),
			);
			
			return $defaults;
		}
		
		function validate($data = array()) {
			global $Html, $Field;
			$this -> errors = array();
			
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
				switch ($_GET['method']) {
					case 'save'						:
						// Check for empty or invalid values
						if (empty($title)) { $this -> errors['title'] = __('Please fill in a title', $this -> plugin_name); }
						
						$email_field = false;
						$email_field_id = $Field -> email_field_id();
						if (!empty($data['form_fields'])) {
							foreach ($data['form_fields'] as $field_id => $field_values) {
								if ($field_id == $email_field_id) {
									$email_field = true;
									break;
								}
							}
						}
						
						if (empty($email_field) || $email_field == false) {
							$this -> errors[] = __('An email address field is mandatory.', $this -> plugin_name);
						}	
						
						$list_field = false;
						$list_field_id = $Field -> list_field_id();
						if (!empty($data['form_fields'])) {
							foreach ($data['form_fields'] as $field_id => $field_values) {
								if ($field_id == $list_field_id) {
									$list_field = true;
									break;
								}
							}
						}
						
						if (empty($list_field) || $list_field == false) {
							$this -> errors[] = __('A mailing list field is mandatory', $this -> plugin_name);
						}
						break;
					case 'settings'					:
						if (empty($ajax)) { $this -> data -> ajax = 0; }
						if (empty($captcha)) { $this -> data -> captcha = 0; }
						break;
				}
			} else {
				$this -> errors[] = __('No data was provided', $this -> plugin_name);
			}
			
			return $this -> errors;
		}
	}
}

?>