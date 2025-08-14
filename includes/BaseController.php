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
}
