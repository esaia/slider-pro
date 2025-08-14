<?php


if (!defined('ABSPATH')) {
    exit;
}

register_activation_hook(SLIDER_PRO_PLUGIN_PATH, 'slider_pro__create_tables');


function slider_pro__create_tables()
{
    global $wpdb;

    // Define table names with WordPress prefix
    $sliders_table = $wpdb->prefix . 'slider_pro_sliders';
    $slider_metas_table = $wpdb->prefix . 'slider_pro_slider_metas';

    // Get the WordPress charset and collation
    $charset_collate = $wpdb->get_charset_collate();

    // SQL for creating sliders table (removed trailing comma)
    $sliders_sql = "CREATE TABLE $sliders_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        slides JSON,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // SQL for creating slider metas table (removed foreign key constraint)
    $slider_metas_sql = "CREATE TABLE $slider_metas_table (
        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        slider_id bigint(20) unsigned NOT NULL,
        meta_key varchar(255) NOT NULL,
        meta_value longtext,
        PRIMARY KEY (meta_id),
        KEY slider_id (slider_id),
        KEY meta_key (meta_key)
    ) $charset_collate;";

    // Include the WordPress database upgrade library
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Create the tables
    dbDelta($sliders_sql);
    dbDelta($slider_metas_sql);
}
