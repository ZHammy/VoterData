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

// Include necessary files
require get_template_directory() . '/inc/enqueue-scripts.php';
?>