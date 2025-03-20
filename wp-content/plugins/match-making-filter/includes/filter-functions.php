<?php
function get_filtered_matches() {
    if (!is_user_logged_in()) {
        echo json_encode(array('success' => false, 'message' => 'You must be logged in!'));
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'dating_users';
    $current_user_email = wp_get_current_user()->user_email;

    // Get filter parameters
    $age_min = isset($_POST['age_min']) ? intval($_POST['age_min']) : null;
    $age_max = isset($_POST['age_max']) ? intval($_POST['age_max']) : null;
    $gender = isset($_POST['gender']) ? sanitize_text_field($_POST['gender']) : '';
    $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
    $relationship_type = isset($_POST['relationship_type']) ? sanitize_text_field($_POST['relationship_type']) : '';

    // Build query to load all profiles by default
    $query = "SELECT * FROM $table_name WHERE email != %s";
    $params = [$current_user_email];

    if ($age_min && $age_max) {
        $query .= " AND age BETWEEN %d AND %d";
        array_push($params, $age_min, $age_max);
    }
    if ($gender) {
        $query .= " AND gender = %s";
        $params[] = $gender;
    }
    if ($country) {
        $query .= " AND country = %s";
        $params[] = $country;
    }
    if ($relationship_type) {
        $query .= " AND relationship_type = %s";
        $params[] = $relationship_type;
    }

    // Fetch results with a limit of 20 users
    $query .= " ORDER BY RAND() LIMIT 20";
    $results = $wpdb->get_results($wpdb->prepare($query, ...$params));

    // Return the profiles
    echo json_encode(['success' => true, 'data' => $results]);
    wp_die();
}

add_action('wp_ajax_get_filtered_matches', 'get_filtered_matches');
add_action('wp_ajax_nopriv_get_filtered_matches', 'get_filtered_matches');


// Register AJAX actions
add_action('wp_ajax_get_filtered_matches', 'get_filtered_matches');
add_action('wp_ajax_nopriv_get_filtered_matches', 'get_filtered_matches');
?>
