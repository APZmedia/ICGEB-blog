# Incident Resolution: Version URL 404 Error - July 2025

## Incident Summary

**Date**: July 2025  
**Issue**: Version-specific URLs returning 404 errors  
**Affected URLs**: `https://monitoringgenedrives.com/article-name/release/3/`  
**Impact**: Blocked article publication scheduled for next day  
**Resolution Time**: ~2 hours  
**Status**: ✅ RESOLVED

## 🚨 Problem Description

### Initial Report
Client reported that version-specific URLs were returning "Page not found" (404) errors:
- `https://monitoringgenedrives.com/how-models-predict-gene-drive-spread/release/3/`
- All version URLs with `/release/[number]/` pattern were affected
- Issue started today and was blocking scheduled article publication

### Symptoms
- ✅ Regular article URLs worked: `/article-name/`
- ❌ Version URLs failed: `/article-name/release/3/`
- ✅ AJAX version switching worked on article pages
- ❌ Direct version URL access returned 404

## 🔍 Root Cause Analysis

### Initial Investigation
1. **WordPress Permalinks**: Set to "Custom Structure" (`/%postname%/`)
2. **Plugin Status**: "DOI and Version for Articles" plugin was active
3. **File Permissions**: .htaccess file existed and was writable
4. **Server Logs**: No recent errors (old errors from December 2024 were irrelevant)

### Critical Discovery
The `.htaccess` file was **missing the custom rewrite rules** for version URLs:

```apache
# MISSING RULE:
RewriteRule ^([^/]+)/release/([0-9]+)/?$ index.php?name=$1&version=$2 [QSA,L]
```

### Root Cause Identified
**Plugin Code Flaw**: The `add_rewrite_rules()` function in the DOI and Version plugin had an incorrect admin check:

```php
// PROBLEMATIC CODE:
public function add_rewrite_rules() {
    if (is_admin() || is_preview()) {
        return; // This prevented rewrite rules from being registered!
    }
    add_rewrite_rule('([^/]+)/release/([0-9]+)/?$', 'index.php?name=$matches[1]&version=$matches[2]', 'top');
}
```

**Why This Failed**:
- Plugin activation happens in admin context
- Admin check caused function to return early
- Rewrite rules were never registered with WordPress
- .htaccess never received the custom rules

## 🛠️ Solution Implementation

### Code Fix Applied
**File**: `plugins/doi-version-plugin/doi-version-plugin.php`  
**Lines**: 90-94

**Before**:
```php
public function add_rewrite_rules() {
    if (is_admin() || is_preview()) {
        return; 
    }
    add_rewrite_rule('([^/]+)/release/([0-9]+)/?$', 'index.php?name=$matches[1]&version=$matches[2]', 'top');
}
```

**After**:
```php
public function add_rewrite_rules() {
    add_rewrite_rule('([^/]+)/release/([0-9]+)/?$', 'index.php?name=$matches[1]&version=$matches[2]', 'top');
    
    // Force flush if this is the first time
    if (!get_option('doi_version_rewrite_flushed')) {
        flush_rewrite_rules();
        update_option('doi_version_rewrite_flushed', true);
    }
}
```

### Changes Made
1. **Removed admin check** that prevented rule registration
2. **Added force flush mechanism** for first-time activation
3. **Ensured proper WordPress integration**

## 📋 Resolution Steps

### Step 1: Code Analysis
- Analyzed plugin codebase for rewrite rule registration
- Identified the problematic admin check in `add_rewrite_rules()`
- Confirmed the function was never executing due to early return

### Step 2: Code Fix
- Removed the `is_admin()` check that was blocking rule registration
- Added automatic flush mechanism for first-time activation
- Maintained proper WordPress integration patterns

### Step 3: Plugin Reactivation
- Deactivated the "DOI and Version for Articles" plugin
- Reactivated the plugin to trigger the fixed activation hook
- Verified rewrite rules were properly registered

### Step 4: Permalink Flush
- Navigated to **Settings → Permalinks**
- Clicked **"Save Changes"** to flush rewrite rules
- Confirmed .htaccess was updated with custom rules

### Step 5: Testing
- Tested version URLs: `/article-name/release/3/`
- Verified 404 errors were resolved
- Confirmed AJAX functionality still worked

