<?php

if (!class_exists('wpmlHistory')) {
	class wpmlHistory extends wpmlDbHelper {
	
		var $id;
		var $subject;
		var $message;
		var $list_id;
		var $theme_id = 0;
		var $group;
		var $attachment;
		var $attachmentfile;
		var $error = array();
		var $model = 'History';
		var $controller = 'history';
		var $table_name = 'wpmlhistory';
		
		var $table_fields = array(
			'id'				=> 	"INT(11) NOT NULL AUTO_INCREMENT",
			'from'				=>	"VARCHAR(150) NOT NULL DEFAULT ''",
			'fromname'			=> 	"VARCHAR(150) NOT NULL DEFAULT ''",
			'subject'			=>	"VARCHAR(255) NOT NULL DEFAULT ''",
			'message'			=>	"LONGTEXT NOT NULL",
			'text'				=>	"LONGTEXT NOT NULL",
			'language'			=>	"VARCHAR(20) NOT NULL DEFAULT ''",
			'preheader'			=>	"VARCHAR(100) NOT NULL DEFAULT ''",
			'spamscore'			=>	"VARCHAR(20) NOT NULL DEFAULT ''",
			'mailinglists'		=>	"TEXT NOT NULL",
			'status'			=>	"VARCHAR(50) NOT NULL DEFAULT 'active'",
			'groups'			=>	"TEXT NOT NULL",
			'roles'				=>	"TEXT NOT NULL",
			'theme_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
			'condquery'			=>	"TEXT NOT NULL",
			'conditions'		=>	"TEXT NOT NULL",
			'conditionsscope'	=>	"VARCHAR(50) NOT NULL DEFAULT 'all'",
			'daterange'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'daterangefrom'		=>	"VARCHAR(50) NOT NULL DEFAULT ''",
			'daterangeto'		=>	"VARCHAR(50) NOT NULL DEFAULT ''",
			'sent'				=>	"INT(11) NOT NULL DEFAULT '0'",
			'post_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
			'p_id'				=>	"INT(11) NOT NULL DEFAULT '0'",
			'user_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
			'senddate'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'recurring'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'recurringvalue'	=>	"INT(11) NOT NULL DEFAULT '0'",
			'recurringinterval'	=>	"VARCHAR(20) NOT NULL DEFAULT ''",
			'recurringdate'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'recurringlimit'	=>	"INT(11) NOT NULL DEFAULT '0'",
			'recurringsent'		=>	"INT(11) NOT NULL DEFAULT '0'",
			'scheduled'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'format'			=>	"ENUM('html','text') NOT NULL DEFAULT 'html'",
			'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`theme_id`), INDEX(`sent`), INDEX(`post_id`), INDEX(`user_id`)",
		);
		
		var $tv_fields = array(
			'id'				=> 	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'from'				=>	array("VARCHAR(150)", "NOT NULL DEFAULT ''"),
			'fromname'			=>	array("VARCHAR(150)", "NOT NULL DEFAULT ''"),
			'subject'			=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'message'			=>	array("LONGTEXT", "NOT NULL"),
			'text'				=>	array("LONGTEXT", "NOT NULL"),
			'language'			=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
			'preheader'			=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'spamscore'			=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
			'mailinglists'		=>	array("TEXT", "NOT NULL"),
			'status'			=>	array("VARCHAR(50)", "NOT NULL DEFAULT 'active'"),
			'groups'			=>	array("TEXT", "NOT NULL"),
			'roles'				=>	array("TEXT", "NOT NULL"),
			'theme_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'condquery'			=>	array("TEXT", "NOT NULL"),
			'conditions'		=>	array("TEXT", "NOT NULL"),
			'conditionsscope'	=>	array("VARCHAR(50)", "NOT NULL DEFAULT 'all'"),
			'daterange'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'daterangefrom'		=>	array("VARCHAR(50)", "NOT NULL DEFAULT ''"),
			'daterangeto'		=>	array("VARCHAR(50)", "NOT NULL DEFAULT ''"),
			'sent'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'post_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'p_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'user_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'senddate'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'recurring'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'recurringvalue'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'recurringinterval'	=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
			'recurringdate'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'recurringlimit'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'recurringsent'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'scheduled'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'format'			=>	array("ENUM('html','text')", "NOT NULL DEFAULT 'html'"),
			'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`theme_id`), INDEX(`sent`), INDEX(`post_id`), INDEX(`user_id`)",					   
		);
		
		var $indexes = array('theme_id', 'sent', 'post_id', 'user_id');
		
		function __construct($data = array()) {
			global $Db, $HistoriesList, $Mailinglist, $HistoriesAttachment;
		
			$this -> table = $this -> pre . $this -> controller;
		
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					$this -> {$key} = stripslashes_deep($val);
					
					switch ($key) {
						case 'groups'			:
							$this -> groups = maybe_unserialize($val);
							break;	
					}
				}
				
				/* Attachments */
				$this -> attachments = array();
				$Db -> model = $HistoriesAttachment -> model;
				if ($attachments = $Db -> find_all(array('history_id' => $this -> id))) {
					foreach ($attachments as $attachment) {
						$this -> attachments[] = array(
							'id'					=>	$attachment -> id,
							'title'					=>	$attachment -> title,
							'filename'				=>	$attachment -> filename,
							'subdir'				=>	$attachment -> subdir,
						);	
					}
				}
				
				/* Mailing Lists */
				$this -> mailinglists = false;
				$Db -> model = $HistoriesList -> model;
				
				if ($historieslists = $Db -> find_all(array('history_id' => $this -> id))) {			
					foreach ($historieslists as $hl) {
						$Db -> model = $Mailinglist -> model;
					
						if ($list = $Db -> find(array('id' => $hl -> list_id))) {
							$this -> mailinglists[] = $hl -> list_id;
						}
					}
				}
			}
			
			$Db -> model = $this -> model;
			return true;
		}
		
		function queue_recurring( ) {
			global $wpdb, $Db, $Field, $Mailinglist, $Queue, $Subscriber, $Unsubscribe, $Bounce, $SubscribersList;
			
			$recurring_queued = 0;
			
			$query = "SELECT * FROM " . $wpdb -> prefix . $this -> table . " WHERE `recurring` = 'Y' 
			AND (`recurringlimit` = '' OR `recurringlimit` = '0' OR `recurringlimit` > `recurringsent`) 
			AND (`recurringdate` <= NOW())";
			
			$query_hash = md5($query);
			if ($ob_histories = $this -> get_cache($query_hash)) {
				$histories = $ob_histories;
			} else {
				$histories = $wpdb -> get_results($query);
				$this -> set_cache($query_hash, $histories);
			}
			
			do_action('newsletters_queue_recurring_start', $histories);
			
			if (!empty($histories)) {
				foreach ($histories as $history) {
					$this -> remove_server_limits();
					$mailinglists = maybe_unserialize($history -> mailinglists);
					$roles = maybe_unserialize($history -> roles);
					$fieldsconditions = maybe_unserialize($history -> conditions);
					$subscriberids = array();
					$subscriberemails = array();
					
					if (!empty($mailinglists) || !empty($roles)) {
						$m = 1;
						if (!empty($mailinglists)) {			
							$mailinglistscondition = "(";		
							foreach ($mailinglists as $mailinglist_id) {
								$mailinglistscondition .= "list_id = '" . $mailinglist_id . "'";
								if ($m < count($mailinglists)) { $mailinglistscondition .= " OR "; }
								$m++;	
							}
						}
						
						/* Fields Conditions */
						if (!empty($fieldsconditions)) {
							$fieldsquery = "";			
							$scopeall = (empty($history -> conditionsscope) || $history -> conditionsscope == "all") ? true : false;
										
							if (!empty($fieldsconditions)) {
								$fieldsquery .= " AND (";
								$f = 1;
							
								foreach ($fieldsconditions as $field_slug => $field_value) {
									if (!empty($field_value) && $field_value != "0") {
										$Db -> model = $Field -> model;
										if ($customfield = $Db -> find(array('slug' => $field_slug), array('id', 'type', 'slug'))) {
											switch ($customfield -> type) {
												case 'text'					:
													$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " LIKE '%" . $field_value . "%'";
													break;
												default						:
													$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " = '" . $field_value . "'";
													break;
											}
											
											if ($f < count($fieldsconditions)) {
												$fieldsquery .= ($scopeall) ? " AND" : " OR";
											}
										}
									}
									
									$f++;
								}
								
								$fieldsquery .= ")";
								$fieldsquery = str_replace(" AND)", "", $fieldsquery);
								$fieldsquery = str_replace(" OR)", "", $fieldsquery);
								$fieldsquery .= ")";
								$fieldsquery = str_replace("))", ")", $fieldsquery);
							}
						}
						
						if (!empty($history -> daterange) && $history -> daterange == "Y") {
							if (!empty($history -> daterangefrom) && !empty($history -> daterangeto)) {
								$daterangefrom = date_i18n("Y-m-d", strtotime($history -> daterangefrom));
								$daterangeto = date_i18n("Y-m-d", strtotime($history -> daterangeto));
								$fieldsquery .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . $daterangefrom . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . $daterangeto . "')";
							}
						}
						
						/* Attachments */
						$Db -> model = $this -> model;
						$history = $Db -> find(array('id' => $history -> id));
						
						$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
						. $wpdb -> prefix . $Subscriber -> table . ".email FROM " 
						. $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
						. $wpdb -> prefix . $SubscribersList -> table . " ON "
						. $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id";
						
						if (!empty($mailinglistscondition)) {
							$query .= " WHERE " . $mailinglistscondition . ")";
						}
						
						$query .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'"
						. str_replace(" AND ()", "", $fieldsquery);
						
						$sentmailscount = 0;
						$q_queries = array();
						
						$datasets = array();
						$q_queries = array();
						$d = 0;
						
						// Send to user roles?
						if (!empty($roles)) {
							$users = array();
							$exclude_users_query = "SELECT GROUP_CONCAT(`user_id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `user_id` != '0'";
							$exclude_users = $wpdb -> get_var($exclude_users_query);
							
							foreach ($roles as $role_key) {
								$users_arguments = array(
									'blog_id'				=>	$GLOBALS['blog_id'],
									'role'					=>	$role_key,
									'exclude'				=>	$exclude_users,
									'fields'				=>	array('ID', 'user_email', 'user_login'),
								);
								
								$role_users = get_users($users_arguments);
								$users = array_merge($users, $role_users);
							}
							
							if (!empty($users)) {
								foreach ($users as $user) {
									$this -> remove_server_limits();
									
									$q_queries[] = $Queue -> save(
										false,
										$user, 
										$history -> subject, 
										$history -> message, 
										$history -> attachments, 
										$history -> post_id, 
										$history -> id, 
										true, 
										$history -> theme_id, 
										$history -> senddate
									);
									
									$d++;
								}
							}
						}
	
						if (!empty($mailinglists)) {
							$query_hash = md5($query);
							if ($ob_subscribers = $this -> get_cache($query_hash)) {
								$subscribers = $ob_subscribers;
							} else {
								$subscribers = $wpdb -> get_results($query);
								$this -> set_cache($query_hash, $subscribers);
							}
												
							if (!empty($subscribers)) {						
								foreach ($subscribers as $subscriber) {
									$this -> remove_server_limits();											
									$subscriber -> mailinglist_id = $mailinglists[0];										
									$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, $mailinglists);
									
									$q_queries[] = $Queue -> save(
										$subscriber,
										false,
										$history -> subject, 
										$history -> message, 
										$history -> attachments, 
										$history -> post_id, 
										$history -> id, 
										true, 
										$history -> theme_id, 
										$history -> senddate
									);
									
									$d++;
								}
							}
						}
						
						if (!empty($q_queries)) {												
							foreach ($q_queries as $q_query) {
								$this -> remove_server_limits();
								if (!empty($q_query)) {
									$wpdb -> query($q_query);
									$recurring_queued++;
								}
							}
						}
						
						$recurringdate = date_i18n("Y-m-d H:i:s", strtotime($history -> recurringdate . " +" . $history -> recurringvalue . " " . $history -> recurringinterval));
						$recurringsent = ($history -> recurringsent + 1);
						$sent = ($history -> sent + 1);
						
						$Db -> model = $this -> model;
						$Db -> save_field('recurringdate', $recurringdate, array('id' => $history -> id));
						$Db -> model = $this -> model;
						$Db -> save_field('recurringsent', $recurringsent, array('id' => $history -> id));
						$Db -> model = $this -> model;
						$Db -> save_field('sent', $sent, array('id' => $history -> id));
					}
				}
			}
			
			echo sprintf(__('%s recurring emails queued', $this -> plugin_name), $recurring_queued) . '<br/>';
			
			return true;
		}
		
		function queue_scheduled() {
			global $wpdb, $Db, $Field, $Mailinglist, $Queue, $Unsubscribe, $Bounce, $Subscriber, $SubscribersList;
			
			$query = "SELECT * FROM " . $wpdb -> prefix . $this -> table . " WHERE `senddate` <= '" . date_i18n("Y-m-d H:i:s", time()) . "' AND `scheduled` = 'Y' LIMIT 1";
			
			$query_hash = md5($query);
			if ($ob_histories = $this -> get_cache($query_hash)) {
				$histories = $ob_histories;
			} else {
				$histories = $wpdb -> get_results($query);
				$this -> set_cache($query_hash, $histories);
			}
			
			if (!empty($histories)) {		
				foreach ($histories as $history) {
					$this -> remove_server_limits();
					$mailinglists = maybe_unserialize($history -> mailinglists);
					$roles = maybe_unserialize($history -> roles);
					$fieldsconditions = maybe_unserialize($history -> conditions);
					$condquery = maybe_unserialize($history -> condquery);
					$subscriberids = array();
					$subscriberemails = array();
					
					if (!empty($mailinglists) || !empty($roles)) {
						$mailinglistscondition = "(";
						$m = 1;
						
						foreach ($mailinglists as $mailinglist_id) {
							$mailinglistscondition .= "list_id = '" . $mailinglist_id . "'";
							if ($m < count($mailinglists)) { $mailinglistscondition .= " OR "; }
							$m++;	
						}
						
						/* Fields Conditions */
						if (!empty($fieldsconditions)) {
							$fieldsquery = "";			
							$scopeall = (empty($history -> conditionsscope) || $history -> conditionsscope == "all") ? true : false;
										
							if (!empty($fieldsconditions)) {
								$fieldsquery .= " AND (";
								$f = 1;
							
								foreach ($fieldsconditions as $field_slug => $field_value) {
									if (!empty($field_value)) {
										$Db -> model = $Field -> model;
										$customfield = $Db -> find(array('slug' => $field_slug), array('id', 'slug', 'type'));
										
										switch ($customfield -> type) {
											case 'checkbox'					:
												$condition = $condquery[$customfield -> slug];
												switch ($condition) {
													case 'contains'				:
														$query .= " wp_wpmlsubscribers.id IN (SELECT subscriber_id FROM " . $wpdb -> prefix . $this -> SubscribersOption() -> table . " WHERE `field_id` = '" . $field_id . "' AND `option_id` = '" . $field[1] . "')";
														break;
													case 'equals'				:
														$query .= " wp_wpmlsubscribers.id IN (SELECT subscriber_id FROM " . $wpdb -> prefix . $this -> SubscribersOption() -> table . " WHERE `field_id` = '" . $field_id . "' AND `option_id` = '" . $field[1] . "')";
														break;
												}
												
												if ($f < count($fieldsconditions)) {
													//$query .= ($scopeall) ? " AND" : " OR";
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
											default							:
												$condition = $condquery[$field_slug];
												
												switch ($condition) {
													case 'smaller'				:
														$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " < '" . $field_value . "'";
														break;
													case 'larger'				:
														$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " > '" . $field_value . "'";
														break;
													case 'contains'				:
														$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " LIKE '%" . $field_value . "%'";
														break;
													case 'equals'				:
													default						:
														$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " = '" . $field_value . "'";
														break;
												}
												
												if ($f < count($fieldsconditions)) {
													$fieldsquery .= ($scopeall) ? " AND" : " OR";
												}	
												break;
										}
									}
									
									$f++;
								}
								
								$fieldsquery .= ")";
								$fieldsquery = str_replace(" AND)", "", $fieldsquery);
								$fieldsquery = str_replace(" OR)", "", $fieldsquery);
								$fieldsquery .= ")";
								$fieldsquery = str_replace("))", ")", $fieldsquery);
							}
						}
						
						if (!empty($history -> daterange) && $history -> daterange == "Y") {
							if (!empty($history -> daterangefrom) && !empty($history -> daterangeto)) {
								$daterangefrom = date_i18n("Y-m-d", strtotime($history -> daterangefrom));
								$daterangeto = date_i18n("Y-m-d", strtotime($history -> daterangeto));
								$query .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . $daterangefrom . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . $daterangeto . "')";
							}
						}
						
						/* Attachments */
						$Db -> model = $this -> model;
						$history = $Db -> find(array('id' => $history -> id));
						
						$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
						. $wpdb -> prefix . $Subscriber -> table . ".email FROM " 
						. $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
						. $wpdb -> prefix . $SubscribersList -> table . " ON "
						. $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id WHERE "
						. $mailinglistscondition . ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'"
						. str_replace(" AND ()", "", $fieldsquery);
						
						$sentmailscount = 0;
						$datasets = array();
						$q_queries = array();
						$d = 0;
						
						// Send to user roles?
						if (!empty($roles)) {						
							$users = array();
							$exclude_users_query = "SELECT GROUP_CONCAT(`user_id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `user_id` != '0'";
							$exclude_users = $wpdb -> get_var($exclude_users_query);
							
							foreach ($roles as $role_key) {
								$users_arguments = array(
									'blog_id'				=>	$GLOBALS['blog_id'],
									'role'					=>	$role_key,
									'exclude'				=>	$exclude_users,
									'fields'				=>	array('ID', 'user_email', 'user_login'),
								);
								
								$role_users = get_users($users_arguments);
								$users = array_merge($users, $role_users);
							}
							
							if (!empty($users)) {
								foreach ($users as $user) {
									$this -> remove_server_limits();
									
									$q_queries[] = $Queue -> save(
										false, 
										$user,
										$history -> subject, 
										$history -> message, 
										$history -> attachments, 
										$history -> post_id, 
										$history -> id, 
										true, 
										$history -> theme_id, 
										$history -> senddate
									);
									
									$d++;
								}
							}
						}
						
						if (!empty($mailinglists)) {
							$query_hash = md5($query);
							if ($ob_subscribers = $this -> get_cache($query_hash)) {
								$subscribers = $ob_subscribers;
							} else {
								$subscribers = $wpdb -> get_results($query);
								$this -> set_cache($query_hash, $subscribers);
							}
														
							if (!empty($subscribers)) {							
								foreach ($subscribers as $subscriber) {
									$this -> remove_server_limits();											
									$subscriber -> mailinglist_id = $mailinglists[0];										
									$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, $mailinglists);
									
									$q_queries[] = $Queue -> save(
										$subscriber, 
										false,
										$history -> subject, 
										$history -> message, 
										$history -> attachments, 
										$history -> post_id, 
										$history -> id, 
										true, 
										$history -> theme_id, 
										$history -> senddate
									);
									
									$d++;
								}
							}
						}
						
						if (!empty($q_queries)) {												
							foreach ($q_queries as $q_query) {
								$this -> remove_server_limits();
								if (!empty($q_query)) {
									$wpdb -> query($q_query);
								}
							}
						
							$Db -> model = $this -> model;
							$Db -> save_field('scheduled', "N", array('id' => $history -> id));
							
							$sent = ($history -> sent + 1);
							$Db -> model = $this -> model;
							$Db -> save_field('sent', $sent, array('id' => $history -> id));	
						}
					}
				}
			}
			
			return true;
		}
		
		/**
		 * Saves a history record.
		 * Used for both INSERT and UPDATE queries
		 * @param ARRAY. An array of posted data
		 * @param BOOLEAN. Determines whether $data should be validated or not
		 *
		 */
		function save($data = array(), $validate = true, $insertpost = true) {
			global $wpdb, $Html, $Db, $HistoriesList, $user_ID;
			
			$errors = false;
			
			$defaults = array(
				'theme_id'			=>		0,
				'attachment'		=>		"N",
				'attachmentfile'	=>		"",
				'sent'				=>		0,
				'created' 			=> 		$Html -> gen_date(), 
				'modified' 			=> 		$Html -> gen_date()
			);
			
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];		
			$data = apply_filters('newsletters_db_data_before_validate', $data, $this -> model);
			
			$r = wp_parse_args($data, $defaults);
			$this -> data = (array) $this -> data;
			$this -> data[$this -> model] = $this -> array_to_object($r);		
			extract($r, EXTR_SKIP);
		
			//check if validation is necessary
			if ($validate == true) {
				if (empty($subject)) { $this -> errors['subject'] = __('No subject specified', $this -> plugin_name); }
				if (empty($message)) { $this -> errors['message'] = __('No message specified', $this -> plugin_name); }
			}
			
			//ensure that there are no errors.
			if (empty($this -> errors)) {		
				//check if an ID was passed.
				$this -> table_fields = apply_filters('newsletters_db_table_fields', $this -> table_fields, $this -> model);
				
				if (!empty($id)) {
					$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET";
					$c = 1;
					unset($this -> table_fields['key']);
					unset($this -> table_fields['created']);
					unset($this -> table_fields['spamscore']);
					unset($this -> table_fields['p_id']);
					
					foreach (array_keys($this -> table_fields) as $field) {				
						switch ($field) {
							case 'user_id'			:
								if (empty($user_id)) {
									$user_id = get_current_user_id();
								}
								break;
							case 'mailinglists'		:
								if (!empty($mailinglists) && is_array($mailinglists)) {
									$mailinglists = maybe_serialize($mailinglists);
								}
								break;
							case 'modified'			:
								${$field} = $Html -> gen_date();
								break;
						}
					
						$query .= " `" . $field . "` = '" . esc_sql(${$field}) . "'";
						
						if ($c < count($this -> table_fields)) {
							$query .= ", ";
						}
						
						$c++;
					}
					
					$query .= " WHERE `id` = '" . $id . "'";
				} else {
					$query1 = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (";
					$query2 = "";
					$c = 1;
					
					unset($this -> table_fields['key']);
					unset($this -> table_fields['id']);
					unset($this -> table_fields['spamscore']);
					unset($this -> table_fields['p_id']);
					
					foreach (array_keys($this -> table_fields) as $field) {				
						switch ($field) {
							case 'mailinglists'		:
								if (!empty($mailinglists) && is_array($mailinglists)) {
									$mailinglists = maybe_serialize($mailinglists);
								}
								break;
							case 'user_id'			:
								if (empty($user_id)) {
									$user_id = get_current_user_id();
								}
								break;
						}
					
						$query1 .= "`" . $field . "`";
						$query2 .= "'" . esc_sql(${$field}) . "'";
						
						if ($c < count($this -> table_fields)) {
							$query1 .= ", ";
							$query2 .= ", ";
						}
						
						$c++;
					}
					
					$query1 .= ") VALUES (";
					$query = $query1 . $query2 . ")";
				}
							
				//execute the INSERT or UPDATE query
				if ($wpdb -> query($query)) {
					//the query was successful
					$this -> insertid = (empty($id)) ? $wpdb -> insert_id : $id;
					$history_id = $this -> insertid;
					
					/* attachments */
					if (!empty($newattachments)) {
						global $Db, $HistoriesAttachment;
						
						foreach ($newattachments as $akey => $newattachment) {
							$newattachment['history_id'] = $this -> insertid;						
							$Db -> model = $HistoriesAttachment -> model;
							$Db -> save($newattachment, true);
						}
					}
					
					/* Custom post type */
					if ($history = $this -> get($history_id, false)) {
						if (!empty($insertpost) && $insertpost == true) {
							$custompostslug = $this -> get_option('custompostslug');
							
							$post_status = (empty($history -> sent)) ? 'draft' : 'publish';
							
							$post_data = array(
								'ID'							=>	((empty($history -> p_id)) ? false : $history -> p_id),
								'post_content'					=>	$history -> message,
								'post_title'					=>	$history -> subject,
								'post_status'					=>	$post_status,
								'post_type'						=>	$custompostslug,
								'post_author'					=>	$user_ID,
							);
							
							if ($p_id = wp_insert_post($post_data, false)) {
								//set the history_id on the post
								update_post_meta($p_id, 'newsletters_history_id', $history_id);
								
								//custom post has been inserted/updated
								$Db -> model = $this -> model;
								$Db -> save_field('p_id', $p_id, array('id' => $history_id));
							}
						}
					}
					
					/* mailing lists */
					$Db -> model = $HistoriesList -> model;
					$Db -> delete_all(array('history_id' => $this -> insertid));
					$mailinglists = maybe_unserialize($mailinglists);
					if (!empty($mailinglists) && is_array($mailinglists)) {								
						foreach ($mailinglists as $list_id) {
							$Db -> model = $HistoriesList -> model;
							
							if (!$Db -> find(array('history_id' => $this -> insertid, 'list_id' => $list_id))) {
								$hl_data = array(
									'HistoriesList'			=>	array(
										'history_id'			=>	$this -> insertid,
										'list_id'				=>	$list_id,
									)
								);
								
								$Db -> save($hl_data, true);
							}
						}
					}
					
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * Retrieves a single history record by ID.
		 * @param INT. The ID of the record to fetch
		 * @param BOOLEAN/OBJ.
		 *
		 */
		function get($history_id = null, $assign = true) {
			global $wpdb;
			
			if (!empty($history_id)) {
				$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $history_id . "' LIMIT 1";
				
				$query_hash = md5($query);
				if ($ob_history = $this -> get_cache($query_hash)) {
					return $ob_history;	
				}		
				
				if ($history = $wpdb -> get_row($query)) {
					$history = $this -> init_class($this -> model, $history);
					
					if ($assign == true) {
						$this -> data = (array) $this -> data;
						$this -> data[$this -> model] = $history;
					}
					
					$this -> set_cache($query_hash, $history);
					return $history;
				}
			}
			
			return false;
		}
		
		/**
		 * Retrieves the very first history email ever created.
		 * Simply orders records by "created" date in an ascending manner.
		 * @return OBJ an object with the history email's values.
		 *
		 */
		function get_first($assign = true) {
			global $wpdb;
			
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` ORDER BY `created` ASC LIMIT 1";
			
			$query_hash = md5($query);
			if ($ob_history = $this -> get_cache($query_hash)) {
				return $ob_history;
			}
			
			if ($email = $wpdb -> get_row($query)) {
				$history = $this -> init_class($this -> model, $email);
				
				if ($assign == true) {
					$this -> data[$this -> model] = $history;
				}
				
				$this -> set_cache($query_hash, $history);
				return $history;
			}
			
			return false;
		}
		
		function get_latest($assign = true) {
			global $wpdb;
			
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` ORDER BY `created` DESC LIMIT 1";
			
			$query_hash = md5($query);
			if ($ob_history = $this -> get_cache($query_hash)) {
				return $ob_history;
			}
			
			if ($email = $wpdb -> get_row($query)) {
				$history = $this -> init_class($this -> model, $email);
				
				if ($assign == true) {
					$this -> data[$this -> model] = $history;
				}
				
				$this -> set_cache($query_hash, $history);
				return $history;
			}
			
			return false;
		}
		
		/**
		 * Deletes a single history record by ID.
		 * @param INT. The ID of the record to remove
		 * @return BOOLEAN
		 *
		 */
		function delete($id = null) {
			global $wpdb, $Db, $Email, $wpmlClick, $HistoriesAttachment, $HistoriesList, $Queue;
		
			if (!empty($id)) {
				$p_id = $this -> field('p_id', array('id' => $id));
				
				if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $id . "' LIMIT 1")) {
					
					$Db -> model = $Email -> model;
					$Db -> delete_all(array('history_id' => $id));
					
					$Db -> model = $HistoriesAttachment -> model;
					$Db -> delete_all(array('history_id' => $id));
					
					$Db -> model = $HistoriesList -> model;
					$Db -> delete_all(array('history_id' => $id));
					
					$Db -> model = $Queue -> model;
					$Db -> delete_all(array('history_id' => $id));
					
					//$wpmlClick -> delete_all(array('history_id' => $id));
					$this -> Click() -> delete_all(array('history_id' => $id));
					
					if (!empty($p_id)) {
						wp_delete_post($p_id, true);
					}
				
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * Removes multiple history email records.
		 * Makes use of the History::delete() method to delete records.
		 * @param $data ARRAY an array of history ID values.
		 * @return BOOLEAN either true or false depending on whether the operation was successful or not.
		 *
		 */
		function delete_array($data = array()) {
			global $wpdb;
			
			if (!empty($data)) {
				foreach ($data as $history_id) {
					$this -> delete($history_id);
				}
				
				return true;
			}
			
			return false;
		}
		
		function truncate() {
			global $wpdb;
			
			$query = "TRUNCATE TABLE `" . $wpdb -> prefix . "" . $this -> table . "`";
			
			if ($wpdb -> query($query)) {
				return true;
			}
			
			return false;
		}
		
		function select($limit = false) {
			global $Db;
			$historyselect = array();
			$Db -> model = $this -> model;
			
			if ($histories = $Db -> find_all(false, false, array('modified', "DESC"), $limit)) {
				foreach ($histories as $history) {
					$historyselect[$history -> id] = $history -> id . ' - ' . __($history -> subject) . ' (' . date_i18n("Y-m-d", strtotime($history -> modified)) . ')';
				}
			}
			
			return $historyselect;
		}
	}
}

?>