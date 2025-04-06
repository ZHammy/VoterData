# My WordPress Theme

## Description
This is a custom WordPress theme designed to display a searchable and filterable table using data from the `wp_Bills`, `wp_Candidates`, and `wp_Votes` tables. The theme includes various components such as styles, scripts, and template files to enhance the user experience.

## Installation
1. Download the theme files.
2. Upload the `my-wordpress-theme` folder to the `/wp-content/themes/` directory of your WordPress installation.
3. Go to the WordPress admin dashboard.
4. Navigate to Appearance > Themes.
5. Activate the "My WordPress Theme".

## Usage
- The theme includes a searchable and filterable table that can be accessed through the designated template.
- Ensure that the necessary data is present in the `wp_Bills`, `wp_Candidates`, and `wp_Votes` tables for the table to display correctly.

## Files Overview
- **assets/css/style.css**: Main stylesheet for the theme.
- **assets/js/main.js**: JavaScript for interactivity in the theme.
- **inc/table-functions.php**: Functions for fetching and displaying data from the database.
- **inc/enqueue-scripts.php**: Enqueues CSS and JavaScript files.
- **templates/searchable-table.php**: HTML structure and PHP code for the searchable table.
- **functions.php**: Main functions file for theme support and includes.
- **index.php**: Main template file for displaying content.
- **style.css**: Theme header information and additional styles.

## Support
For any issues or questions, please refer to the WordPress documentation or seek help from the WordPress community.