# API Reference - ICGEB Blog

## Overview

This document provides a comprehensive reference for all API endpoints, AJAX handlers, and data structures used in the ICGEB Blog WordPress installation.

## WordPress REST API Endpoints

### Core WordPress Endpoints

#### Posts
```
GET /wp-json/wp/v2/posts
GET /wp-json/wp/v2/posts/{id}
POST /wp-json/wp/v2/posts
PUT /wp-json/wp/v2/posts/{id}
DELETE /wp-json/wp/v2/posts/{id}
```

**Parameters:**
- `per_page` (integer): Number of posts per page (default: 10)
- `page` (integer): Page number
- `search` (string): Search query
- `categories` (array): Category IDs
- `orderby` (string): Sort by (date, title, id, etc.)
- `order` (string): Sort order (asc, desc)

**Response Example:**
```json
{
  "id": 123,
  "date": "2024-01-15T10:00:00",
  "title": {
    "rendered": "Article Title"
  },
  "content": {
    "rendered": "Article content...",
    "protected": false
  },
  "excerpt": {
    "rendered": "Article excerpt..."
  },
  "author": 1,
  "categories": [5, 8],
  "meta": {
    "doi": "10.1234/abc123",
    "version": "2",
    "version_history": {
      "1": "Original content...",
      "2": "Updated content..."
    }
  }
}
```

#### Categories
```
GET /wp-json/wp/v2/categories
GET /wp-json/wp/v2/categories/{id}
```

**Response Example:**
```json
{
  "id": 5,
  "name": "Gene Drive Research",
  "slug": "gene-drive-research",
  "description": "Research on gene drive technology",
  "count": 15
}
```

#### Users
```
GET /wp-json/wp/v2/users
GET /wp-json/wp/v2/users/{id}
```

**Response Example:**
```json
{
  "id": 1,
  "name": "Dr. Jane Smith",
  "first_name": "Jane",
  "last_name": "Smith",
  "description": "Research scientist",
  "url": "https://example.com"
}
```

## Custom AJAX Endpoints

### Version Content Fetching

#### Endpoint
```
POST /wp-admin/admin-ajax.php
```

#### Request Parameters
```javascript
{
  action: 'fetch_version_content',
  post_id: 123,
  version: '2',
  nonce: 'nonce_string'
}
```

#### Response
```json
{
  "success": true,
  "data": {
    "content": "<p>Version 2 content...</p>",
    "version": "2"
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "data": {
    "message": "Version not found."
  }
}
```

#### JavaScript Usage
```javascript
fetch(ajaxurl, {
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
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    // Update content
    document.getElementById('version-content').innerHTML = data.data.content;
  }
});
```

### Contact Form Submission

#### Endpoint
```
POST /wp-admin/admin-ajax.php
```

#### Request Parameters
```javascript
{
  action: 'submit_contact_form',
  name: 'John Doe',
  email: 'john@example.com',
  message: 'Contact message',
  security: 'nonce_string'
}
```

#### Response
```json
{
  "success": true,
  "data": {
    "message": "Your message has been sent successfully."
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "data": {
    "message": "All fields are required."
  }
}
```

#### JavaScript Usage
```javascript
jQuery.ajax({
  url: my_ajax_object.ajax_url,
  type: 'POST',
  data: {
    action: 'submit_contact_form',
    name: name,
    email: email,
    message: message,
    security: my_ajax_object.nonce
  },
  success: function(response) {
    if (response.success) {
      // Show success message
    } else {
      // Show error message
    }
  }
});
```

### Contact Form Submission Deletion

#### Endpoint
```
POST /wp-admin/admin-ajax.php
```

#### Request Parameters
```javascript
{
  action: 'delete_submission',
  submission_id: 123,
  security: 'nonce_string'
}
```

#### Response
```json
{
  "success": true
}
```

**Error Response:**
```json
{
  "success": false,
  "data": "Failed to delete the submission."
}
```

## Custom URL Rewrite Rules

### Version-Specific URLs

#### Pattern
```
([^/]+)/release/([0-9]+)/?$
```

#### Rewrite Rule
```
index.php?name=$matches[1]&version=$matches[2]
```

