<?php
function my_theme_setup() {
    // Add theme support features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    // Register menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-wordpress-theme'),
    ));
}

add_action('after_setup_theme', 'my_theme_setup');

function enqueue_select2_assets() {
    wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
    wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_select2_assets');

// Include necessary files
require get_template_directory() . '/inc/enqueue-scripts.php';
?>