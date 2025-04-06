# Custom WordPress Theme

## Overview
This is a custom WordPress theme designed to facilitate the upload of CSV files containing meeting member votes for the year 2025. The theme includes a user-friendly interface for uploading CSV files, which are then processed and stored in a dedicated database table.

## Features
- Custom upload form for CSV files.
- Data validation and parsing of CSV content.
- Integration with the WordPress database to store uploaded data.
- Responsive design with custom styles.

## File Structure
```
custom-wordpress-theme
├── assets
│   ├── css
│   │   └── style.css
│   ├── js
│       └── upload-csv.js
├── includes
│   ├── csv-handler.php
│   └── database-functions.php
├── templates
│   └── upload-form.php
├── functions.php
├── index.php
├── style.css
└── README.md
```

## Installation
1. Download the theme files.
2. Upload the `custom-wordpress-theme` folder to the `/wp-content/themes/` directory of your WordPress installation.
3. Activate the theme through the 'Themes' menu in WordPress.

## Usage
- Navigate to the upload form provided by the theme.
- Use the form to select and upload your CSV file.
- The uploaded data will be processed and stored in the `wp_MeetingMemberVotes2025` database table.

## Requirements
- WordPress 5.0 or higher.
- PHP 7.0 or higher.

## Support
For any issues or feature requests, please open an issue in the repository or contact the theme author.