<?php

/**
 * Same day delivery.
 * 
 * Validation based on store hours settings.
 */

add_action('woocommerce_checkout_process', function () {
    if (!($enabled = get_option('st_enable', false)) || $enabled == 'no') {
        return;
    }

    $check_same_day_cutoff = function ($date) {
        if (!empty(get_option('dp_timezone'))) {
        $dp_timezone = get_option('dp_timezone');
    }else{
        $dp_timezone = "Europe/London";    
    }
        $tz = new DateTimeZone($dp_timezone);
        $delivery = Datetime::createFromFormat('d/m/Y', $date, $tz);

        if (!empty(get_option('cut_off_day')) && get_option('cut_off_day')=="next_day") {
            $now = new Datetime('+1 Day');
            $message="Next-Day Delivery";
        } else {
           $now = new Datetime('now', $tz);
           $message="Same-Day Delivery";
        } 
        

        // check only same day
        if ($delivery->format('Y-m-d') != $now->format('Y-m-d')) {
            return;
        }

        // Cut off 11am Same-Day Delivery
        $day_key = 'st_'.strtolower(date('l'));
        $cutoffValue = get_option($day_key, 13);
        $cutoff = clone $now;
        $cutoff->setTime($cutoffValue, 0);

        if ($now->getTimestamp() > $cutoff->getTimestamp()) {
            $showCutoff = FloristDatePicker_to_human_hours($cutoffValue);
            throw new Exception("<strong>Cut off $showCutoff $message </strong> Please change delivery date.");
        }
    };

    foreach (WC()->cart->get_cart_contents() as $item) {
        if (isset($item[WCPA_CART_ITEM_KEY]))
        foreach ($item[WCPA_CART_ITEM_KEY] as $field) {
            if (isset($field['label']) && $field['label'] == 'Delivery Date' && !empty($field['value'])) {
                $check_same_day_cutoff($field['value']);
            }
        }
    }
}, 20, 2);

 
/**
* Plugin Name: Date Picker Plugin
* Description: This is Date picker plugin It create based on London Time zone.
* Version: 1.3
* Author: Helal Uddin Ujjal
**/

// Modified for FloristDatePicker store hours

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('woocommerce_get_sections_products' , function ($settings_tab) {
    $settings_tab['bl_store_hours'] = __('Store Hours');
    return $settings_tab;
}, 20, 1);


add_filter( 'woocommerce_get_settings_products' , function ($settings, $current_section) {
    if($current_section != 'bl_store_hours') {
        return $settings;
    }

    $custom_settings =  array(
        array(
            'name' => __('Store Hours Settings'),
            'type' => 'title',
            'desc' => __( 'Manage store open/close times and  delivery day and cutoff Time' ),
            'id'   => 'date_picker' 
        ),
        array(
            'name' => __('Use Settings'),
            'type' => 'checkbox',
            'desc' => __('Activate these settings in product/checkout page'),
            'id'   => 'st_enable' 
        ),
        array(
            'name' => __( 'Set Timezone', 'woocommerce'),
            'type' => 'select',
            'desc_tip' => true,
            'desc' => __( 'Default time zone is UK'),
            'id'    => 'dp_timezone',
            'class' =>'form-control',
            'default' => 'Europe/London',
            'options' => array(
            'Europe/London' => 'UK(London)',
            'Europe/Paris' => 'France(Paris)',
            'Europe/Berlin' => 'Germany(Berlin)',
            'Europe/Rome' => 'Italy(Rome)',
            'Europe/Amsterdam' => 'Netherlands(Amsterdam)'
             ),
        ), array(
            'name' => __( 'Holiday', 'woocommerce'),
            'type' => 'text',
            'desc_tip' => true,
            'desc' => __( 'you must be folow this proccess to input holliday like                       [mm,dd,yyyy],[mm,dd,yyyy],[mm,dd,yyyy]'),
            'id'    => 'dp_holliday',
            'class' =>'form-control',
        ),
        array(
            'name' => __('Cut Off Day Delivery Settings'),
            'type' => 'radio',
            'id'   => 'cut_off_day',
            'default'=>'same_day',
            'options' => array(
                'same_day' => __('Same Day Delivery','woocommerce'),
                'next_day' => __('Next Day Delivery','woocommerce'),
            )
        ),
    );
    
    if (!empty(get_option('cut_off_day')) && get_option('cut_off_day')=="next_day") {
            $cut_off_day= __("Next Day Delivery",'woocommerce');
        } else {
            $cut_off_day= __("Same Day Delivery",'woocommerce');
        } 

    $options = array(
        '-1' => __('Close', 'woocommerce'),
        '' => __('Open', 'woocommerce'),
    );
    $start = 10;
    for ($i = 0; $i < 24; $i++) {
        $h = $start + $i;
        if ($h > 24) {
            $h = $h - 24;
        }
        $options[$h] = sprintf('Open - '.$cut_off_day.' Cut-off %s', FloristDatePicker_to_human_hours($h));
    }
    $days = array(
        'st_sunday' => '-1',
        'st_monday' => 13,
        'st_tuesday' => 13,
        'st_wednesday' => 13,
        'st_thursday' => 13,
        'st_friday' => 13,
        'st_saturday' => 13,
    );
    foreach ($days as $id => $default) {
        $day = ucfirst(str_replace('st_', '', $id));
        $custom_settings[] = array(
            'name' => $day,
            'type' => 'select',
            'id'    => $id,
            'class' =>'form-control',
            'options' => $options,
            'value' => get_option($id, $default),
        );
    }
    $custom_settings[] = array(
        'type' => 'sectionend',
        'id' => 'date_picker'
    );
    return $custom_settings;
}, 20, 2);
  
