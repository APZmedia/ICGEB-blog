# ICGEB Blog - Project Overview

## Project Description

The ICGEB Blog is a specialized WordPress installation designed for **"Monitoring Gene Drives"** - a scientific research blog focused on gene drive research monitoring. The project is implemented by ICGEB (International Centre for Genetic Engineering and Biotechnology) and supported by Open Philanthropy's Innovation Policy Program.

## Purpose & Mission

This WordPress installation serves as a platform for:
- Publishing and archiving scientific research articles on gene drive technology
- Providing version-controlled academic content with proper citation systems
- Facilitating public engagement with scientific research
- Supporting policy makers and stakeholders in genetic engineering
- Creating a professional, accessible interface for academic content

## Target Audience

- **Scientific researchers** and academics working on gene drive research
- **Policy makers** and stakeholders in genetic engineering
- **Academic institutions** and research organizations
- **General public** interested in scientific research
- **Students** and educators in biotechnology fields

## Technical Architecture

### Frontend Technologies
- **WordPress Core** (Latest stable version)
- **Custom Theme**: `icgeb-blog` (Tailwind CSS-based)
- **JavaScript**: Vanilla JS + jQuery for AJAX operations
- **CSS Framework**: Tailwind CSS for responsive design
- **Mobile-First**: Responsive design with progressive enhancement

### Backend Technologies
- **PHP**: WordPress core and custom plugins
- **MySQL**: WordPress database + custom tables
- **AJAX**: Dynamic content loading and version switching
- **REST API**: WordPress REST API for dynamic features

### External Dependencies
- **DOMPDF**: PDF generation library
- **Google Tag Manager**: Analytics and tracking
- **Tailwind CSS**: CDN-based styling framework

## Key Features

### 1. Academic Content Management
- **DOI System**: Automatic Digital Object Identifier generation
- **Version Control**: Complete article versioning with history
- **Citation System**: Automatic academic citation generation
- **Category Organization**: Scientific content categorization

### 2. User Experience
- **Responsive Design**: Mobile-first approach with desktop optimization
- **Search Functionality**: Advanced search with results display
- **PDF Downloads**: Professional PDF generation of articles
- **Interactive Elements**: Version switching, citation copying, details panels

### 3. Content Publishing
- **Version Management**: Automatic version incrementing on content changes
- **URL Rewriting**: Clean URLs for specific article versions
- **RSS Feeds**: Content syndication support
- **SEO Optimization**: Search engine friendly structure

### 4. Administrative Features
- **Contact Form Management**: Submission storage and admin interface
- **Content Versioning**: Complete version history tracking
- **User Management**: WordPress standard user roles and permissions
- **Analytics Integration**: Google Tag Manager for tracking

## File Structure

```
ICGEB-blog/
├── plugins/
│   ├── contact-form-submissions/     # Contact form management
│   ├── doi-version-plugin/          # DOI and versioning system
│   └── my-pdf-download-plugin/      # PDF generation
├── themes/
│   └── icgeb-blog/                  # Custom theme
│       ├── js/                      # JavaScript files
│       ├── *.php                    # Template files
│       └── style.css               # Theme styles
└── .notes/                         # Documentation
```

## Development Guidelines

### Code Standards
- **WordPress Coding Standards**: Follow WordPress PHP coding standards
- **Security**: Proper nonce verification, data sanitization, and permission checks
- **Performance**: Optimized database queries and efficient AJAX calls
- **Accessibility**: WCAG compliance for inclusive design

### Maintenance
- **Regular Updates**: WordPress core, themes, and plugins
- **Security Monitoring**: Regular security audits and updates
- **Backup Strategy**: Regular database and file backups
- **Performance Monitoring**: Analytics and performance tracking

## Deployment Information

### Server Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **WordPress**: Latest stable version
- **Web Server**: Apache or Nginx

### Configuration
- **Custom Permalinks**: Enabled for clean URLs
- **File Permissions**: Proper WordPress file permissions
- **Database Optimization**: Regular database maintenance
- **Caching**: Recommended caching plugin for performance

## Support & Maintenance

### Contact Information
- **Developer**: APZmedia
- **Organization**: ICGEB (International Centre for Genetic Engineering and Biotechnology)
- **Support**: Open Philanthropy's Innovation Policy Program

### Documentation
- **User Guide**: See `user-guide.md`
- **Technical Documentation**: See `technical-docs.md`
- **API Reference**: See `api-reference.md`
- **Troubleshooting**: See `troubleshooting.md`

---

*Last Updated: [Current Date]*
*Version: 1.0* 