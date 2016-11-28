<?php 
	
/*$params = array(
	'who' 		=> 	"authors", 
	'name' 		=> 	'user_id', 
	'selected' 	=> 	((empty($_POST['user_id'])) ? get_current_user_id() : $_POST['user_id']),
);
	
wp_dropdown_users($params);*/

$user_id = (empty($_POST['user_id'])) ? get_current_user_id() : $_POST['user_id'];
$user = get_userdata($user_id);

$args = array(
	'number'				=>	10,
	'orderby'				=>	'registered',
	'order'					=>	"DESC",
);

$user_query = new WP_User_Query($args);
$latestusers = $user_query -> get_results();

?>

<select name="user_id" id="authorsautocomplete" style="min-width:300px; width:auto;">
	<option selected="selected" value="<?php echo $user_id; ?>"><?php echo $user -> display_name; ?></option>
	<?php if (!empty($latestusers)) : ?>
		<?php foreach ($latestusers as $latestuser) : ?>
			<option value="<?php echo $latestuser -> ID; ?>"><?php echo $latestuser -> display_name; ?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#authorsautocomplete').select2({
	  placeholder: '<?php _e('Search users', $this -> plugin_name); ?>',
	  ajax: {
	        url: newsletters_ajaxurl + "action=newsletters_autocomplete_users",
	        dataType: 'json',
	        data: function (params) {
		      return {
		        q: params.term, // search term
		        page: params.page
		      };
		    },
		    processResults: function (data, page) {
		      return {
		        results: data
		      };
		    },
	    },
	  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
	  minimumInputLength: 1,
	  templateResult: formatResult,
	  templateSelection: formatSelection,
    }).next().css('width', "auto").css('min-width', "300px");
});

function formatResult(data) {
    return data.text;
};

function formatSelection(data) {
    return data.text;
};

function filter_value(filtername, filtervalue) {	    			
    if (filtername != "") {
        document.cookie = "<?php echo $this -> pre; ?>filter_" + filtername + "=" + filtervalue + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
    }
}
</script>