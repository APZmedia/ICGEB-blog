# Development Guide - ICGEB Blog

## Table of Contents
1. [Development Environment Setup](#development-environment-setup)
2. [Code Standards](#code-standards)
3. [Architecture Overview](#architecture-overview)
4. [Plugin Development](#plugin-development)
5. [Theme Development](#theme-development)
6. [Database Schema](#database-schema)
7. [API Development](#api-development)
8. [Testing](#testing)
9. [Deployment](#deployment)
10. [Contributing](#contributing)

## Development Environment Setup

### Prerequisites
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **WordPress**: Latest stable version
- **Web Server**: Apache or Nginx
- **Git**: Version control

### Local Development Setup

#### 1. WordPress Installation
```bash
# Download WordPress
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz
cd wordpress

# Configure database
cp wp-config-sample.php wp-config.php
# Edit wp-config.php with database credentials
```

#### 2. Plugin Installation
```bash
# Copy custom plugins to wp-content/plugins/
cp -r plugins/doi-version-plugin wp-content/plugins/
cp -r plugins/contact-form-submissions wp-content/plugins/
cp -r plugins/my-pdf-download-plugin wp-content/plugins/

# Activate plugins in WordPress admin
```

#### 3. Theme Installation
```bash
# Copy custom theme to wp-content/themes/
cp -r themes/icgeb-blog wp-content/themes/

# Activate theme in WordPress admin
```

### Development Tools

#### Recommended IDE/Editor
- **VS Code** with WordPress extensions
- **PHPStorm** with WordPress support
- **Sublime Text** with PHP packages

#### Essential Extensions
- **WordPress Coding Standards**
- **PHP Debug**
- **Git Integration**
- **Tailwind CSS IntelliSense**

#### Debug Configuration
```php
// wp-config.php - Development settings
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', true);
```

## Code Standards

### WordPress Coding Standards

#### PHP Standards
```php
// Use WordPress naming conventions
function icgeb_plugin_function() {
    // Function names use underscores
}

class ICGEB_Plugin_Class {
    // Class names use underscores and are capitalized
    public function __construct() {
        // Constructor
    }
}

// Use WordPress functions
$post_id = get_the_ID();
$title = get_the_title();
$content = get_the_content();
```

#### File Organization
```
plugin-name/
├── plugin-name.php          # Main plugin file
├── includes/                # PHP classes and functions
│   ├── class-main.php
│   └── functions.php
├── admin/                   # Admin-specific code
│   ├── admin.php
│   └── css/
├── public/                  # Frontend code
│   ├── public.php
│   └── js/
├── languages/               # Translation files
└── readme.txt              # Plugin readme
```

#### Documentation Standards
```php
/**
 * Plugin Name: ICGEB DOI and Version Plugin
 * Description: Adds DOI and versioning system for articles
 * Version: 1.0.0
 * Author: APZmedia
 * License: GPL v2 or later
 */

/**
 * Generate DOI for published posts
 *
 * @param int    $post_id Post ID
 * @param object $post    Post object
 * @return void
 */
function icgeb_generate_doi($post_id, $post) {
    // Function implementation
}
```

### JavaScript Standards

#### ES6+ Standards
```javascript
// Use modern JavaScript features
const versionContent = document.getElementById('version-content');
const fetchVersion = async (version) => {
    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'fetch_version_content',
                post_id: postId,
                version: version,
                nonce: nonce
            })
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching version:', error);
        throw error;
    }
};
```

#### WordPress JavaScript Standards
```javascript
// Use WordPress localized scripts
wp_enqueue_script('my-script', plugin_dir_url(__FILE__) . 'js/script.js', ['jquery'], '1.0', true);

wp_localize_script('my-script', 'my_ajax_object', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('my_action'),
    'post_id' => get_the_ID()
));
```

### CSS Standards

#### Tailwind CSS Usage
```css
/* Use Tailwind utility classes */
.version-dropdown {
    @apply absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-xl z-20;
}

/* Custom styles when needed */
.custom-component {
    @apply bg-blue-500 text-white px-4 py-2 rounded;
}
```

## Architecture Overview

### Plugin Architecture

#### Main Plugin Class
```php
class ICGEB_DOI_Version_Plugin {
    private $version;
    private $plugin_name;
    
    public function __construct() {
        $this->version = '1.0.0';
        $this->plugin_name = 'icgeb-doi-version';
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'register_post_meta'));
        add_action('publish_post', array($this, 'generate_doi'));
        add_action('post_updated', array($this, 'increment_version'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
}
```

#### Hook Organization
```php
// WordPress hooks by priority
add_action('init', array($this, 'init'), 0);           // Early initialization
add_action('wp_loaded', array($this, 'setup'), 10);    // After WordPress loads
add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 20); // Scripts
add_action('wp_footer', array($this, 'footer_scripts'), 100); // Late scripts
```

### Theme Architecture

#### Template Hierarchy
```php
// Custom template hierarchy
function icgeb_template_hierarchy($template) {
    if (is_single() && get_post_type() === 'post') {
        $new_template = locate_template(array('single-article.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'icgeb_template_hierarchy');
```

#### Custom Post Types (if needed)
```php
function icgeb_register_post_types() {
    register_post_type('icgeb_article', array(
        'labels' => array(
            'name' => 'Articles',
            'singular_name' => 'Article'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'articles')
    ));
}
add_action('init', 'icgeb_register_post_types');
```

## Plugin Development

### Creating New Plugins

#### Plugin Structure
```php
<?php
/**
 * Plugin Name: ICGEB Custom Plugin
 * Description: Custom functionality for ICGEB blog
 * Version: 1.0.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ICGEB_PLUGIN_VERSION', '1.0.0');
define('ICGEB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ICGEB_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Main plugin class
class ICGEB_Custom_Plugin {
    public function __construct() {
        $this->init();
    }
    
    private function init() {
        add_action('init', array($this, 'setup_hooks'));
    }
    
    public function setup_hooks() {
        // Add your hooks here
    }
}

// Initialize plugin
new ICGEB_Custom_Plugin();
```

#### AJAX Handler Development
```php
class ICGEB_AJAX_Handler {
    public function __construct() {
        add_action('wp_ajax_my_action', array($this, 'handle_ajax'));
        add_action('wp_ajax_nopriv_my_action', array($this, 'handle_ajax'));
    }
    
    public function handle_ajax() {
        // Verify nonce
        check_ajax_referer('my_action_nonce', 'nonce');
        
        // Check permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        // Process request
        $data = $this->process_request();
        
        // Send response
        wp_send_json_success($data);
    }
    
    private function process_request() {
        // Process the AJAX request
        return array('message' => 'Success');
    }
}
```

### Extending Existing Plugins

#### Adding Features to DOI Plugin
```php
// Add to doi-version-plugin.php
class ICGEB_DOI_Extensions {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_doi_meta_box'));
        add_action('save_post', array($this, 'save_doi_meta'));
    }
    
    public function add_doi_meta_box() {
        add_meta_box(
            'doi_meta_box',
            'DOI Information',
            array($this, 'render_doi_meta_box'),
            'post',
            'side',
            'high'
        );
    }
    
    public function render_doi_meta_box($post) {
        $doi = get_post_meta($post->ID, 'doi', true);
        echo '<input type="text" name="doi_field" value="' . esc_attr($doi) . '" />';
    }
}
```

## Theme Development

### Custom Template Development

#### Single Post Template
```php
<?php
/**
 * Template for displaying single articles with version control
 */

get_header();
?>

<div class="article-container">
    <?php while (have_posts()) : the_post(); ?>
        <article class="article-content">
            <header class="article-header">
                <h1><?php the_title(); ?></h1>
                <div class="article-meta">
                    <span class="doi">DOI: <?php echo get_post_meta(get_the_ID(), 'doi', true); ?></span>
                    <span class="version">Version: <?php echo get_post_meta(get_the_ID(), 'version', true); ?></span>
                </div>
            </header>
            
            <div class="article-body">
                <?php the_content(); ?>
            </div>
            
            <footer class="article-footer">
                <div class="version-control">
                    <?php icgeb_version_dropdown(); ?>
                </div>
            </footer>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
```

#### Custom Functions
```php
// functions.php additions
function icgeb_version_dropdown() {
    $post_id = get_the_ID();
    $version_history = get_post_meta($post_id, 'version_history', true);
    $current_version = get_post_meta($post_id, 'version', true);
    
    if (is_array($version_history) && count($version_history) > 1) {
        echo '<select id="version-selector">';
        foreach (array_keys($version_history) as $version) {
            $selected = ($version === $current_version) ? 'selected' : '';
            echo '<option value="' . esc_attr($version) . '" ' . $selected . '>';
            echo 'Version ' . esc_html($version);
            echo '</option>';
        }
        echo '</select>';
    }
}
```

### Custom CSS Development

#### Component-Based Styling
```css
/* components/version-control.css */
.version-control {
    @apply mt-8 p-4 bg-gray-50 rounded-lg;
}

.version-selector {
    @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.version-info {
    @apply text-sm text-gray-600 mt-2;
}

/* components/article-header.css */
.article-header {
    @apply mb-8 pb-4 border-b border-gray-200;
}

.article-title {
    @apply text-3xl font-bold text-gray-900 mb-4;
}

.article-meta {
    @apply flex flex-wrap gap-4 text-sm text-gray-600;
}
```

## Database Schema

### Custom Tables

#### Contact Form Submissions
```sql
CREATE TABLE wp_contact_form_submissions (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    message text NOT NULL,
    time datetime NOT NULL,
    ip_address varchar(45) DEFAULT NULL,
    user_agent text DEFAULT NULL,
    status enum('new','read','replied') DEFAULT 'new',
    PRIMARY KEY (id),
    KEY time (time),
    KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Version History (Alternative Implementation)
```sql
CREATE TABLE wp_version_history (
    id int(11) NOT NULL AUTO_INCREMENT,
    post_id int(11) NOT NULL,
    version_number int(11) NOT NULL,
    content longtext NOT NULL,
    created_at datetime NOT NULL,
    created_by int(11) NOT NULL,
    change_summary text DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY post_version (post_id, version_number),
    KEY post_id (post_id),
    KEY created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Database Operations

#### Safe Database Queries
```php
class ICGEB_Database {
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function get_contact_submissions($limit = 10, $offset = 0) {
        $table_name = $this->wpdb->prefix . 'contact_form_submissions';
        
        $query = $this->wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY time DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        );
        
        return $this->wpdb->get_results($query);
    }
    
    public function save_contact_submission($name, $email, $message) {
        $table_name = $this->wpdb->prefix . 'contact_form_submissions';
        
        $result = $this->wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'time' => current_time('mysql'),
                'ip_address' => $this->get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        return $result ? $this->wpdb->insert_id : false;
    }
    
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}
```

## API Development

### REST API Endpoints

#### Custom REST API
```php
class ICGEB_REST_API {
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    public function register_routes() {
        register_rest_route('icgeb/v1', '/articles', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_articles'),
                'permission_callback' => '__return_true'
            )
        ));
        
        register_rest_route('icgeb/v1', '/articles/(?P<id>\d+)/versions', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_article_versions'),
                'permission_callback' => '__return_true'
            )
        ));
    }
    
    public function get_articles($request) {
        $posts = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => 10,
            'post_status' => 'publish'
        ));
        
        $articles = array();
        foreach ($posts as $post) {
            $articles[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'doi' => get_post_meta($post->ID, 'doi', true),
                'version' => get_post_meta($post->ID, 'version', true),
                'url' => get_permalink($post->ID)
            );
        }
        
        return new WP_REST_Response($articles, 200);
    }
    
    public function get_article_versions($request) {
        $post_id = $request['id'];
        $version_history = get_post_meta($post_id, 'version_history', true);
        
        if (!$version_history) {
            return new WP_Error('no_versions', 'No versions found', array('status' => 404));
        }
        
        return new WP_REST_Response($version_history, 200);
    }
}
```

### AJAX API Development

#### Structured AJAX Handler
```php
class ICGEB_AJAX_API {
    private $actions = array();
    
    public function __construct() {
        $this->register_actions();
        $this->add_hooks();
    }
    
    private function register_actions() {
        $this->actions = array(
            'fetch_version_content' => array(
                'callback' => array($this, 'fetch_version_content'),
                'nonce' => 'fetch_version_nonce',
                'capability' => null // Public action
            ),
            'submit_contact_form' => array(
                'callback' => array($this, 'submit_contact_form'),
                'nonce' => 'submit_contact_form',
                'capability' => null // Public action
            ),
            'delete_submission' => array(
                'callback' => array($this, 'delete_submission'),
                'nonce' => 'delete_submission_nonce',
                'capability' => 'edit_pages'
            )
        );
    }
    
    private function add_hooks() {
        foreach ($this->actions as $action => $config) {
            add_action("wp_ajax_$action", array($this, 'handle_ajax'));
            if (!$config['capability']) {
                add_action("wp_ajax_nopriv_$action", array($this, 'handle_ajax'));
            }
        }
    }
    
    public function handle_ajax() {
        $action = $_POST['action'] ?? '';
        
        if (!isset($this->actions[$action])) {
            wp_send_json_error('Invalid action');
        }
        
        $config = $this->actions[$action];
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['security'] ?? '', $config['nonce'])) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check capability
        if ($config['capability'] && !current_user_can($config['capability'])) {
            wp_send_json_error('Insufficient permissions');
        }
        
        // Call callback
        call_user_func($config['callback']);
    }
    
    public function fetch_version_content() {
        $post_id = intval($_POST['post_id']);
        $version = sanitize_text_field($_POST['version']);
        
        $version_history = get_post_meta($post_id, 'version_history', true);
        
        if (!isset($version_history[$version])) {
            wp_send_json_error('Version not found');
        }
        
        $content = apply_filters('the_content', $version_history[$version]);
        
        wp_send_json_success(array(
            'content' => $content,
            'version' => $version
        ));
    }
}
```

## Testing

### Unit Testing

#### PHPUnit Setup
```php
// tests/bootstrap.php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

// Load WordPress test environment
$_tests_dir = getenv('WP_TESTS_DIR');
if (!$_tests_dir) {
    $_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';
require_once $_tests_dir . '/includes/bootstrap.php';
```

#### Test Cases
```php
// tests/test-doi-generation.php
class Test_DOI_Generation extends WP_UnitTestCase {
    private $plugin;
    
    public function setUp(): void {
        parent::setUp();
        $this->plugin = new ICGEB_DOI_Version_Plugin();
    }
    
    public function test_doi_generation() {
        $post_id = $this->factory->post->create(array(
            'post_status' => 'publish'
        ));
        
        $doi = get_post_meta($post_id, 'doi', true);
        
        $this->assertNotEmpty($doi);
        $this->assertMatchesRegularExpression('/^10\.1234\/[a-f0-9]+$/', $doi);
    }
    
    public function test_version_increment() {
        $post_id = $this->factory->post->create(array(
            'post_status' => 'publish',
            'post_content' => 'Original content'
        ));
        
        $original_version = get_post_meta($post_id, 'version', true);
        
        // Update content
        wp_update_post(array(
            'ID' => $post_id,
            'post_content' => 'Updated content'
        ));
        
        $new_version = get_post_meta($post_id, 'version', true);
        
        $this->assertEquals(intval($original_version) + 1, intval($new_version));
    }
}
```

### Integration Testing

#### Browser Testing
```php
// tests/integration/test-frontend.php
class Test_Frontend_Integration extends WP_UnitTestCase {
    public function test_version_switching() {
        // Create test post with multiple versions
        $post_id = $this->factory->post->create(array(
            'post_status' => 'publish',
            'post_content' => 'Version 1 content'
        ));
        
        // Update to create version 2
        wp_update_post(array(
            'ID' => $post_id,
            'post_content' => 'Version 2 content'
        ));
        
        // Test AJAX endpoint
        $_POST['action'] = 'fetch_version_content';
        $_POST['post_id'] = $post_id;
        $_POST['version'] = '1';
        $_POST['security'] = wp_create_nonce('fetch_version_nonce');
        
        try {
            $this->_handleAjax('fetch_version_content');
        } catch (WPAjaxDieContinueException $e) {
            // Expected
        }
        
        $response = json_decode($this->_last_response, true);
        
        $this->assertTrue($response['success']);
        $this->assertContains('Version 1 content', $response['data']['content']);
    }
}
```

### Performance Testing

#### Database Query Testing
```php
// tests/performance/test-database.php
class Test_Database_Performance extends WP_UnitTestCase {
    public function test_version_history_query_performance() {
        // Create multiple posts with version history
        for ($i = 0; $i < 100; $i++) {
            $post_id = $this->factory->post->create();
            
            $version_history = array();
            for ($v = 1; $v <= 10; $v++) {
                $version_history[$v] = "Content version $v for post $i";
            }
            
            update_post_meta($post_id, 'version_history', $version_history);
        }
        
        // Test query performance
        $start_time = microtime(true);
        
        $posts = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => 50,
            'meta_query' => array(
                array(
                    'key' => 'version_history',
                    'compare' => 'EXISTS'
                )
            )
        ));
        
        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;
        
        $this->assertLessThan(1.0, $execution_time, 'Query took too long');
    }
}
```

## Deployment

### Production Deployment

#### Deployment Checklist
```bash
#!/bin/bash
# deploy.sh

echo "Starting deployment..."

# 1. Backup current installation
echo "Creating backup..."
tar -czf backup-$(date +%Y%m%d-%H%M%S).tar.gz wp-content/

# 2. Update code
echo "Updating code..."
git pull origin main

# 3. Update dependencies
echo "Updating dependencies..."
composer install --no-dev --optimize-autoloader

# 4. Clear caches
echo "Clearing caches..."
wp cache flush

# 5. Update database
echo "Updating database..."
wp db upgrade

# 6. Set proper permissions
echo "Setting permissions..."
chmod 644 wp-config.php
chmod 755 wp-content/
chmod 755 wp-content/plugins/
chmod 755 wp-content/themes/

echo "Deployment complete!"
```

#### Environment Configuration
```php
// wp-config.php - Production settings
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);

// Security settings
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);

// Performance settings
define('WP_CACHE', true);
define('WP_MEMORY_LIMIT', '256M');

// Custom settings
define('ICGEB_ENVIRONMENT', 'production');
define('ICGEB_DEBUG', false);
```

### Staging Environment

#### Staging Setup
```bash
# Create staging environment
wp db export staging.sql
wp search-replace 'production-domain.com' 'staging-domain.com' --all-tables

# Configure staging
cp wp-config.php wp-config-staging.php
# Edit staging configuration
```

#### Testing in Staging
```php
// wp-config-staging.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Staging-specific settings
define('ICGEB_ENVIRONMENT', 'staging');
define('ICGEB_DEBUG', true);
```

## Contributing

### Development Workflow

#### Git Workflow
```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes
git add .
git commit -m "Add new feature"

# Push to remote
git push origin feature/new-feature

# Create pull request
# Review and merge
```

#### Code Review Process
1. **Self-review**: Check code against standards
2. **Peer review**: Another developer reviews
3. **Testing**: Ensure all tests pass
4. **Documentation**: Update relevant docs
5. **Deployment**: Deploy to staging for testing

### Documentation Standards

#### Code Documentation
```php
/**
 * Generate DOI for published posts
 *
 * This function automatically generates a unique DOI when a post is published.
 * The DOI follows the format 10.1234/unique-identifier and is stored in post meta.
 *
 * @since 1.0.0
 * @param int    $post_id The post ID
 * @param object $post    The post object
 * @return void
 * @throws Exception If DOI generation fails
 */
function icgeb_generate_doi($post_id, $post) {
    // Implementation
}
```

#### API Documentation
```php
/**
 * REST API endpoint for fetching article versions
 *
 * @api {get} /wp-json/icgeb/v1/articles/:id/versions Get article versions
 * @apiName GetArticleVersions
 * @apiGroup Articles
 * @apiVersion 1.0.0
 *
 * @apiParam {Number} id Article ID
 *
 * @apiSuccess {Object[]} versions List of versions
 * @apiSuccess {String} versions.version Version number
 * @apiSuccess {String} versions.content Version content
 *
 * @apiError {String} error Error message
 */
```

### Release Process

#### Version Management
```php
// Version constants
define('ICGEB_PLUGIN_VERSION', '1.1.0');
define('ICGEB_PLUGIN_DB_VERSION', '1.1');

// Database upgrade
function icgeb_upgrade_database() {
    $current_version = get_option('icgeb_db_version', '1.0');
    
    if (version_compare($current_version, '1.1', '<')) {
        // Perform upgrade tasks
        icgeb_upgrade_to_1_1();
        update_option('icgeb_db_version', '1.1');
    }
}
```

#### Release Notes Template
```markdown
# Version 1.1.0 - Release Notes

## New Features
- Added version comparison functionality
- Implemented bulk DOI generation
- Enhanced PDF export options

## Improvements
- Improved performance for version switching
- Better error handling in AJAX calls
- Enhanced mobile responsiveness

## Bug Fixes
- Fixed DOI generation for draft posts
- Resolved version history display issues
- Corrected citation formatting

## Breaking Changes
- None

## Migration Notes
- Database upgrade required
- Clear cache after update
```

---

*Last Updated: [Current Date]*
*Version: 1.0* 