jQuery(document).ready(function ($) {
    $('#match-filter-form').submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: match_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_filtered_matches',
                age_min: $('input[name="age_min"]').val(),
                age_max: $('input[name="age_max"]').val(),
                gender: $('select[name="gender"]').val(),
                country: $('input[name="country"]').val(),
                relationship_type: $('select[name="relationship_type"]').val()
            },
            beforeSend: function () {
                $('#match-results').html('<p>Loading...</p>');
            },
            success: function (response) {
                let data = JSON.parse(response);
                console.log(data);

                if (data.success && data.data.length > 0) {
                    let resultsHTML = '<h3>Matches Found</h3><div class="match-grid">';

                    data.data.forEach(function (user) {
                        let profileImg = user.profile_image ? user.profile_image : 'default-profile.png';
                        resultsHTML += `
                            <div class="match-card">
                                <img src="${profileImg}" alt="${user.name}">
                                <div class="match-info">
                                    <strong>${user.name}</strong>
                                    <p>Age: ${user.age}</p>
                                    <p>Gender: ${user.gender}</p>
                                    <p>Country: ${user.country}</p>
                                </div>
                            </div>
                        `;
                    });

                    resultsHTML += '</div>';
                    $('#match-results').html(resultsHTML);
                } else {
                    $('#match-results').html('<p>No matches found.</p>');
                }
            },
            error: function () {
                $('#match-results').html('<p>Error fetching matches.</p>');
            }
        });
    });
});
