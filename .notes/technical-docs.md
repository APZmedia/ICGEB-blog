# Technical Documentation

## Architecture Overview

### WordPress Customization
The ICGEB Blog extends WordPress core functionality through:
- **Custom Theme**: `icgeb-blog` with Tailwind CSS
- **Custom Plugins**: Three specialized plugins for academic features
- **Custom Post Meta**: DOI and versioning data storage
- **Custom Database Tables**: Contact form submissions

## Theme Architecture (`icgeb-blog`)

### Template Hierarchy
```
themes/icgeb-blog/
├── index.php          # Main template, handles homepage and archive
├── single.php         # Single post template with version control
├── header.php         # Header with navigation and branding
├── footer.php         # Footer with ICGEB branding
├── search.php         # Custom search results template
├── page.php           # Static page template
├── functions.php      # Theme functions and customizations
├── style.css          # Theme stylesheet
└── js/
    └── main.js        # Theme JavaScript functionality
```

### Key Theme Features

#### 1. Responsive Design
- **Mobile-first approach** using Tailwind CSS
- **Breakpoints**: sm (640px), md (768px), lg (1024px), xl (1280px)
- **Flexible layouts** that adapt to screen size
- **Touch-friendly navigation** with hamburger menu

#### 2. Branding Integration
- **ICGEB Blue**: Primary color (#0066b3)
- **Logo Integration**: Monitoring Gene Drives branding
- **Consistent typography**: Professional academic styling
- **Footer branding**: ICGEB and Open Philanthropy logos

#### 3. Content Display
- **Grid layouts** for homepage and search results
- **Card-based design** for article previews
- **Typography hierarchy** for readability
- **Image optimization** with responsive sizing

### JavaScript Functionality

#### Mobile Navigation
```javascript
// Mobile menu toggle with smooth animations
const toggleButton = document.getElementById('mobileNavToggle');
const mainNav = document.getElementById('mainNav');
// Handles mobile menu open/close with transform animations
```

#### Version Switching
```javascript
// AJAX-based version content loading
function fetchVersionContent(version) {
    fetch(ajaxurl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'fetch_version_content',
            post_id: postId,
            version: version,
            nonce: nonce
        })
    })
}
```

## Plugin Architecture

### 1. DOI and Version Plugin

#### Core Class: `DOI_Version_Plugin`
```php
class DOI_Version_Plugin {
    public function __construct() {
        // Hooks for post meta, versioning, and AJAX
        add_action('init', array($this, 'register_post_meta'));
        add_action('publish_post', array($this, 'generate_doi'));
        add_action('post_updated', array($this, 'increment_version'));
        // ... additional hooks
    }
}
```

#### Key Methods

**DOI Generation**
```php
public function generate_doi($post_id, $post) {
    if ($post->post_type !== 'post') return;
    
    $existing_doi = get_post_meta($post_id, 'doi', true);
    if (empty($existing_doi)) {
        $new_doi = '10.1234/' . uniqid();
        update_post_meta($post_id, 'doi', $new_doi);
    }
}
```

**Version Management**
```php
public function increment_version($post_id, $post_after, $post_before) {
    if ($post_after->post_content === $post_before->post_content) return;
    
    $version_history = get_post_meta($post_id, 'version_history', true);
    $current_version = get_post_meta($post_id, 'version', true);
    $new_version = strval(intval($current_version) + 1);
    
    $version_history[$new_version] = $post_after->post_content;
    update_post_meta($post_id, 'version', $new_version);
    update_post_meta($post_id, 'version_history', $version_history);
}
```

#### Database Schema
```sql
-- Post meta for DOI and versioning
wp_postmeta:
  - meta_key: 'doi' (string)
  - meta_key: 'version' (string)
  - meta_key: 'version_history' (serialized array)
```

### 2. Contact Form Submissions Plugin

#### Database Table
```sql
CREATE TABLE wp_contact_form_submissions (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    message text NOT NULL,
    time datetime NOT NULL,
    PRIMARY KEY (id)
);
```

#### Admin Interface
- **WordPress Admin Menu**: Custom page for viewing submissions
- **AJAX Deletion**: Secure submission removal
- **Email Notifications**: Admin alerts for new submissions

### 3. PDF Download Plugin

#### DOMPDF Integration
```php
require_once __DIR__ . '/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
```

#### PDF Structure
1. **Cover Page**: Title, author, publication date, URL
2. **Content Pages**: Article content with headers/footers
3. **Styling**: Professional academic formatting

## Database Design

### WordPress Core Tables
- `wp_posts`: Articles and pages
- `wp_postmeta`: DOI, version, and version history
- `wp_users`: User accounts
- `wp_usermeta`: User metadata
- `wp_terms`, `wp_term_relationships`, `wp_term_taxonomy`: Categories

### Custom Tables
```sql
-- Contact form submissions
wp_contact_form_submissions (
    id, name, email, message, time
)
```

## API Endpoints

### WordPress REST API
- **Posts**: `/wp-json/wp/v2/posts`
- **Categories**: `/wp-json/wp/v2/categories`
- **Users**: `/wp-json/wp/v2/users`

### Custom AJAX Endpoints
```php
// Version content fetching
add_action('wp_ajax_fetch_version_content', array($this, 'fetch_version_content'));
add_action('wp_ajax_nopriv_fetch_version_content', array($this, 'fetch_version_content'));

// Contact form submission
add_action('wp_ajax_submit_contact_form', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact_form', 'handle_contact_form_submission');
```

## Security Implementation

### Data Sanitization
```php
// Input sanitization
$name = sanitize_text_field($_POST['name']);
$email = sanitize_email($_POST['email']);
$message = sanitize_textarea_field($_POST['message']);
```

### Nonce Verification
```php
// AJAX security
check_ajax_referer('submit-contact-form', 'security');
check_ajax_referer('fetch_version_nonce', 'nonce');
```

### Permission Checks
```php
// User capability verification
if (!current_user_can('edit_post', $post_id)) {
    return;
}
```

## Performance Optimization

### Database Optimization
- **Indexed queries** for post meta lookups
- **Efficient version history** storage
- **Optimized contact form** queries

### Frontend Optimization
- **CDN-based Tailwind CSS** for faster loading
- **Minified JavaScript** for reduced file sizes
- **Responsive images** with appropriate sizing
- **Lazy loading** for better performance

### Caching Strategy
- **WordPress caching** plugin recommended
- **Database query optimization**
- **Static asset caching**

## Error Handling

### AJAX Error Handling
```javascript
.catch(error => {
    console.error('AJAX Error:', error);
    // Fallback to current version if there's an error
    fetchVersionContent(wp_data.current_version);
});
```

### PHP Error Handling
```php
if ($result) {
    wp_send_json_success(['message' => 'Success']);
} else {
    wp_send_json_error(['message' => 'Failed to submit']);
}
```

## Deployment Considerations

### File Permissions
```bash
# WordPress directories
chmod 755 wp-content/
chmod 755 wp-content/themes/
chmod 755 wp-content/plugins/

# WordPress files
chmod 644 wp-config.php
chmod 644 .htaccess
```

### Environment Configuration
- **WordPress configuration** in wp-config.php
- **Database credentials** and connection settings
- **Custom constants** for admin email and other settings

### Backup Strategy
- **Database backups**: Regular MySQL dumps
- **File backups**: Complete WordPress installation
- **Version control**: Git repository for code changes

---

*Last Updated: [Current Date]*
*Version: 1.0* 