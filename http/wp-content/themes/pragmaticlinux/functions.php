<?php

/*
 * Call Javascript and CSS files
 */

function pragmaticlinux_script_enqueue() {
    $baseDir = get_template_directory_uri() . "/assets/";
    wp_enqueue_style('bootstrap-style', $baseDir . "css/bootstrap.min.css", array(), "1.0.0", "all");
    wp_enqueue_style('pragmaticlinux-style', $baseDir . "css/pragmaticlinux.css", array(), "1.0.0", "all");
    wp_enqueue_script('jquery-js', $baseDir . "js/jquery.1.12.4.min.js", array(), "1.0.0", true);
    wp_enqueue_script('bootstrap-js', $baseDir . "js/bootstrap.min.js", array(), "1.0.0", true);
}

add_action('wp_enqueue_scripts', 'pragmaticlinux_script_enqueue');

/**
 * Setup the content every time the theme is init
 */
function pragmaticlinux_themes() {
    add_theme_support("menus");
    register_nav_menu('primary', 'Menu Header');
}

add_action("init", "pragmaticlinux_themes");

/**
 * Create post features
 */
function features_post() {
    register_post_type('features', array(
        'labels' => array(
            'name' => __('Features'),
            'singular_name' => __('Features')
        ),
        'public' => true,
        'featured_image' => "thumbnail",
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt')
            )
    );
}

add_action('init', 'features_post');

/**
 * Create Partner Post Type
 */
function partner_post() {
    register_post_type('partner_post', array(
        'labels' => array(
            'name' => __('Partner'),
            'singular_name' => __('Partner')
        ),
        'public' => true,
        'featured_image' => "thumbnail",
        'supports' => array('title', 'thumbnail')
            )
    );
}

add_action('init', 'partner_post');

/**
 * Create Video Type
 */
function video_post() {
    register_post_type('video', array(
        'labels' => array(
            'name' => __('Video'),
            'singular_name' => __('Video')
        ),
        'public' => true,
        'featured_image' => "thumbnail",
        'supports' => array('title', 'thumbnail', 'editor')
            )
    );
}

add_action('init', 'video_post');
add_theme_support('post-thumbnails');
