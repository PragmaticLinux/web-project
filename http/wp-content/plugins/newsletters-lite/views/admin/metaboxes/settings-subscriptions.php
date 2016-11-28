<!-- Paid Subscription Settings -->

<?php

$paidsubscriptionredirect = $this -> get_option('paidsubscriptionredirect');
$paymentmethod = $this -> get_option('paymentmethod');

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="subscriptionsY"><?php _e('Paid Subscriptions', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Turn this setting On to allow paid subscriptions. You can then create paid lists under Newsletters > Mailing Lists with an interval and a price for the subscription.', $this -> plugin_name)); ?></th>
			<td>
				<label><input onclick="jQuery('#subscriptionsoptionsdiv').show();" <?php echo ($this -> get_option('subscriptions') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="subscriptions" value="Y" id="subscriptionsY" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#subscriptionsoptionsdiv').hide();" <?php echo ($this -> get_option('subscriptions') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="subscriptions" value="N" id="subscriptionsN" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Turn On to allow paid subscriptions.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="subscriptionsoptionsdiv" style="display:<?php echo ($this -> get_option('subscriptions') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>currency"><?php _e('Currency', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Choose your preferred currency which will be used on the site and for the payment gateway used. It is highly recommended that you set this currency to be the same as the currency configured in your PayPal or 2CheckOut account to prevent conflicts.', $this -> plugin_name)); ?></th>
				<td>
					<?php $currencies = $this -> get_option('currencies'); ?>
					<?php if (!empty($currencies)) : ?>
						<select class="widefat" style="width:auto;" id="<?php echo $this -> pre; ?>currency" name="currency">
							<?php foreach ($currencies as $abb => $att) : ?>
							<option <?php echo ($this -> get_option('currency') == $abb) ? 'selected="selected"' : ''; ?> value="<?php echo $abb; ?>"><?php echo $att['symbol']; ?> - <?php echo $att['name']; ?> (<?php echo $abb; ?>)</option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
					<span class="howto"><?php _e('Choose the currency to charge your subscribers in.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="paidsubscriptionredirect_Y"><?php _e('Redirect Immediately to Payment', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo (!empty($paidsubscriptionredirect) && $paidsubscriptionredirect == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="paidsubscriptionredirect" value="Y" id="paidsubscriptionredirect_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
					<label><input <?php echo (!empty($paidsubscriptionredirect) && $paidsubscriptionredirect == "N") ? 'checked="checked"' : ''; ?> type="radio" name="paidsubscriptionredirect" value="N" id="paidsubscriptionredirect_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Should the subscriber be redirected immediately to make a payment?', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>adminordernotify"><?php _e('Admin Notification On Order', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('With this notification turned on, the email address specified in the Administrator Email setting will receive a notification when a paid subscription order has been placed.', $this -> plugin_name)); ?></th>
				<td>
					<label><input id="<?php echo $this -> pre; ?>adminordernotify" <?php echo ($this -> get_option('adminordernotify') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="adminordernotify" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
					<label><input <?php echo ($this -> get_option('adminordernotify') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="adminordernotify" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Do you want to be notified via email when a paid subscription has been paid for?', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="paymentmethod_paypal"><?php _e('Payment Method', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Simply choose the payment method that you want to use. Either PayPal or 2CheckOut. If you are going to use PayPal you can make use of the auto recurring payments which will make management of paid subscriptions easier and increase residual income.', $this -> plugin_name)); ?></th>
				<td>
					<?php /*<label><input <?php echo (empty($paymentmethod) || $paymentmethod == "choice") ? 'checked="checked"' : ''; ?> type="radio" name="paymentmethod" value="choice" id="paymentmethod_choice" /> <?php _e('Choice', $this -> plugin_name); ?></label>
					<label><input id="<?php echo $this -> pre; ?>paymentmethod" onclick="jQuery('#paypal_settings').show(); jQuery('#2checkout_settings').hide();" <?php echo ($this -> get_option('paymentmethod') == "paypal") ? 'checked="checked"' : ''; ?> type="radio" name="paymentmethod" value="paypal" /> <?php _e('PayPal', $this -> plugin_name); ?></label>
					<label><input onclick="jQuery('#2checkout_settings').show(); jQuery('#paypal_settings').hide();" <?php echo ($this -> get_option('paymentmethod') == "2co") ? 'checked="checked"' : ''; ?> type="radio" name="paymentmethod" value="2co" /> <?php _e('2CheckOut', $this -> plugin_name); ?></label>*/ ?>
					
					<label><input <?php echo (!empty($paymentmethod) && in_array('paypal', $paymentmethod)) ? 'checked="checked"' : ''; ?> type="checkbox" name="paymentmethod[]" value="paypal" id="paymentmethod_paypal" /> <?php _e('PayPal', $this -> plugin_name); ?></label>
					<label><input <?php echo (!empty($paymentmethod) && in_array('2co', $paymentmethod)) ? 'checked="checked"' : ''; ?> type="checkbox" name="paymentmethod[]" value="2co" id="paymentmethod_2co" /> <?php _e('2CheckOut', $this -> plugin_name); ?></label>
					
					<span class="howto"><?php _e('Which payment method should be used for the paid subscriptions?', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>