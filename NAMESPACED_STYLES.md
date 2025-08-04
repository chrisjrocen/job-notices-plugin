# Job Notices Plugin - Namespaced Styles Documentation

## Overview

The Job Notices plugin has been completely refactored to use namespaced CSS classes to prevent conflicts with WordPress themes and other plugins. All styles are now scoped under the `.job-notices` namespace.

## Namespace Structure

### Root Namespace

- **`.job-notices`** - The main wrapper class that contains all plugin styles

### BEM Naming Convention

The plugin uses BEM (Block Element Modifier) naming convention:

- **Block**: `.job-notices__container`
- **Element**: `.job-notices__job-card`
- **Modifier**: `.job-notices__button--primary`

## CSS Architecture

### 1. Scoped CSS Reset

All CSS resets are scoped within `.job-notices` to prevent global style pollution:

```scss
.job-notices {
    // Reset box-sizing, margins, paddings, etc.
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }
    
    h1, h2, h3, h4, h5, h6 {
        margin: 0;
        padding: 0;
        font-weight: inherit;
        font-size: inherit;
        line-height: inherit;
    }
}
```

### 2. Placeholder Mixins

Reusable styles using SCSS placeholders:

```scss
%job-notices__application-button {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--wp-fundi-alternative-color, #fcdfb7);
    border-radius: 0.375rem;
    background: var(--wp-fundi-primary-color, #552732);
    // ... more styles
}
```

### 3. Component-Based Structure

Each component has its own section:

- Container styles
- Job header styles
- Job card styles
- Filter styles
- Results styles
- Loading states
- Pagination styles
- Responsive design

## Class Reference

### Container Classes

- `.job-notices__container` - Main container wrapper
- `.job-notices__filters` - Filter sidebar
- `.job-notices__results` - Results section

### Job Card Classes

- `.job-notices__job-card` - Individual job card
- `.job-notices__job-card-inner` - Card inner wrapper
- `.job-notices__job-header` - Job header section
- `.job-notices__company-logo` - Company logo container
- `.job-notices__job-meta` - Job metadata section
- `.job-notices__job-title` - Job title
- `.job-notices__job-details` - Job details
- `.job-notices__job-tags` - Job tags container

### Filter Classes

- `.job-notices__filter-form` - Filter form
- `.job-notices__filter-group` - Filter group container
- `.job-notices__button` - Button base class
- `.job-notices__button--primary` - Primary button
- `.job-notices__button--secondary` - Secondary button

### Results Classes

- `.job-notices__results-header` - Results header
- `.job-notices__results-count` - Results count
- `.job-notices__results-controls` - Results controls
- `.job-notices__job-cards-grid` - Job cards grid
- `.job-notices__no-jobs-found` - No jobs message

### Loading States

- `.job-notices__results--loading` - Loading state modifier
- `.job-notices__loading-spinner` - Loading spinner
- `.job-notices__spinner` - Spinner animation
- `.job-notices__filter-error` - Error message

### Utility Classes

- `.job-notices__tag` - Tag base class
- `.job-notices__tag--type` - Type tag modifier
- `.job-notices__tag--urgent` - Urgent tag modifier
- `.job-notices__badge` - Badge base class
- `.job-notices__badge--featured` - Featured badge modifier

## Implementation in Templates

### PHP Templates

All PHP templates now use the namespaced classes:

```php
// JobFilters.php
<div class="job-notices">
    <form class="job-notices__filter-form">
        <div class="job-notices__filter-group">
            <label>Search by Keywords</label>
            <input type="text" id="keywords">
        </div>
    </form>
</div>

// JobCard.php
<div class="job-notices__job-card">
    <div class="job-notices__job-card-inner">
        <div class="job-notices__job-header">
            <div class="job-notices__company-logo">
                <img src="...">
            </div>
            <div class="job-notices__job-meta">
                <h2 class="job-notices__job-title">
                    <a href="...">Job Title</a>
                </h2>
            </div>
        </div>
    </div>
</div>
```

### JavaScript Integration

JavaScript selectors updated to use namespaced classes:

```javascript
// Old selectors
const resultsContainer = document.querySelector('.jobs-results');
const form = document.querySelector('.job-filter-form');

// New selectors
const resultsContainer = document.querySelector('.job-notices__results');
const form = document.querySelector('.job-notices__filter-form');
```

## CSS Variables

The plugin uses CSS custom properties with fallbacks:

```scss
.job-notices__button--primary {
    background: var(--wp-fundi-primary-color, #552732);
    color: var(--wp-fundi-light-alternative-color, #ffffff);
}
```

### Available Variables

- `--wp-fundi-primary-color` - Primary brand color
- `--wp-fundi-alternative-color` - Alternative color
- `--wp-fundi-main-text-color` - Main text color
- `--wp-fundi-secondary-text-color` - Secondary text color
- `--wp-fundi-border-color` - Border color
- `--wp-fundi-subtle-background-color` - Subtle background
- `--wp-fundi-light-alternative-color` - Light alternative
- `--wp-fundi-success-color` - Success color
- `--wp-fundi-error-color` - Error color
- `--wp-fundi-warning-color` - Warning color
- `--wp-fundi-info-color` - Info color

## Responsive Design

The plugin includes comprehensive responsive design:

```scss
@media (max-width: 768px) {
    .job-notices__container {
        grid-template-columns: 1fr;
        padding: 5rem 0.2rem;
    }
    
    .job-notices__job-header {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .job-notices__container {
        padding: 2rem 1rem;
    }
}
```

## Backward Compatibility

Legacy classes are maintained for backward compatibility:

```scss
// Legacy support
.jobs-archive.jobs-container,
.single-job.jobs-container,
.hero-search-block {
    @extend .job-notices__container;
}

.job-filter-form {
    @extend .job-notices__filter-form;
}
```

## Benefits

### 1. Conflict Prevention

- All styles are scoped under `.job-notices`
- No global style pollution
- Prevents conflicts with themes and other plugins

### 2. Maintainability

- Clear BEM naming convention
- Organized component structure
- Easy to understand and modify

### 3. Portability

- Styles work across different themes
- No dependency on theme CSS
- Self-contained styling system

### 4. Performance

- Efficient CSS selectors
- Minimal specificity conflicts
- Optimized for modern browsers

## Migration Guide

### For Developers

1. Update PHP templates to use new class names
2. Update JavaScript selectors
3. Test across different themes
4. Verify all functionality works

### For Users

- No action required
- Plugin automatically uses new styles
- Backward compatibility maintained

## Testing

### Cross-Theme Testing

Test the plugin on various themes:

- Default WordPress themes (Twenty Twenty-Four, etc.)
- Popular themes (Astra, GeneratePress, etc.)
- Custom themes
- Themes with aggressive CSS resets

### Browser Testing

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile browsers
- Older browser versions (if needed)

## Future Enhancements

### Potential Improvements

- CSS-in-JS implementation
- Dynamic theme integration
- Advanced customization options
- Performance optimizations

### Maintenance

- Regular CSS audits
- Performance monitoring
- Cross-browser compatibility checks
- Theme compatibility testing
