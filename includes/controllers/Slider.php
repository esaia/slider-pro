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

class SliderProAjaxHandler
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
        $ajax_actions = [
            // Slider CRUD operations
            'create_slider' => 'create_slider',
            'update_slider' => 'update_slider',
            'delete_slider' => 'delete_slider',
            'get_slider' => 'get_slider',
            'get_sliders' => 'get_sliders',

            // Slider Meta operations
            'update_slider_meta_bulk' => 'update_slider_meta_bulk',

        ];

        foreach ($ajax_actions as $action => $method) {
            add_action("wp_ajax_slider_pro_{$action}", [$this, $method]);
        }

        // Public AJAX (for frontend)
        add_action('wp_ajax_nopriv_slider_pro_get_slider_public', [$this, 'get_slider_public']);
    }

    /**
     * Verify nonce and user permissions
     * 
     * @param string $action Nonce action
     * @return bool
     */
    private function verify_request($action = 'slider_pro_nonce')
    {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', $action)) {
            $this->send_error('Security check failed', 403);
        }

        if (!current_user_can('manage_options')) {
            $this->send_error('Insufficient permissions', 403);
        }

        return true;
    }

    /**
     * Send JSON error response
     * 
     * @param string $message Error message
     * @param int $code HTTP status code
     */
    private function send_error($message, $code = 400)
    {
        wp_send_json_error(['message' => $message], $code);
    }

    /**
     * Send JSON success response
     * 
     * @param array $data Response data
     */
    private function send_success($data)
    {
        wp_send_json_success($data);
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
     * Validate and sanitize slider data
     * 
     * @return array Sanitized data
     */
    private function validate_slider_data()
    {
        $title = sanitize_text_field($_POST['title'] ?? '');
        $slides = wp_unslash($_POST['slides'] ?? []);
        $status = sanitize_text_field($_POST['status'] ?? 'active');

        if (empty($title)) {
            $this->send_error('Title is required');
        }

        if ($slides) {
            $slides = wp_json_encode($slides);
        }

        return compact('title', 'slides', 'status');
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
     * Get single slider
     */
    public function get_slider()
    {
        $this->verify_request();

        $slider_id = $this->get_slider_id();

        $slide = SliderProDb::table($this->sliders_table)->find($slider_id);

        if ($slide) {
            $slide = $this->map_slider_data($slide);
        }


        $this->send_success($slide);
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
    new SliderProAjaxHandler();
}
