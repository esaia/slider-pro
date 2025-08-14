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

class SliderProAjaxHandler extends SliderProBaseAjaxHandler
{


    /**
     * Constructor - Initialize AJAX hooks and table names
     */
    public function __construct()
    {
        parent::__construct();

        $this->init_ajax_hooks();
    }


    /**
     * Initialize AJAX hooks
     */
    private function init_ajax_hooks()
    {
        $ajax_actions = [
            // Slider CRUD operations
            'create_slider' => 'create_slider',
            'update_slider' => 'update_slider',
            'delete_slider' => 'delete_slider',
            // 'get_slider' => 'get_slider',
            'get_sliders' => 'get_sliders',

            // Slider Meta operations
            'update_slider_meta_bulk' => 'update_slider_meta_bulk',

        ];

        foreach ($ajax_actions as $action => $method) {
            add_action("wp_ajax_slider_pro_{$action}", [$this, $method]);
        }
    }


    /**
     * Validate and sanitize slider data
     * 
     * @return array Sanitized data
     */
    private function validate_slider_data()
    {
        $title = sanitize_text_field($_POST['title'] ?? '');
        $slides = wp_unslash($_POST['slides'] ?? []);

        if (empty($title)) {
            $this->send_error('Title is required');
        }

        $slides = wp_json_encode($slides);

        return compact('title', 'slides');
    }



    /**
     * Create a new slider
     */
    public function create_slider()
    {
        $this->verify_request();

        $data = $this->validate_slider_data();

        $query = SliderProDb::table($this->sliders_table)->create($data);

        $this->send_success([
            'message' => 'Slider created successfully',
            'slider_id' => $query,
        ]);
    }

    /**
     * Update existing slider
     */
    public function update_slider()
    {
        $this->verify_request();

        $slider_id = $this->get_slider_id();
        $data = $this->validate_slider_data();

        SliderProDb::table($this->sliders_table)->where('id', '=',  $slider_id)->update($data);

        $this->send_success([
            'message' => 'Slider updated successfully',
        ]);
    }

    /**
     * Delete slider
     */
    public function delete_slider()
    {
        $this->verify_request();

        $slider_id = $this->get_slider_id();

        SliderProDb::table($this->sliders_table)->where('id', '=',  $slider_id)->delete();

        $this->send_success(['message' => 'Slider deleted successfully']);
    }


    /**
     * Get all sliders with pagination and filtering
     */
    public function get_sliders()
    {
        $this->verify_request();

        $page = absint($_POST['page'] ?? 1);
        $perPage = absint($_POST['perPage'] ?? 10);

        $data = SliderProDb::table($this->sliders_table)->orderBy('created_at', "DESC")->paginate($page, $perPage);

        if ($data) {
            $data['data'] = array_map([$this, 'map_slider_data'], $data['data']);
        }


        $this->send_success($data);
    }



    /**
     * Update slider meta bulk
     */
    public function update_slider_meta_bulk()
    {
        $this->verify_request();

        $slider_id = $this->get_slider_id();
        $meta = $_POST['meta'] ?? [];

        if (empty($meta) || !is_array($meta)) {
            $this->send_error('Invalid meta data provided');
        }

        global $wpdb;
        $wpdb->query('START TRANSACTION');

        try {
            foreach ($meta as $meta_key => $meta_value) {
                $this->upsert_slider_meta($slider_id, $meta_key, $meta_value);
            }

            $wpdb->query('COMMIT');
            $this->send_success(['message' => 'Bulk meta updated successfully']);
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            $this->send_error('Failed to update bulk meta: ' . $e->getMessage());
        }
    }



    /**
     * Insert or update slider meta (upsert)
     * 
     * @param int $slider_id Slider ID
     * @param string $meta_key Meta key
     * @param mixed $meta_value Meta value
     */
    private function upsert_slider_meta($slider_id, $meta_key, $meta_value)
    {

        $existing = SliderProDb::table($this->slider_metas_table)
            ->where('slider_id', '=', $slider_id)
            ->where('meta_key', '=', $meta_key)
            ->get();


        if ($meta_value && is_array($meta_value)) {
            $meta_value = wp_json_encode($meta_value);
        }

        if ($existing) {
            $data = ['meta_value' => $meta_value];

            SliderProDb::table($this->slider_metas_table)
                ->where('slider_id', '=', $slider_id)
                ->where('meta_key', '=', $meta_key)
                ->update($data);
        } else {
            $data = [
                'slider_id' => $slider_id,
                'meta_key' => $meta_key,
                'meta_value' => $meta_value
            ];

            SliderProDb::table($this->slider_metas_table)
                ->create($data);
        }
    }
}

// Initialize the AJAX handler
if (is_admin() || wp_doing_ajax()) {
    new SliderProAjaxHandler();
}
