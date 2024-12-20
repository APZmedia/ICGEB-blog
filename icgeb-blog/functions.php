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
    echo '<p class="description">Please enter a DOI. This field is required to publish the post.</p>';
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

// Flush rewrite rules on theme activation
function icgeb_rewrite_flush() {
    custom_search_rewrite_rule();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'icgeb_rewrite_flush');

function custom_search_rewrite_rule() {
    add_rewrite_rule('^search/?$', 'index.php?post_type=post&s=', 'top');
}
add_action('init', 'custom_search_rewrite_rule');


add_action('template_redirect', function () {
    if (is_search() && empty(get_query_var('s'))) {
        global $wp_query;
        $wp_query->is_search = true;
        $wp_query->is_home = false; // Prevent treating it as a home page
    }
});

function custom_search_query($query) {
    if ($query->is_main_query() && $query->is_search() && !is_admin()) {
        $query->set('post_type', 'post'); // Search only posts
    }
}
add_action('pre_get_posts', 'custom_search_query');

