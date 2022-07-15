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

//Override temp CORS issues until frontend and backend domains match
// add_action('init', 'handle_preflight');
// function handle_preflight() {
//     $origin = get_http_origin();
//     if ($origin === 'http://locahost:3000') {
//         header("Access-Control-Allow-Origin: locahost:3000");
//         header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
//         header("Access-Control-Allow-Credentials: true");
//         header('Access-Control-Allow-Headers: Origin, X-Requested-With, X-WP-Nonce, Content-Type, Accept, Authorization');
//         if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
//             status_header(200);
//             exit();
//         }
//     }
// }
// add_filter('rest_authentication_errors', 'rest_filter_incoming_connections');
// function rest_filter_incoming_connections($errors) {
//     $request_server = $_SERVER['REMOTE_ADDR'];
//     $origin = get_http_origin();
//     if ($origin !== 'http://locahost:3000') return new WP_Error('forbidden_access', $origin, array(
//         'status' => 403
//     ));
//     return $errors;
// }

function initCors( $value ) {
    $origin_url = '*';
  
    // Check if production environment or not
    if (ENVIRONMENT === 'production') {
      $origin_url = 'https://asparagusmagazine.com';
    }
  
    header( 'Access-Control-Allow-Origin: ' . $origin_url );
    header( 'Access-Control-Allow-Methods: GET' );
    header( 'Access-Control-Allow-Credentials: true' );
    return $value;
  }


// ... initCors function

add_action( 'rest_api_init', function() {

	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

	add_filter( 'rest_pre_serve_request', initCors);
}, 15 );