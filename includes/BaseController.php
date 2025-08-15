<?php

/**
 * Base AJAX Handler
 * 
 * Provides common AJAX functionality for all handlers
 * 
 * @package SliderPro
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class SliderProBaseAjaxHandler
{

    /**
     * Database table names
     */
    protected $sliders_table;
    protected $slider_metas_table;



    /**
     * Constructor - Initialize AJAX hooks and table names
     */
    public function __construct()
    {
        global $wpdb;

        $this->sliders_table = $wpdb->prefix . 'slider_pro_sliders';
        $this->slider_metas_table = $wpdb->prefix . 'slider_pro_slider_metas';
    }

    /**
     * Verify nonce and user permissions
     * 
     * @param string $action Nonce action
     * @return bool
     */
    protected function verify_request($action = 'slider_pro_nonce')
    {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', $action)) {
            $this->send_error('Security check failed', 403);
        }

        return true;
    }

    /**
     * Send JSON error response
     * 
     * @param string $message Error message
     * @param int $code HTTP status code
     */
    protected function send_error($message, $code = 400)
    {
        wp_send_json_error(['message' => $message], $code);
    }

    /**
     * Send JSON success response
     * 
     * @param array $data Response data
     */
    protected function send_success($data)
    {
        wp_send_json_success($data);
    }





    /**
     * Get and validate slider ID from POST data
     * 
     * @param bool $required Whether slider ID is required
     * @return int Validated slider ID
     */
    protected function get_slider_id($required = true)
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



    protected function map_slider_data($item)
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

    /**
     * Get slider meta
     */
    private function get_slider_meta($id = null)
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
}
