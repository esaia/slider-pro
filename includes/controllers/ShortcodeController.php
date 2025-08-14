<?php

/**
 * Slider Pro AJAX Handler
 * 
 * Handles all AJAX operations for the Slider Pro plugin
 * 
 * @package SliderPro
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SliderProShortcodeAjaxHandler extends SliderProBaseAjaxHandler
{
    /**
     * Database table names
     */
    private $sliders_table;
    private $slider_metas_table;


    /**
     * Constructor - Initialize AJAX hooks and table names
     */
    public function __construct()
    {
        global $wpdb;

        $this->sliders_table = $wpdb->prefix . 'slider_pro_sliders';
        $this->slider_metas_table = $wpdb->prefix . 'slider_pro_slider_metas';

        $this->init_ajax_hooks();
    }

    /**
     * Initialize AJAX hooks
     */
    private function init_ajax_hooks()
    {
        add_action('wp_ajax_slider_pro_get_slider_shortcode', [$this, 'get_slider_shortcode']);
        // add_action('wp_ajax_nopriv_slider_pro_get_slider_shortcode', [$this, 'get_slider_shortcode']);
    }



    /**
     * Check if slider exists
     * 
     * @param int $slider_id Slider ID
     * @return bool
     */
    private function slider_exists($slider_id)
    {
        $count = SliderProDb::table($this->sliders_table)
            ->where('id', '=', $slider_id)
            ->count();

        return $count > 0;
    }


    /**
     * Get and validate slider ID from POST data
     * 
     * @param bool $required Whether slider ID is required
     * @return int Validated slider ID
     */
    private function get_slider_id($required = true)
    {
        $slider_id = intval($_POST['sliderId'] ?? 0);

        if ($required && !$slider_id) {
            $this->send_error('Slider ID is required');
        }

        if ($slider_id && !$this->slider_exists($slider_id)) {
            $this->send_error('Slider not found', 404);
        }

        return $slider_id;
    }


    /**
     * Get all sliders with pagination and filtering
     */
    public function get_slider_shortcode()
    {
        $this->verify_request();

        $slider_id = $this->get_slider_id();

        $data = SliderProDb::table($this->sliders_table)->find($slider_id);


        if ($data) {
            $data = $this->map_slider_data($data);
        }

        $this->send_success($data);
    }




    /**
     * Get slider meta
     */
    public function get_slider_meta($id = null)
    {
        $this->verify_request();
        $slider_id = $id ?? $this->get_slider_id();
        $meta_data = $this->get_slider_meta_data($slider_id);
        return $meta_data;
    }


    /**
     * Get slider meta data as associative array
     * 
     * @param int $slider_id Slider ID
     * @return array Meta data
     */
    private function get_slider_meta_data($slider_id)
    {
        $metas = SliderProDb::table($this->slider_metas_table)
            ->where('slider_id', '=', $slider_id)
            ->get();

        $formatted_metas = [];

        foreach ($metas as $meta) {
            $value = $meta->meta_value;

            // Try to decode JSON values
            $decoded = json_decode($value, true);

            // Store the decoded value if valid JSON, otherwise keep original
            $formatted_metas[$meta->meta_key] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
        }

        return $formatted_metas;
    }



    private function map_slider_data($item)
    {
        $item = (array) $item;
        if (!empty($item['slides'])) {
            $item['slides'] = json_decode($item['slides'], true);
        } else {
            $item['slides'] = [];
        }

        $meta = $this->get_slider_meta($item['id']);

        $item['meta'] = $meta;

        return $item;
    }
}

// Initialize the AJAX handler
if (is_admin() || wp_doing_ajax()) {
    new SliderProShortcodeAjaxHandler();
}
