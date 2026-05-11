<?php
/**
 * ICGEB Theme functions and definitions
 */

// Theme Setup: Title tag, post thumbnails, navigation menus
function icgeb_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus(array(
        'primary-menu' => __('Primary Menu', 'icgeb-theme')
    ));
}
add_action('after_setup_theme', 'icgeb_theme_setup');


// Add DOI (Digital Object Identifier) meta box to post editor
function icgeb_doi_meta_box_setup() {
    add_meta_box(
        'icgeb_doi_meta_box',      // Unique ID (prefixed)
        'DOI (Digital Object Identifier)', // Meta box title
        'icgeb_doi_meta_box_callback', // Callback function
        'post',                    // Post type
        'side',                    // Context
        'default'                  // Priority
    );
}
add_action('add_meta_boxes', 'icgeb_doi_meta_box_setup');

// Callback function for rendering the DOI meta box content
function icgeb_doi_meta_box_callback($post) {
    // Add a nonce field for security
    wp_nonce_field('icgeb_save_doi_meta_box', 'icgeb_doi_meta_box_nonce');

    // Retrieve the current DOI value
    $current_doi = get_post_meta($post->ID, '_icgeb_doi', true); // Prefixed meta key

    // Render input field
    echo '<label for="icgeb_doi_field">DOI:</label>';
    echo '<input type="text" id="icgeb_doi_field" name="icgeb_doi_field" value="' . esc_attr($current_doi) . '" size="25" />';
}

// Save DOI field data when a post is saved
function icgeb_save_doi_meta_box($post_id) {
    // Verify nonce
    if (!isset($_POST['icgeb_doi_meta_box_nonce']) || !wp_verify_nonce($_POST['icgeb_doi_meta_box_nonce'], 'icgeb_save_doi_meta_box')) {
        return;
    }

    // Do not save during autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (empty($_POST['post_type']) || 'post' !== $_POST['post_type'] || !current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save or update the DOI
    if (isset($_POST['icgeb_doi_field'])) {
        $doi = sanitize_text_field($_POST['icgeb_doi_field']);
        update_post_meta($post_id, '_icgeb_doi', $doi); // Prefixed meta key
    } else {
        // If the field is not submitted (e.g., empty and you want to delete the meta), you might handle that here.
        // delete_post_meta($post_id, '_icgeb_doi');
    }
}
add_action('save_post_post', 'icgeb_save_doi_meta_box'); // Hook specifically to 'post' post type for saving

// Enqueue Admin Script for DOI Validation (if you have a doi-validation.js file)
function icgeb_enqueue_doi_admin_script($hook) {
    // Only load on post edit screens
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post_type;
        if ('post' === $post_type) { // Only for 'post' post type
            $doi_script_path = get_template_directory() . '/js/doi-validation.js';
            if (file_exists($doi_script_path)) {
                wp_enqueue_script(
                    'icgeb-doi-validation', // Theme-prefixed handle
                    get_template_directory_uri() . '/js/doi-validation.js', // Path to the JavaScript file in your theme
                    array('jquery'), // Dependencies
                    filemtime($doi_script_path), // Version for cache busting
                    true // Load in footer
                );
            }
        }
    }
}
add_action('admin_enqueue_scripts', 'icgeb_enqueue_doi_admin_script');


// Add custom CSS classes to menu items (e.g., for active link styling)
function icgeb_add_custom_menu_link_classes($classes, $item, $args) {
    if (isset($args->theme_location) && 'primary-menu' === $args->theme_location) { // Check for specific menu location
        if (in_array('current-menu-item', $classes) || in_array('current-menu-ancestor', $classes)) {
            $classes[] = 'text-[#6EC1FF]'; // Example: Tailwind class for active item text color
            // $classes[] = 'font-bold'; // Another example if needed
        }
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'icgeb_add_custom_menu_link_classes', 10, 3);

// IMPORTANT: The following `preview_post_link` filter modification is generally NOT needed
// and might interfere with WordPress core preview functionality.
// WordPress handles preview links correctly by default.
// Consider removing this if you encounter preview issues.
/*
add_filter('preview_post_link', function ($url, $post) {
    // This custom logic might not be necessary and could conflict.
    // WordPress default: yourdomain.com/?p=POST_ID&preview=true or /SLUG/?preview_id=POST_ID&preview_nonce=NONCE&preview=true
    return get_permalink($post->ID) . '&preview=true';
}, 10, 2);
*/

// Modify the main query for search results page
function icgeb_custom_search_query($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->set('post_type', 'post'); // Ensure search results are only 'post' type
    }
}
add_action('pre_get_posts', 'icgeb_custom_search_query');

