<?php
function get_filtered_matches() {
    if (!is_user_logged_in()) {
        echo json_encode(array('success' => false, 'message' => 'You must be logged in!'));
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'dating_users';
    $current_user_email = wp_get_current_user()->user_email;

    $age_min = isset($_POST['age_min']) ? intval($_POST['age_min']) : 18;
    $age_max = isset($_POST['age_max']) ? intval($_POST['age_max']) : 100;
    $gender = isset($_POST['gender']) ? sanitize_text_field($_POST['gender']) : '';
    $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
    $relationship_type = isset($_POST['relationship_type']) ? sanitize_text_field($_POST['relationship_type']) : '';

    // Base Query
    $query = "SELECT id, name, age, gender, country, profile_image FROM $table_name WHERE email != %s";
    $params = [$current_user_email];

    // Dynamic Filters
    if (!empty($gender)) {
        $query .= " AND gender = %s";
        $params[] = $gender;
    }
    if (!empty($country)) {
        $query .= " AND country = %s";
        $params[] = $country;
    }
    if (!empty($relationship_type)) {
        $query .= " AND relationship_type = %s";
        $params[] = $relationship_type;
    }

    $query .= " ORDER BY RAND() LIMIT 10";

    // Debugging: Print final query (Check in error_log or response)
    error_log($wpdb->prepare($query, ...$params)); // Debugging SQL Query

    $results = $wpdb->get_results($wpdb->prepare($query, ...$params));

    // Debugging: Check if results are fetched
    if (empty($results)) {
        echo json_encode(['success' => false, 'message' => 'No matches found!']);
    } else {
        echo json_encode(['success' => true, 'data' => $results]);
    }

    wp_die();
}



// Register AJAX actions
add_action('wp_ajax_get_filtered_matches', 'get_filtered_matches');
add_action('wp_ajax_nopriv_get_filtered_matches', 'get_filtered_matches');
?>
