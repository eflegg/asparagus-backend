<?php
/**
 * Theme for the Postlight Headless WordPress Starter Kit.
 *
 * Read more about this project at:
 * https://postlight.com/trackchanges/introducing-postlights-wordpress-react-starter-kit
 *
 * @package  Postlight_Headless_WP
 */

// Frontend origin.
require_once 'inc/frontend-origin.php';

// ACF commands.
require_once 'inc/class-acf-commands.php';

// Logging functions.
require_once 'inc/log.php';

// CORS handling.
require_once 'inc/cors.php';

// Admin modifications.
require_once 'inc/admin.php';

// Add Menus.
require_once 'inc/menus.php';

// Add Headless Settings area.
require_once 'inc/acf-options.php';

// Add GraphQL resolvers.
require_once 'inc/graphql/resolvers.php';

// Enable featured image
add_theme_support( 'post-thumbnails' );

/**
 * Enable REST API support for the ad post type
 */
add_filter( 'advanced-ads-post-type-params', function( $post_type_params ) {

    $post_type_params[ 'show_in_rest' ] = true;

    return $post_type_params;
} );


//Enable filtering ACF NOT WORKING

add_filter( 'rest_contributor_query', function( $args ) {

    $filters = [
         'relation' => 'AND',
    ];

    foreach($_GET as $key => $value){
         $filter = [
              'key' => $key,
              'value' => $value,
              'compare' => '='
         ];
         array_push($filters, $filter);
    }

    $args['meta_query'] = $filters;

    return $args;
} );


add_filter( 'rest_query_vars', function ( $valid_vars ) {
    return array_merge( $valid_vars, array( 'writer', 'meta_query' ) );
} );
add_filter( 'rest_contributor_query', function( $args, $request ) {
    $writer   = $request->get_param( 'writer' );

    if ( ! empty( $highlight ) ) {
        $args['meta_query'] = array(
            array(
                'key'     => 'writer',
                'value'   => $writer,
                'compare' => '=',
            )
        );      
    }

    return $args;
}, 10, 2 );




/**
 * Modify internal link URLs to point to the decoupled frontend app.
 *
 * @param string $content Post content.
 *
 * @return string Post content, with internal link URLs replaced.
 */
function replace_headless_content_link_urls(string $content): string
{
    if (!is_graphql_request() && !defined('REST_REQUEST')) {
        return $content;
    }

    // TODO: Get this value from an environment variable or the database.
    $frontend_app_url = 'http://localhost:3000';
    $site_url         = site_url();

    return str_replace('href="' . $site_url, 'data-internal-link="true" href="' . $frontend_app_url, $content);
}
add_filter('the_content', 'replace_headless_content_link_urls');

