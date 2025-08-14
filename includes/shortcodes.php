<?php

if (!defined('ABSPATH')) {
    exit;
}



/**
 * Render the shortcode to display a Vue component with dynamic project ID.
 *
 * @param array $atts Shortcode attributes, including the project ID.
 *
 * @return string The HTML markup for the Vue component.
 */
function slider_pro__shortcode($atts)
{

    // Set default attribute values for the shortcode
    $atts = shortcode_atts(array(
        'id' => 0, // Default to 0 if no ID is provided
    ), $atts);

    // Get and sanitize the project ID
    $project_id = intval($atts['id']);
    // Generate a unique ID for the div container to prevent conflicts
    $unique_id = 'slider-pro-shortcode-' . uniqid();

    ob_start(); // Start output buffering
?>
    <div id="<?php echo esc_attr($unique_id); ?>" data-slider-id="<?php echo esc_attr($project_id); ?>">
    </div>
<?php
    return ob_get_clean(); // Return the generated HTML content
}

add_shortcode('slider-pro', 'slider_pro__shortcode');
