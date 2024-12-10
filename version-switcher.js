jQuery(document).ready(function ($) {
    // When a version switch button is clicked
    $('.switch-version').on('click', function () {
        const version = $(this).data('version'); // Add this field in wp_localize_script
        const post_id = wp_data.post_id; // Retrieve the post ID from the WordPress data object

        // Make an AJAX request to fetch the selected version's content
        $.ajax({
            url: wp_data.ajaxurl, // The AJAX endpoint provided by WordPress
            type: 'POST',
            data: {
                action: 'fetch_version_content', // Action defined in PHP for fetching version content
                security: wp_data.fetch_nonce, // Nonce for security
                post_id: post_id, // Current post ID
                version: version, // The selected version to fetch
            },
            success: function (response) {
                if (response.success) {
                    // Update the page with the fetched version content
                    $('#version-content').html(response.data.content);
                    // Update the displayed version
                    $('.doi-version-info p:contains("Version:")').text('Version: ' + response.data.version);
                } else {
                    // Handle error case
                    alert('Error: ' + response.data.message || 'Could not fetch version.');
                }
            },
            error: function () {
                // Handle general AJAX error
                alert('An unexpected error occurred. Please try again later.');
            },
        });
    });
});

