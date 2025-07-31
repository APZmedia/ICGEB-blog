# ICGEB Blog Documentation

Welcome to the ICGEB Blog documentation. This folder contains comprehensive documentation for the WordPress installation used for "Monitoring Gene Drives" - a scientific research blog focused on gene drive research monitoring.

## Documentation Index

### 📋 [Project Overview](project-overview.md)
Complete overview of the project, including purpose, architecture, key features, and deployment information.

### 🔧 [Technical Documentation](technical-docs.md)
Detailed technical specifications, code architecture, database design, and implementation details.

### 👥 [User Guide](user-guide.md)
Comprehensive guide for content creators, administrators, and end users of the system.

### 🔌 [API Reference](api-reference.md)
Complete reference for all API endpoints, AJAX handlers, and data structures.

### 🛠️ [Troubleshooting Guide](troubleshooting.md)
Common issues, error messages, and solutions for maintaining the system.

### 💻 [Development Guide](development-guide.md)
Guide for developers working with, extending, or maintaining the system.

## Quick Start

### For Content Creators
1. Read the [User Guide](user-guide.md) to understand how to create and manage content
2. Learn about the [DOI System](#doi-system) and [Version Control](#version-control) features
3. Understand how to use the [PDF Generation](#pdf-generation) feature

### For Administrators
1. Review the [Project Overview](project-overview.md) for system understanding
2. Use the [Troubleshooting Guide](troubleshooting.md) for common issues
3. Reference the [API Reference](api-reference.md) for technical details

### For Developers
1. Start with the [Development Guide](development-guide.md)
2. Review the [Technical Documentation](technical-docs.md) for architecture details
3. Use the [API Reference](api-reference.md) for integration work

## Key Features

### 🆔 DOI System
- Automatic Digital Object Identifier generation
- Academic citation support
- Permanent content identification

### 📝 Version Control
- Complete article versioning with history
- Dynamic version switching
- URL-based version access

### 📄 PDF Generation
- Professional academic PDF formatting
- Automatic cover page generation
- Image and content optimization

### 📧 Contact Management
- Contact form submission storage
- Admin interface for managing submissions
- Email notification system

### 🔍 Search & Navigation
- Advanced search functionality
- Category-based organization
- Mobile-responsive navigation

## System Architecture

```
ICGEB Blog
├── WordPress Core
├── Custom Theme (icgeb-blog)
│   ├── Tailwind CSS Framework
│   ├── Responsive Design
│   └── Custom Templates
├── Custom Plugins
│   ├── DOI and Version Plugin
│   ├── Contact Form Submissions
│   └── PDF Download Plugin
└── Database
    ├── WordPress Core Tables
    ├── Custom Post Meta (DOI, Version)
    └── Contact Form Submissions Table
```

## Technology Stack

- **Backend**: WordPress, PHP 7.4+, MySQL 5.7+
- **Frontend**: Tailwind CSS, Vanilla JavaScript, jQuery
- **PDF Generation**: DOMPDF Library
- **Analytics**: Google Tag Manager
- **Hosting**: Apache/Nginx web server

## Support & Maintenance

### Contact Information
- **Developer**: APZmedia
- **Organization**: ICGEB (International Centre for Genetic Engineering and Biotechnology)
- **Support**: Open Philanthropy's Innovation Policy Program

### Maintenance Tasks
- Regular WordPress core updates
- Plugin and theme updates
- Database optimization
- Security monitoring
- Performance optimization

## File Structure

```
.notes/
├── README.md                 # This file
├── project-overview.md       # Project overview and architecture
├── technical-docs.md         # Technical implementation details
├── user-guide.md            # End user documentation
├── api-reference.md         # API and integration reference
├── troubleshooting.md       # Common issues and solutions
└── development-guide.md     # Developer documentation
```

## Contributing

When contributing to the documentation:

1. **Update relevant files** when making changes to the system
2. **Follow the existing format** and structure
3. **Include code examples** where appropriate
4. **Test documentation** to ensure accuracy
5. **Update version numbers** and dates

## Version History

- **v1.0** - Initial documentation release
- Comprehensive coverage of all system features
- Detailed technical and user documentation

---

*Last Updated: [Current Date]*
*Documentation Version: 1.0* 