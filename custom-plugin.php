<?php
/**
 * Plugin Name: Custom REST Post Creator
 * Plugin URI: https://learning.devbuggs.com/
 * Description: Simple custom plugin that lets you add posts via REST API (POST method).
 * Version: 1.0
 * Author: Hafi
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit; //  Prevent direct access

// ✅ Register custom REST route
add_action('rest_api_init', function () {
    register_rest_route('custom/v2', '/addpost', [
        'methods'  => 'POST',
        'callback' => 'custom_create_post',
        'permission_callback' => '__return_true' // (open for testing, secure later)
    ]);
});

// ✅ Function to handle POST request
function custom_create_post( $request ) {

    // Get JSON data
    $data = $request->get_json_params();

    $title   = sanitize_text_field( $data['title'] ?? 'Untitled' );
    $content = sanitize_textarea_field( $data['content'] ?? '' );
    $status  = sanitize_text_field( $data['status'] ?? 'draft' );

    // Create post
    $post_id = wp_insert_post([
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => $status,
        'post_author'  => 1, // admin user
    ]);

    if ( is_wp_error( $post_id ) ) {
        return new WP_Error( 'create_failed', 'Post creation failed', [ 'status' => 500 ] );
    }

    return [
        'success' => true,
        'post_id' => $post_id,
        'message' => 'Post created successfully!',
    ];
}

/**
 * ✅ Plugin Activation Hook
 */
function custom_rest_post_creator_activate() {
    update_option( 'custom_rest_post_creator_activated', time() ); //savong activation time +refresh permalink\
    flush_rewrite_rules();

// CREATING A PAGE WHEN ACTIVATING PLUGIN
 if (!get_page_by_path('boiler-api')) {
        wp_insert_post([
            'post_title'   => 'Boiler Api',
            'post_name'    => 'boiler-api',
            'post_content' => '[get_boiler_api]',
            'post_status'  => 'publish',
            'post_type'    => 'page'
        ]);
    }


// CREATING SINGLE BOILER PAGE ON ACTIVATION
 if (!get_page_by_path('single-boiler')) {
        wp_insert_post([
            'post_title'   => 'Single Boiler',
            'post_name'    => 'single-boiler',
            'post_content' => '[boiler_detail]',
            'post_status'  => 'publish',
            'post_type'    => 'page'
        ]);
    }


}
register_activation_hook( __FILE__, 'custom_rest_post_creator_activate' );

/**
 * ✅ Plugin Deactivation Hook
 */
function custom_rest_post_creator_deactivate() {
    // Example: cleanup options or transient data
    delete_option( 'custom_rest_post_creator_activated' );

    // Flush rewrite rules to clean up routes
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'custom_rest_post_creator_deactivate' );


// JQUERY
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('jquery');
    wp_add_inline_script('jquery', "console.log('jQuery loaded:', typeof jQuery !== 'undefined');");
});



//CUSTOM CSS
add_action('wp_enqueue_scripts', 'my_custom_style_enqueue');
function my_custom_style_enqueue() {
    wp_enqueue_style(
        'my-custom-style', // Handle
        plugin_dir_url(__FILE__) . 'style.css' // Path to CSS file
    );
}



// CUSTOM REST API  FILE ATTACH
require_once plugin_dir_path(__FILE__) . 'custom-user-post-rest.php';

 
// BOILERS API FILE
require_once plugin_dir_path(__FILE__) . 'get_boiler_api.php';
// SEARCH BOILER API FILE
require_once plugin_dir_path(__FILE__) . 'search_boiler_api.php';

require_once plugin_dir_path(__FILE__) . 'rough.php';
require_once plugin_dir_path(__FILE__) . 'single-boiler.php';


 
// ADDING JS FILE FOR SEARCH BOILER API
add_action('wp_enqueue_scripts', function () {

  wp_enqueue_script(
    'search-boiler-api',
    plugin_dir_url(__FILE__) . 'search_boiler_api.js',
    ['jquery'],
    null,
    true
  );

  wp_localize_script(
    'search-boiler-api',
    'ajax_obj',
    ['ajaxurl' => admin_url('admin-ajax.php')]
  );

});
                
