# job-notices
A plugin for WordPress with a custom post type for jobs management.

## Features
Schema

## Setup
- Run `npm install` to install all the sass + dev dependencies.
- Run `composer install` to get the PHP setup.

## Changelog

### 0.6.2
- Added Render jobs block. This is a block that uses the same design as the archive. 

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

TODO: Add settings page for dynamic settings.