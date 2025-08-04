# Job Notices Plugin - Visual Consistency Documentation

## Overview

This document outlines the comprehensive visual consistency improvements made across all job-related components in the Job Notices plugin. All components now share a unified design system with consistent spacing, typography, colors, and layout patterns.

## Design System Components

### 1. Container System

All job components use a consistent container structure:

```scss
.job-notices__container {
    // Base container styles
    &--single { /* Single job page variant */ }
    &--block { /* Gutenberg block variant */ }
}
```

### 2. Typography Scale

Consistent typography hierarchy across all components:

- **H1**: 2rem (32px) - Main page titles
- **H2**: 1.75rem (28px) - Section headers
- **H3**: 1.5rem (24px) - Subsection headers
- **H4**: 1.25rem (20px) - Card titles
- **H5**: 1.125rem (18px) - Meta information
- **H6**: 1rem (16px) - Small text

### 3. Spacing System

Consistent spacing using a modular scale:

- **Small**: 0.5rem (8px) - Tight spacing
- **Medium**: 1rem (16px) - Standard spacing
- **Large**: 1.5rem (24px) - Section spacing
- **Extra Large**: 2rem (32px) - Page spacing

### 4. Color Palette

Unified color system using CSS custom properties:

```scss
--wp-fundi-primary-color: #552732
--wp-fundi-alternative-color: #fcdfb7
--wp-fundi-main-text-color: #192a3d
--wp-fundi-secondary-text-color: #000000
--wp-fundi-border-color: #687279
--wp-fundi-subtle-background-color: #f2f5f7
--wp-fundi-light-alternative-color: #ffffff
```

## Component Consistency

### Job Cards

All job cards now share identical structure and styling:

#### Archive Job Cards

```scss
.job-notices__job-card {
    // Base card styles
    .job-notices__job-header {
        grid-template-columns: 1fr 6fr 2fr;
    }
}
```

#### Single Job Header

```scss
.job-notices__job-header--single {
    // Larger company logo
    .job-notices__company-logo img {
        width: 120px;
        height: 120px;
    }
    
    // Larger title
    .job-notices__job-title h2 {
        font-size: 1.5rem;
    }
}
```

#### Related Job Cards

```scss
.job-notices__job-card--related {
    // Smaller, compact layout
    .job-notices__company-logo img {
        width: 60px;
        height: 60px;
    }
    
    .job-notices__job-title h2 {
        font-size: 1rem;
    }
}
```

#### Block Job Cards

```scss
.job-notices__job-card--block {
    // Hover effects for blocks
    transition: transform 0.2s ease;
    
    &:hover {
        transform: translateY(-2px);
    }
}
```

### Content Areas

Consistent content styling across all templates:

```scss
.job-notices__content {
    background: var(--wp-fundi-light-alternative-color);
    padding: 2rem;
    border-radius: var(--wp-fundi-containers-border-radius);
    border: 1px solid var(--wp-fundi-border-color);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}
```

### Filter Forms

Unified filter form styling:

```scss
.job-notices__filter-form {
    .job-notices__filter-group {
        margin-bottom: 1.5rem;
    }
    
    .job-notices__button {
        &--primary { /* Primary button styles */ }
        &--secondary { /* Secondary button styles */ }
    }
}
```

## Layout Consistency

### Grid Systems

All components use consistent grid layouts:

#### Archive Layout

```scss
grid-template-areas:
    "job-header job-header"
    "job-content job-sidebar"
    "related-jobs related-jobs";
```

#### Single Job Layout

```scss
grid-template-areas:
    "job-header job-header"
    "job-content job-sidebar"
    "related-jobs related-jobs";
```

#### Block Layout

```scss
grid-template-areas:
    "job-content";
```

### Responsive Behavior

Consistent responsive breakpoints:

- **Desktop**: > 768px
- **Tablet**: 768px - 480px
- **Mobile**: < 480px

## Template Updates

### SingleJob.php

Updated to use namespaced classes:

- `single-job jobs-container` → `job-notices job-notices__container job-notices__container--single`
- `single-job-header` → `job-notices__job-header job-notices__job-header--single`
- `job-content` → `job-notices__content`
- `job-sidebar` → `job-notices__sidebar`
- `related-jobs` → `job-notices__related-jobs`

