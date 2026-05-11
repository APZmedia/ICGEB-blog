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
        add_action('add_meta_boxes', array($this, 'add_faq_meta_box'));
        add_action('save_post', array($this, 'save_faq_meta_box'));
        add_action('publish_post', array($this, 'generate_doi'), 10, 2);
        add_action('post_updated', array($this, 'increment_version'), 10, 3);
        add_filter('the_content', array($this, 'display_doi_version'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('init', array($this, 'add_rewrite_rules'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'handle_version_request'));
        add_action('wp_ajax_fetch_version_content', array($this, 'fetch_version_content'));
        add_action('wp_ajax_nopriv_fetch_version_content', array($this, 'fetch_version_content'));
        add_action('wp_head', array($this, 'output_structured_data'), 20);
    }

    public function register_post_meta() {
        register_post_meta('post', 'doi', array('show_in_rest' => true, 'single' => true, 'type' => 'string'));
        register_post_meta('post', 'version', array('show_in_rest' => true, 'single' => true, 'type' => 'string'));
        register_post_meta('post', 'version_history', array('show_in_rest' => false, 'single' => true, 'type' => 'array'));

        foreach (array('post', 'page') as $post_type) {
            register_post_meta($post_type, 'icgeb_faq_enabled', array('show_in_rest' => true, 'single' => true, 'type' => 'boolean', 'default' => false));
            register_post_meta($post_type, 'icgeb_faq_items', array('show_in_rest' => false, 'single' => true, 'type' => 'array', 'default' => array()));
        }
    }

    public function add_faq_meta_box() {
        add_meta_box('icgeb_faq_schema_meta_box', 'FAQ Schema', array($this, 'render_faq_meta_box'), array('post', 'page'), 'normal', 'default');
    }

    public function render_faq_meta_box($post) {
        wp_nonce_field('icgeb_save_faq_schema', 'icgeb_faq_schema_nonce');
        $faq_enabled = (bool) get_post_meta($post->ID, 'icgeb_faq_enabled', true);
        $faq_items = get_post_meta($post->ID, 'icgeb_faq_items', true);
        if (!is_array($faq_items)) {
            $faq_items = array();
        }
        ?>
        <p>
            <label><input type="checkbox" name="icgeb_faq_enabled" value="1" <?php checked($faq_enabled); ?> /> Enable FAQPage schema for this content</label>
        </p>
        <p><label for="icgeb_faq_json"><strong>FAQ entries (JSON array)</strong></label></p>
        <textarea id="icgeb_faq_json" name="icgeb_faq_json" rows="10" style="width:100%;font-family:monospace;"><?php echo esc_textarea(wp_json_encode($faq_items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></textarea>
        <p class="description">Use format: [{"question":"...","answer":"..."}]. Empty or invalid rows are ignored in output.</p>
        <?php
    }

    public function save_faq_meta_box($post_id) {
        if (!isset($_POST['icgeb_faq_schema_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['icgeb_faq_schema_nonce'])), 'icgeb_save_faq_schema')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        update_post_meta($post_id, 'icgeb_faq_enabled', isset($_POST['icgeb_faq_enabled']) ? '1' : '0');
        $raw_json = isset($_POST['icgeb_faq_json']) ? wp_unslash($_POST['icgeb_faq_json']) : '';
        $decoded = json_decode($raw_json, true);
        if (!is_array($decoded)) {
            update_post_meta($post_id, 'icgeb_faq_items', array());
            return;
        }

        $validated_items = array();
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }
            $question = isset($item['question']) ? trim(wp_strip_all_tags((string) $item['question'])) : '';
            $answer = isset($item['answer']) ? trim(wp_kses_post((string) $item['answer'])) : '';
            if ($question === '' || $answer === '') {
                continue;
            }
            $validated_items[] = array('question' => $question, 'answer' => $answer);
        }
        update_post_meta($post_id, 'icgeb_faq_items', $validated_items);
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

    public function output_structured_data() {
        if (!is_singular(array('post', 'page'))) {
            return;
        }

        if (is_singular('post')) {
            $scholarly_article = $this->build_scholarly_article_schema(get_queried_object_id());
            if (!empty($scholarly_article)) {
                echo "\n<script type=\"application/ld+json\">" . wp_json_encode($scholarly_article, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . "</script>\n";
            }
        }

        $faq_page = $this->build_faq_page_schema(get_queried_object_id());
        if (!empty($faq_page)) {
            echo "\n<script type=\"application/ld+json\">" . wp_json_encode($faq_page, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . "</script>\n";
        }
    }

    private function build_scholarly_article_schema($post_id) {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'post') {
            return array();
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'ScholarlyArticle',
            'headline' => get_the_title($post_id),
            'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => get_permalink($post_id)),
            'url' => get_permalink($post_id),
            'datePublished' => get_post_time('c', true, $post_id),
            'dateModified' => get_post_modified_time('c', true, $post_id),
            'publisher' => array('@type' => 'Organization', 'name' => get_bloginfo('name'), 'url' => home_url('/')),
        );

        $author_name = get_the_author_meta('display_name', (int) $post->post_author);
        $orcid_url = trim((string) get_the_author_meta('orcid', (int) $post->post_author));
        if (!empty($author_name)) {
            $author = array('@type' => 'Person', 'name' => $author_name);
            if (!empty($orcid_url)) {
                $author['@id'] = $orcid_url;
                $author['sameAs'] = array($orcid_url);
            }
            $schema['author'] = $author;
        }

        $doi = trim((string) get_post_meta($post_id, 'doi', true));
        if (!empty($doi)) {
            $schema['identifier'] = array('@type' => 'PropertyValue', 'propertyID' => 'DOI', 'value' => $doi);
        }

        $tags = get_the_terms($post_id, 'post_tag');
        if (is_array($tags) && !empty($tags)) {
            $schema['keywords'] = implode(', ', wp_list_pluck($tags, 'name'));
        }

        return $schema;
    }

    private function build_faq_page_schema($post_id) {
        if (!(bool) get_post_meta($post_id, 'icgeb_faq_enabled', true)) {
            return array();
        }

        $faq_items = get_post_meta($post_id, 'icgeb_faq_items', true);
        if (!is_array($faq_items) || empty($faq_items)) {
            return array();
        }

        $main_entity = array();
        foreach ($faq_items as $item) {
            $question = isset($item['question']) ? trim((string) $item['question']) : '';
            $answer = isset($item['answer']) ? trim((string) $item['answer']) : '';
            if ($question === '' || $answer === '') {
                continue;
            }
            $main_entity[] = array('@type' => 'Question', 'name' => $question, 'acceptedAnswer' => array('@type' => 'Answer', 'text' => wp_strip_all_tags($answer)));
        }

        if (empty($main_entity)) {
            return array();
        }

        return array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $main_entity);
    }
}

new DOI_Version_Plugin();

register_activation_hook(__FILE__, function() {
    $plugin = new DOI_Version_Plugin();
    $plugin->add_rewrite_rules();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
