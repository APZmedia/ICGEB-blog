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
        // Versions are stored as plain numbers (1, 2, 3)
        const newUrl = `${baseUrl}/release/${version}/`;
        if (window.location.href !== newUrl) {
            window.history.pushState({ version: version }, '', newUrl);
        }
    }

    function fetchVersionContent(version) {
        // Normalize version - remove 'v' prefix if present for backward compatibility
        const cleanVersion = String(version).replace(/^v/, '');
        
        $.ajax({
            url: wp_data.ajaxurl,
            type: 'POST',
            data: {
                action: 'fetch_version_content',
                version: cleanVersion,
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
        fetchVersionContent(version);
    });

    $versionToggle.on('click', function() {
        $versionList.toggle();
    });

    $(window).on('popstate', function(event) {
        const pathParts = window.location.pathname.split('/');
        const versionIndex = pathParts.indexOf('release');
        if (versionIndex !== -1 && pathParts[versionIndex + 1]) {
            const version = pathParts[versionIndex + 1];
            fetchVersionContent(version);
        } else {
            // If no version in URL, fetch the latest version
            fetchVersionContent(wp_data.current_version);
        }
    });

    // Set initial state
    const pathParts = window.location.pathname.split('/');
    const initialVersion = (pathParts[pathParts.indexOf('release') + 1] || wp_data.current_version);
    window.history.replaceState({ version: initialVersion }, '', window.location.href);
});