// Add custom rewrite rules for prettier search URLs (e.g., /search/yourterm)
// Rules are added once, then permalinks need to be flushed.
function icgeb_custom_search_rewrite_rule() {
    add_rewrite_rule('^search/(.+)/?$', 'index.php?s=$matches[1]', 'top');
    add_rewrite_rule('^search/?$', 'index.php?s=', 'top');
    // After adding/changing rewrite rules, you must visit Settings > Permalinks and click "Save Changes" ONCE.
}
add_action('init', 'icgeb_custom_search_rewrite_rule');


// Enqueue scripts for the contact form and other general scripts
function icgeb_enqueue_theme_scripts() {
    // Enqueue main.js (which might contain mobile menu logic)
    $main_script_path = get_template_directory() . '/js/main.js';
    if (file_exists($main_script_path)) {
        wp_enqueue_script(
            'icgeb-main-script', // Theme-prefixed handle
            get_template_directory_uri() . '/js/main.js',
            array(), // Dependencies
            filemtime($main_script_path), // Version for cache busting
            true // Load in footer
        );
    }

    // Localize script with data for AJAX calls (used by contact form on page.php)
    // This object will be available if any script is enqueued with 'icgeb-main-script' handle or if another script is enqueued here.
    // If main.js is not for the contact form, you might enqueue a specific contact-form.js here instead.
    wp_localize_script(
        'icgeb-main-script',      // Hook to an enqueued script; if main.js isn't it, change this handle
        'icgeb_contact_form_ajax', // The name of the JS object (prefixed)
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('icgeb_submit_contact_form_nonce'), // Prefixed nonce name
        )
    );
}
add_action('wp_enqueue_scripts', 'icgeb_enqueue_theme_scripts');


// Handle contact form submission via AJAX
function icgeb_handle_contact_form_submission() {
    // Verify the AJAX nonce
    check_ajax_referer('icgeb_submit_contact_form_nonce', 'security');

    // Basic validation
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
        wp_send_json_error(array('message' => 'All fields are required.'));
        return; // Important to exit after sending JSON response
    }
    if (!is_email($_POST['email'])) {
        wp_send_json_error(array('message' => 'Invalid email address.'));
        return;
    }

    $name    = sanitize_text_field($_POST['name']);
    $email   = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    // Save the submission to the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form_submissions'; // Make sure this table exists

    $result = $wpdb->insert(
        $table_name,
        array(
            'name'    => $name,
            'email'   => $email,
            'message' => $message,
            'time'    => current_time('mysql', 1), // GMT time
        ),
        array('%s', '%s', '%s', '%s')
    );

    if ($result) {
        // Prepare email content
        $admin_email = defined('ADMIN_CONTACT_EMAIL') ? ADMIN_CONTACT_EMAIL : get_option('admin_email');
        $subject     = 'New Contact Form Submission from ' . get_bloginfo('name');
        $body        = "You have a new contact form submission:\n\n";
        $body       .= "Name: " . esc_html($name) . "\n";
        $body       .= "Email: " . esc_html($email) . "\n";
        $body       .= "Message:\n" . esc_html($message) . "\n\n";
        $body       .= "Submitted on: " . current_time('mysql') . "\n";

        $headers = array('Content-Type: text/plain; charset=UTF-8'); // Plain text email is often better for deliverability
        $headers[] = 'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>'; // Set a From header
        $headers[] = 'Reply-To: ' . esc_html($name) . ' <' . esc_html($email) . '>';

        // Send the email
        wp_mail($admin_email, $subject, $body, $headers);

        wp_send_json_success(array('message' => 'Your message has been sent successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Failed to submit your message. Database error. Please try again.'));
    }
    // wp_die(); // is called automatically by wp_send_json_success/error
}
// Hook for logged-in users
add_action('wp_ajax_submit_contact_form', 'icgeb_handle_contact_form_submission');
// Hook for non-logged-in users
add_action('wp_ajax_nopriv_submit_contact_form', 'icgeb_handle_contact_form_submission');

/**
 * IMPORTANT: One-time action after theme activation or when rewrite rules are added/changed.
 * It's generally better to hook this to theme activation.
 * For now, after updating this functions.php, you MUST go to
 * WordPress Admin -> Settings -> Permalinks and click "Save Changes" once.
 */
