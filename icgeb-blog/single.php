<?php
// Helper function to get the current version
function get_current_version($post_id) {
    $requested_version = get_query_var('version', false);
    $current_version = get_post_meta($post_id, 'version', true);
    $version_history = get_post_meta($post_id, 'version_history', true);

    if ($requested_version && is_array($version_history) && isset($version_history['v' . $requested_version])) {
        return 'v' . $requested_version;
    }

    return $current_version;
}

get_header();
?>

<main class="min-h-screen">
    <div class="bg-[#0066b3] text-white py-8">
        <div class="container mx-auto px-4 lg:w-full">
            <div class="border-t border-white/20 mb-6"></div>

            <?php if (have_posts()) : while (have_posts()) : the_post(); 
                $post_id = get_the_ID();
                $version_history = get_post_meta($post_id, 'version_history', true);
                $current_version = get_current_version($post_id);
                $doi = get_post_meta($post_id, 'doi', true);
                $author_id = get_the_author_meta('ID');
                $author_first_name = get_the_author_meta('first_name', $author_id);
                $author_last_name = get_the_author_meta('last_name', $author_id);

                $citation = sprintf(
                    '%s, %s (%s). %s. DOI: %s. %s',
                    $author_last_name,
                    $author_first_name,
                    get_the_date('Y'),
                    get_the_title(),
                    $doi,
                    trailingslashit(get_permalink()) . 'release/' . ltrim($current_version, 'v') . '/'
                );
            ?>
                <div id="mainContent">
                    <div class="flex flex-col md:flex-row justify-between items-start">
                        <div class="w-full md:w-3/4 mb-6 md:mb-0">
                            <div class="flex flex-wrap gap-2 mb-4">
                                <?php
                                $categories = get_the_category();
                                if ($categories) {
                                    foreach($categories as $category) {
                                        echo '<span class="px-3 py-1 bg-white/20 rounded-full text-xs sm:text-sm">' . 
                                             esc_html($category->name) . 
                                             '</span>';
                                    }
                                }
                                ?>
                            </div>

                            <h1 class="text-2xl lg:text-4xl mb-4">
                                <?php the_title(); ?>
                            </h1>
                            
                            <?php if (get_post_meta($post_id, 'subtitle', true)) : ?>
                                <h2 class="text-lg md:text-xl mb-4 font-normal">
                                    <?php echo get_post_meta($post_id, 'subtitle', true); ?>
                                </h2>
                            <?php endif; ?>

                            <div class="text-sm italic mt-4">
                                by <?php the_author(); ?>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center bg-white/20 px-3 py-1 rounded-full text-xs sm:text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?>
                                </span>
                                <?php
                                $doi = get_post_meta(get_the_ID(), 'doi', true);
                                $current_version = get_post_meta(get_the_ID(), 'version', true);
                                if ($doi && $current_version) :
                                ?>
                                    <span class="inline-flex items-center bg-white/20 px-3 py-1 rounded-full text-xs sm:text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        DOI: <?php echo esc_html($doi); ?>
                                    </span>
                                    <span class="inline-flex items-center bg-white/20 px-3 py-1 rounded-full text-xs sm:text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        Version:&nbsp;<span id="current-version">
                                            <?php
                                            $post_id = get_the_ID();
                                            $version_history = get_post_meta($post_id, 'version_history', true);
                                            $current_version = get_post_meta($post_id, 'version', true);
                                            $requested_version = get_query_var('version', $current_version);
                                            $display_version = isset($version_history[$requested_version]) ? $requested_version : $current_version;

                                            echo esc_html($display_version);
                                            ?>
                                        </span>
                                    </span>
                                    <div class="version-dropdown inline-block relative">
                                        <button id="version-toggle" class="bg-white/20 hover:bg-white/30 text-white text-xs sm:text-sm py-1 px-3 rounded-full inline-flex items-center">
                                            <span>View Versions</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div id="version-list" class="absolute hidden right-0 mt-2 py-2 w-10 items-center bg-white rounded-md shadow-xl z-20">
                                            <?php
                                            $version_history = get_post_meta(get_the_ID(), 'version_history', true);
                                            if ($version_history && is_array($version_history)) {
                                                foreach (array_keys($version_history) as $version) {
                                                    $version_number = ltrim($version, 'v'); // Remove 'v' for display
                                                    echo sprintf(
                                                        '<a href="%s" class="version-item block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-version="%s">%s</a>',
                                                        esc_url(add_query_arg('version', $version_number, get_permalink())),
                                                        esc_attr($version), // Keep the 'v' prefix for data-version
                                                        esc_html($version)
                                                    );
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex flex-row sm:flex-col gap-2 mt-4 sm:mt-0 border-red-500">
                            <button id="showDetailsBtn" 
                                    class="flex items-center justify-between gap-2 bg-white/20 px-3 py-1 rounded transition-colors hover:bg-white/30 text-xs sm:text-sm">
                                <span>SHOW DETAILS</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="relative">
                                <button id="citeBtn" class="flex items-center gap-2 bg-transparent hover:bg-white/10 px-4 py-2 rounded transition-colors">
                                    <span>CITE</span>
                                    <span class="inline-flex items-center justify-center w-6 h-6 border border-white rounded">#</span>
                                </button>
                                <div id="citeDropdown" class="hidden absolute right-0 mt-2 w-52 lg:w-64 bg-white rounded-md shadow-lg z-30 text-gray-800">
                                    <div class="p-4">
                                        <h3 class="text-md lg:text-lg font-semibold mb-2">Citation</h3>
                                        <div class="bg-gray-100 p-2 rounded text-sm mb-2 break-words overflow-hidden">
                                            <?php echo esc_html($citation); ?>
                                        </div>
                                        <button id="copyCitation" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition-colors">
                                            Copy Citation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="detailsPanel" class="hidden mt-8">
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-xl md:text-2xl"><?php the_title(); ?></h2>
                        <button id="hideDetailsBtn" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded transition-colors">
                            <span>HIDE DETAILS</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div>
                            <h3 class="font-bold mb-2">Contributors</h3>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 bg-white/20 rounded text-sm">
                                    <?php echo substr(get_the_author(), 0, 2); ?>
                                </span>
                                <?php the_author(); ?>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold mb-2">Published</h3>
                            <div><?php the_time('M j, Y'); ?></div>
                        </div>
                        <div>
                            <h3 class="font-bold mb-2">Cite as</h3>
                            <div class="text-sm">
                                <div class="bg-white/10 p-3 rounded mb-2">
                                    <?php echo esc_html($citation); ?>
                                </div>
                                <button class="copy-btn flex items-center gap-2 text-sm hover:bg-white/10 px-2 py-1 rounded"
                                        data-citation="<?php echo esc_attr($citation); ?>">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                    </svg>
                                    Copy
                                </button>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold mb-2">Appears in Collections (<?php echo count($categories); ?>)</h3>
                            <div class="flex flex-col gap-1">
                                <?php
                                if ($categories) {
                                    foreach($categories as $category) {
                                        echo '<div>' . esc_html($category->name) . '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; endif; ?>
        </div>
    </div>

    <div class="bg-white text-gray-800 py-8">
        <div class="container mx-auto px-4 lg:w-full">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article id="version-content" class="content-area content-heading content-paragraph content-list content-links">
                    <?php
                    $post_id = get_the_ID();
                    $current_version = get_current_version($post_id);
                    $version_history = get_post_meta($post_id, 'version_history', true);

                    if (is_array($version_history) && isset($version_history[$current_version])) {
                        echo apply_filters('the_content', $version_history[$current_version]);
                    } else {
                        the_content();
                    }
                    ?>
                </article>
            <?php endwhile; endif; ?>
        </div>
    </div>
</main>

<style>
/* General Gutenberg Block Styling */
.content-area {
    margin-bottom: 1.5rem;
}

/* Headings */
.content-heading h1,
h1 {
    font-size: 2.25rem;
    font-weight: 800; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h2,
h2 {
    font-size: 1.875rem;
    font-weight: 700; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h3,
h3 {
    font-size: 1.5rem;
    font-weight: 700; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h4,
h4 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h5,
h5 {
    font-size: 1rem; 
    font-weight: 500; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h6,
h6 {
    font-size: 0.75rem;
    font-weight: 500; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

/* Paragraphs */
.content-paragraph p {
    font-size: 1rem; 
    color: #4a5568; 
    margin-bottom: 1.5rem;
    line-height: 1.75;
}

/* Lists */
.content-list ul {
    list-style: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
    font-size: 1rem; 
    color: #4a5568; 
}

.content-list li {
    list-style: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
    font-size: 1rem; 
    color: #4a5568; 
}

/* Links */
.content-links a {
    color: #2563eb;
    text-decoration: underline;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.getElementById('mainContent');
    const detailsPanel = document.getElementById('detailsPanel');
    const showDetailsBtn = document.getElementById('showDetailsBtn');
    const hideDetailsBtn = document.getElementById('hideDetailsBtn');
    const copyBtns = document.querySelectorAll('.copy-btn');
    const versionToggle = document.getElementById('version-toggle');

    const versionList = document.getElementById('version-list');


    const versionContent = document.getElementById('version-content');
    const currentVersion = document.getElementById('current-version');
    const citeBtn = document.getElementById('citeBtn');
    const citeDropdown = document.getElementById('citeDropdown');
    const copyCitation = document.getElementById('copyCitation');
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

    // Show/Hide Details Panel
    showDetailsBtn.addEventListener('click', () => {
        mainContent.classList.add('hidden');
        detailsPanel.classList.remove('hidden');
    });

    hideDetailsBtn.addEventListener('click', () => {
        detailsPanel.classList.add('hidden');
        mainContent.classList.remove('hidden');
    });

    // Copy Citation
    copyBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            const citation = btn.dataset.citation;
            try {
                await navigator.clipboard.writeText(citation);
                const originalText = btn.innerHTML;
                btn.innerHTML = 'Copied!';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            } catch (err) {
                console.error('Failed to copy text: ', err);
            }
        });
    });

    // Version Dropdown
    versionToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        versionList.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!versionToggle.contains(e.target) && !versionList.contains(e.target)) {
            versionList.classList.add('hidden');
        }
    });

    // Version Selection
    document.querySelectorAll('.version-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            let version = item.dataset.version;
            if (!version.startsWith('v')) {
                version = 'v' + version; // Ensure the version key starts with 'v'
            }
            fetchVersionContent(version);
            versionList.classList.add('hidden');
        });
    });

    // Fetch Version Content
    function fetchVersionContent(version) {
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'fetch_version_content',
                post_id: <?php echo get_the_ID(); ?>,
                version: version,
                nonce: '<?php echo wp_create_nonce('fetch_version_nonce'); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                versionContent.innerHTML = data.data.content;
                currentVersion.textContent = data.data.version;
                updateUrl(version);
            } else {
                console.error('Error fetching version content:', data.data.message);
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
        });
    }

    // Update URL
    function updateUrl(version) {
        const baseUrl = window.location.pathname.split('/release/')[0];
        const newUrl = `${baseUrl}/release/${version.replace('v', '')}/`;
        window.history.pushState({ version: version }, '', newUrl);
    }

    // Citation Dropdown
    citeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        citeDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!citeBtn.contains(e.target) && !citeDropdown.contains(e.target)) {
            citeDropdown.classList.add('hidden');
        }
    });

    copyCitation.addEventListener('click', async () => {
        const citation = document.querySelector('#citeDropdown .bg-gray-100').textContent;
        try {
            await navigator.clipboard.writeText(citation);
            copyCitation.textContent = 'Copied!';
            setTimeout(() => {
                copyCitation.textContent = 'Copy Citation';
            }, 2000);
        } catch (err) {
            console.error('Failed to copy citation: ', err);
        }
    });

    // Initial state setting
    const pathParts = window.location.pathname.split('/');
    const versionIndex = pathParts.indexOf('release');
    const initialVersion = versionIndex !== -1 && pathParts[versionIndex + 1] 
        ? 'v' + pathParts[versionIndex + 1] 
        : '<?php echo esc_js(get_post_meta(get_the_ID(), 'version', true)); ?>';

    if (initialVersion !== '<?php echo esc_js(get_post_meta(get_the_ID(), 'version', true)); ?>') {
        fetchVersionContent(initialVersion);
    }
    window.history.replaceState({ version: initialVersion }, '', window.location.href);

    // Handle browser back/forward
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.version) {
            fetchVersionContent(event.state.version);
        }
    });
});
</script>

<?php get_footer(); ?>

