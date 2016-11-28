<!-- Form Subscriptions -->

<div class="wrap newsletters">	
	<h2><?php _e('Form Subscriptions', $this -> plugin_name); ?></h2>
	
	<?php $this -> render('forms' . DS . 'navigation', array('form' => $form), true, 'admin'); ?>
	
	<?php $this -> render('subscribers' . DS . 'loop', array('subscribers' => $subscribers, 'paginate' => $paginate), true, 'admin'); ?>
</div>