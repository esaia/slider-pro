<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


add_action('admin_menu', 'slider_pro_add_admin_menu');

/**
 * Adds a custom menu item to the WordPress admin dashboard.
 *
 * This function creates a top-level menu for the plugin in the admin sidebar.
 */
function slider_pro_add_admin_menu()
{
    // Add a top-level menu page for the plugin
    add_menu_page(
        'Slider Pro',       // The title of the page
        'Slider Pro',       // The text displayed in the menu
        'manage_options',   // The capability required for access (only users with 'manage_options' can see it)
        'slider-pro',       // The unique slug for the menu page
        'slider_pro_render_page', // The callback function that renders the page content
        'dashicons-slides', // The icon for the menu item (a building icon)
        100                 // The position of the menu item in the admin menu (higher numbers push it lower)
    );
}


/**
 * Renders the content for the plugin's admin page.
 *
 * This function is called when the user clicks on the 'building svg' menu item in the admin sidebar.
 */
function slider_pro_render_page()
{
    // Include the template file that will generate the admin page content
    include_once plugin_dir_path(SLIDER_PRO_PLUGIN_PATH) . './templates/index.php';
}



/**
 * Enqueue Vue.js and related assets (JavaScript and CSS) for the plugin.
 * 
 * This function is used both for the admin area and the frontend.
 * It checks if the scripts/styles are already loaded before enqueuing them again.
 */
function slider_pro_enqueue_vue_assets()
{
    // Only enqueue once if not already enqueued
    if (wp_script_is('slider-pro-vue-js', 'enqueued')) {
        return; // If Vue.js script is already enqueued, do nothing
    }

    // Enqueue Vue.js JavaScript and CSS for the plugin
    wp_enqueue_script('slider-pro-vue-js', plugin_dir_url(SLIDER_PRO_PLUGIN_PATH) . 'dist/assets/index.js', [], null, true);
    wp_enqueue_style('slider-pro-vue-styles', plugin_dir_url(SLIDER_PRO_PLUGIN_PATH) . 'dist/assets/index.css');

    wp_localize_script('slider-pro-vue-js', 'irePlugin', array(
        'nonce' => wp_create_nonce('irep_nonce'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'plugin_url' => SLIDER_PRO_PLUGIN_URL,
        'plugin_assets_path' => plugins_url('assets/', SLIDER_PRO_PLUGIN_PATH),
    ));
}

add_action('admin_enqueue_scripts', 'slider_pro_enqueue_vue_assets', 20);
