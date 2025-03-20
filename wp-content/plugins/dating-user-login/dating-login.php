<?php
/**
 * Plugin Name: Dating User Login
 * Description: A custom login system for dating site users (excluding admins).
 * Version: 1.1
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue styles and scripts
function dating_login_enqueue_assets() {
    wp_enqueue_style('dating-login-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('dating-login-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);
    wp_localize_script('dating-login-script', 'dating_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'dating_login_enqueue_assets');

// Shortcode to display login form
function dating_login_form_shortcode() {
    ob_start();
    ?>
    <form id="dating-login-form">
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Password:</label>
        <input type="password" name="password" required minlength="6">
        
        <button type="submit">Login</button>
    </form>

    <div id="login-message"></div>
    <script>
        jQuery(document).ready(function($) {
            $('#dating-login-form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.post(dating_ajax.ajax_url, formData + '&action=dating_user_login', function(response) {
                    var data = JSON.parse(response);
                    $('#login-message').html(data.message);
                    if (data.success) {
                        setTimeout(function() { window.location.href = data.redirect; }, 2000);
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('dating_login_form', 'dating_login_form_shortcode');

// Handle user login
function dating_user_login() {
    global $wpdb;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and Password are required.']);
            wp_die();
        }

        // Check if user exists in WordPress
        $user = get_user_by('email', $email);

        if ($user) {
            // Verify password
            if (!wp_check_password($password, $user->user_pass, $user->ID)) {
                echo json_encode(['success' => false, 'message' => 'Invalid password.']);
                wp_die();
            }

            // Allow admins to log in normally
            if (user_can($user, 'administrator')) {
                wp_set_auth_cookie($user->ID, true);
                echo json_encode(['success' => true, 'message' => 'Admin login successful!', 'redirect' => admin_url()]);
                wp_die();
            }

            // Allow non-admin users to log in
            wp_set_auth_cookie($user->ID, true);
            echo json_encode(['success' => true, 'message' => 'Login successful! Redirecting...', 'redirect' => home_url('/dating-dashboard')]);
            wp_die();
        }

        // Check if user exists in the custom dating_users table
        $table_name = $wpdb->prefix . 'dating_users';
        $dating_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email));

        if (!$dating_user) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            wp_die();
        }

        // Verify password
        if (!wp_check_password($password, $dating_user->password)) {
            echo json_encode(['success' => false, 'message' => 'Invalid password.']);
            wp_die();
        }

        // Set session for dating users
        session_start();
        $_SESSION['dating_user_id'] = $dating_user->id;
        $_SESSION['dating_user_email'] = $dating_user->email;

        echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => home_url('/dating-dashboard')]);
        wp_die();
    }
}

add_action('wp_ajax_dating_user_login', 'dating_user_login');
add_action('wp_ajax_nopriv_dating_user_login', 'dating_user_login');


// Logout function
function dating_user_logout() {
    if (is_user_logged_in()) {
        wp_logout(); // Log out WordPress users
    }
    
    // Destroy custom session for dating users
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    
    // Clear cookies
    setcookie('wordpress_logged_in_' . COOKIEHASH, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
    setcookie('wordpress_sec_' . COOKIEHASH, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
    setcookie('dating_user_id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
    
    // Redirect to login page
    wp_redirect(home_url('/dating-login'));
    exit;
}
add_action('wp_ajax_dating_user_logout', 'dating_user_logout');
add_action('wp_ajax_nopriv_dating_user_logout', 'dating_user_logout');


// Redirect logged-in users away from WP admin
function dating_restrict_admin_access() {
    if (is_admin() && !current_user_can('administrator') && !wp_doing_ajax()) {
        wp_redirect(home_url('/dating-dashboard'));
        exit;
    }
}

add_action('init', 'dating_restrict_admin_access');






function dating_logout_button_shortcode() {
    ob_start();
    ?>
    <button id="dating-logout-btn">Logout</button>
<!-- yes -->
    <script>
        document.getElementById('dating-logout-btn').addEventListener('click', function() {
            fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=dating_user_logout"
            }).then(response => {
                window.location.href = "<?php echo home_url('/dating-login'); ?>";
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('dating_logout_button', 'dating_logout_button_shortcode');


?>
