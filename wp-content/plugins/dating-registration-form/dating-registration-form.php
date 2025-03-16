<?php
/**
 * Plugin Name: Dating Registration Form
 * Description: A registration form for a matchmaking dating site with login support.
 * Version: 1.2
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue Styles and Scripts
function dating_register_enqueue_assets() {
    wp_enqueue_style('dating-form-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('dating-form-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);
    wp_localize_script('dating-form-script', 'dating_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'dating_register_enqueue_assets');

// Shortcode to Display Form
function dating_registration_form_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'form-template.php';
    return ob_get_clean();
}
add_shortcode('dating_registration_form', 'dating_registration_form_shortcode');

// Handle Registration
function dating_handle_registration() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        global $wpdb;

        // Validate required fields
        if (!isset($_POST['name'], $_POST['email'], $_POST['password'])) {
            echo json_encode(array('success' => false, 'message' => 'Missing required fields.'));
            wp_die();
        }

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $age = intval($_POST['age']);
        $height = sanitize_text_field($_POST['height']);
        $gender = sanitize_text_field($_POST['gender']);
        $interested_in = sanitize_text_field($_POST['interested_in']);
        $country = sanitize_text_field($_POST['country']);
        $phone = sanitize_text_field($_POST['phone']);
        $description = sanitize_textarea_field($_POST['description']);
        $likes = sanitize_text_field($_POST['likes']);
        $income = sanitize_text_field($_POST['income']);
        $zodiac = sanitize_text_field($_POST['zodiac']);
        $relationship_type = sanitize_text_field($_POST['relationship_type']);

        // Check if email exists
        if (email_exists($email)) {
            echo json_encode(array('success' => false, 'message' => 'Email already registered!'));
            wp_die();
        }

        // Create WordPress user
        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            echo json_encode(array('success' => false, 'message' => 'User registration failed: ' . $user_id->get_error_message()));
            wp_die();
        }

        // Set user role as "subscriber" (or custom role for dating users)
        wp_update_user(array('ID' => $user_id, 'role' => 'subscriber'));

        // Profile Image Upload
        $profile_image = '';
        if (!empty($_FILES['profile_image']['name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $upload = wp_handle_upload($_FILES['profile_image'], array('test_form' => false));

            if (isset($upload['error'])) {
                echo json_encode(array('success' => false, 'message' => 'Image upload failed: ' . $upload['error']));
                wp_die();
            }

            if (isset($upload['url'])) {
                $profile_image = $upload['url'];
            }
        }

        // Insert into Custom Dating Table
        $table_name = $wpdb->prefix . 'dating_users';
        $result = $wpdb->insert($table_name, array(
            'user_id' => $user_id,
            'name' => $name,
            'email' => $email,
            'age' => $age,
            'height' => $height,
            'gender' => $gender,
            'interested_in' => $interested_in,
            'country' => $country,
            'phone' => $phone,
            'profile_image' => $profile_image,
            'description' => $description,
            'likes' => $likes,
            'income' => $income,
            'zodiac' => $zodiac,
            'relationship_type' => $relationship_type,
            'created_at' => current_time('mysql'),
        ));

        if ($result === false) {
            echo json_encode(array('success' => false, 'message' => 'Database error: ' . $wpdb->last_error));
            wp_delete_user($user_id); // Rollback WordPress user if DB insert fails
            wp_die();
        }

        echo json_encode(array('success' => true, 'message' => 'Registration successful! You can now log in.'));
        wp_die();
    }

    echo json_encode(array('success' => false, 'message' => 'Invalid request method.'));
    wp_die();
}
add_action('wp_ajax_dating_handle_registration', 'dating_handle_registration');
add_action('wp_ajax_nopriv_dating_handle_registration', 'dating_handle_registration');

// Create Database Table on Plugin Activation
function dating_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dating_users';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        age INT NOT NULL,
        height VARCHAR(50) NOT NULL,
        gender VARCHAR(50) NOT NULL,
        interested_in VARCHAR(50) NOT NULL,
        country VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        profile_image TEXT NULL,
        description TEXT NULL,
        likes VARCHAR(255) NULL,
        income VARCHAR(100) NULL,
        zodiac VARCHAR(50) NULL,
        relationship_type VARCHAR(100) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'dating_create_table');

?>
