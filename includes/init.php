<?php

if (!defined('ABSPATH')) {
    exit;
}



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

add_action('admin_menu', 'slider_pro_add_admin_menu');



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
function slider_pro_enqueue_vue_assets($hook)
{
    if ($hook !== 'toplevel_page_slider-pro') return;

    // Only enqueue once if not already enqueued
    if (wp_script_is('slider-pro-vue-js', 'enqueued')) {
        return; // If Vue.js script is already enqueued, do nothing
    }


    // Enqueue WordPress media manager scripts (used for media handling in the plugin)
    wp_enqueue_media();

    wp_enqueue_script(
        'slider-pro-vue-js',
        plugin_dir_url(SLIDER_PRO_PLUGIN_PATH) . 'dist/assets/index.js',
        ['jquery', 'wp-util'],
        null,
        true
    );

    wp_enqueue_style(
        'slider-pro-vue-styles',
        plugin_dir_url(SLIDER_PRO_PLUGIN_PATH) . 'dist/assets/index.css'
    );

    wp_localize_script('slider-pro-vue-js', 'irePlugin', array(
        'nonce' => wp_create_nonce('irep_nonce'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'plugin_url' => SLIDER_PRO_PLUGIN_URL,
        'plugin_assets_path' => plugins_url('assets/', SLIDER_PRO_PLUGIN_PATH),
    ));
}

add_action('admin_enqueue_scripts', 'slider_pro_enqueue_vue_assets', 5);




/**
 * Add the 'type="module"' and 'defer' attributes to the Vue.js script tag.
 *
 * @param string $tag The HTML script tag.
 * @param string $handle The handle of the script being enqueued.
 *
 * @return string Modified script tag with 'module' type and 'defer' attribute.
 */
function irep_force_module_type_attribute($tag, $handle)
{
    if ($handle === 'slider-pro-vue-js') {
        $script_url = plugin_dir_url(SLIDER_PRO_PLUGIN_PATH) . 'dist/assets/index.js';

        return '<script type="module" defer src="' . esc_url($script_url) . '"></script>';
    }

    return $tag;
}


add_filter('script_loader_tag', 'irep_force_module_type_attribute', 10, 2);
