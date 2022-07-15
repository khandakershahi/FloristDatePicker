<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// This field is provided by the plugin: Checkout Field Editor for WooCommerce
$shipping_phone = null;

// Capture the shipping phone when email is being prepared
add_filter('woocommerce_email_order_meta_fields', function ($fields, $sent_to_admin, $order) use (&$shipping_phone) {
    if (isset($fields['shipping_phone'])) {
        $shipping_phone = $fields['shipping_phone']['value'];
        unset($fields['shipping_phone']);
    }
    return $fields;
}, 20, 3);

// Only show the phone when email is rendering ie. has value
add_filter('woocommerce_order_get_formatted_shipping_address', function ($address, $raw_address, $order) use (&$shipping_phone) {
    if ($shipping_phone) {
        return $address . '<br>' . wc_make_phone_clickable($shipping_phone);
    }
    return $address;
}, 20, 3);