add_action('woocommerce_before_add_to_cart_button', function () {
    if (!($enabled = get_option('st_enable', false)) || $enabled == 'no') {
        return;
    }

    $settings = new stdClass();
    foreach (array('st_sunday', 'st_monday', 'st_tuesday', 'st_wednesday', 'st_thursday', 'st_friday', 'st_saturday') as $i => $name) {
        $settings->$i = get_option($name);
    }
    $settings = json_encode($settings);
    $holiday = get_option('dp_holliday');
    if (!empty(get_option('dp_timezone'))) {
        $dp_timezone = get_option('dp_timezone');
    }else{
        $dp_timezone = "Europe/London";    
    }
    if (!empty(get_option('cut_off_day')) && get_option('cut_off_day')=="next_day") {
            $cut_off_day= get_option('cut_off_day');
        } else {
            $cut_off_day= get_option('cut_off_day');
        } 
    //echo $dp_timezone;die;
// YOUR SCRIPT HERE BELOW 
echo "
<script type='text/javascript'>
var store_hours = $settings;
var timezone = '$dp_timezone';
var cut_off_day = '$cut_off_day';
jQuery(document).ready(function ($) {
    var origdp = $('#datepicker'),
        dp = $(origdp[0].outerHTML).removeClass('hasDatepicker');
    
    $(dp).attr('readonly', true);
	$(dp).attr('onfocus', \"this.value='';\");
    
    var holiday = [$holiday]; 
    
    function checkOutDisableDays(date) {
        var day = date.getDay(), Sunday = 0, Monday = 1, Tuesday = 2, Wednesday = 3, Thursday = 4, Friday = 5, Saturday = 6;
        
        if (store_hours[day] == -1) {
            return [false];
        }
        
        for (i = 0; i < holiday.length; i++) {
            if (date.getMonth() == holiday[i][0] - 1 &&
            date.getDate() == holiday[i][1] &&
            date.getFullYear() == holiday[i][2]) {
                return [false];
            }
        }
        
        return [true];
    }

	var uk = new Date().toLocaleString('en-US', { timeZone: timezone });
	var d = new Date(uk);
	var ukDay = d.getDay();
    console.log(ukDay);
    var cutoff = store_hours[ukDay];
    var ukHours = d.getHours().toLocaleString();

    console.log(d, cutoff);

    $(dp).datepicker({
        beforeShowDay: checkOutDisableDays,
        dateFormat: 'dd/mm/yy',
        minDate: (function () {
            if (cutoff == '' || cutoff == -1) {
                if(cut_off_day=='next_day'){
                    return 1;

                }else{
                    return 0;
                }
            }

            var co = parseInt(cutoff);
            if (isNaN(co) || ukHours < co) {
                if(cut_off_day=='next_day'){
                    return 1;

                }else{
                    return 0;
                }
            }

            if(cut_off_day=='next_day'){
                    return 2;

                }else{
                    return 1;
                }
        }())
    });

    origdp.after(dp);
    origdp.remove();
});
</script>";
});

add_action('wp_enqueue_scripts', function () {
//     // enqueue all our scripts
//     wp_register_script( 'jquery-ui-datepicker', 'https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js', array( 'jquery' ), null, true );
//     wp_enqueue_script( 'jquery-ui-datepicker' );
//     wp_enqueue_style( 'ui-jquery', plugins_url( '/assets/ui-jquery.css', __FILE__ ) );
//     wp_enqueue_style( 'style', plugins_url( '/assets/style.css', __FILE__ ) );
wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
wp_enqueue_style( 'jquery-ui' ); 

// Load the datepicker script (pre-registered in WordPress).
wp_enqueue_script( 'jquery-ui-datepicker' );
});


// $plugin = plugin_basename(__FILE__);
// add_filter("plugin_action_links_$plugin", function ($links) {
//     $settings_link = '<a href="admin.php?page=wc-settings&tab=products&section=wp_datepicker_notices">Settings</a>';
//     array_push( $links, $settings_link );
//     return $links;
// });

function FloristDatePicker_to_human_hours($n) {
    $h = $n > 12 ? $n - 12 : $n;
    $p = $n == 24 || $n < 12 ? 'AM' : 'PM';
    return str_pad($h, 2, '0', STR_PAD_LEFT).':00 '.$p;
}
