jQuery(document).ready(function ($) {
    $('.version-item').on('click', function (e) {
        e.preventDefault(); // Prevent the default link behavior

        const url = $(this).attr('href');
        const version = url.split('/').pop(); // Extract the version from the URL

        console.log('Selected URL:', url); // Debug the URL
        console.log('Selected Version:', version); // Debug the version

        // Update the browser's URL
        window.history.pushState({}, '', url);

        // Make an AJAX request to fetch the content for the selected version
        $.ajax({
            url: wp_data.ajaxurl,
            type: 'POST',
            data: {
                action: 'fetch_version_content',
                version: version,
                post_id: wp_data.post_id,
                security: wp_data.fetch_nonce,
            },
            success: function (response) {
                if (response.success) {
                    $('#version-content').html(response.data.content); // Update the content
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown); // Debug the error
                alert('An unexpected error occurred. Please try again later.');
            },
        });
    });
});

jQuery(document).ready(function($) {
    $('#version-toggle').on('click', function() {
        $('#version-list').toggle();
    });
});
