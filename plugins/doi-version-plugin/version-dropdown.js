jQuery(document).ready(function($) {
    const $versionContent = $('#version-content');
    const $currentVersion = $('#current-version');
    const $versionToggle = $('#version-toggle');
    const $versionList = $('#version-list');

    function updateContent(version, content) {
        $versionContent.html(content);
        $currentVersion.text(version);
        updateUrl(version);
    }

    function updateUrl(version) {
        const baseUrl = window.location.href.split('/release/')[0];
        const versionNumber = version.replace(/^v/, '');
        const newUrl = `${baseUrl}/release/${versionNumber}/`;
        if (window.location.href !== newUrl) {
            window.history.pushState({ version: versionNumber }, '', newUrl);
        }
    }

    function fetchVersionContent(version) {
        $.ajax({
            url: wp_data.ajaxurl,
            type: 'POST',
            data: {
                action: 'fetch_version_content',
                version: version,
                post_id: wp_data.post_id,
                nonce: wp_data.nonce,
            },
            success: function(response) {
                if (response.success) {
                    updateContent(response.data.version, response.data.content);
                } else {
                    console.error('Error fetching version content:', response.data.message);
                    // Fallback to current version if there's an error
                    fetchVersionContent(wp_data.current_version);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                // Fallback to current version if there's an error
                fetchVersionContent(wp_data.current_version);
            },
        });
    }

    $('.version-item').on('click', function(e) {
        e.preventDefault();
        const version = $(this).data('version');
        fetchVersionContent(version.replace(/^v/, '')); // Remove 'v' prefix if it exists

    });

    $versionToggle.on('click', function() {
        $versionList.toggle();
    });

    $(window).on('popstate', function(event) {
        const pathParts = window.location.pathname.split('/');
        const versionIndex = pathParts.indexOf('release');
        if (versionIndex !== -1 && pathParts[versionIndex + 1]) {
            const version = pathParts[versionIndex + 1];
            fetchVersionContent(version.replace(/^v/, '')); // Remove 'v' prefix if it exists

        } else {
            // If no version in URL, fetch the latest version
            fetchVersionContent(wp_data.current_version);
        }
    });

    // Set initial state
    const pathParts = window.location.pathname.split('/');
    const initialVersion = (pathParts[pathParts.indexOf('release') + 1] || wp_data.current_version).replace(/^v/, '');
    window.history.replaceState({ version: initialVersion }, '', window.location.href);
});

