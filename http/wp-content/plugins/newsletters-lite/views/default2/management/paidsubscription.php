<!-- Paid Subscription -->

<?php
	
$intervals = $this -> get_option('intervals');
$paymentmethod = $this -> get_option('paymentmethod');
	
?>

<div class="newsletters">
	<p>
		<a class="btn btn-default" href="<?php echo esc_attr(stripslashes($this -> get_managementpost(true, false, false))); ?>"><?php _e('&laquo; Back to Manage Subscriptions', $this -> plugin_name); ?></a>	
	</p>
	
	<h2><?php _e('Paid Subscription', $this -> plugin_name); ?></h2>
	<p>
		<?php echo sprintf(__('You are paying for your subscription to %s.', $this -> plugin_name), __($mailinglist -> title)); ?><br/>
		<?php echo sprintf(__('You will be charged %s %s', $this -> plugin_name), $Html -> currency() . number_format($mailinglist -> price, 2, '.', ''), $intervals[$mailinglist -> interval]); ?>
	</p>
	
	<?php
	
	if (!empty($paymentmethod)) {
		if (count($paymentmethod) > 1) {
			foreach ($paymentmethod as $pmethod) {
				$this -> paidsubscription_form($subscriber, $mailinglist, false, "_self", $extend, $pmethod);
			}
		} else {
			$this -> paidsubscription_form($subscriber, $mailinglist, true, "_self", $extend, $paymentmethod[0]);
		}
	}	
		
	?>
</div>