<!-- LITE Upgrade -->

<?php

$plugin_link = "http://tribulant.com/plugins/view/1/wordpress-newsletter-plugin";

global $Db, $Mailinglist, $Subscriber;

/* List Limits */
$Db -> model = $Mailinglist -> model;
$list_count = $Db -> count();
$lists = $list_count;
$lists_percentage = (($lists / 1) * 100);

/* Subscriber Limits */
$Db -> model = $Subscriber -> model;
$subscriber_count = $Db -> count();
$subscribers = $subscriber_count;
$subscribers_percentage = (($subscribers / 500) * 100);

/* Email Limits */
$newsletters_lite = new newsletters_lite();
$emails = $newsletters_lite -> lite_current_emails_all(1000, 'monthly');
$emails_percentage = (($emails / 1000) * 100);

?>

<div class="wrap newsletters about-wrap">
	<h1><?php echo __('Upgrade to Tribulant Newsletters PRO', $this -> plugin_name); ?></h1>
	<div class="about-text">
		<?php echo sprintf(__('Thank you for installing the %s. You are using the Tribulant Newsletters LITE plugin which contains all of the powerful features of the PRO plugin but with some limits. You can upgrade to Tribulant Newsletters PRO by submitting a serial key. If you do not have a serial key, you can buy one now.', $this -> plugin_name), '<a href="' . $plugin_link . '" target="_blank">' . __('Tribulant Newsletters plugin', $this -> plugin_name) . '</a>', $this -> version); ?>
	</div>
	<div class="newsletters-badge">
		<div>
			<i class="fa fa-envelope fa-fw" style="font-size: 72px !important; color: white;"></i>
		</div>
		<?php echo sprintf('Version %s', $this -> version); ?>
	</div>
	
	<div class="changelog newsletters-changelog">
		<h3><?php _e('Upgrade to Tribulant Newsletters PRO', $this -> plugin_name); ?></h3>
		<p><?php _e('You can click "Submit Serial" below to activate Tribulant Newsletters PRO.', $this -> plugin_name); ?><br/>
		<?php _e('Alternatively, click "Buy PRO Now" to open the plugin page and buy a serial key to submit.', $this -> plugin_name); ?></p>
		<p>
			<a class="button button-primary button-hero" href="<?php echo $plugin_link; ?>" target="_blank"><?php _e('Buy PRO Now', $this -> plugin_name); ?></a>
			<p><?php _e('or', $this -> plugin_name); ?> <a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> submitserial); ?>" onclick="jQuery.colorbox({href:newsletters_ajaxurl + 'action=<?php echo $this -> pre; ?>serialkey'}); return false;"><?php _e('submit a serial key', $this -> plugin_name); ?></a></p>
		</p>
	</div>
		
	<div class="changelog newsletters-changelog">
		<div class="feature-section three-col">
			<div class="col">
				<h4><?php _e('Current Limits', $this -> plugin_name); ?></h4>
				<p><?php _e('Your current limits in Tribulant Newsletters LITE:', $this -> plugin_name); ?></p>
				<ul>
					<li><?php echo sprintf(__('<strong>%s of 1</strong> (%s&#37;) mailing lists used', $this -> plugin_name), $lists, $lists_percentage); ?></li>
					<li><?php echo sprintf(__('<strong>%s of 500</strong> (%s&#37;) subscribers used', $this -> plugin_name), $subscribers, $subscribers_percentage); ?></li>
					<li><?php echo sprintf(__('<strong>%s of 1000</strong> (%s&#37;) emails per month', $this -> plugin_name), $emails, $emails_percentage); ?></li>
					<li><?php _e('No dynamic, custom fields', $this -> plugin_name); ?></li>
				</ul>
			</div>
			<div class="col">
				<h4><?php _e('Benefits of PRO', $this -> plugin_name); ?></h4>
				<p><?php _e('Tribulant Newsletters PRO gives these benefits:', $this -> plugin_name); ?></p>
				<ul>
					<li><?php echo sprintf(__('PRO, %s', $this -> plugin_name), '<a href="http://tribulant.com/support/" target="_blank">' . __('priority support', $this -> plugin_name) . '</a>'); ?></li>
					<li><?php _e('Unlimited mailing lists', $this -> plugin_name); ?></li>
					<li><?php _e('Unlimited subscribers', $this -> plugin_name); ?></li>
					<li><?php _e('Unlimited email sending', $this -> plugin_name); ?></li>
					<li><?php _e('Dynamic, custom fields', $this -> plugin_name); ?></li>
				</ul>
			</div>
			<div class="col last-feature">
				<h4><?php _e('Upgrade to PRO', $this -> plugin_name); ?></h4>
				<p><?php _e('Upgrading to Tribulant Newsletters PRO is quick and easy by clicking the button below:', $this -> plugin_name); ?></p>
				<p><a href="<?php echo $plugin_link; ?>" class="button button-primary" target="_blank"><?php _e('Buy PRO Now', $this -> plugin_name); ?></a></p>
				<p><?php _e('Once you have purchased a serial key, simply submit it to activate Tribulant Newsletters PRO:', $this -> plugin_name); ?></p>
				<p><a class="button button-secondary" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> submitserial); ?>" onclick="jQuery.colorbox({href:newsletters_ajaxurl + 'action=<?php echo $this -> pre; ?>serialkey'}); return false;"><?php _e('Submit Serial', $this -> plugin_name); ?></a></p>
			</div>
		</div>
	</div>
	
	<div class="changelog newsletters-changelog">
		<h3><?php _e('About Tribulant Software', $this -> plugin_name); ?></h3>
		<p><a href="http://tribulant.com" target="_blank"><img src="<?php echo $this -> url(); ?>/images/logo.png" alt="tribulant" /></a></p>
		<p><?php _e('At Tribulant Software, we strive to provide the best WordPress plugins on the market.', $this -> plugin_name); ?><br/>
		<?php _e('We are a full-time business developing, promoting and supporting WordPress plugins to the community.', $this -> plugin_name); ?></p>
		<p>
			<a class="button button-primary button-large" target="_blank" href="http://tribulant.com"><?php _e('Visit Our Site', $this -> plugin_name); ?></a>
		</p>
		
		<h3><?php _e('Find Us On Social Networks', $this -> plugin_name); ?></h3>
		<p>
			<!-- Facebook Like -->
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=229106274013";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			
			<div class="fb-like" data-href="https://www.facebook.com/tribulantsoftware" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div>
			
			<!-- Twitter Follow -->
			<a href="https://twitter.com/tribulant" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @tribulant</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
			
			<!-- Google+ Follow -->
			<!-- Place this tag in your head or just before your close body tag. -->
			<script src="https://apis.google.com/js/platform.js" async defer></script>
			
			<!-- Place this tag where you want the widget to render. -->
			<div class="g-follow" data-annotation="none" data-height="20" data-href="//plus.google.com/u/0/116807944061700692613" data-rel="publisher"></div>
		</p>
	</div>
</div>