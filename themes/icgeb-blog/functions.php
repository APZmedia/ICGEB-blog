<?php
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

// Add DOI meta box
function doi_meta_box_setup() {
    add_meta_box(
        'doi_meta_box',           // Unique ID
        'DOI (Digital Object Identifier)', // Meta box title
        'doi_meta_box_callback', // Callback function
        'post',                  // Post type
        'side',                  // Context
        'default'                // Priority
    );
}
add_action('add_meta_boxes', 'doi_meta_box_setup');

// Callback function for rendering the DOI meta box
function doi_meta_box_callback($post) {
    // Add a nonce field for security
    wp_nonce_field('save_doi_meta_box', 'doi_meta_box_nonce');

    // Retrieve the current DOI value
    $current_doi = get_post_meta($post->ID, 'doi', true);

    // Render input field
    echo '<label for="doi_field">DOI:</label>';
    echo '<input type="text" id="doi_field" name="doi_field" value="' . esc_attr($current_doi) . '" size="25" />';
    // echo '<p class="description">Please enter a DOI. This field is required to publish the post.</p>';
}

// Save DOI field data
function save_doi_meta_box($post_id) {
    // Verify nonce
    if (!isset($_POST['doi_meta_box_nonce']) || !wp_verify_nonce($_POST['doi_meta_box_nonce'], 'save_doi_meta_box')) {
        return;
    }

    // Do not save during autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save or update the DOI
    if (isset($_POST['doi_field'])) {
        $doi = sanitize_text_field($_POST['doi_field']);
        update_post_meta($post_id, 'doi', $doi);
    }
}
add_action('save_post', 'save_doi_meta_box');

// Enqueue Admin Script for DOI Validation
function enqueue_doi_admin_script($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_script(
            'doi-validation',
            get_template_directory_uri() . '/js/doi-validation.js', // Path to the JavaScript file
            ['jquery'],
            null,
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'enqueue_doi_admin_script');

function icgeb_register_menus() {
    register_nav_menus(array(
        'primary-menu' => __('Primary Menu', 'icgeb-theme'), // Register "Primary Menu"
    ));
}
add_action('after_setup_theme', 'icgeb_register_menus');

function add_custom_menu_link_classes($classes, $item, $args) {
    if (in_array('current-menu-item', $classes)) {
        $classes[] = 'text-[#6EC1FF]'; // Add Tailwind's `font-bold` class for the active menu item
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_custom_menu_link_classes', 10, 3);


add_filter('preview_post_link', function ($url, $post) {
    return get_permalink($post->ID) . '&preview=true';
}, 10, 2);

add_action('init', function() {
    flush_rewrite_rules();
});

function custom_search_query($query) {
    if ($query->is_main_query() && $query->is_search() && !is_admin()) {
        $query->set('post_type', 'post'); // Search only posts
    }
}
add_action('pre_get_posts', 'custom_search_query');



add_action('init', 'custom_search_rewrite_rule');
function custom_search_rewrite_rule() {
    add_rewrite_rule('^search/(.+)/?$', 'index.php?s=$matches[1]', 'top');
    add_rewrite_rule('^search/?$', 'index.php?s=', 'top');
}


function my_plugin_enqueue_scripts() {
    wp_enqueue_script(
        'my-script-handle', 
        plugin_dir_url(__FILE__) . 'js/my-script.js', 
        ['jquery'], 
        '1.0', 
        true
    );

    // Create a JS object called "my_ajax_object"
    wp_localize_script(
        'my-script-handle',    // Must match the handle in wp_enqueue_script
        'my_ajax_object',      // The name of the JS object available in the script
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('submit-contact-form'),
        ]
    );
}
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');


// Handle contact form submission
function handle_contact_form_submission() {
    check_ajax_referer('submit-contact-form', 'security');

    if ( empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message']) ) {
        wp_send_json_error(['message' => 'All fields are required.']);
    }

    $name    = sanitize_text_field($_POST['name']);
    $email   = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    // Save the submission to the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form_submissions';

    $result = $wpdb->insert(
        $table_name,
        [
            'name'    => $name,
            'email'   => $email,
            'message' => $message,
            'time'    => current_time('mysql'),
        ],
        ['%s', '%s', '%s', '%s']
    );

    if ( $result ) {
        /**
         * 1) Prepare the email content
         */
        $admin_email = $admin_email = defined('ADMIN_CONTACT_EMAIL')
    ? ADMIN_CONTACT_EMAIL
    : get_option('admin_email');  // fallback

        $subject     = 'New Contact Form Submission';
        $body        = sprintf(
            '<strong>Name:</strong> %s <br/>
             <strong>Email:</strong> %s <br/>
             <strong>Message:</strong> %s <br/>',
            esc_html($name),
            esc_html($email),
            nl2br(esc_html($message))
        );

        // HTML email headers
        $headers = array('Content-Type: text/html; charset=UTF-8');

        /**
         * 2) Send the email
         */
        wp_mail($admin_email, $subject, $body, $headers);

        // Finally send back success response for AJAX
        wp_send_json_success(['message' => 'Your message has been sent successfully.']);
    } else {
        wp_send_json_error(['message' => 'Failed to submit your message. Please try again.']);
    }
}
add_action('wp_ajax_submit_contact_form', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact_form', 'handle_contact_form_submission');


