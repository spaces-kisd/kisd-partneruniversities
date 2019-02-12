<?php
/**
 * Grab latest post title by an author!
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 * 
 * @todo https://support.advancedcustomfields.com/forums/topic/google-map-city-name/
 */


function load_scripts_styles() {
	wp_enqueue_style( 'mapbox-gl-css', 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.52.0/mapbox-gl.css', false, null );
}
add_action( 'wp_enqueue_scripts', 'load_scripts_styles', 100 );

add_theme_support( 'post-thumbnails' );


add_filter( 'acf/fields/google_map/api', 'my_acf_google_map_api' );
function my_acf_google_map_api( $api ) {

	$api['key'] = 'AIzaSyDT7T5y4YL-ilXptDnmn0LWkJ3tDN9W0uo';
	return $api;
}

/**
 * @todo: better caching for this function!
 *
 * @param [type] $data
 * @return void
 */
function my_awesome_func( $data ) {
	// $data['id']; <- thats how we fetch stuffs...
	$post_type_name = $data['type'];
	$args           = array(
		'post_type'   => array( $post_type_name ),
		'post_status' => array( 'publish' ),
		'nopaging'    => true,
		'orderby'     => 'meta_value',
		'meta_type'   => 'NUMERIC', // because prio is a number.
		'meta_key'    => 'priority',
		'order'       => 'DESC',
		// 'orderby'                => 'menu_order',
	);
	$query    = new WP_Query( $args );
	$features = [];

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			global $post;
			$query->the_post();
			$location = get_field( 'location' );
			$priority = get_field( 'priority' );
			$priority = $priority ? $priority : 5;

			if ( ! empty( $location ) ) {
				// for ( $i = 0; $i < 20; $i++ ) {
					array_push(
						$features,
						[
							'type'       => 'Feature',
							'properties' => [
								'id'         => get_the_ID(),
								'title'         => get_the_title(),
								'priority'      => $priority,
								'excerpt'       => get_the_excerpt(),
								'thumbnail'       => get_the_post_thumbnail_url(),
								'link_relative' => wp_make_link_relative( get_permalink() ),
							],
							'geometry'   => [
								'type'        => 'Point',
								'coordinates' => [ $location['lng'], $location['lat'] ],
							],
						]
					);
				// }
			}
		}
	}

	return (
		[
			'type'     => 'FeatureCollection',
			'features' => $features,
		]
	);
	// Restore original Post Data
	wp_reset_postdata();
}

// http://wp.local/wp-json/map/v1/features/solution
add_action(
	'rest_api_init',
	function() {
		error_log( 'fired!' );
		register_rest_route(
			'map/v1',
			'/features/(?P<type>.+)',
			// '/features',
			array(
				'methods'  => 'GET',
				'callback' => 'my_awesome_func',
			)
		);
	}
);


add_action( 'rest_api_init', 'slug_register_starship' );
function slug_register_starship() {
	register_rest_field(
		'post',
		'link_relative',
		array(
			'get_callback'    => 'slug_get_link_relative',
			'update_callback' => null,
			'schema'          => null,
		)
	);
}

/**
 * Get the value of the "link_relative" field
 *
 * @param array           $object Details of current post.
 * @param string          $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function slug_get_link_relative( $object, $field_name, $request ) {
	error_log( print_r( $object, true ) );
	return wp_make_link_relative( $object['link'] );
}

