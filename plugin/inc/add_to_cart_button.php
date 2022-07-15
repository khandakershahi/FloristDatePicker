<?php

/**
 * Category/shop products list
 * 
 * Hide add to cart button.
 * Show in cart (extra).
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('woocommerce_loop_add_to_cart_link', function ($link, $product, $args) {
	if (wc_get_loop_prop('name') == '') {
		return null;
	}

	return $link;
}, 20, 3);
