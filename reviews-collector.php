<?php
/*
Plugin Name: Reviews Collector
Description: Collects and manages customer reviews with customizable settings.
Version: 1.0
Author: velmoweb
*/

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'src_activate_plugin');
register_deactivation_hook(__FILE__, 'src_deactivate_plugin');

// Activate Plugin
function src_activate_plugin() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        review_rating tinyint(1) NOT NULL,
        review_text text NOT NULL,
        reviewer_name varchar(255) NOT NULL,
        reviewer_email varchar(255) NOT NULL,
        ip_address varchar(100) NOT NULL,
        user_id bigint(20) NOT NULL,
        submission_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Deactivate Plugin
function src_deactivate_plugin() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}

// Include necessary files
require_once(plugin_dir_path(__FILE__) . 'includes/reviews-collector-settings.php');
require_once(plugin_dir_path(__FILE__) . 'includes/reviews-collector-handler.php');
require_once(plugin_dir_path(__FILE__) . 'includes/reviews-collector-form.php');

// Enqueue scripts and styles
function src_enqueue_scripts() {
    wp_enqueue_style('src-styles', plugins_url('/assets/css/style.css', __FILE__));
    wp_enqueue_script('src-scripts', plugins_url('/assets/js/script.js', __FILE__), array('jquery'), null, true);

    // Pass PHP variables to JavaScript
    $google_reviews_url = esc_url(get_option('src_google_reviews_url'));
    $redirect_after_submit_url = esc_url(get_option('src_redirect_after_submit_url'));
    wp_localize_script('src-scripts', 'srcSettings', array(
        'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
        'nonce'   => wp_create_nonce('src_review_nonce'),
        'googleReviewsUrl' => $google_reviews_url,
        'redirectAfterFormSubmit' => $redirect_after_submit_url,
    ));
}
add_action('wp_enqueue_scripts', 'src_enqueue_scripts');

?>