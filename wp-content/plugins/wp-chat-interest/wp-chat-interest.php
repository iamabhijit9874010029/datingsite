<?php

/**
 * Plugin Name: WP Chat Interest
 * Description: A standalone chat plugin where users can send interest requests and chat after acceptance.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Activation Hook - Create Database Tables
register_activation_hook(__FILE__, 'wpci_create_tables');

function wpci_create_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Interest Requests Table
    $requests_table = $wpdb->prefix . 'wpci_interest_requests';
    $requests_sql = "CREATE TABLE IF NOT EXISTS $requests_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        sender_id BIGINT(20) NOT NULL,
        receiver_id BIGINT(20) NOT NULL,
        status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    // Chat Messages Table
    $messages_table = $wpdb->prefix . 'wpci_chat_messages';
    $messages_sql = "CREATE TABLE IF NOT EXISTS $messages_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        sender_id BIGINT(20) NOT NULL,
        receiver_id BIGINT(20) NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($requests_sql);
    dbDelta($messages_sql);
}

// Enqueue Scripts & Styles
add_action('wp_enqueue_scripts', 'wpci_enqueue_scripts');

function wpci_enqueue_scripts()
{
    wp_enqueue_style('wpci-style', plugin_dir_url(__FILE__) . 'assets/style.css');
    wp_enqueue_script('wpci-script', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);
    wp_localize_script('wpci-script', 'wpci_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}

// Shortcode to Show Chat & Interest Request System
add_shortcode('wpci_chat', 'wpci_chat_shortcode');

function wpci_chat_shortcode()
{
    ob_start();

    if (!is_user_logged_in()) {
        return '<p>Please log in to chat.</p>';
    }

    global $wpdb;
    $current_user_id = get_current_user_id();
    $profile_user_id = isset($_GET['profile_id']) ? intval($_GET['profile_id']) : 0;

    if ($current_user_id === $profile_user_id) {
        return '<p>You cannot chat with yourself.</p>';
    }

    $requests_table = $wpdb->prefix . 'wpci_interest_requests';

    // Check if the current user has sent a request to the profile user
    $sent_request = $wpdb->get_row($wpdb->prepare(
        "SELECT id, status FROM $requests_table WHERE sender_id = %d AND receiver_id = %d",
        $current_user_id,
        $profile_user_id
    ));

    // Check if the current user has received a request from the profile user
    $received_request = $wpdb->get_row($wpdb->prepare(
        "SELECT id, status FROM $requests_table WHERE sender_id = %d AND receiver_id = %d",
        $profile_user_id, $current_user_id
    ));


    // global $wpdb;
    // $received_request = $wpdb->get_row($wpdb->prepare(
    //     "SELECT id, status FROM {$wpdb->prefix}wpci_interest_requests WHERE sender_id = %d AND receiver_id = %d",
    //     $profile_user_id,
    //     $current_user_id
    // ));

    // if (!$received_request) {
    //     echo "No request found for sender_id: $profile_user_id and receiver_id: $current_user_id";
    // } else {
    //     echo "Request Found: ";
    //     print_r($received_request);
    // }

    // echo "Current User ID: " . $current_user_id . "<br>";
    // echo "Profile User ID: " . $profile_user_id . "<br>";


    // abhijit
    echo '<div id="wpci-chat-box">';

    // if ($received_request && $received_request->status === 'pending') {
    //     // If current user received a pending request, show the accept button.
    //     echo '<button class="wpci-accept-request" data-request-id="' . esc_attr($received_request->id) . '">Accept Request</button>';
    // } elseif ($sent_request && $sent_request->status === 'pending') {
    //     // If current user already sent a pending request, show a message.
    //     echo '<p>Interest request sent. Awaiting acceptance.</p>';
    // } else {
    //     // If no pending request exists, show the send interest button.
    //     echo '<button class="wpci-send-interest" data-receiver-id="' . esc_attr($profile_user_id) . '">Send Interest</button>';
    // }

    include plugin_dir_path(__FILE__) . 'templates/chat-ui.php';

    echo '</div>';

    return ob_get_clean();
}


// AJAX: Send Interest Request
add_action('wp_ajax_wpci_send_interest', 'wpci_send_interest');

function wpci_send_interest()
{
    global $wpdb;

    $sender_id = get_current_user_id();
    $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;

    if ($receiver_id <= 0 || $receiver_id === $sender_id) {
        echo json_encode(array('success' => false, 'message' => 'Invalid receiver!'));
        wp_die();
    }

    $requests_table = $wpdb->prefix . 'wpci_interest_requests';

    $wpdb->insert($requests_table, array(
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'status' => 'pending'
    ));

    echo json_encode(array('success' => true, 'message' => 'Interest request sent!'));
    wp_die();
}

// AJAX: Accept Interest Request
add_action('wp_ajax_wpci_accept_interest', 'wpci_accept_interest');

// function wpci_accept_interest()
// {
//     global $wpdb;
//     $request_id = intval($_POST['request_id']);

//     $requests_table = $wpdb->prefix . 'wpci_interest_requests';
//     $wpdb->update($requests_table, array('status' => 'accepted'), array('id' => $request_id));

//     echo json_encode(array('success' => true, 'message' => 'Interest request accepted!'));
//     wp_die();
// }

function wpci_accept_interest() {
    global $wpdb;
    $current_user_id = get_current_user_id();
    $request_id = intval($_POST['request_id']);

    $requests_table = $wpdb->prefix . 'wpci_interest_requests';

    // Validate if the current user is the receiver of this request
    $request = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $requests_table WHERE id = %d AND receiver_id = %d AND status = 'pending'",
        $request_id, $current_user_id
    ));

    if (!$request) {
        echo json_encode(array('success' => false, 'message' => 'Invalid request or permission denied!'));
        wp_die();
    }

    // Accept the interest request
    $wpdb->update($requests_table, array('status' => 'accepted'), array('id' => $request_id));

    echo json_encode(array('success' => true, 'message' => 'Interest request accepted! You can now chat.'));
    wp_die();
}


// AJAX: Send Chat Message
add_action('wp_ajax_wpci_send_message', 'wpci_send_message');

function wpci_send_message()
{
    global $wpdb;
    $sender_id = get_current_user_id();
    $receiver_id = intval($_POST['receiver_id']);
    $message = sanitize_text_field($_POST['message']);

    $messages_table = $wpdb->prefix . 'wpci_chat_messages';
    $wpdb->insert($messages_table, array(
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message' => $message
    ));

    echo json_encode(array('success' => true, 'message' => 'Message sent!'));
    wp_die();
}


add_action('wp_ajax_wpci_fetch_chat', 'wpci_fetch_chat');

function wpci_fetch_chat() {
    global $wpdb;
    $current_user_id = get_current_user_id();
    $receiver_id = intval($_POST['receiver_id']);

    if (!$receiver_id || $receiver_id === $current_user_id) {
        echo json_encode(array('success' => false, 'message' => 'Invalid chat user!'));
        wp_die();
    }

    $messages_table = $wpdb->prefix . 'wpci_chat_messages';

    // Fetch chat history between current user and receiver
    $messages = $wpdb->get_results($wpdb->prepare(
        "SELECT sender_id, message, created_at FROM $messages_table 
         WHERE (sender_id = %d AND receiver_id = %d) 
         OR (sender_id = %d AND receiver_id = %d) 
         ORDER BY created_at ASC",
        $current_user_id, $receiver_id, $receiver_id, $current_user_id
    ));

    $chat_html = "";
    foreach ($messages as $msg) {
        $class = ($msg->sender_id == $current_user_id) ? 'wpci-message-sent' : 'wpci-message-received';
        $chat_html .= "<div class='wpci-message $class'><p>" . esc_html($msg->message) . "</p></div>";
    }

    echo json_encode(array('success' => true, 'chat_html' => $chat_html));
    wp_die();
}
