<?php if (!empty($checkoutdata)) : ?>
	<?php if ($this -> get_option('paypalsandbox') == "Y") : ?>
		<form id="<?php echo $formid; ?>" action="<?php echo $this -> get_option('paypalsandurl'); ?>" method="post" target="<?php echo $target; ?>">
	<?php else : ?>
		<form id="<?php echo $formid; ?>" action="<?php echo $this -> get_option('paypalliveurl'); ?>" method="post" target="<?php echo $target; ?>">
	<?php endif; ?>	
		<?php foreach ($checkoutdata as $ckey => $cval) : ?>
			<input type="hidden" name="<?php echo $ckey; ?>" value="<?php echo $cval; ?>" />
		<?php endforeach; ?>
		<?php $buttontext = (empty($extend)) ? __('Pay with PayPal', $this -> plugin_name) : __('Extend with PayPal', $this -> plugin_name); ?>
		<input type="submit" class="<?php echo $this -> pre; ?>button btn btn-success paybutton" name="checkout" value="<?php echo $buttontext; ?>" />
	</form>
	
	<?php if ($autosubmit) : ?>
		<script type="text/javascript">
		document.getElementById('<?php echo $formid; ?>').submit();
		</script>
	<?php endif; ?>
<?php endif; ?>