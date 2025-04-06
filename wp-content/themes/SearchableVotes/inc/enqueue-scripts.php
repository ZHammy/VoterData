<?php
function my_theme_enqueue_scripts() {
    wp_enqueue_style('my-theme-style', get_template_directory_uri() . '/assets/css/style.css');
    wp_enqueue_script('my-theme-main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');
?>