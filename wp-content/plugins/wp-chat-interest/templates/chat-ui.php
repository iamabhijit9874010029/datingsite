<?php
global $wpdb;
$current_user_id = get_current_user_id();

// Fetch the profile user ID correctly
$profile_user_id = get_query_var('author');

// If profile_user_id is empty, get it from query parameters (fallback)
if (!$profile_user_id) {
    $profile_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
}

// Ensure it's not the same as the logged-in user
if ($profile_user_id == $current_user_id || !$profile_user_id) {
    echo "<p>Invalid user profile.</p>";
    return;
}

// Database table reference
$requests_table = $wpdb->prefix . 'wpci_interest_requests';

// Check if a chat request exists and its status
$chat_status = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT status FROM $requests_table 
        WHERE (sender_id = %d AND receiver_id = %d) 
        OR (sender_id = %d AND receiver_id = %d) 
        ORDER BY created_at DESC LIMIT 1",
        $current_user_id,
        $profile_user_id,
        $profile_user_id,
        $current_user_id
    )
);
?>

<div class="wpci-chat-container">
    <?php if (!$chat_status): ?>
        <!-- No request found, show "Send Interest" button -->
        <button class="wpci-send-interest" data-receiver-id="<?php echo esc_attr($profile_user_id); ?>">Send Interest</button>

    <?php elseif ($chat_status === 'pending'): ?>
        <!-- Request pending, show "Accept Request" button for the receiver -->
        <?php
        $pending_request = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $requests_table 
                WHERE receiver_id = %d AND sender_id = %d AND status = 'pending'",
                $current_user_id,
                $profile_user_id
            )
        );
        if ($pending_request): ?>
            <button class="wpci-accept-request" data-request-id="<?php echo esc_attr($pending_request); ?>">Accept Request</button>
        <?php endif; ?>

    <?php elseif ($chat_status === 'accepted'): ?>
        <!-- Request accepted, show chat UI -->
        <p>Chat Request Accepted. Start Chatting!</p>
        <div id="wpci-chat-box">
            <div class="wpci-chat-container">
                <div id="wpci-chat-history">
                    <!-- Chat messages will be dynamically loaded here -->
                </div>
            </div>
            <textarea id="wpci-chat-message"></textarea>
            <button class="wpci-send-message" data-receiver-id="<?php echo esc_attr($profile_user_id); ?>">Send</button>
        </div>
    <?php endif; ?>
</div>