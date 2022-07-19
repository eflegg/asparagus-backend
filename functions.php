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
<?php

	function QikkerAcfPostObjectFix()
	{

		function qikker_fix_acf_post_ojects($value, $original_post_id, $field)
		{

			if ($field[ 'return_format' ] !== 'object') {

				return $value;

			}

			remove_filter('acf/format_value/type=relationship', 'qikker_fix_acf_post_ojects', 20);
			remove_filter('acf/format_value/type=post_object', 'qikker_fix_acf_post_ojects', 20);

			if (is_array($value)) {

				foreach ($value as $post) {

					$formatted[] = convert_post_object_to_rest_response($post, $original_post_id);

				}

			} else {

				$formatted = convert_post_object_to_rest_response($value, $original_post_id);

			}

			add_filter('acf/format_value/type=relationship', 'qikker_fix_acf_post_ojects', 20, 3);
			add_filter('acf/format_value/type=post_object', 'qikker_fix_acf_post_ojects', 20, 3);

			return $formatted;

		}

		function convert_post_object_to_rest_response($post, $original_post_id)
		{

			global $wp_rest_server;
			$post_type = get_post_type_object($post->post_type);

			$request = WP_REST_Request::from_url(rest_url(sprintf('wp/v2/%s/%d', $post_type->rest_base, $post->ID)));
			$request = rest_do_request($request);
			$data = $wp_rest_server->response_to_data($request, isset($_GET[ '_embed' ]));

			// For the line below, see https://core.trac.wordpress.org/ticket/43502#ticket
			$GLOBALS[ 'post' ] = $original_post_id;

			return $data;

		}

		add_filter('acf/format_value/type=relationship', 'qikker_fix_acf_post_ojects', 20, 3);
		add_filter('acf/format_value/type=post_object', 'qikker_fix_acf_post_ojects', 20, 3);

	}

	add_action('rest_api_init', 'QikkerAcfPostObjectFix');