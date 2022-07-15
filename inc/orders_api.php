<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// ADDING 2 NEW COLUMNS WITH THEIR TITLES (keeping "Total" and "Actions" columns at the end)
add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns)
{
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
            // Inserting after "Status" column
            $reordered_columns['my-column1'] = __( 'Delivery Date','theme_domain');
            }
    }
    return $reordered_columns;
}

// Adding custom fields meta data for each new column (example)
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id )
{
    switch ( $column )
    {
        case 'my-column1' :
            // Get custom post meta data
			global $post;
			$order = wc_get_order( $post->ID );
			$items = $order->get_items(); 
			//print_r($item);
			foreach ( $order->get_items() as $item_id => $item ) {

				// Here you get your data
				$custom_field = wc_get_order_item_meta( $item_id, 'Delivery Date', true ); 

				// To test data output (uncomment the line below)
				//print_r($item_id);

				// If it is an array of values
				//if( is_array( $custom_field ) ){
				//echo implode( '<br>', $custom_field ); // one value displayed by line 
				//} 
				// just one value (a string)
			//else {
			//		echo $custom_field;
			//	}
			}
            $my_var_one = $custom_field;
            if(!empty($my_var_one))
                echo $my_var_one;

            // Testing (to be removed) - Empty value case
            else
                echo '<small>(<em>no value</em>)</small>';

            break;

		}
	}


add_filter('woocommerce_rest_shop_order_object_query', function ($args, $request) {
	global $wpdb;

	if (!empty($request['delivery_date'])) {
		$order_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT 'order_id'
				FROM '{$wpdb->prefix}woocommerce_order_items'
				WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE 'meta_key' = 'Delivery Date' AND 'meta_value' = '%s' )
				AND order_item_type = 'line_item'",
				$request['delivery_date']
			)
		);

		// Force WP_Query return empty if don't found any order.
		$order_ids = ! empty( $order_ids ) ? $order_ids : array( 0 );

		$args['post__in'] = $order_ids;
	}

	return $args;
}, 20, 2);