## ✅ Results

### Immediate Resolution
- ✅ **Version URLs work**: No more 404 errors
- ✅ **AJAX functionality preserved**: Version dropdown still works
- ✅ **Plugin activation fixed**: Rewrite rules register properly
- ✅ **No manual .htaccess editing required**: Automatic management

### Long-term Benefits
- ✅ **Permanent fix**: Won't be overwritten by updates
- ✅ **Proper WordPress integration**: Uses native rewrite system
- ✅ **Self-maintaining**: No manual intervention required
- ✅ **Scalable**: Works for all future articles

## 🎯 Technical Details

### Rewrite Rule Pattern
```apache
RewriteRule ^([^/]+)/release/([0-9]+)/?$ index.php?name=$1&version=$2 [QSA,L]
```

**Components**:
- `([^/]+)` - Captures article slug
- `release/([0-9]+)` - Captures version number
- `/?$` - Optional trailing slash
- `index.php?name=$1&version=$2` - WordPress query parameters

### WordPress Integration
- **Query Variable**: `version` added to WordPress query vars
- **Template Handling**: `single.php` processes version parameter
- **Content Display**: Version-specific content loaded via AJAX

## 📚 Lessons Learned

### 1. **Plugin Design Flaws**
- **Problem**: Admin checks can prevent critical functionality
- **Lesson**: Rewrite rules should be registered regardless of context
- **Prevention**: Test plugin activation in various contexts

### 2. **WordPress Rewrite System**
- **Problem**: Rewrite rules must be explicitly registered
- **Lesson**: Always use `add_rewrite_rule()` and `flush_rewrite_rules()`
- **Prevention**: Include proper activation/deactivation hooks

### 3. **Debugging Approach**
- **Problem**: Initial focus was on hosting/server issues
- **Lesson**: Code analysis revealed the actual root cause
- **Prevention**: Always examine plugin code when functionality fails

### 4. **Documentation Importance**
- **Problem**: No clear documentation of expected behavior
- **Lesson**: Proper documentation helps with troubleshooting
- **Prevention**: Maintain comprehensive system documentation

## 🔧 Prevention Measures

### Code Quality
- ✅ **Remove unnecessary admin checks** in rewrite functions
- ✅ **Add proper error handling** for rule registration
- ✅ **Include activation/deactivation hooks**
- ✅ **Test in multiple contexts** (admin, frontend, activation)

### Monitoring
- ✅ **Regular testing** of version URL functionality
- ✅ **Monitor .htaccess** for proper rule inclusion
- ✅ **Check plugin activation** logs
- ✅ **Verify rewrite rule registration**

### Documentation
- ✅ **Update troubleshooting guide** with this incident
- ✅ **Document expected .htaccess content**
- ✅ **Include plugin activation procedures**
- ✅ **Maintain incident resolution records**

## 📋 Post-Incident Actions

### Completed
- ✅ **Code fix implemented** and tested
- ✅ **Plugin reactivated** successfully
- ✅ **Version URLs verified** working
- ✅ **Documentation updated** with incident details

### Recommended
- 🔄 **Monitor system** for 24-48 hours
- 🔄 **Test with new articles** to ensure functionality
- 🔄 **Update plugin version** to reflect the fix
- 🔄 **Review other plugins** for similar issues

## 🎉 Conclusion

### Success Metrics
- **Resolution Time**: ~2 hours (acceptable for critical issue)
- **Root Cause**: Identified and fixed permanently
- **User Impact**: Minimal (AJAX functionality remained available)
- **System Health**: Fully restored with improvements

### Key Takeaways
1. **Plugin code quality** is critical for WordPress functionality
2. **Rewrite rules** require explicit registration and flushing
3. **Admin context checks** can block essential functionality
4. **Proper debugging** starts with code analysis, not server configuration

### Future Recommendations
1. **Code review** all plugins for similar issues
2. **Automated testing** of version URL functionality
3. **Monitoring alerts** for 404 errors on version URLs
4. **Regular plugin maintenance** and updates

---

**Incident Owner**: Development Team  
**Resolution Date**: July 2025  
**Next Review**: August 2025  
**Status**: ✅ CLOSED 