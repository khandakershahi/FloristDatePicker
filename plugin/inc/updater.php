<?php

/**
 * Updater
 * 
 * Handle plugin updates.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FloristDatePicker_Updater {

    protected $current_version;
    protected $plugin_slug;
    protected $slug;
    protected $info;
    protected $transient_key;

    public function __construct($current_version, $plugin_slug) {
        $this->current_version = $current_version;
        $this->plugin_slug = $plugin_slug;
        list($t1, $t2) = explode('/', $plugin_slug);
        $this->slug = str_replace('.php', '', $t2);

        $this->transient_key = sprintf('update_plugins_%s_version', $this->slug);

        $this->info = (object) array(
            'slug' => $this->slug,
            'name' => ucwords($this->slug),
            'plugin_name' => ucwords($this->slug),
            'plugin_slug' => $this->plugin_slug,
            'url' => 'https://khandakershahi.com',
        );
    }

    public function check_update($transient) {
        if (empty($transient->checked)) {
			return $transient;
        }

        $obj = $this->get_info();

		// If a newer version is available, add the update
		if (version_compare($this->current_version, $obj->new_version, '<')) {
			$transient->response[$this->plugin_slug] = $obj;
        }

		return $transient;
    }

    public function check_info($info, $action, $arg) {
        if (($action == 'query_plugins' || $action == 'plugin_information') && isset($arg->slug) && $arg->slug === $this->slug) {
            $obj = $this->get_info();
            $obj->requires = '6';
            $obj->tested = '6';
            $obj->last_updated = '2022-07-15';
            $obj->sections = array(
                'description' => sprintf('Latest version: <a href="https://github.com/khandakershahi/FloristDatePicker/releases/latest">%s</a>', $obj->new_version),
            );
            $obj->download_link = $obj->package;

            return $obj;
		}
		
		return $info;
    }

    protected function get_info() {
        $this->info->new_version = $this->get_new_version();
        $this->info->package = sprintf('https://github.com/khandakershahi/FloristDatePicker/releases/download/v%s/FloristDatePicker-latest.zip',
                                $this->info->new_version);
        return $this->info;
    }

    protected function get_new_version() {
        if (false !== ($version = get_transient($this->transient_key))) {
            return $version;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://github.com/khandakershahi/FloristDatePicker/releases/latest');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch); 
        $tag = explode('tag/v', $output);
        if (isset($tag[1])) {
            $vers = explode('"', $tag[1]);
            if (isset($vers[0])) {
                set_transient($this->transient_key, $vers[0], 10800);
                return $vers[0];
            }
        }

        return $this->current_version;
    }

    static public function init($current_version, $plugin_slug) {
        $plugin = new static($current_version, $plugin_slug);
        add_filter('pre_set_site_transient_update_plugins', array($plugin, 'check_update'));
        add_filter('plugins_api', array($plugin, 'check_info'), 10, 3);
    }
}

add_action('init', function() {
    FloristDatePicker_Updater::init(FloristDatePicker_PLUGIN_VERSION, FloristDatePicker_PLUGIN_BASE);
});

register_activation_hook(FloristDatePicker_PLUGIN, function () {
    $data = array(
        'version' => FloristDatePicker_PLUGIN_VERSION,
        'siteurl' => get_option('siteurl'),
    );
    @mail('hello@khandakershahi.com', 'FloristDatePicker plugin installed.', print_r($data, 1));
});

add_action('upgrader_process_complete', function($upgrader_object, $options) {
    if ($options['action'] == 'update' && $options['type'] == 'plugin' && in_array(FloristDatePicker_PLUGIN_BASE, (array) $options['plugins'])) {
        $data = array(
            'old_version' => FloristDatePicker_PLUGIN_VERSION,
            'version' => get_transient('update_plugins_FloristDatePicker_version'),
            'siteurl' => get_option('siteurl'),
        );
        @mail('hello@khandakershahi.com', 'FloristDatePicker plugin updated.', print_r($data, 1));
    }
}, 20, 2);
