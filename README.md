# Job Notices

=== Job Notices ===
Contributors: ocenchris
Donate link: [https://wp-fundi.com/](https://wp-fundi.com/)
Tags: jobs
Requires at least: 4.7
Tested up to: 5.4
Stable tag: 4.3
Requires PHP: 7.0
License: GPLv2 or later
License URI: [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)
A plugin to turn your website into a job board.

## Features

This is a list of all the features intended for the plugin. Some are not yet created.

- Use maps to plot jobs location and impelement geo-location searc
- User managment (Employer and employee). Allow employers to list jobs in the frontend and employees to apply for jobs.
- Application status tracking
- Social and email login
- Apply with Linkedin or indeed.
- Email alerts
- Resume Builder. (can be a separate plugin)

## Road map for v1

- Featured jobs on top
- Add schema to jobs.

## Setup

- Run `npm install` to install all the sass + dev dependencies.
- Run `composer install` to get the PHP setup.

## Changelog

### 0.9.0

- Bug fixes.
- Added missing schema.org fields.
- Added a taxonomy filter if we're on a taxonomy archive.
- Added application links for binds and scholarships.
- Added featured tag for bids and scholarships.
- Added radio input for location type.

## 0.8.9

- Added a shortcodes to render custom taxonomies in the sidebar.

### 0.8.8

- Added Taxonomies to the top sidebar.

### 0.8.7

- Fixed bug preventing display on hero search block
- Fixed mobile view for archive page

### 0.8.6

- Removed URL update when ajax is loading.
- Added bids and scholarships. Now live search and layout working independently for different post types.

### 0.8.5

-Skipped

### 0.8.4

- Ran WordPress plugin-check to ensure compliace to WP Plugin Standards.

### 0.8.3

- Added redirect jobs to external link.
- Added cron job to hide expired jobs from archive. In the single job template, expired jobs are marked.
- Added auto default expiry date for jobs.

### 0.8.2

- Added block controls on the render-jobs block; numberOfJobs and showPagination.
- Added settings to toggle sidebar on and off.
- Added category links on the archive sidebar.

### 0.8.1

- Design cleanup for related jobs section
- Add custom design to job_category taxonomy archive

### 0.8.0

- Added category section to the sidebar
- Added JobPosting schema for job posts

### 0.7.0

- Refactored to use namespaced CSS classes to prevent conflicts with WordPress themes and other plugins.
- visual consistency improvements made across all job-related components in the Job Notices plugin. All components now share a unified design system with consistent spacing, typography, colors, and layout patterns.

### 0.6.4

- Added a live Ajax-based filtering system that allows users to filter jobs in real-time without page reloads.

### 0.6.2

- Added Render jobs block. This is a block that uses the same design as the archive.
- Added custom fields for the jobs.

Note: Thinking of doing a way with the archive template. The idea is to use a block that can be used to display jobs, with settings to manpulate the jobs query to users preferences.

### 0.6.1

- Fixed bug with autoloading.

### 0.6.0

- Added hero-search block.

### 0.5.0

- Added Jobs Counter shortcode.

### 0.4.0

- Added carousel block. Using swiper.js library

### 0.3.0

- Added user roles (employer and Job seeker) REG MODULE NEEDED
- Added employer meta data.

### 0.2.0

- Improved design for archive.
- Added design for single job page.

### 0.1.0

- Added basic jobs archive. Completed MVP design

### 0.0.1

- Initial Release.
- Created `JobsPostTypeTrait` to modularize CPT registration
- Included taxonomy registration within the trait
