<?php
    /*
    Plugin Name: The Slide by SimpleReach
    Plugin URI: https://www.simplereach.com
    Description: After installation, you must click '<a href='options-general.php?page=SimpleReach-Slide'>Settings &rarr; SimpleReach Slide</a>' to turn on The Slide.
    Version: 0.7.6
    Author: SimpleReach
    Author URI: https://www.simplereach.com
    */

define('SRSLIDE_PLUGIN_VERSION', '0.7.6');
define('SRSLIDE_PLUGIN_DIR', dirname(__FILE__));
define('SRSLIDE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SRSLIDE_PLUGIN_SUPPORT_EMAIL', 'support@simplereach.com');
define('WP_DEBUG', true);
require_once('srslide_functions.php');

if (is_admin()) {
    add_action('admin_init', 'srslide_admin_init');
}
add_filter('query_vars', 'srslide_query_vars');
add_filter('get_header', 'srslide_render_css');
add_filter('the_content', 'srslide_insert_slide_js');
add_filter('plugin_action_links', 'srslide_plugin_action_links', 10, 2);
add_action('admin_menu','srslide_admin_actions');
add_action('add_meta_boxes','srslide_post_meta_box');
add_action('save_post','srslide_post_save_data');
add_action('edit_post','srslide_post_save_data');
add_action('srslide_loaded', 'srslide_textdomain', 2);
add_action('plugins_loaded', 'srslide_loaded', 20);
?>
