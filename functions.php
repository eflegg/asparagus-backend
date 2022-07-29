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
  

    // TODO: Get this value from an environment variable or the database.
    $frontend_app_url = 'http://localhost:3000';
    $site_url         = site_url();

    return str_replace('href="' . $site_url, 'data-internal-link="true" href="' . $frontend_app_url, $content);
}
add_filter('the_content', 'replace_headless_content_link_urls');



//add all data from custom post types as relation fields



// function get_fields_recursive( $item ) {
//     if ( is_object( $item ) ) {
//         $item->acf = array();
//         if ( $fields = get_fields( $item ) ) {
//             $item->acf = $fields;                   
//             array_walk_recursive( $item->acf, 'get_fields_recursive' );
//         }
//     }
// }

// $post_types = array_merge(get_post_types(), cptui_get_post_type_slugs());
// foreach ($post_types as $type) {
// 	add_filter( 'acf/rest_api/' . $type . '/get_fields', function( $data ) {
//         if ( ! empty( $data ) ) {
//            array_walk_recursive( $data, 'get_fields_recursive' );
//         }
      
//         return $data;
//       } );
// }



// $postTypes = array_merge(get_post_types(), cptui_get_post_type_slugs());

// add_filter( 'acf/rest_api/contributors/get_fields', function( $data, $request, $response ) {
//     if ( $response instanceof WP_REST_Response ) {
//         $data = $response->get_data();
//     }

//     array_walk_recursive($data, 'deepIncludeACFFields', array('contributor'));

//     return $data;
// }, 1000, 3);

// function deepIncludeACFFields(&$item, $key, $postTypes) {

//     if( isset($item->post_type) && in_array($item->post_type, $postTypes) ) {
//         $item = get_fields($item->ID);
//     }
//     // Add this portion here to look up arrays within the array
//     if(is_array($item)) {
//         array_walk_recursive($item, 'deepIncludeACFFields', $postTypes);
//     }
// }






add_filter( 'acf/rest_api/recursive/types', function( $types ) {
	if ( isset( $types['post'] ) ) {
		unset( $types['post'] );
	}

	return $types;
} );



//attempt to get featured image of relationship field