// function icgeb_theme_activation_flush_rewrites() {
//    flush_rewrite_rules();
// }
// add_action('after_switch_theme', 'icgeb_theme_activation_flush_rewrites');

// Function to fetch versioned content for single.php AJAX
function icgeb_fetch_version_content_callback() {
    check_ajax_referer('fetch_version_nonce', 'nonce');

    if (empty($_POST['post_id']) || empty($_POST['version'])) {
        wp_send_json_error(array('message' => 'Missing parameters.'));
    }

    $post_id = intval($_POST['post_id']);
    $requested_version = sanitize_text_field($_POST['version']); // e.g., "v1.2"

    // Ensure the user can read the post
    if (!current_user_can('read_post', $post_id)) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    $version_history = get_post_meta($post_id, 'version_history', true);

    if (is_array($version_history) && isset($version_history[$requested_version])) {
        $content = apply_filters('the_content', $version_history[$requested_version]);
        wp_send_json_success(array(
            'content' => $content,
            'version' => $requested_version
        ));
    } else {
        // Fallback to current post content if specific version not found or history is not an array
        // This case should ideally be handled client-side or by ensuring data integrity.
        // For robustness, let's try to fetch the main content if version is not in history
        $post = get_post($post_id);
        if ($post) {
            $content = apply_filters('the_content', $post->post_content);
             wp_send_json_success(array(
                'content' => $content,
                'version' => get_post_meta($post_id, 'version', true) // Send back the actual current version
            ));
        } else {
            wp_send_json_error(array('message' => 'Version not found or post does not exist.'));
        }
    }
}
add_action('wp_ajax_fetch_version_content', 'icgeb_fetch_version_content_callback');
add_action('wp_ajax_nopriv_fetch_version_content', 'icgeb_fetch_version_content_callback');

function icgeb_get_primary_category_for_post($post_id) {
    $categories = get_the_category($post_id);
    if (empty($categories) || !is_array($categories)) {
        return null;
    }

    $primary_category_id = (int) get_post_meta($post_id, '_yoast_wpseo_primary_category', true);
    if ($primary_category_id > 0) {
        foreach ($categories as $category) {
            if ((int) $category->term_id === $primary_category_id) {
                return $category;
            }
        }
    }

    usort($categories, function($a, $b) {
        return (int) $a->term_id <=> (int) $b->term_id;
    });

    return $categories[0];
}

function icgeb_get_breadcrumb_items() {
    $items = array(array('name' => 'Home', 'url' => home_url('/')));

    if (is_single()) {
        $post_id = get_queried_object_id();
        $primary_category = icgeb_get_primary_category_for_post($post_id);
        if ($primary_category) {
            $items[] = array('name' => $primary_category->name, 'url' => get_category_link($primary_category->term_id));
        }
        $items[] = array('name' => get_the_title($post_id), 'url' => get_permalink($post_id));
    } elseif (is_page()) {
        $page_id = get_queried_object_id();
        $parent_ids = array_reverse(get_post_ancestors($page_id));
        foreach ($parent_ids as $parent_id) {
            $items[] = array('name' => get_the_title($parent_id), 'url' => get_permalink($parent_id));
        }
        $items[] = array('name' => get_the_title($page_id), 'url' => get_permalink($page_id));
    }

    return $items;
}

function icgeb_output_breadcrumb_schema() {
    if (!is_single() && !is_page()) {
        return;
    }

    $breadcrumb_items = icgeb_get_breadcrumb_items();
    if (count($breadcrumb_items) < 2) {
        return;
    }

    $item_list = array();
    foreach ($breadcrumb_items as $item) {
        $name = isset($item['name']) ? trim((string) $item['name']) : '';
        $url = isset($item['url']) ? esc_url_raw((string) $item['url']) : '';
        if ($name === '' || $url === '') {
            continue;
        }

        $item_list[] = array(
            '@type' => 'ListItem',
            'position' => count($item_list) + 1,
            'name' => $name,
            'item' => $url,
        );
    }

    if (count($item_list) < 2) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $item_list,
    );

    echo "\n<script type=\"application/ld+json\">" . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . "</script>\n";
}
add_action('wp_head', 'icgeb_output_breadcrumb_schema', 25);

function icgeb_get_featured_image_alt($post_id) {
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if (!$thumbnail_id) {
        return '';
    }

    $alt = trim((string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true));
    if ($alt !== '') {
        return $alt;
    }

    return sprintf('Featured image for %s', get_the_title($post_id));
}


?>
