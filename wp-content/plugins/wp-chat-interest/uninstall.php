<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Define table names
$requests_table = $wpdb->prefix . 'wpci_interest_requests';
$messages_table = $wpdb->prefix . 'wpci_chat_messages';

// Delete the tables
$wpdb->query("DROP TABLE IF EXISTS $requests_table");
$wpdb->query("DROP TABLE IF EXISTS $messages_table");
