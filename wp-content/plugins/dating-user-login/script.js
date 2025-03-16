jQuery(document).ready(function($) {
    $('#dating-login-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.post(dating_ajax.ajax_url, formData, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                $('#login-message').html('<p style="color:red;">' + data.message + '</p>');
            }
        });
    });
});
