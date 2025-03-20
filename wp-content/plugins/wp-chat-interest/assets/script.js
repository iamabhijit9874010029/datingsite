jQuery(document).ready(function ($) {
    $(".wpci-send-interest").click(function () {
        let receiver_id = $(this).data("receiver-id");
        console.log("Sending Interest Request. Receiver ID:", receiver_id); // Debugging

        if (!receiver_id || receiver_id === 0) {
            alert("Invalid receiver ID! Please check the profile page.");
            return;
        }

        $.post(wpci_ajax.ajax_url, {
            action: "wpci_send_interest",
            receiver_id: receiver_id
        }, function (response) {
            try {
                let data = JSON.parse(response);
                alert(data.message);
                if (data.success) location.reload();
            } catch (e) {
                console.error("Error parsing response:", e);
            }
        });
    });

    // Send Message
    $(".wpci-send-message").click(function () {
        let receiver_id = $(this).data("receiver-id");
        let message = $("#wpci-chat-message").val().trim();

        if (!receiver_id || receiver_id === 0) {
            alert("Invalid receiver ID!");
            return;
        }
        if (message.length === 0) {
            alert("Message cannot be empty!");
            return;
        }

        $.post(wpci_ajax.ajax_url, {
            action: "wpci_send_message",
            receiver_id: receiver_id,
            message: message
        }, function (response) {
            try {
                let data = JSON.parse(response);
                if (data.success) {
                    $("#wpci-chat-message").val(""); // Clear input field
                    loadChatHistory(receiver_id); // Reload chat history
                } else {
                    console.error("Message sending failed:", data.message);
                }
            } catch (e) {
                console.error("Error parsing response:", e);
            }
        });
    });

    // Load Chat History
    function loadChatHistory(receiver_id) {
        $.post(wpci_ajax.ajax_url, {
            action: "wpci_fetch_chat",
            receiver_id: receiver_id
        }, function (response) {
            try {
                let data = JSON.parse(response);
                if (data.success) {
                    $("#wpci-chat-history").html(data.chat_html);

                    // Auto-scroll to bottom
                    let chatContainer = $("#wpci-chat-history");
                    chatContainer.scrollTop(chatContainer.prop("scrollHeight"));
                }
            } catch (e) {
                console.error("Error fetching chat history:", e);
            }
        });
    }

    // Auto-refresh chat every 5 seconds
    function startChatPolling(receiver_id) {
        setInterval(function () {
            loadChatHistory(receiver_id);
        }, 5000);
    }

    // Start chat polling when the chat UI loads
    let receiver_id = $(".wpci-send-message").data("receiver-id");
    if (receiver_id) {
        loadChatHistory(receiver_id);
        startChatPolling(receiver_id);
    }

    $(".wpci-accept-request").click(function () {
        let request_id = $(this).data("request-id");

        if (!request_id || request_id === 0) {
            alert("Invalid request ID!");
            return;
        }

        $.post(wpci_ajax.ajax_url, {
            action: "wpci_accept_interest",
            request_id: request_id
        }, function (response) {
            try {
                let data = JSON.parse(response);
                alert(data.message);
                if (data.success) location.reload();
            } catch (e) {
                console.error("Error parsing response:", e);
            }
        });
    });
});
