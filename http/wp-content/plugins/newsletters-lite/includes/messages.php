<?php
	
$messages = array();

$messages[1] = __('Preview has been sent', $this -> plugin_name);
$messages[2] = __('Preview cannot be sent to %s, %s.', $this -> plugin_name);
$messages[3] = __('%s is an invalid email address', $this -> plugin_name);
$messages[4] = __('Draft has been successfully saved. It has been saved to your email history.', $this -> plugin_name);
$messages[5] = __('Draft could not be saved. Please fill in all required fields', $this -> plugin_name);
$messages[6] = __('Configuration settings successfully updated', $this -> plugin_name);
$messages[7] = __('Newsletters database update done', $this -> plugin_name);
$messages[8] = __("Subscriber has been saved", $this -> plugin_name);
$messages[9] = __('Subscribers could not be imported', $this -> plugin_name);
$messages[10] = __('Snippet has been saved', $this -> plugin_name);
$messages[11] = __('No action was specified', $this -> plugin_name);
$messages[12] = __('No bounces were specified', $this -> plugin_name);
$messages[13] = __('Bounces were deleted', $this -> plugin_name);
$messages[14] = __('Subscribers were deleted', $this -> plugin_name);
$messages[15] = __('Defaults have been loaded', $this -> plugin_name);
$messages[16] = __('No records were selected', $this -> plugin_name);
$messages[17] = __('No action was selected', $this -> plugin_name);
$messages[18] = __('Selected records were deleted', $this -> plugin_name);

$messages = apply_filters('newsletters_messageserrors', $messages);	
	
?>