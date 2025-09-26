# Job Notices

Job Notices is a WordPress plugin designed to turn your website into a fully-featured job board. It supports job listings, employer and employee management, application tracking, and more. The plugin uses semantic HTML, namespaced CSS, and includes features for filtering, custom fields, and integration with external services.

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

## Setup

- Run `npm install` to install all the sass + dev dependencies.
- Run `composer install` to get the PHP setup.

## Changelog

### 1.1.3

- Fixed pagination bug

### 1.1.2

-CSS Fixes. Sidebar taxonomies set to persist on the sidebar after ad injection.

### 1.1.1

-CSS Fixes.

### 1.1.0

- Bug fix: Expiry dates for bids and scholarships post types.
- Bug fix: Added exit  tp template_redirect for arhives. Solves output of archives under the footer.
- Bug fix: Added support for old clipboards.
- Added Whatsapp share button
- Clean deprecated/unused code.

### 1.0.1

-Bug fix: Removed inrecognised property in schema for job posting.

### 1.0.0

Initial live release with;

- Semantic HTML.
- Schema.org fields and JobPosting schema.
- Design consistency and mobile responsiveness.
- Ajax-based live filtering, block controls, shortcodes and settings for sidebar toggling.
- Cron jobs for expiring and deleting jobs, and auto default expiry dates.
- Archive and single page design templates.
- Modularized CPT and taxonomy registration.

## Roadmap

### Planned features

- Map integration for job locations and geo-location search.
- Advanced user management for employers and employees.
- Frontend job listing and application submission.
- Application status tracking.
- Social and email login options.
- "Apply with LinkedIn" or "Apply with Indeed" functionality.
- Email alerts for job seekers.
- Resume builder (potentially as a separate plugin).
