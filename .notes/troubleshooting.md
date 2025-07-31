# Troubleshooting Guide - ICGEB Blog

## Table of Contents
1. [Common Issues](#common-issues)
2. [Error Messages](#error-messages)
3. [Performance Problems](#performance-problems)
4. [Security Issues](#security-issues)
5. [Database Issues](#database-issues)
6. [Plugin Conflicts](#plugin-conflicts)
7. [Theme Issues](#theme-issues)
8. [Browser Compatibility](#browser-compatibility)
9. [Server Configuration](#server-configuration)
10. [Getting Help](#getting-help)

## Common Issues

### Version Control Problems

#### Issue: Versions Not Updating
**Symptoms:**
- Version number doesn't increment when content changes
- Version dropdown shows old versions
- URL doesn't reflect current version

**Possible Causes:**
1. Content hasn't actually changed
2. Post status is not "Published"
3. Caching plugin interference
4. JavaScript errors preventing AJAX calls

**Solutions:**
```php
// Check if content actually changed
if ($post_after->post_content === $post_before->post_content) {
    return; // No change, no version increment
}

// Verify post status
if ($post_after->post_status !== 'publish') {
    return; // Only published posts get versioned
}
```

**Debugging Steps:**
1. Check browser console for JavaScript errors
2. Disable caching plugins temporarily
3. Verify post is published, not draft
4. Check if content actually differs from previous version

#### Issue: Version History Not Loading
**Symptoms:**
- Version dropdown is empty
- AJAX requests fail
- Version content doesn't update

**Solutions:**
```javascript
// Check AJAX response
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Update content
        document.getElementById('version-content').innerHTML = data.data.content;
    } else {
        console.error('Error:', data.data.message);
        // Fallback to current version
        fetchVersionContent(wp_data.current_version);
    }
})
.catch(error => {
    console.error('AJAX Error:', error);
    // Fallback behavior
});
```

### DOI System Issues

#### Issue: DOI Not Generating
**Symptoms:**
- DOI field is empty in admin
- DOI not displayed on frontend
- Citation missing DOI

**Possible Causes:**
1. Post not published
2. Post type is not 'post'
3. User lacks permissions
4. Database write errors

**Solutions:**
```php
// Ensure DOI generation only for published posts
public function generate_doi($post_id, $post) {
    if ($post->post_type !== 'post') {
        return;
    }
    
    if ($post->post_status !== 'publish') {
        return;
    }
    
    $existing_doi = get_post_meta($post_id, 'doi', true);
    if (empty($existing_doi)) {
        $new_doi = '10.1234/' . uniqid();
        $result = update_post_meta($post_id, 'doi', $new_doi);
        
        if (!$result) {
            error_log("Failed to save DOI for post $post_id");
        }
    }
}
```

**Debugging Steps:**
1. Check post status in admin
2. Verify post type is 'post'
3. Check user permissions
4. Review error logs for database issues

#### Issue: DOI Format Invalid
**Symptoms:**
- DOI doesn't follow standard format
- Citation system rejects DOI
- External systems can't resolve DOI

**Solutions:**
```php
// Validate DOI format
function validate_doi($doi) {
    $pattern = '/^10\.\d{4,}\/[^\s]+$/';
    return preg_match($pattern, $doi);
}

// Generate proper DOI
$new_doi = '10.1234/' . uniqid();
if (!validate_doi($new_doi)) {
    error_log("Invalid DOI generated: $new_doi");
}
```

### Contact Form Issues

#### Issue: Submissions Not Saving
**Symptoms:**
- Form submission shows success but no data in admin
- Email notifications not sent
- Database table missing

**Solutions:**
```php
// Check if table exists
function check_contact_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form_submissions';
    
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    
    if (!$table_exists) {
        // Create table
        $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            message text NOT NULL,
            time datetime NOT NULL,
            PRIMARY KEY (id)
        )";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
```

**Debugging Steps:**
1. Check if database table exists
2. Verify form submission process
3. Check for JavaScript errors
4. Ensure proper nonce verification

#### Issue: Email Notifications Not Sending
**Symptoms:**
- Submissions saved but no email received
- Email configuration issues
- Spam filter blocking emails

**Solutions:**
```php
// Configure email headers
$headers = array(
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
    'Reply-To: ' . $email
);

// Send email with error handling
$sent = wp_mail($admin_email, $subject, $body, $headers);
if (!$sent) {
    error_log("Failed to send contact form email");
}
```

### PDF Generation Problems

#### Issue: PDF Not Downloading
**Symptoms:**
- PDF download link doesn't work
- Server errors when generating PDF
- Memory limit exceeded

**Solutions:**
```php
// Increase memory limit for PDF generation
ini_set('memory_limit', '256M');

// Check server requirements
if (!extension_loaded('gd')) {
    wp_die('GD extension required for PDF generation');
}

// Handle large content
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');
```

**Debugging Steps:**
1. Check server memory limits
2. Verify DOMPDF library installation
3. Test with smaller content
4. Check file permissions

#### Issue: PDF Formatting Problems
**Symptoms:**
- PDF layout broken
- Images not displaying
- Text formatting issues

**Solutions:**
```php
// Process images for PDF
$content = preg_replace(
    '/<img([^>]+)>/i',
    '<img$1 style="max-width:350px; height:auto; display:block; margin:auto;">',
    $content
);

// Ensure proper HTML structure
$html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . 
        $content . '</body></html>';
```

## Error Messages

### WordPress Errors

#### "Fatal error: Allowed memory size exhausted"
**Cause:** PHP memory limit too low for PDF generation
**Solution:**
```php
// Increase memory limit
ini_set('memory_limit', '256M');

// Or add to wp-config.php
define('WP_MEMORY_LIMIT', '256M');
```

#### "Database connection failed"
**Cause:** Database server issues or configuration problems
**Solution:**
1. Check database credentials in wp-config.php
2. Verify database server is running
3. Check network connectivity
4. Review database logs

#### "Plugin could not be activated"
**Cause:** Plugin conflicts or missing dependencies
**Solution:**
1. Deactivate all plugins
2. Activate plugins one by one
3. Check for PHP version compatibility
4. Review error logs

### AJAX Errors

#### "AJAX Error: 403 Forbidden"
**Cause:** Nonce verification failed or insufficient permissions
**Solution:**
```php
// Verify nonce
if (!wp_verify_nonce($_POST['nonce'], 'action_name')) {
    wp_send_json_error('Invalid nonce');
}

// Check permissions
if (!current_user_can('edit_posts')) {
    wp_send_json_error('Insufficient permissions');
}
```

#### "AJAX Error: 500 Internal Server Error"
**Cause:** Server-side error in AJAX handler
**Solution:**
1. Check PHP error logs
2. Enable WordPress debug mode
3. Review AJAX handler code
4. Test with simplified request

### JavaScript Errors

#### "Uncaught TypeError: Cannot read property of null"
**Cause:** DOM element not found
**Solution:**
```javascript
// Check if element exists before accessing
const element = document.getElementById('version-content');
if (element) {
    element.innerHTML = content;
} else {
    console.error('Element not found');
}
```

#### "Uncaught ReferenceError: ajaxurl is not defined"
**Cause:** WordPress AJAX URL not localized
**Solution:**
```php
// Localize script with AJAX URL
wp_localize_script('script-handle', 'ajax_object', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('action_name')
));
```

## Performance Problems

### Slow Page Loading

#### Database Query Optimization
```php
// Use efficient queries
$posts = get_posts(array(
    'post_type' => 'post',
    'posts_per_page' => 4,
    'no_found_rows' => true, // Skip pagination count
    'update_post_meta_cache' => false, // Skip meta cache
    'update_post_term_cache' => false  // Skip term cache
));
```

#### Caching Implementation
```php
// Cache version history
$cache_key = 'version_history_' . $post_id;
$version_history = wp_cache_get($cache_key);

if (false === $version_history) {
    $version_history = get_post_meta($post_id, 'version_history', true);
    wp_cache_set($cache_key, $version_history, '', 3600);
}
```

### Memory Usage Issues

#### Optimize Image Processing
```php
// Limit image sizes for PDF
$content = preg_replace(
    '/<img([^>]+)>/i',
    '<img$1 style="max-width:350px; height:auto;">',
    $content
);
```

#### Database Cleanup
```sql
-- Clean up old contact form submissions
DELETE FROM wp_contact_form_submissions 
WHERE time < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Optimize tables
OPTIMIZE TABLE wp_posts;
OPTIMIZE TABLE wp_postmeta;
```

## Security Issues

### Nonce Verification Failures

#### Debugging Nonce Issues
```php
// Debug nonce verification
if (!wp_verify_nonce($_POST['nonce'], 'action_name')) {
    error_log('Nonce verification failed');
    error_log('Expected nonce: ' . wp_create_nonce('action_name'));
    error_log('Received nonce: ' . $_POST['nonce']);
    wp_send_json_error('Invalid nonce');
}
```

#### Regenerating Nonces
```php
// Regenerate nonce if needed
$new_nonce = wp_create_nonce('action_name');
wp_send_json_success(array('nonce' => $new_nonce));
```

### SQL Injection Prevention

#### Proper Query Preparation
```php
// Use prepared statements
$result = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}contact_form_submissions WHERE id = %d",
    $submission_id
);
```

### XSS Prevention

#### Output Sanitization
```php
// Sanitize output
echo esc_html($user_input);
echo wp_kses_post($html_content);
```

## Database Issues

### Table Corruption

#### Check and Repair Tables
```sql
-- Check table status
CHECK TABLE wp_contact_form_submissions;

-- Repair if needed
REPAIR TABLE wp_contact_form_submissions;
```

#### Backup and Restore
```bash
# Backup database
mysqldump -u username -p database_name > backup.sql

# Restore database
mysql -u username -p database_name < backup.sql
```

### Missing Tables

#### Create Missing Tables
```php
// Check and create contact form table
function ensure_contact_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form_submissions';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            message text NOT NULL,
            time datetime NOT NULL,
            PRIMARY KEY (id)
        ) " . $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
```

## Plugin Conflicts

### Identifying Conflicts

#### Deactivation Test
1. Deactivate all plugins
2. Test functionality
3. Activate plugins one by one
4. Identify conflicting plugin

#### Debug Mode
```php
// Enable debug mode in wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Common Conflicts

#### Caching Plugins
- **Problem:** Cached content prevents version updates
- **Solution:** Clear cache or exclude version-specific URLs

#### Security Plugins
- **Problem:** Block AJAX requests
- **Solution:** Whitelist AJAX endpoints

#### SEO Plugins
- **Problem:** Modify URLs or meta data
- **Solution:** Configure to work with version URLs

## Theme Issues

### Template Problems

#### Missing Template Files
```php
// Check if template exists
if (file_exists(get_template_directory() . '/single.php')) {
    // Template exists
} else {
    // Fallback to default
    get_template_part('index');
}
```

#### Template Hierarchy Issues
```php
// Debug template loading
add_action('template_redirect', function() {
    error_log('Template file: ' . get_page_template());
});
```

### Styling Issues

#### CSS Conflicts
```css
/* Use more specific selectors */
.icgeb-blog .version-dropdown {
    /* Styles */
}

/* Override conflicting styles */
.icgeb-blog .version-dropdown {
    !important;
}
```

## Browser Compatibility

### JavaScript Issues

#### Feature Detection
```javascript
// Check for required features
if (typeof fetch !== 'undefined') {
    // Use fetch API
} else {
    // Fallback to jQuery AJAX
}
```

#### Polyfills
```javascript
// Add polyfills for older browsers
if (!Array.prototype.includes) {
    Array.prototype.includes = function(searchElement) {
        return this.indexOf(searchElement) !== -1;
    };
}
```

### CSS Compatibility

#### Vendor Prefixes
```css
/* Add vendor prefixes for older browsers */
.version-dropdown {
    -webkit-transform: translateX(0);
    -moz-transform: translateX(0);
    -ms-transform: translateX(0);
    transform: translateX(0);
}
```

## Server Configuration

### PHP Configuration

#### Required Extensions
```php
// Check required extensions
$required_extensions = array('gd', 'mbstring', 'xml');
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        error_log("Required PHP extension missing: $ext");
    }
}
```

#### PHP Settings
```ini
; Recommended PHP settings
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 64M
post_max_size = 64M
```

### Web Server Configuration

#### Apache Configuration
```apache
# Enable rewrite rules
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

#### Nginx Configuration
```nginx
# WordPress rewrite rules
location / {
    try_files $uri $uri/ /index.php?$args;
}
```

## Getting Help

### Debugging Tools

#### WordPress Debug Mode
```php
// Enable in wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

#### Browser Developer Tools
- **Console:** Check for JavaScript errors
- **Network:** Monitor AJAX requests
- **Elements:** Inspect DOM structure

#### Database Tools
- **phpMyAdmin:** Database management
- **MySQL Workbench:** Advanced database operations
- **WP-CLI:** Command-line WordPress management

### Support Resources

#### Documentation
- **WordPress Codex:** Official WordPress documentation
- **Plugin Documentation:** Individual plugin guides
- **Theme Documentation:** Theme-specific information

#### Community Support
- **WordPress Forums:** Community support
- **Stack Overflow:** Technical questions
- **GitHub Issues:** Plugin-specific issues

#### Professional Support
- **APZmedia:** Developer support
- **ICGEB:** Content and organizational support
- **WordPress Hosting:** Server and hosting support

### Error Reporting

#### Information to Include
1. **Error Message:** Exact error text
2. **Steps to Reproduce:** How to recreate the issue
3. **Environment:** WordPress version, PHP version, server info
4. **Browser:** Browser type and version
5. **Screenshots:** Visual evidence of the problem
6. **Error Logs:** Relevant log files

#### Log Locations
```bash
# WordPress debug log
wp-content/debug.log

# PHP error log
/var/log/php_errors.log

# Apache error log
/var/log/apache2/error.log

# Nginx error log
/var/log/nginx/error.log
```

---

*Last Updated: [Current Date]*
*Version: 1.0* 