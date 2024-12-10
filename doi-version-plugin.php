<?php
/**
 * Plugin Name: DOI and Version for Articles
 * Description: Adds a unique DOI and versioning system for articles.
 * Version: 1.2
 * Author: APZMedia
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DOI_Version_Plugin {
    public function __construct() {
        add_action('init', array($this, 'register_post_meta'));
        add_action('publish_post', array($this, 'generate_doi'), 10, 2);
        add_action('post_updated', array($this, 'increment_version'), 10, 3);
        add_filter('the_content', array($this, 'display_doi_version'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('init', array($this, 'add_rewrite_rules'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'redirect_to_latest_version'));
        add_action('wp_ajax_fetch_version_content', array($this, 'fetch_version_content'));
        add_action('wp_ajax_nopriv_fetch_version_content', array($this, 'fetch_version_content'));
        add_action('wp_footer', function () {
            if (is_single()) {
                $post_id = get_the_ID();
                $version_history = get_post_meta($post_id, 'version_history', true);
                echo '<pre>' . print_r($version_history, true) . '</pre>';
            }
        });
    }


    public function register_post_meta() {
        register_post_meta('post', 'doi', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));

        register_post_meta('post', 'version', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));

        register_post_meta('post', 'version_history', array(
            'show_in_rest' => false, // Do not expose history in REST
            'single' => true,
            'type' => 'array',
            'default' => array(),
        ));
    }

    public function generate_doi($post_id, $post) {
        if ($post->post_type !== 'post') {
            return;
        }

        $existing_doi = get_post_meta($post_id, 'doi', true);
        if (empty($existing_doi)) {
            $doi = $this->create_unique_doi();
            update_post_meta($post_id, 'doi', $doi);
            update_post_meta($post_id, 'version', 'v1'); // Initialize version as v1
            update_post_meta($post_id, 'version_history', array(
                'v1' => $post->post_content,
            ));
        }
    }

    private function create_unique_doi() {
        $prefix = '10.1234'; // Replace with your DOI prefix
        $suffix = uniqid();
        return $prefix . '/' . $suffix;
    }

    public function increment_version($post_id, $post_after, $post_before) {
        if ($post_after->post_type !== 'post' || $post_after->post_content === $post_before->post_content) {
            return;
        }

        // Avoid incrementing if this is a new post being created
        if ($post_before->post_status === 'auto-draft' || $post_before->post_status === 'draft') {
        return;
        }

        // Avoid double incrementing within a single request
        if (get_transient('post_version_update_' . $post_id)) {
            return;
        }

        set_transient('post_version_update_' . $post_id, true, 5); // Prevent re-trigger for 5 seconds

        // Fetch the current version
        $current_version = get_post_meta($post_id, 'version', true);

        // Increment the numeric part of the version
        preg_match('/v(\d+)/', $current_version, $matches);
        $current_version_number = isset($matches[1]) ? intval($matches[1]) : 1;
        $new_version = 'v' . ($current_version_number + 1);

        update_post_meta($post_id, 'version', $new_version);

        $version_history = get_post_meta($post_id, 'version_history', true);
        if (!is_array($version_history)) {
            $version_history = array();
        }
        $version_history[$new_version] = $post_after->post_content;
        update_post_meta($post_id, 'version_history', $version_history);
    }

    public function display_doi_version($content) {
        if (is_single() && get_post_type() === 'post') {
            $post_id = get_the_ID();
            $doi = get_post_meta($post_id, 'doi', true);
            $requested_version = get_query_var('version', false); // Retrieve version from URL
            $version_history = get_post_meta($post_id, 'version_history', true);
            if (!is_array($version_history)) {
                $version_history = array();
            }

            if (!$requested_version) {
                $requested_version = get_post_meta($post_id, 'version', true);
            }

            // Default to current version if no specifc version ir requested
            $version = isset($version_history[$requested_version]) ? $requested_version : get_post_meta($post_id, 'version', true);

            $content = '<div class="doi-version-info">';
            $content .= '<p>DOI: ' . esc_html($doi) . '</p>';
            $content .= '<p>Version: ' . esc_html($version) . '</p>';
            $content .= '<div id="version-content">' . wp_kses_post($version_history[$version]) . '</div>';
            $content .= $this->get_version_dropdown_html($post_id);
            $content .= '</div>';
        }

        return $content;
    }

    private function get_version_dropdown_html($post_id) {
        $version_history = get_post_meta($post_id, 'version_history', true);
        if (!is_array($version_history) || empty($version_history)) {
            return '<p>No versions available.</p>';
        }

        $current_url = get_permalink($post_id);      

        $html = '<div class="version-dropdown">';
        $html .= '<button id="version-toggle">View Versions</button>';
        $html .= '<div id="version-list" style="display: none;">';

        foreach ($version_history as $version => $content) {
            $html .= sprintf(
                '<a href="%s" class="version-item">%s</a>',
                esc_url(trailingslashit($current_url) . 'release/' . esc_attr(ltrim($version, 'v'))),
                esc_html($version)
            );        
        }
        $html .= '</div></div>';

        return $html;
    }

    public function fetch_version_content() {
        if (!check_ajax_referer('fetch_version_nonce', 'security', false)) {
            error_log('Nonce validation failed.');
            wp_send_json_error(array('message' => 'Invalid security token.'));
            return;
        }

        $post_id = intval($_POST['post_id']);
        $version = sanitize_text_field($_POST['version']);
        error_log("Fetching version content: Post ID = $post_id, Version = $version");

        $version_history = get_post_meta($post_id, 'version_history', true);
        if (!is_array($version_history)) {
            error_log('Version history not found or invalid.');
            wp_send_json_error(array('message' => 'Version history not found.'));
            return;
        }

        $version = 'v' . intval($version);
        if (isset($version_history[$version])) {
            wp_send_json_success(array(
                'content' => wp_kses_post($version_history[$version]),
            ));
        } else {
            error_log("Version $version not found in history.");
            wp_send_json_error(array('message' => 'Version not found.'));
        }
    }


    public function filter_post_version($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') === 'post' && $query->is_single()) {
            $version = get_query_var('version', false);

            if ($version) {
                $post_id = $query->get_queried_object_id();

                if ($post_id) {
                    $version_history = get_post_meta($post_id, 'version_history', true);

                    // Map numeric version to v<number>
                    $version = 'v' . intval($version);

                    if (isset($version_history[$version])) {
                        add_filter('the_content', function () use ($version, $version_history) {
                            return wp_kses_post($version_history[$version]);
                        });
                    } else {
                        // If the version doesn't exist, show a 404 error
                        $query->set_404();
                        status_header(404);
                        nocache_headers();
                    }
                }
            }
        }
    }

    public function redirect_to_latest_version() {
        if (is_singular('post') && get_query_var('version') === '') {
            global $post;

            // Fetch version history
            $version_history = get_post_meta($post->ID, 'version_history', true);
            if (is_array($version_history)) {
                // Find the latest version (highest numeric version)
                $latest_version = max(array_map(function ($v) {
                    return intval(substr($v, 1)); // Extract numeric part of version
                }, array_keys($version_history)));

                // Redirect to the latest version
                wp_redirect(get_permalink($post->ID) . 'release/' . $latest_version);
                exit;
            }
        }
    }


    public function enqueue_scripts() {
        if (is_single() && get_post_type() === 'post') {
            wp_enqueue_script(
                'version-dropdown-js',
                plugin_dir_url(__FILE__) . 'version-dropdown.js',
                array('jquery'),
                '1.0',
                true
            );

            // Localize script to pass dynamic data
            wp_localize_script('version-dropdown-js', 'wp_data', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'fetch_nonce' => wp_create_nonce('fetch_version_nonce'),
                'post_id' => get_the_ID(),
            ));

            // Enqueue optional styles for better dropdown appearance
            wp_enqueue_style(
                'doi-version-style-css',
                plugin_dir_url(__FILE__) . 'doi-version-style.css',
                '1.0'
            );
        }
    }

    public function add_rewrite_rules() {
        // Match release with a version number
        add_rewrite_rule(
            '^([^/]+)/release/(\d+)/?$',
            'index.php?name=$matches[1]&version=$matches[2]&post_type=post',
            'top'
        );

        // Match release without a version number
        add_rewrite_rule(
            '^([^/]+)/release/?$',
            'index.php?name=$matches[1]&post_type=post',
            'top'
        );
    }


    public function add_query_vars($vars) {
        $vars[] = 'version';
        return $vars;
    }

    public function add_admin_menu() {
        add_menu_page(
            'DOI and Version Manager',
            'DOI Manager',
            'manage_options',
            'doi-version-manager',
            array($this, 'admin_page'),
            'dashicons-admin-generic',
            30
        );
    }

    public function admin_page() {
        echo '<div class="wrap">';
        echo '<h1>DOI and Version Manager</h1>';
        echo '<p>Manage DOIs and versions for your articles here.</p>';
        echo '</div>';
    }
}

register_activation_hook(__FILE__, function () {
    (new DOI_Version_Plugin())->add_rewrite_rules();
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');

new DOI_Version_Plugin();
