<?php
/**
 * Plugin Name: Gutenberg Page Templates
 * Author: S.Schat
 * Description: (NOT READY FOR PRODUCTION) Just to get the 'idea' tested of having page-templates. Setup your templates in the CPT, and with any new page select your template and get going.
 * Version: 0.0.1
 */
/**
 * Based on ideas and knowledge of the following sources:
 *
 * sources :
 * https://gist.github.com/jasonbahl/2af7959e5d10c7eb6781fb86c097786e
 * https://rudrastyh.com/gutenberg/get-posts-in-dynamic-select-control.html#query_parameters
 *
 */
// Exit if accessed directly.
use function sschat\_get_plugin_url;

defined('ABSPATH') || exit;

function register_cpts()
{
    $labels = array(
        'name' => _x('GB Templates', 'post type general name', 'sschat'),
        'singular_name' => _x('GB Template', 'post type singular name', 'sschat'),
        'menu_name' => _x('GB Templates', 'admin menu', 'sschat'),
        'name_admin_bar' => _x('GB Templates', 'add new on admin bar', 'sschat'),
        'add_new' => _x('Add New', 'book', 'sschat'),
        'add_new_item' => __('Add New Template', 'sschat'),
        'new_item' => __('New Template', 'sschat'),
        'edit_item' => __('Edit Template', 'sschat'),
        'view_item' => __('View Template', 'sschat'),
        'all_items' => __('All Templates', 'sschat'),
        'search_items' => __('Search Templates', 'sschat'),
        'parent_item_colon' => __('Parent Templates:', 'sschat'),
        'not_found' => __('No Templates found.', 'sschat'),
        'not_found_in_trash' => __('No Templates found in Trash.', 'sschat')
    );


    $args = array(
        'labels' => $labels,
        'description' => __('Create Page templates with Gutenberg.', 'sschat'),
        'public' => true,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'gb-template'),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 10,
        'supports' => array('title', 'editor'),
        'show_in_rest' => true
    );
    register_post_type('gb-template', $args);
}

add_action('init', 'register_cpts');


// you have to use it within the "init" hook
add_action('init', function () {

    register_meta('post', 'sschat_page_layout', array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
    ));

});


add_action('enqueue_block_editor_assets', function () {
    $screen = get_current_screen();
//    if ($screen->post_type !== 'page') return; // enabled for Pages

    wp_enqueue_script(
        'sschat-sidebar',
        plugin_dir_url(__FILE__) . '/sidebar.js',
        array('wp-i18n', 'wp-blocks', 'wp-edit-post', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-plugins', 'wp-edit-post'),
        filemtime(dirname(__FILE__) . '/sidebar.js')
    );
});

/**
 * this function can lock the page down
 * BUT... it will complain about the template not matching the template we have to define here
 */
function myplugin_register_template() {
    $post_type_object = get_post_type_object( 'page' );
    $post_type_object->template = array(
        array( 'core/paragraph', array(
            'placeholder' => 'Add Description...',
        ) ),
    );
    $post_type_object->template_lock = 'all';
}
add_action( 'init', 'myplugin_register_template' );