#### Examples
- `/article-title/release/1/` → Latest version
- `/article-title/release/2/` → Version 2
- `/article-title/release/3/` → Version 3

### Search URLs

#### Pattern
```
^search/(.+)/?$
```

#### Rewrite Rule
```
index.php?s=$matches[1]
```

#### Examples
- `/search/gene-drive/` → Search for "gene-drive"
- `/search/` → Empty search

## Data Structures

### Post Meta Fields

#### DOI Field
```php
// Field name: 'doi'
// Type: string
// Format: '10.1234/unique-identifier'
// Example: '10.1234/abc123def456'
```

#### Version Field
```php
// Field name: 'version'
// Type: string
// Format: incrementing number
// Example: '1', '2', '3'
```

#### Version History Field
```php
// Field name: 'version_history'
// Type: array (serialized)
// Structure:
array(
  '1' => 'Original content...',
  '2' => 'Updated content...',
  '3' => 'Latest content...'
)
```

### Contact Form Submissions Table

#### Table Name
```sql
wp_contact_form_submissions
```

#### Schema
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

#### Example Data
```sql
INSERT INTO wp_contact_form_submissions 
(name, email, message, time) 
VALUES 
('John Doe', 'john@example.com', 'Contact message', '2024-01-15 10:00:00');
```

## JavaScript Objects

### WordPress Data Object
```javascript
// Available globally in admin
var ajaxurl = "https://example.com/wp-admin/admin-ajax.php";
```

### Custom AJAX Object
```javascript
// Localized script data
var my_ajax_object = {
    ajax_url: "https://example.com/wp-admin/admin-ajax.php",
    nonce: "nonce_string"
};
```

### Version Data Object
```javascript
// Localized script data for version functionality
var wp_data = {
    ajaxurl: "https://example.com/wp-admin/admin-ajax.php",
    post_id: 123,
    current_version: "2",
    nonce: "nonce_string"
};
```

## Error Handling

### AJAX Error Responses

#### Common Error Codes
- `400`: Bad Request - Invalid parameters
- `403`: Forbidden - Insufficient permissions
- `404`: Not Found - Resource doesn't exist
- `500`: Internal Server Error - Server-side error

#### Error Response Format
```json
{
  "success": false,
  "data": {
    "message": "Error description",
    "code": "ERROR_CODE"
  }
}
```

### JavaScript Error Handling
```javascript
.catch(error => {
    console.error('AJAX Error:', error);
    // Fallback behavior
    fetchVersionContent(wp_data.current_version);
});
```

## Security Measures

### Nonce Verification
```php
// Verify nonce for AJAX requests
check_ajax_referer('action_name', 'security');
```

### Permission Checks
```php
// Check user capabilities
if (!current_user_can('edit_pages')) {
    wp_send_json_error('Insufficient permissions');
}
```

### Data Sanitization
```php
// Sanitize input data
$name = sanitize_text_field($_POST['name']);
$email = sanitize_email($_POST['email']);
$message = sanitize_textarea_field($_POST['message']);
```

## Performance Considerations

### Database Optimization
- Use indexed queries for post meta lookups
- Limit version history size for performance
- Optimize contact form queries with proper indexing

### Caching
- Implement WordPress caching for API responses
- Cache version content to reduce database queries
- Use transients for frequently accessed data

### AJAX Optimization
- Minimize AJAX requests with efficient data loading
- Use debouncing for search functionality
- Implement proper error handling and fallbacks

## Rate Limiting

### Recommended Limits
- **Version Content Fetching**: 10 requests per minute per user
- **Contact Form Submissions**: 5 submissions per hour per IP
- **Search Requests**: 20 requests per minute per user

### Implementation
```php
// Example rate limiting check
function check_rate_limit($action, $user_id) {
    $key = "rate_limit_{$action}_{$user_id}";
    $count = get_transient($key);
    
    if ($count === false) {
        set_transient($key, 1, 60); // 1 minute
        return true;
    }
    
    if ($count >= 10) {
        return false;
    }
    
    set_transient($key, $count + 1, 60);
    return true;
}
```

---

*Last Updated: [Current Date]*
*Version: 1.0* 