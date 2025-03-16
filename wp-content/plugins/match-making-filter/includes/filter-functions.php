<?php
function get_filtered_matches() {
    if (!is_user_logged_in()) {
        echo json_encode(array('success' => false, 'message' => 'You must be logged in!'));
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'dating_users';
    $current_user_email = wp_get_current_user()->user_email;

    if (empty($current_user_email)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid user session.'));
        wp_die();
    }

    // Get filter parameters
    $age_min = isset($_POST['age_min']) ? intval($_POST['age_min']) : 18;
    $age_max = isset($_POST['age_max']) ? intval($_POST['age_max']) : 100;
    $gender = !empty($_POST['gender']) ? sanitize_text_field($_POST['gender']) : null;
    $country = !empty($_POST['country']) ? sanitize_text_field($_POST['country']) : null;
    $relationship_type = !empty($_POST['relationship_type']) ? sanitize_text_field($_POST['relationship_type']) : null;

    // Base query
    $query = "SELECT name, age, gender, country, profile_image FROM $table_name WHERE email != %s AND age BETWEEN %d AND %d";
    $params = [$current_user_email, $age_min, $age_max];

    // Add filters dynamically
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

    // Add ORDER BY and LIMIT at the end
    $query .= " ORDER BY RAND() LIMIT 10";

    // Prepare and execute query
    $results = $wpdb->get_results($wpdb->prepare($query, ...$params));

    // Return results
    echo json_encode(['success' => true, 'data' => $results]);
    wp_die();
}

// Register AJAX actions
add_action('wp_ajax_get_filtered_matches', 'get_filtered_matches');
add_action('wp_ajax_nopriv_get_filtered_matches', 'get_filtered_matches');
?>
