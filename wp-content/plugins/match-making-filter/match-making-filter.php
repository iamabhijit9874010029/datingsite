<?php
/**
 * Plugin Name: Match-Making Filter
 * Description: A match-making filter plugin for the dating site, excluding the logged-in user.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue Styles & Scripts
function match_filter_enqueue_assets() {
    wp_enqueue_style('match-filter-style', plugin_dir_url(__FILE__) . 'assets/style.css');
    wp_enqueue_script('match-filter-script', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);

    wp_localize_script('match-filter-script', 'match_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'match_filter_enqueue_assets');

// Include Filter Functions
require_once plugin_dir_path(__FILE__) . 'includes/filter-functions.php';

// Shortcode for Filter Form
function match_filter_form_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/match-filter-form.php';
    return ob_get_clean();
}
add_shortcode('match_filter_form', 'match_filter_form_shortcode');

// Handle AJAX Request
add_action('wp_ajax_get_filtered_matches', 'get_filtered_matches');
add_action('wp_ajax_nopriv_get_filtered_matches', 'get_filtered_matches');
