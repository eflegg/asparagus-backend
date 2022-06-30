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



/* Register function to run at rest_api_init hook */
add_action( 'rest_api_init', function () {
    /* Setup siteurl/wp-json/menus/v2/header */
	register_rest_route( 'menus/v2', '/header', array(
		'methods' => 'GET',
		'callback' => 'header_menu', 
		'args' => array(
			'id' => array(
				'validate_callback' => function($param, $request, $key) {
					return is_numeric( $param );
				}
			),
		)
	) );
} );


/* Register function to run at rest_api_init hook */
add_action( 'rest_api_init', function () {
    /* Setup siteurl/wp-json/menus/v2/footer */
	register_rest_route( 'menus/v2', '/footer', array(
		'methods' => 'GET',
		'callback' => 'footer_menu', 
		'args' => array(
			'id' => array(
				'validate_callback' => function($param, $request, $key) {
					return is_numeric( $param );
				}
			),
		)
	) );
} );

/* Register function to run at rest_api_init hook */
add_action( 'rest_api_init', function () {
    /* Setup siteurl/wp-json/menus/v2/connect */
	register_rest_route( 'menus/v2', '/connect', array(
		'methods' => 'GET',
		'callback' => 'connect_menu', 
		'args' => array(
			'id' => array(
				'validate_callback' => function($param, $request, $key) {
					return is_numeric( $param );
				}
			),
		)
	) );
} );


function header_menu( $data ) {
    /* Verify that menu locations are available in your WordPress site */
    if (($locations = get_nav_menu_locations()) && isset($locations[ 'header-menu' ])) {

    /* Retrieve the menu in location header-menu */
    $menu = wp_get_nav_menu_object($locations['header-menu']);

    /* Create an empty array to store our JSON */
    $menuItems = array();

    /* If the menu isn't empty, start process of building an array, otherwise return a 404 error */
    if (!empty($menu)) {

        /* Assign array of navigation items to $menu_items variable */
        $menu_items = wp_get_nav_menu_items($menu->term_id);

            /* if $menu_items isn't empty */
            if ($menu_items) {

                /* for each menu item, verify the menu item has no parent and then push the menu item to the $menuItems array */
                foreach ($menu_items as $key => $menu_item) {
                    if ($menu_item->menu_item_parent == 0) {
                        array_push(
                            $menuItems, array(
                                'title' => $menu_item->title,
                                'url' => $menu_item->url,
                                'slug' => $menu_item->slug,
                            )
                        );
                    }
                }
            }
        }
    } else {
        return new WP_Error(
            'no_menus',
            'Could not find any menus',
            array(
                'status' => 404
            )
        );
    }

    /* Return array of list items with title and url properties */
	return $menuItems;
}


function footer_menu( $data ) {
    /* Verify that menu locations are available in your WordPress site */
    if (($locations = get_nav_menu_locations()) && isset($locations[ 'footer-menu' ])) {

    /* Retrieve the menu in location header-menu */
    $footerMenu = wp_get_nav_menu_object($locations['footer-menu']);

    /* Create an empty array to store our JSON */
    $footerMenuItems = array();

    /* If the menu isn't empty, start process of building an array, otherwise return a 404 error */
    if (!empty($footerMenu)) {

        /* Assign array of navigation items to $menu_items variable */
        $footer_menu_items = wp_get_nav_menu_items($footerMenu->term_id);

            /* if $menu_items isn't empty */
            if ($footer_menu_items) {

                /* for each menu item, verify the menu item has no parent and then push the menu item to the $menuItems array */
                foreach ($footer_menu_items as $key => $footer_menu_item) {
                    if ($footer_menu_item->menu_item_parent == 0) {
                        array_push(
                            $footerMenuItems, array(
                                'title' => $footer_menu_item->title,
                                'url' => $footer_menu_item->url
                            )
                        );
                    }
                }
            }
        }
    } else {
        return new WP_Error(
            'no_menus',
            'Could not find any menus',
            array(
                'status' => 404
            )
        );
    }

    /* Return array of list items with title and url properties */
	return $footerMenuItems;
}



function connect_menu( $data ) {
    /* Verify that menu locations are available in your WordPress site */
    if (($locations = get_nav_menu_locations()) && isset($locations[ 'connect-menu' ])) {

    /* Retrieve the menu in location header-menu */
    $connectMenu = wp_get_nav_menu_object($locations['connect-menu']);

    /* Create an empty array to store our JSON */
    $connectMenuItems = array();

    /* If the menu isn't empty, start process of building an array, otherwise return a 404 error */
    if (!empty($connectMenu)) {

        /* Assign array of navigation items to $menu_items variable */
        $connect_menu_items = wp_get_nav_menu_items($connectMenu->term_id);

            /* if $menu_items isn't empty */
            if ($connect_menu_items) {

                /* for each menu item, verify the menu item has no parent and then push the menu item to the $menuItems array */
                foreach ($connect_menu_items as $key => $connect_menu_item) {
                    if ($connect_menu_item->menu_item_parent == 0) {
                        array_push(
                            $connectMenuItems, array(
                                'title' => $connect_menu_item->title,
                                'url' => $connect_menu_item->url
                            )
                        );
                    }
                }
            }
        }
    } else {
        return new WP_Error(
            'no_menus',
            'Could not find any menus',
            array(
                'status' => 404
            )
        );
    }

    /* Return array of list items with title and url properties */
	return $connectMenuItems;
}