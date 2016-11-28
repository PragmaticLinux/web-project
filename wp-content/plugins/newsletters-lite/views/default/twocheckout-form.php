<?php
	
$tcoaccount = $this -> get_option('tcoaccount');
$url = (empty($tcoaccount) || $tcoaccount == "live") ? 
'https://www.2checkout.com/2co/buyer/purchase' : 
'https://sandbox.2checkout.com/checkout/purchase';	
	
?>

<?php if (!empty($checkoutdata)) : ?>
	<form action="<?php echo esc_attr(stripslashes($url)); ?>" method="post" id="<?php echo $formid; ?>" target="<?php echo $target; ?>">
		<?php foreach ($checkoutdata as $key => $val) : ?>
			<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>" />
		<?php endforeach; ?>
		<?php $buttontext = (empty($extend)) ? __('Pay with 2CO', $this -> plugin_name) : __('Extend with 2CO', $this -> plugin_name); ?>
		<input type="submit" class="<?php echo $this -> pre; ?>button ui-button-success paybutton" value="<?php echo $buttontext; ?>" name="checkout" />
	</form>
	
	<?php if ($autosubmit) : ?>
		<script type="text/javascript">
		document.getElementById('<?php echo $formid; ?>').submit();
		</script>
	<?php endif; ?>
<?php endif; ?>