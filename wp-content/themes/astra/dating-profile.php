<?php
/* Template Name: Dating Profile */
get_header();

global $wpdb;

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $table_name = $wpdb->prefix . 'dating_users';

    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $user_id));

    if ($user) {
        echo "<style>
        .match-container {
    display: flex;
    gap: 20px;
    max-width: 1000px;
    margin: auto;
}

.filter-section {
    width: 30%;
    padding: 20px;
    border-radius: 8px;
    background: #f9f9f9;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.results-section {
    width: 70%;
    padding: 20px;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.match-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.match-card {
    width: 30%;
    padding: 15px;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease-in-out;
}

.match-card:hover {
    transform: scale(1.05);
}

.match-card img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}

.match-card a {
    text-decoration: none;
    color: inherit;
    display: block;
}

.match-info {
    margin-top: 10px;
}

.profile-container {
    max-width: 500px;
    margin: auto;
    text-align: center;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

        </style>";
        echo "<div class='profile-container'>";
        echo "<img src='" . esc_url($user->profile_image ? $user->profile_image : 'default-profile.png') . "' alt='Profile Image'>";
        echo "<h2>" . esc_html($user->name) . "</h2>";
        echo "<p>Age: " . esc_html($user->age) . "</p>";
        echo "<p>Gender: " . esc_html($user->gender) . "</p>";
        echo "<p>Country: " . esc_html($user->country) . "</p>";
        echo "<p>Relationship Type: " . esc_html($user->relationship_type) . "</p>";
        echo "</div>";
    } else {
        echo "<p>User profile not found.</p>";
    }
} else {
    echo "<p>Invalid user profile.</p>";
}

get_footer();
