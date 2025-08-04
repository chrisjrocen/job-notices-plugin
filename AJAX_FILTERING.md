# Ajax Job Filtering System

## Overview

The job-notices plugin now includes a live Ajax-based filtering system that allows users to filter jobs in real-time without page reloads. This provides a much better user experience compared to the previous static filtering.

## Features

### Live Filtering

- **Keywords**: Search by job title, keywords, or company name
- **Location**: Filter by city or postcode
- **Category**: Filter by job category (taxonomy-based)
- **Job Type**: Filter by employment type (full-time, part-time, etc.)
- **Salary Range**: Filter by salary using a range slider
- **Sorting**: Sort by date, salary (ascending/descending)
- **Pagination**: Dynamic pagination with configurable items per page

### User Experience Features

- **Debouncing**: Text inputs have 500ms debounce to prevent excessive API calls
- **Loading States**: Visual feedback during Ajax requests
- **Error Handling**: User-friendly error messages
- **URL Updates**: Browser URL updates to reflect current filters
- **Clear Filters**: One-click button to reset all filters
- **Responsive Design**: Works on all device sizes

## Technical Implementation

### Files Modified/Created

1. **`core/Templates/JobsArchive.php`**
   - Added Ajax action handlers (`wp_ajax_filter_jobs`, `wp_ajax_nopriv_filter_jobs`)
   - Implemented `ajax_filter_jobs()` method with comprehensive query building
   - Added security with nonce verification
   - Returns JSON response with HTML and metadata

2. **`core/Templates/JobFilters.php`**
   - Added nonce field for security
   - Removed old `onkeyup` handlers
   - Added clear filters button
   - Improved form structure

3. **`assets/js/frontend/job-archive.js`**
   - Complete rewrite with modern JavaScript
   - Implemented debouncing for text inputs
   - Added loading states and error handling
   - URL management without page reloads
   - Event delegation for all filter inputs

4. **`core/Base/BaseController.php`**
   - Added script localization with Ajax URL and nonce
   - Included translatable strings

5. **`assets/scss/job-styles.scss`**
   - Added CSS for loading states
   - Improved filter form styling
   - Added responsive design improvements
   - Loading spinner animations

### Ajax Endpoint

**Action**: `filter_jobs`
**URL**: `/wp-admin/admin-ajax.php`

**POST Parameters**:

- `action`: 'filter_jobs'
- `nonce`: Security nonce
- `keywords`: Search keywords
- `location`: Location filter
- `category`: Category ID
- `job_type`: Job type slug
- `salary_min`: Minimum salary
- `salary_max`: Maximum salary
- `sort`: Sort order
- `paged`: Page number
- `posts_per_page`: Items per page

**Response Format**:

```json
{
  "success": true,
  "html": "<div>...</div>",
  "count": 25,
  "max_pages": 3,
  "debug": {
    "query_args": {...},
    "found_posts": 25,
    "post_count": 12
  }
}
```

## Security Features

- **Nonce Verification**: All Ajax requests require a valid nonce
- **Input Sanitization**: All user inputs are properly sanitized
- **Capability Checks**: Proper WordPress capability checks
- **SQL Injection Prevention**: Uses WordPress query methods

## Performance Optimizations

- **Debouncing**: Prevents excessive API calls during typing
- **Query Optimization**: Efficient WP_Query usage
- **Caching Ready**: Structure supports future caching implementation
- **Lazy Loading**: Only loads necessary data

## Browser Support

- Modern browsers with ES6+ support
- Fallback to form submission for older browsers
- Progressive enhancement approach

## Usage

### For Users

1. Navigate to the jobs archive page
2. Use any filter input to see live results
3. Combine multiple filters for precise results
4. Use the clear filters button to reset
5. Sort and paginate as needed

### For Developers

The system is extensible and can be easily modified:

```javascript
// Add custom filter
currentFilters.custom_filter = 'value';
triggerFilter();

// Listen for filter events
document.addEventListener('jobFilterComplete', function(e) {
    console.log('Filter completed:', e.detail);
});
```

## Troubleshooting

### Common Issues

1. **Ajax requests failing**
   - Check if `ajaxurl` is properly localized
   - Verify nonce is being sent correctly
   - Check browser console for errors

2. **Filters not working**
   - Ensure job posts have the correct meta fields
   - Verify taxonomy terms exist
   - Check query arguments in debug response

3. **Loading states not showing**
   - Verify CSS is loaded
   - Check for JavaScript errors
   - Ensure DOM elements exist

### Debug Mode

The Ajax response includes debug information when WP_DEBUG is enabled:

- Query arguments used
- Number of posts found
- Post count returned

## Future Enhancements

- [ ] Add caching layer for better performance
- [ ] Implement infinite scroll pagination
- [ ] Add filter presets/saved searches
- [ ] Export filtered results
- [ ] Advanced search with multiple criteria
- [ ] Filter analytics and reporting
