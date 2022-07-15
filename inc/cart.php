<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product page/add to cart form
 * 
 * If the cart already has item with info (date, message),
 * set the delivery date and mark it not required.
 */
add_filter('wcpa_product_form_fields', function($fields, $product_id) {
    $name = 'datepicker';
    $date = '';

    foreach (WC()->cart->get_cart_contents() as $item) {
        if (isset($item[WCPA_CART_ITEM_KEY]))
        foreach ($item[WCPA_CART_ITEM_KEY] as $field) {
            if (isset($field['name']) && $field['name'] == $name) {
                $date = $field['value'];
                break 2;
            }
        }
    }
    if ($date)
    foreach ($fields as $field) {
        if (!empty($field->name)) {
            
            if ($field->name == $name) {
                $field->value = $date;
                $field->required = false;
                break;
            }
        }
    }

    return $fields;

}, 10, 2);

// Add new cart details
$new_cart_item_data = null;

/**
 * Add to cart
 * 
 * Capture the new info so that we can update the existing cart item later.
 */
add_filter('woocommerce_add_cart_item_data', function($cart_item_data, $product_id, $variation_id) use (&$new_cart_item_data) {
    $has_info = false;
    foreach (WC()->cart->get_cart_contents() as $item) {
        if (isset($item[WCPA_CART_ITEM_KEY])) {
            $has_info = true;
            break;
        }
    }
    if ($has_info && isset($cart_item_data[WCPA_CART_ITEM_KEY])) {
        $new_cart_item_data = $cart_item_data[WCPA_CART_ITEM_KEY];
        unset($cart_item_data[WCPA_CART_ITEM_KEY]);
    }
    return $cart_item_data;

}, 20, 3);

/**
 * Cart changed
 * 
 * When the cart is updated and we have new info (date, message),
 * find the existing item and update.
 */
add_filter('woocommerce_cart_contents_changed', function($contents) use (&$new_cart_item_data) {
    if ($new_cart_item_data) {
        $date = '';
        $message = '';
        foreach ($new_cart_item_data as $field) {
            if ($field['name'] == 'datepicker') {
                $date = $field['value'];
            } elseif ($field['label'] == 'Your Card Message') {
                $message = $field['value'];
            }
        }
        if ($date || $message)
        foreach ($contents as $content_key => $content) {
            if (isset($content[WCPA_CART_ITEM_KEY]))
            foreach ($content[WCPA_CART_ITEM_KEY] as $field_key => $field) {
                if ($field['name'] == 'datepicker' && trim($date) != '') {
                    $contents[$content_key][WCPA_CART_ITEM_KEY][$field_key]['value'] = $date;
                } elseif ($field['label'] == 'Your Card Message' && trim($message) != '') {
                    $contents[$content_key][WCPA_CART_ITEM_KEY][$field_key]['value'] = $message;
                }
            }
        }
        $new_cart_item_data = null;
    }
    return $contents;
}, 20, 1);

/**
 * Delete item
 * 
 * When the item with info (date, message) is removed from the cart,
 * copy the info to the next non-add item (flower not wine, choc etc.)
 */
add_action('woocommerce_cart_item_removed', function($cart_item_key, $cart) {
    $removed = $cart->get_removed_cart_contents();
    if (!isset($removed[$cart_item_key]) || !isset($removed[$cart_item_key][WCPA_CART_ITEM_KEY])) {
        return;
    }

    $info = $removed[$cart_item_key][WCPA_CART_ITEM_KEY];
    foreach ($cart->get_cart_contents() as $key => $content) {
        if ($content['variation_id']) {
            $cart->cart_contents[$key][WCPA_CART_ITEM_KEY] = $info;
        }
    }
}, 20, 2);

/**
 * Restore deleted item
 * 
 * If item with info (date, message) is deleted and restored,
 * ignore the info assuming we already copy the info.
 */
add_action('woocommerce_cart_item_restored', function($cart_item_key, $cart) {
    $contents = $cart->get_cart_contents();
    if (isset($contents[$cart_item_key]) && isset($contents[$cart_item_key][WCPA_CART_ITEM_KEY])) {
        $cart->cart_contents[$cart_item_key][WCPA_CART_ITEM_KEY] = $info;
    }
}, 20, 2);
