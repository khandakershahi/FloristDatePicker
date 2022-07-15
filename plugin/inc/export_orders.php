<?php

/**
 * One click export orders by delivery date = today.
 */

add_filter('option_woocommerce-order-export-now', function ($options) {
    $options['statuses'] = array('wc-completed');
    $options['item_metadata'] = array('line_item:Delivery Date = '.date('d/m/Y'));
    $options['format'] = 'PDF';
    $options['sort_direction'] = 'ASC';
    $options['sort'] = 'order_id';
    $options['all_products_from_order'] = 1;
    $options['skip_refunded_items'] = 1;
    $options['from_date'] = '';
    $options['to_date'] = '';
    $options['export_filename'] = 'orders-%y-%m-%d-%h-%i-%s.pdf';
    $options['duplicated_fields_settings'] = array(
        'products' => array(
            'repeat' => 'rows',
            'populate_other_columns' => 0,
            'max_cols' => 10,
            'group_by' => 'product',
            'line_delimiter' => '\\n',
        ),
        'coupons' => array(
            'repeat' => 'columns',
            'max_cols' => 10,
            'group_by' => 'product',
            'line_delimiter' => '\\n',
        ),
    );
    $options['order_fields'] = array(
        array(
            'segment' => 'products',
            'key' => 'products',
            'colname' => 'Products',
            'label' => 'Products',
            'format' => 'undefined',
        ),
        array(
            'segment' => 'coupons',
            'key' => 'coupons',
            'colname' => 'Coupons',
            'label' => 'Coupons',
            'format' => 'undefined',
        ),
        array(
            'segment' => 'products',
            'key' => 'plain_products_Delivery Date',
            'label' => 'Delivery Date',
            'format' => 'undefined',
            'colname' => 'Delivery Date',
        ),
        array(
            'segment' => 'products',
            'key' => 'plain_products_sku',
            'label' => 'SKU',
            'format' => 'string',
            'colname' => 'SKU',
        ),
        array(
            'segment' => 'products',
            'key' => 'plain_products_name',
            'label' => 'Item',
            'format' => 'string',
            'colname' => 'Item',
        ),
        array(
            'segment' => 'products',
            'key' => 'plain_products_qty',
            'label' => 'Quantity',
            'format' => 'number',
            'colname' => 'Quantity',
        ),
        array(
            'segment' => 'shipping',
            'key' => 'shipping_full_name',
            'label' => 'Name',
            'format' => 'string',
            'colname' => 'Name',
        ),
        array(
            'segment' => 'shipping',
            'key' => 'plain_orders_shipping_phone',
            'label' => 'shipping_phone',
            'format' => 'string',
            'colname' => 'Phone',
        ),
        array(
            'segment' => 'shipping',
            'key' => 'shipping_address',
            'label' => 'Address',
            'format' => 'string',
            'colname' => 'Address',
        ),
        array(
            'segment' => 'shipping',
            'key' => 'shipping_citystatezip',
            'label' => 'City, State, Zip',
            'format' => 'string',
            'colname' => 'City, State, Zip',
        ),
        // array(
        //     'segment' => 'products',
        //     'key' => 'plain_products_Your Card Message',
        //     'label' => 'Your Card Message',
        //     'format' => 'undefined',
        //     'colname' => 'Message',
        // ),
    );

    return $options;
}, 20, 1);
