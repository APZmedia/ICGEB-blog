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
        }
    }

    private function create_unique_doi() {
        $prefix = '10.1234'; // Replace with your DOI prefix
        $suffix = uniqid();
        return $prefix . '/' . $suffix;
    }

    public function increment_version($post_id, $post_after, $post_before) {
        if ($post_after->post_type !== 'post') {
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
    }

    public function display_doi_version($content) {
        if (is_single() && get_post_type() === 'post') {
            $post_id = get_the_ID();
            $doi = get_post_meta($post_id, 'doi', true);
            $version = get_post_meta($post_id, 'version', true);

            $doi_version_info = '<div class="doi-version-info">';
            $doi_version_info .= '<p>DOI: ' . esc_html($doi) . '</p>';
            $doi_version_info .= '<p>Version: ' . esc_html($version) . '</p>';
            $doi_version_info .= '</div>';

            $content = $doi_version_info . $content;
        }

        return $content;
    }
}

new DOI_Version_Plugin();
