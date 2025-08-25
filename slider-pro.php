<?php
/*
Plugin Name: Awesome Slider
Version: 1.0.0
Description: Create awesome sliders in WordPress.
Author: <a href="https://portfolioesaia.netlify.app" target="_blank">Esaia Gaprindashvili</a>
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit;
}


define('SLIDER_PRO_PLUGIN_PATH', __FILE__);
define('SLIDER_PRO_PLUGIN_URL', admin_url('admin.php?page=slider-pro'));


function dd($data): void
{
    error_log(print_r($data, true));
    die();
}



require_once  plugin_dir_path(SLIDER_PRO_PLUGIN_PATH) . 'includes/migrations.php';
require_once  plugin_dir_path(SLIDER_PRO_PLUGIN_PATH) . 'includes/Db.php';
require_once  plugin_dir_path(SLIDER_PRO_PLUGIN_PATH) . 'includes/init.php';
include_once  plugin_dir_path(SLIDER_PRO_PLUGIN_PATH) . './includes/shortcodes.php';

// Controllers
include_once  plugin_dir_path(SLIDER_PRO_PLUGIN_PATH) . './includes/BaseController.php';

include_once  plugin_dir_path(SLIDER_PRO_PLUGIN_PATH) . './includes/controllers/Slider.php';
include_once  plugin_dir_path(SLIDER_PRO_PLUGIN_PATH) . './includes/controllers/ShortcodeController.php';
