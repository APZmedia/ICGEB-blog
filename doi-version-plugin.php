<?php
/**
 * Plugin Name: DOI and Version for Articles
 * Description: Adds a unique DOI and versioning system for articles.
 * Version: 1.2
 * Author: Your Name
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
        add_action('wp_ajax_fetch_version_content', array($this, 'fetch_version_content'));
        add_action('wp_ajax_nopriv_fetch_version_content', array($this, 'fetch_version_content'));
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
            update_post_meta($post_id, 'version', 'v0'); // Initialize version as v0
            update_post_meta($post_id, 'version_history', array(
                'v0' => $post->post_content,
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

        if ($current_version === '') {
            $new_version = 'v0'; // Fallback if the version is missing
        } else {
            // Increment the numeric part of the version
            preg_match('/v(\d+)/', $current_version, $matches);
            $current_version_number = isset($matches[1]) ? intval($matches[1]) : 0;
            $new_version = 'v' . ($current_version_number + 1);
        }

        update_post_meta($post_id, 'version', $new_version);

        $version_history = get_post_meta($post_id, 'version_history', true);
        $version_history[$new_version] = $post_after->post_content;
        update_post_meta($post_id, 'version_history', $version_history);
    }

    public function display_doi_version($content) {
        if (is_single() && get_post_type() === 'post') {
            $post_id = get_the_ID();
            $doi = get_post_meta($post_id, 'doi', true);
            $version = get_post_meta($post_id, 'version', true);

            $content = '<div class="doi-version-info">';
            $content .= '<p>DOI: ' . esc_html($doi) . '</p>';
            $content .= '<p>Version: ' . esc_html($version) . '</p>';
            $content .= '<div id="version-content">' . get_the_content() . '</div>';
            $content .= $this->get_version_switcher_html($post_id);
            $content .= '</div>';
        }

        return $content;
    }

    private function get_version_switcher_html($post_id) {
        $version_history = get_post_meta($post_id, 'version_history', true);
        $html = '<div class="version-switcher">';
        foreach ($version_history as $version => $content) {
            $html .= '<button class="switch-version" data-version="' . esc_attr($version) . '">' . esc_html($version) . '</button>';
        }
        $html .= '</div>';
        return $html;
    }

    public function fetch_version_content() {
        check_ajax_referer('fetch_version_nonce', 'security');

        $post_id = intval($_POST['post_id']);
        $version = sanitize_text_field($_POST['version']);
        $version_history = get_post_meta($post_id, 'version_history', true);

        if (isset($version_history[$version])) {
            // Update the current version in post meta
            update_post_meta($post_id, 'version', $version);

            wp_send_json_success(array(
                'content' => wp_kses_post($version_history[$version]),
                'version' => $version, // Send the updated version back to the front-end
            ));
        } else {
            wp_send_json_error(array('message' => 'Version not found.'));
        }
    }

    public function enqueue_scripts() {
        if (is_single() && get_post_type() === 'post') {
            wp_enqueue_script(
                'version-switcher-js',
                plugin_dir_url(__FILE__) . 'version-switcher.js',
                array('jquery'),
                null,
                true
            );

            wp_localize_script('version-switcher-js', 'wp_data', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('fetch_version_nonce'),
                'post_id' => get_the_ID(),
            ));
        }
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

new DOI_Version_Plugin();
