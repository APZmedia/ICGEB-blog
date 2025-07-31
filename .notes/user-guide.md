# User Guide - ICGEB Blog

## Table of Contents
1. [Getting Started](#getting-started)
2. [Content Management](#content-management)
3. [Version Control System](#version-control-system)
4. [DOI System](#doi-system)
5. [Contact Form Management](#contact-form-management)
6. [PDF Generation](#pdf-generation)
7. [Search and Navigation](#search-and-navigation)
8. [Administrative Tasks](#administrative-tasks)
9. [Troubleshooting](#troubleshooting)

## Getting Started

### Accessing the System
- **Admin Access**: Navigate to `/wp-admin` and log in with your credentials
- **Public Access**: Visit the main site URL to view published content
- **User Roles**: Different permission levels for authors, editors, and administrators

### Dashboard Overview
The WordPress dashboard provides access to:
- **Posts**: Create and manage scientific articles
- **Pages**: Static content like About, Privacy Policy
- **Categories**: Organize content by research topics
- **Contact Submissions**: View and manage contact form submissions
- **Users**: Manage user accounts and permissions

## Content Management

### Creating a New Article

#### Step 1: Create Post
1. Navigate to **Posts → Add New**
2. Enter the article title
3. Add content using the WordPress editor
4. Set featured image (recommended: 1200x630px)

#### Step 2: Configure DOI and Version
- **DOI**: Automatically generated when post is published
- **Version**: Starts at "1" and increments automatically with changes
- **Version History**: Automatically maintained by the system

#### Step 3: Categorize Content
1. Select appropriate categories from the right sidebar
2. Categories help organize research by topic
3. Categories appear as tags on published articles

#### Step 4: Publish
1. Review content thoroughly
2. Click **Publish** to make the article live
3. DOI and initial version are automatically assigned

### Editing Existing Articles

#### Content Updates
1. Navigate to **Posts → All Posts**
2. Click **Edit** on the desired article
3. Make your changes in the editor
4. Click **Update** to save changes
5. **Version automatically increments** when content changes

#### Version Management
- **Current Version**: Always displays the latest version
- **Version History**: Accessible through the version dropdown on the frontend
- **URL Structure**: `/post-name/release/version-number/`

### Article Metadata

#### DOI Information
- **Automatic Generation**: DOIs are created when articles are first published
- **Format**: `10.1234/unique-identifier`
- **Permanent**: DOIs remain unchanged throughout the article's lifecycle
- **Citation**: Used in academic references

#### Version Information
- **Current Version**: Shows the latest version number
- **Version History**: Complete record of all content changes
- **URL Access**: Direct links to specific versions
- **Citation Integration**: Version numbers included in citations

## Version Control System

### Understanding Versions

#### Version Numbers
- **Format**: Simple incrementing numbers (1, 2, 3, etc.)
- **Automatic Increment**: Occurs when article content is modified
- **No Manual Control**: System manages versioning automatically

#### Version History
- **Complete Record**: Every version is preserved
- **Content Storage**: Full content of each version is saved
- **Access**: Available through frontend version dropdown

### Using Version Control

#### Viewing Different Versions
1. Navigate to any published article
2. Look for the **"View Versions"** dropdown
3. Select the desired version number
4. Content updates dynamically without page reload

#### Version URLs
- **Current Version**: `/article-name/release/latest-version/`
- **Specific Version**: `/article-name/release/2/`
- **Direct Linking**: Share specific version URLs

#### Version Comparison
- **Side-by-Side**: Compare versions using browser tabs
- **Content Differences**: Review changes between versions
- **Citation Accuracy**: Ensure citations reference correct versions

## DOI System

### Digital Object Identifiers

#### What is a DOI?
- **Permanent Identifier**: Unique, persistent identifier for digital content
- **Academic Standard**: Widely used in scientific publishing
- **Citation Support**: Enables proper academic referencing

#### DOI Features
- **Automatic Assignment**: Generated when articles are published
- **Permanent**: Never changes, even with content updates
- **Citation Integration**: Included in automatic citation generation
- **Academic Compliance**: Follows DOI standards

### Using DOIs

#### Finding DOIs
- **Article Pages**: Displayed in article metadata
- **Citation Panel**: Included in generated citations
- **Admin Panel**: Visible in post editor

#### DOI in Citations
- **Automatic Inclusion**: DOIs are automatically added to citations
- **Academic Format**: Properly formatted for academic use
- **Version Integration**: DOI + version number for precise referencing

## Contact Form Management

### Viewing Submissions

#### Accessing Submissions
1. Navigate to **Contact Submissions** in the admin menu
2. View all submissions in chronological order
3. Each submission shows: time, name, email, and message

#### Submission Details
- **Timestamp**: When the submission was received
- **Contact Information**: Name and email of the person
- **Message Content**: Full message text
- **Management Actions**: Delete submissions as needed

### Managing Submissions

#### Deleting Submissions
1. Click the **Delete** button next to any submission
2. Confirm the deletion in the popup dialog
3. Submission is permanently removed from the database

#### Email Notifications
- **Automatic Alerts**: Admins receive email notifications for new submissions
- **Email Content**: Includes name, email, and message
- **HTML Format**: Properly formatted email notifications

## PDF Generation

### Generating PDFs

#### Automatic PDF Creation
- **URL Trigger**: Add `?download_pdf=1&post_id=POST_ID` to any URL
- **Direct Download**: PDF downloads automatically
- **Professional Format**: Academic-style PDF with proper formatting

#### PDF Features
- **Cover Page**: Title, author, publication date, and URL
- **Content Pages**: Full article content with headers and footers
- **Professional Styling**: Academic formatting and typography
- **Image Support**: Images are included and properly sized

### PDF Structure

#### Cover Page Elements
- **Article Title**: Large, prominent display
- **"Monitoring Gene Drives"** branding
- **Publication Date**: When the article was published
- **Author Information**: Full author name
- **URL Reference**: Direct link to the article

#### Content Pages
- **Full Article Content**: Complete article text and images
- **Headers**: Article title and "Monitoring Gene Drives" on each page
- **Footers**: Page numbers and horizontal line
- **Professional Typography**: Academic-style formatting

## Search and Navigation

### Using the Search Function

#### Basic Search
1. Use the search bar in the header
2. Enter keywords or phrases
3. Click **Search** or press Enter
4. View results in a grid layout

#### Search Results
- **Article Previews**: Title, excerpt, and metadata
- **Category Information**: Shows relevant categories
- **Publication Date**: When the article was published
- **Direct Links**: Click to view full articles

### Navigation Features

#### Main Navigation
- **Primary Menu**: Accessible from the header
- **Mobile Menu**: Hamburger menu for mobile devices
- **Category Links**: Browse content by topic

#### Breadcrumb Navigation
- **Current Location**: Shows where you are in the site
- **Easy Navigation**: Quick access to parent pages
- **SEO Friendly**: Helps with search engine optimization

## Administrative Tasks

### User Management

#### Managing Users
1. Navigate to **Users → All Users**
2. View all registered users
3. Edit user roles and permissions
4. Add or remove users as needed

#### User Roles
- **Administrator**: Full access to all features
- **Editor**: Can publish and manage posts
- **Author**: Can publish their own posts
- **Contributor**: Can write but not publish posts

### Content Organization

#### Categories
- **Create Categories**: Organize content by research topics
- **Assign Categories**: Add categories to articles
- **Category Pages**: View all articles in a category

#### Tags
- **Add Tags**: Use tags for detailed content organization
- **Tag Clouds**: Visual representation of popular tags
- **Tag Pages**: Browse content by specific tags

### System Maintenance

#### Regular Tasks
- **WordPress Updates**: Keep core, themes, and plugins updated
- **Database Optimization**: Regular database maintenance
- **Backup Creation**: Regular backups of content and database
- **Security Monitoring**: Monitor for security issues

#### Performance Optimization
- **Caching**: Enable WordPress caching plugins
- **Image Optimization**: Compress images for faster loading
- **Database Cleanup**: Remove unnecessary data
- **Plugin Management**: Keep only necessary plugins active

## Troubleshooting

### Common Issues

#### Version Control Problems
**Issue**: Versions not updating properly
**Solution**: 
1. Check if content actually changed
2. Verify post status is "Published"
3. Clear any caching plugins
4. Check for JavaScript errors in browser console

#### DOI Issues
**Issue**: DOI not generating
**Solution**:
1. Ensure post is published (not draft)
2. Check post type is "post"
3. Verify user permissions
4. Check for database errors

#### PDF Generation Problems
**Issue**: PDF not downloading
**Solution**:
1. Verify URL parameters are correct
2. Check server has sufficient memory
3. Ensure DOMPDF library is properly installed
4. Check file permissions

#### Contact Form Issues
**Issue**: Submissions not being saved
**Solution**:
1. Check database table exists
2. Verify form submission process
3. Check for JavaScript errors
4. Ensure proper nonce verification

### Getting Help

#### Support Resources
- **WordPress Documentation**: Official WordPress documentation
- **Plugin Documentation**: Check individual plugin documentation
- **Developer Support**: Contact APZmedia for technical issues
- **ICGEB Support**: Contact ICGEB for content-related issues

#### Error Reporting
When reporting issues, include:
- **Error Messages**: Exact error text
- **Steps to Reproduce**: How to recreate the issue
- **Browser Information**: Browser type and version
- **User Role**: Your WordPress user role
- **Screenshots**: Visual evidence of the problem

---

*Last Updated: [Current Date]*
*Version: 1.0* 