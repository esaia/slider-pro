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
            'update_slider_meta' => 'update_slider_meta',
            'get_slider_meta' => 'get_slider_meta',
            'delete_slider_meta' => 'delete_slider_meta',

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
     * Execute database operation with error handling
     * 
     * @param callable $callback Database operation callback
     * @param string $error_message Error message on failure
     * @return mixed Result of the callback
     */
    private function execute_db_operation($callback, $error_message = 'Database operation failed')
    {
        global $wpdb;

        $result = $callback();

        if ($result === false) {
            $this->send_error($error_message . ': ' . $wpdb->last_error, 500);
        }

        return $result;
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
     * Update slider meta
     */
    public function update_slider_meta()
    {
        $this->verify_request();

        $slider_id = $this->get_slider_id();
        $meta_key = sanitize_text_field($_POST['meta_key'] ?? '');
        $meta_value = wp_unslash($_POST['meta_value'] ?? '');

        if (empty($meta_key)) {
            $this->send_error('Meta key is required');
        }

        $this->upsert_slider_meta($slider_id, $meta_key, $meta_value);

        $this->send_success(['message' => 'Slider meta updated successfully']);
    }

    /**
     * Get slider meta
     */
    public function get_slider_meta()
    {
        $this->verify_request();

        $slider_id = $this->get_slider_id();
        $meta_key = sanitize_text_field($_POST['meta_key'] ?? '');

        if ($meta_key) {
            $meta_value = $this->get_slider_meta_value($slider_id, $meta_key);
            $this->send_success([
                'meta_key' => $meta_key,
                'meta_value' => $meta_value
            ]);
        } else {
            $meta_data = $this->get_slider_meta_data($slider_id);
            $this->send_success(['meta' => $meta_data]);
        }
    }

    /**
     * Delete slider meta
     */
    public function delete_slider_meta()
    {
        $this->verify_request();

        $slider_id = $this->get_slider_id();
        $meta_key = sanitize_text_field($_POST['meta_key'] ?? '');

        if (empty($meta_key)) {
            $this->send_error('Meta key is required');
        }

        $this->execute_db_operation(function () use ($slider_id, $meta_key) {
            global $wpdb;

            return $wpdb->delete(
                $this->slider_metas_table,
                ['slider_id' => $slider_id, 'meta_key' => $meta_key],
                ['%d', '%s']
            );
        }, 'Failed to delete slider meta');

        $this->send_success(['message' => 'Slider meta deleted successfully']);
    }


    // Helper Methods

    /**
     * Check if slider exists
     * 
     * @param int $slider_id Slider ID
     * @return bool
     */
    private function slider_exists($slider_id)
    {
        global $wpdb;

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->sliders_table} WHERE id = %d",
            $slider_id
        ));

        return $count > 0;
    }

    /**
     * Get slider data with meta
     * 
     * @param int $slider_id Slider ID
     * @return object|null Slider data
     */
    private function get_slider_data($slider_id)
    {
        global $wpdb;

        $slider = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->sliders_table} WHERE id = %d",
            $slider_id
        ));

        if ($slider) {
            $slider->meta = $this->get_slider_meta_data($slider_id);
        }

        return $slider;
    }

    /**
     * Get slider meta data as associative array
     * 
     * @param int $slider_id Slider ID
     * @return array Meta data
     */
    private function get_slider_meta_data($slider_id)
    {
        global $wpdb;

        $meta_rows = $wpdb->get_results($wpdb->prepare(
            "SELECT meta_key, meta_value FROM {$this->slider_metas_table} WHERE slider_id = %d",
            $slider_id
        ));

        $meta_data = [];
        foreach ($meta_rows as $meta_row) {
            $meta_data[$meta_row->meta_key] = $meta_row->meta_value;
        }

        return $meta_data;
    }

    /**
     * Get specific slider meta value
     * 
     * @param int $slider_id Slider ID
     * @param string $meta_key Meta key
     * @param mixed $default Default value
     * @return mixed Meta value
     */
    private function get_slider_meta_value($slider_id, $meta_key, $default = '')
    {
        global $wpdb;

        $meta_value = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_value FROM {$this->slider_metas_table} WHERE slider_id = %d AND meta_key = %s",
            $slider_id,
            $meta_key
        ));

        return $meta_value !== null ? $meta_value : $default;
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
        global $wpdb;

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_id FROM {$this->slider_metas_table} WHERE slider_id = %d AND meta_key = %s",
            $slider_id,
            $meta_key
        ));

        if ($existing) {
            $this->execute_db_operation(function () use ($meta_value, $slider_id, $meta_key) {
                global $wpdb;

                return $wpdb->update(
                    $this->slider_metas_table,
                    ['meta_value' => $meta_value],
                    ['slider_id' => $slider_id, 'meta_key' => $meta_key],
                    ['%s'],
                    ['%d', '%s']
                );
            }, 'Failed to update slider meta');
        } else {
            $this->execute_db_operation(function () use ($slider_id, $meta_key, $meta_value) {
                global $wpdb;

                return $wpdb->insert(
                    $this->slider_metas_table,
                    [
                        'slider_id' => $slider_id,
                        'meta_key' => $meta_key,
                        'meta_value' => $meta_value
                    ],
                    ['%d', '%s', '%s']
                );
            }, 'Failed to insert slider meta');
        }
    }

    /**
     * Validate JSON string
     * 
     * @param string $string JSON string to validate
     * @return bool
     */
    private function is_valid_json($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }



    private function map_slider_data($item)
    {
        $item = (array) $item;
        if (!empty($item['slides'])) {
            $item['slides'] = json_decode($item['slides'], true);
        } else {
            $item['slides'] = [];
        }
        return $item;
    }
}

// Initialize the AJAX handler
if (is_admin() || wp_doing_ajax()) {
    new SliderProAjaxHandler();
}
