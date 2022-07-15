<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checkout
 * 
 * Required fields validation / html5.
 */
//wp_register_script('FloristDatePicker-checkout', plugins_url('checkout.js', FloristDatePicker_PLUGIN), array('jquery'));
//wp_enqueue_script('FloristDatePicker-checkout');
 function checkout_script() {
    
     wp_enqueue_script('FloristDatePicker-checkout', plugins_url('checkout.js', FloristDatePicker_PLUGIN), array('jquery'));
    
 }

 add_action('wp_enqueue_scripts', 'checkout_script');
