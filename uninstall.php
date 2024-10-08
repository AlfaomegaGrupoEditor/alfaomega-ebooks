<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://github.com/livan2r
 * @since      1.0.0
 *
 * @package    Alfaomega_Ebooks
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'alfaomega_ebooks_slider_options' );

$eBookPosts = get_posts(
    array(
        'post_type' => ALFAOMEGA_EBOOKS_POST_TYPE,
        'number_posts'  => -1,
        'post_status'   => 'any'
    )
);

foreach( $eBookPosts as $post ){
    wp_delete_post( $post->ID, true );
}

$accessPosts = get_posts(
    array(
        'post_type' => ALFAOMEGA_EBOOKS_ACCESS_POST_TYPE,
        'number_posts'  => -1,
        'post_status'   => 'any'
    )
);

foreach( $accessPosts as $post ){
    wp_delete_post( $post->ID, true );
}
