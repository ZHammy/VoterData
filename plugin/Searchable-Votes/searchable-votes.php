<?php
/**
 * Plugin Name: Searchable Votes
 * Description: A plugin to display a searchable and filterable table of bills, candidates, and votes, and upload voter data.
 * Version: 1.1
 * Author: Your Name
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Enqueue styles and scripts
function sv_enqueue_assets() {
    wp_enqueue_style('sv-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('sv-main-js', plugin_dir_url(__FILE__) . 'assets/js/main.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'sv_enqueue_assets');

// Shortcode to render the searchable table
function sv_render_searchable_table_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/searchable-table.php';
    return ob_get_clean();
}
add_shortcode('searchable_votes_table', 'sv_render_searchable_table_shortcode');

// Shortcode to render the voter data upload form
function sv_render_upload_voter_data_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/upload-voter-data.php';
    return ob_get_clean();
}
add_shortcode('upload_voter_data', 'sv_render_upload_voter_data_shortcode');