<?php
/**
 * @done: make sure acf is installed
 * @todo: rename the "solution" post type to "map".
 * @todo: use the acf-string to customize the map fields in the backend.
 * @see: https://www.advancedcustomfields.com/resources/register-fields-via-php/
 *
 * @todo think about the front-page:
 * - we currently use the url /home which brings some disadvantages...
 *
 * @todo improve structure, move map-post type to class?
 *
 * @todo looks we still fetch things twice...
 * @see cats: http://wp.local/wp-json/wp/v2/categories?sort=name&hide_empty=true&per_page=10
 *
 * @todo remove acf frontend dependecy!
 */

require_once 'classes/class-handle-acf.php';
require_once 'classes/class-handle-mapbox.php';
require_once 'classes/class-customize-theme.php';
require_once 'classes/class-manage-local-storage.php';
require_once 'classes/class-add-performance.php';

$my_acf         = new HandleAcf();
$my_mapbox      = new HandleMapbox();
$my_theme       = new CustomizeTheme();
$my_storage     = new MangeLocalStorage();
$my_performance = new AddPerformance();


function cookie_update_redirect() {
	// we are not interested in ajax or rest requests.
	if ( wp_doing_ajax() || is_rest() || is_admin() ) {
		return;
	}

	$cookie_name = 'intro';

	if (
		'/' == $_SERVER['REQUEST_URI'] // you are visiting the main site.
		&& ! isset( $_COOKIE[ $cookie_name ] ) // you havent visited the main site before.
	 ) {
		setcookie( $cookie_name, time(), time() + 1209600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true );
		$pageslug = 'home';
		wp_redirect( get_site_url() . '/' . $pageslug );
		exit;
	}
}
add_action( 'init', 'cookie_update_redirect' );

/**
 * Checks if the current request is a WP REST API request.
 *
 * Case #1: After WP_REST_Request initialisation
 * Case #2: Support "plain" permalink settings
 * Case #3: URL Path begins with wp-json/ (your REST prefix)
 *          Also supports WP installations in subfolders
 *
 * @returns boolean
 * @author matzeeable
 */
function is_rest() {
	$prefix = rest_get_url_prefix();
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST // (#1)
		|| isset( $_GET['rest_route'] ) // (#2)
			&& strpos( trim( $_GET['rest_route'], '\\/' ), $prefix, 0 ) === 0 ) {
		return true;
	}

	// (#3)
	$rest_url    = wp_parse_url( site_url( $prefix ) );
	$current_url = wp_parse_url( add_query_arg( array() ) );
	return strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
}

function load_scripts_styles() {

	wp_enqueue_script( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.min.js', array(), '4.5.0', true );
	// wp_add_inline.
	wp_enqueue_style( 'swiper-css', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/css/swiper.min.css', false, null );

	if ( ! is_admin() ) {
		wp_deregister_script( 'jquery' );
	}
}
add_action( 'wp_enqueue_scripts', 'load_scripts_styles', 100 );


$feature_transient_name = 'feature_transient';

add_action( 'save_post_solution', 'delete_solution_transient' );
add_action( 'update_post_solution', 'delete_solution_transient' );
add_action( 'delete_post_solution', 'delete_solution_transient' );

function delete_solution_transient() {
	global $feature_transient_name;
	error_log( 'del trans' );
	delete_site_transient( $feature_transient_name );
}

/**
 * @todo: we can probably think about a better way to posts and features (<-rename them!), so we don't fetch things so redundant.
 *
 * @param [type] $data
 * @return void
 */
function feature_collection( $data ) {
	global $feature_transient_name;

	$feature_collection = get_site_transient( $feature_transient_name );

	if ( false !== $feature_collection ) {
		// error_log( "return from transient");
		return $feature_collection;
	} else {
		// error_log( "not return from transient");
	}

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

			if ( ! empty( $location ) ) {
				array_push(
					$features,
					[
						'type'       => 'Feature',
						'properties' => [
							'post_id'             => get_the_ID(),
							'slug'                => $post->post_name,
							'title'               => get_the_title(),
							'subtitle'            => get_field( 'subtitle' ),
							'priority'            => intval( $priority ? $priority : 5 ),
							'excerpt'             => get_the_excerpt(),
							'thumbnail'           => get_the_post_thumbnail_url( null, 'medium' ),
							'location_name'       => get_field( 'location_name' ),
							'since'               => get_field( 'since' ),
							'number_of_employees' => get_field( 'number_of_employees' ),
							'link_relative'       => wp_make_link_relative( get_permalink() ),
						],
						'geometry'   => [
							'type'        => 'Point',
							'coordinates' => [ $location['lng'], $location['lat'] ],
						],
					]
				);

			}
		}
	}

	$feature_collection =
	[
		'type'     => 'FeatureCollection',
		'features' => $features,
	];
	$bool_response      = set_site_transient( $feature_transient_name, $feature_collection, 86400 );
	// Restore original Post Data.
	wp_reset_postdata();
	return $feature_collection;
}