### RenderJobs.php

Updated to use namespaced classes:

- `jobs-hero` → `job-notices job-notices__hero-block`
- `jobs-container jobs-archive` → `job-notices__container job-notices__container--block`
- `jobs-results` → `job-notices__results job-notices__results--block`
- `job-cards-grid` → `job-notices__job-cards-grid job-notices__job-cards-grid--block`

### JobCard.php

Consistent structure across all contexts:

- Company logo with placeholder fallback
- Job metadata with consistent spacing
- Application section with unified button styling
- Tags and badges with consistent colors

## Visual Hierarchy

### 1. Primary Information

- Job title (largest text)
- Company name
- Location

### 2. Secondary Information

- Salary
- Job type
- Experience level
- Application deadline

### 3. Tertiary Information

- Tags and badges
- Share buttons
- Related jobs

## Interactive Elements

### Buttons

Consistent button styling across all components:

```scss
.job-notices__button {
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.2s ease;
    
    &--primary {
        background: var(--wp-fundi-primary-color);
        color: white;
    }
    
    &--secondary {
        background: var(--wp-fundi-secondary-color);
        color: white;
    }
}
```

### Links

Consistent link styling:

```scss
%job-notices__link {
    color: var(--wp-fundi-main-text-color);
    text-decoration: none;
    transition: color 0.2s ease;
    
    &:hover {
        color: var(--wp-fundi-border-color);
    }
}
```

### Form Elements

Unified form styling:

```scss
%job-notices__input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--wp-fundi-border-color);
    border-radius: 0.375rem;
    transition: border-color 0.2s ease;
    
    &:focus {
        border-color: var(--wp-fundi-primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(85, 39, 50, 0.2);
    }
}
```

## Accessibility Improvements

### 1. Focus States

All interactive elements have visible focus states:

- Buttons: Box shadow with primary color
- Links: Color change on focus
- Form inputs: Border color change and box shadow

### 2. Color Contrast

All text meets WCAG AA contrast requirements:

- Primary text: 4.5:1 contrast ratio
- Secondary text: 3:1 contrast ratio
- Interactive elements: 3:1 contrast ratio

### 3. Semantic HTML

Proper use of semantic elements:

- `<article>` for job cards
- `<section>` for content areas
- `<aside>` for sidebars
- `<nav>` for pagination

## Performance Optimizations

### 1. CSS Efficiency

- Shared placeholder mixins reduce CSS duplication
- BEM naming prevents specificity conflicts
- Scoped styles prevent global pollution

### 2. Responsive Images

- Consistent image sizes across contexts
- Proper aspect ratios maintained
- Placeholder fallbacks for missing images

### 3. Animation Performance

- Hardware-accelerated transforms for hover effects
- Smooth transitions with appropriate timing
- Reduced motion support for accessibility

## Testing Checklist

### Visual Consistency

- [ ] Job cards look identical across archive, single, and block contexts
- [ ] Typography hierarchy is consistent
- [ ] Spacing and padding are uniform
- [ ] Colors match the design system
- [ ] Interactive elements behave consistently

### Responsive Design

- [ ] All components work on mobile devices
- [ ] Grid layouts adapt properly
- [ ] Touch targets are appropriately sized
- [ ] Text remains readable at all sizes

### Cross-Browser Compatibility

- [ ] Styles work in Chrome, Firefox, Safari, Edge
- [ ] CSS Grid is properly supported
- [ ] CSS custom properties have fallbacks
- [ ] Animations work smoothly

### Theme Compatibility

- [ ] Styles don't conflict with popular themes
- [ ] Namespaced classes prevent style leakage
- [ ] CSS variables integrate with theme colors
- [ ] Backward compatibility is maintained

## Future Enhancements

### 1. Design System Expansion

- Additional color themes
- Dark mode support
- Custom typography options
- Advanced layout variations

### 2. Component Library

- Reusable component patterns
- Documentation for developers
- Style guide for designers
- Interactive component examples

### 3. Performance Improvements

- CSS-in-JS implementation
- Critical CSS extraction
- Lazy loading for images
- Advanced caching strategies
