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
        add_action('wp_ajax_slider_pro_get_slider_shortcode', [$this, 'get_slider_shortcode']);
        // add_action('wp_ajax_nopriv_slider_pro_get_slider_shortcode', [$this, 'get_slider_shortcode']);
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
}

// Initialize the AJAX handler
if (is_admin() || wp_doing_ajax()) {
    new SliderProShortcodeAjaxHandler();
}