function menu_callback( $data ) {
	wp_nav_menu( array( 'theme_location' => $data['type'] ) );
	die();
}

function get_subtitle( $object ) {
	return get_field( 'subtitle', $object['id'] );
}

function get_priority( $object ) {
	return (int) get_field( 'priority', $object['id'] );
}

function get_feature( $object ) {
	return array(
		'thumbnail' => get_the_post_thumbnail_url( $object['id'], 'thumbnail' ),
		'large'     => get_the_post_thumbnail_url( $object['id'], 'large' ),
	);
}

function get_edit_url( $object ) {
	return get_edit_post_link( $object['id'] );
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
	return wp_make_link_relative( $object['link'] );
}


add_action( 'rest_api_init', 'register_routes' );
function register_routes() {
	register_rest_route(
		'map/v1',
		'/frontpage/',
		[
			'methods'  => 'GET',
			'callback' => 'get_frontpage',
		]
	);

	// http://wp.local/wp-json/map/v1/features/solution
	register_rest_route(
		'map/v1',
		'/features/(?P<type>.+)',
		// '/features',
		array(
			'methods'  => 'GET',
			'callback' => 'feature_collection',
		)
	);

	// http://wp.local/wp-json/map/v1/menus/footer
	register_rest_route(
		'map/v1',
		'/menus/(?P<type>.+)',
		array(
			'methods'  => 'GET',
			'callback' => 'menu_callback',
		)
	);

	register_rest_field(
		[ 'post', 'solution', 'page' ],
		'link_relative',
		array(
			'get_callback'    => 'slug_get_link_relative',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	register_rest_field(
		[ 'post', 'solution', 'page' ],
		'edit_url',
		array(
			'get_callback'    => 'get_edit_url',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	register_rest_field(
		[ 'post', 'solution', 'page' ],
		'feature',
		array(
			'get_callback'    => 'get_feature',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	register_rest_field(
		[ 'post', 'solution', 'page' ],
		'subtitle',
		array(
			'get_callback'    => 'get_subtitle',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	register_rest_field(
		[ 'solution' ],
		'priority',
		array(
			'get_callback'    => 'get_priority',
			'update_callback' => null,
			'schema'          => null,
		)
	);
}

function get_frontpage( $request ) {
	// Get the ID of the static frontpage. If not set it's 0.
	$pid = (int) get_option( 'page_on_front' );

	// Get the corresponding post object (let's show our intention explicitly).
	$post = ( $pid > 0 ) ? get_post( $pid ) : null;

	// No static frontpage is set.
	if ( ! is_a( $post, 'WP_Post' ) ) {
		return array(
			'post_title'   => 'No static frontpage.',
			'post_content' => 'Set a static frontpage in the backend under Settings->Reading.',
		);
		/*
			  return new WP_Error(
			'Notice',
			esc_html__( 'No Static Frontpage', 'wpse' ),
			[ 'status' => 404 ]
		); */
	}
	$post->post_content = apply_filters( 'the_content', $post->post_content );
	$post->thumbnail    = get_the_post_thumbnail_url( $pid, 'large' );

	// Response setup.
	return new WP_REST_Response( $post, 200 );
}


register_nav_menus(
	array(
		'footer' => __( 'Footer', 'blankslate' ),
	)
);



function feature_solution_func( $atts, $content = '' ) {
	$atts = shortcode_atts(
		array(
			'class'   => 'md-primary',
			'content' => '',
			'style'   => '',
			'href'    => '',
		),
		$atts,
		'feature_solution'
	);

	$atts['content'] = ( $atts['content'] ) ? $atts['content'] : $content;

	return "
		<ul class='md-list cat-posts md-triple-line md-theme-z'>
			<li to='/solution/algramo/' class='md-list-item'>
			<a
				href='/solution/algramo/'
				class='md-list-item-router md-list-item-container md-button-clean'
				mdripple='true'
			>
				<div class='md-list-item-content md-ripple'>
					<div class='cat-feature-container'>
						<img
						src='https://zerowastelivinglab.com/wp-content/uploads/2019/02/thumbnaill_algramo-1024x768.png'
						class='cat-feature'
						>
					</div>
					<div class='md-list-item-text'>
						<div class='md-title'>Algramo</div> 
						<span>Reusable Refill System for Everyday Products</span>
						<div>
							<p>Algramo offers affordable quantities of everyday products without single-use, non-recyclable packaging</p>
						</div>
					</div>
				</div>
			</a
			</li>
		</ul>
	";
}
add_shortcode( 'feature_solution', 'feature_solution_func' );

