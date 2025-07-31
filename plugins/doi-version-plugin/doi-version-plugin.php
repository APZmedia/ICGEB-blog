<?php
/**
 * Plugin Name: DOI and Version for Articles
 * Description: Adds a unique DOI and versioning system for articles.
 * Version: 1.5.1
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('init', array($this, 'add_rewrite_rules'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'handle_version_request'));
        add_action('wp_ajax_fetch_version_content', array($this, 'fetch_version_content'));
        add_action('wp_ajax_nopriv_fetch_version_content', array($this, 'fetch_version_content'));
    }

    public function register_post_meta() {
        register_post_meta('post', 'doi', array('show_in_rest' => true, 'single' => true, 'type' => 'string'));
        register_post_meta('post', 'version', array('show_in_rest' => true, 'single' => true, 'type' => 'string'));
        register_post_meta('post', 'version_history', array('show_in_rest' => false, 'single' => true, 'type' => 'array'));
    }

    public function generate_doi($post_id, $post) {
        if ($post->post_type !== 'post') {
            return;
        }

        $existing_doi = get_post_meta($post_id, 'doi', true);
        if (empty($existing_doi)) {
            $new_doi = '10.1234/' . uniqid();
            update_post_meta($post_id, 'doi', $new_doi);
        }

        $current_version = get_post_meta($post_id, 'version', true);
        if (empty($current_version)) {
            update_post_meta($post_id, 'version', '1');
            update_post_meta($post_id, 'version_history', array('1' => $post->post_content));
        }
    }


    public function increment_version($post_id, $post_after, $post_before) {
        if ($post_after->post_type !== 'post' || 
            $post_after->post_content === $post_before->post_content ||
            $post_before->post_status === 'auto-draft' || 
            $post_before->post_status !== 'publish') {
            return;
        }
        $version_history = get_post_meta($post_id, 'version_history', true);
        $current_version = get_post_meta($post_id, 'version', true);
        $new_version = strval(intval($current_version) + 1);
        $version_history[$new_version] = $post_after->post_content;
        update_post_meta($post_id, 'version', $new_version);
        update_post_meta($post_id, 'version_history', $version_history);
    }

    public function display_doi_version($content) {
        if (!is_single() || get_post_type() !== 'post') {
            return $content;
        }
        $post_id = get_the_ID();
        $doi = get_post_meta($post_id, 'doi', true);
        $current_version = get_post_meta($post_id, 'version', true);
        $version_history = get_post_meta($post_id, 'version_history', true);
        $requested_version = get_query_var('version', $current_version);
        $display_version = isset($version_history[$requested_version]) ? $requested_version : $current_version;
        $info = '<div class="doi-version-info">';
        $info .= '<p style="display: none;">DOI: ' . esc_html($doi) . '</p>';
        $info .= '<p style="display: none;">Version: <span id="current-version">' . esc_html($display_version) . '</span></p>';
        $info .= '</div>';
        $display_content = isset($version_history[$display_version]) ? $version_history[$display_version] : $content;
        return $info . '<div id="version-content">' . $display_content . '</div>';
    }

    public function enqueue_scripts() {
        if (is_single() && get_post_type() === 'post') {
            wp_enqueue_script('version-dropdown-js', plugin_dir_url(__FILE__) . 'version-dropdown.js', array('jquery'), '1.3', true);
        }
    }

    public function add_rewrite_rules() {
        add_rewrite_rule('([^/]+)/release/([0-9]+)/?$', 'index.php?name=$matches[1]&version=$matches[2]', 'top');
        
        // Force flush if this is the first time
        if (!get_option('doi_version_rewrite_flushed')) {
            flush_rewrite_rules();
            update_option('doi_version_rewrite_flushed', true);
        }
    }

    public function add_query_vars($vars) {
        $vars[] = 'version';
        return $vars;
    }

    public function handle_version_request() {
        if (is_preview()) {
            return; 
        }
        if (is_single() && get_post_type() === 'post') {
            $version = get_query_var('version', false);
            if ($version === false) {
                $post_id = get_the_ID();
                $latest_version = get_post_meta($post_id, 'version', true);
                wp_redirect(trailingslashit(get_permalink($post_id)) . 'release/' . ltrim($latest_version, 'v') . '/');
                exit;
            }
        }
    }

    public function fetch_version_content() {
        check_ajax_referer('fetch_version_nonce', 'nonce');
        $post_id = intval($_POST['post_id']);
        $version = ltrim(sanitize_text_field($_POST['version']), 'v');
        $version_history = get_post_meta($post_id, 'version_history', true);
        if (isset($version_history[$version])) {
            wp_send_json_success(array('content' => apply_filters('the_content', $version_history[$version]), 'version' => $version));
        } else {
            wp_send_json_error(array('message' => 'Version not found.'));
        }
    }
}

new DOI_Version_Plugin();

register_activation_hook(__FILE__, function() {
    $plugin = new DOI_Version_Plugin();
    $plugin->add_rewrite_rules();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
