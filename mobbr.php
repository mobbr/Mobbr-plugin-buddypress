<?php
/**
 * @package Mobbr Payments
 * @author David Semakula
 * @version 0.2
 */
/*
Plugin Name: Mobbr Payments
Plugin URI: http://wordpress.org/plugins/mobbr-payments/
Description: Adds mobbr crowd payments to your WordPress website.
Author: David Semakula
Version: 0.2
Author URI: https://github.com/davidsemakula
*/

require_once("mobbr.config.php");
require_once("mobbr.utils.php");
require_once("mobbr.head.php");
require_once("mobbr.widget.php");
require_once("mobbr.admin.php");
require_once("mobbr.filters.php");
require_once("mobbr.metabox.php");
require_once("mobbr.ajax.php");

add_action( 'wp_head', 'mobbr_participation_meta');
add_action( 'wp_head', 'mobbr_script');

add_action("widgets_init", function() {
    $options = get_mobbr_plugin_options();
    if(isset($options['button_position']) && $options['button_position'] == MOBBR_BUTTON_POSITION_WIDGET) {
        register_widget("MobbrWidget");
    }
});

add_filter('the_content', 'add_mobbr_button_to_content', 99);

add_action('admin_menu', 'plugin_admin_add_page');

add_action('admin_init', 'plugin_admin_init');

add_action('add_meta_boxes', 'mobbr_plugin_add_meta_boxes');

add_action('save_post', 'mobbr_plugin_save_meta_boxes');

add_action('wp_ajax_add_post_meta', 'ajax_save_post_participation_metadata');

$options = get_mobbr_plugin_options();
if($options['require_auth']) {
    add_action('wp_ajax_nopriv_add_post_meta', 'ajax_save_post_participation_metadata');
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'mobbr_plugin_action_links');