<?php

/**
 * Admin > WooCommerce > Orders
 * 
 * Delivery date filter.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('restrict_manage_posts', function () {
    global $typenow;

    if ( !in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
        return;
    }

    $date = ! empty($_GET['_delivery_date']) ? $_GET['_delivery_date'] : '';
    echo sprintf('<input type="text" class="date datepicker date-picker date-picker-field" name="_delivery_date" id="filter_delivery_date" placeholder="%s" value="%s"/>',
        esc_attr( 'Filtrer Delivery Date', 'woocommerce' ), $date);
});

add_filter('request', function ($query_vars) {
    global $typenow, $wpdb;

    if ( !in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
        return $query_vars;
    }

    // Filter the orders by delivery date.
    if ( ! empty( $_GET['_delivery_date'] ) ) {
        $ts = strtotime($_GET['_delivery_date']);
        $date1 = date('Y-m-d', $ts);
        $date2 = date('d-m-Y', $ts);
        $date3 = date('d/m/Y', $ts);

        // Find in woocommerce meta
		$result = $wpdb->get_results("
			SELECT i.order_id FROM {$wpdb->prefix}woocommerce_order_items AS i
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
			LEFT JOIN {$wpdb->prefix}postmeta AS m ON (i.order_id = m.post_id AND m.meta_key='_delivery_date')
			WHERE im.meta_key = 'Delivery Date'
            AND (im.meta_value = '{$date1}' OR im.meta_value='{$date2}' OR im.meta_value = '{$date3}')
            AND m.meta_value IS NULL
            LIMIT 30
        ");

        // and copy to postmeta
        foreach ($result as $row) {
            add_post_meta($row->order_id, '_delivery_date', $date1, true);
        }

        // to be able to search it.
        $query_vars['meta_query'] = array(
            array(
                'key'     => '_delivery_date',
                'value'   => $date1,
                'compare' => '=',
            ),
        );
    }

    return $query_vars;
});