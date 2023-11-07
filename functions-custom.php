<?php
/**
 * @done: make sure acf is installed
 *
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
 * @todo remove acf frontend dependency!
 */

require_once 'includes/classes/class-handle-acf.php';
require_once 'includes/classes/class-handle-mapbox.php';
require_once 'includes/classes/class-customize-theme.php';
require_once 'includes/classes/class-manage-local-storage.php';
require_once 'includes/classes/class-add-performance.php';
require_once 'includes/classes/class-users-on-post.php';


/**
 * @todo rename the "solution" post type to "map".
 * @todo rename feature (alias feature-collection) in js & php because its easy to confuse with feature-image and move here.
 */
require_once 'includes/classes/class-map-post-type.php';

// add the solution post type. (this is going to be renamed to map).
$map_post_type = new MapPostType();

$fields = include 'includes/add_custom_fields.php';
// make sure the ACF-Plugin is there and has the relevant API-Keys.
$my_acf = new HandleAcf($fields );


// add custom fields to the solution post type.
if ( function_exists( 'acf_add_local_field_group' ) ) {
	require_once 'includes/add_custom_fields.php';
} else {
	error_log( esc_html__( 'Make sure the Plugin "Advanced Custom Fields" is installed.' ) );
}

$my_mapbox      = new HandleMapbox();
$my_theme       = new CustomizeTheme();
$my_storage     = new MangeLocalStorage();
$my_performance = new AddPerformance();

add_action( 'wp_ajax_users_on_post', 'Users_On_Post::ajax' );
add_filter( 'load_spaces_editor_dependencies', '__return_false' );


function cookie_update_redirect() {
	// we are not interested in ajax or rest requests.
	if ( wp_doing_ajax() || is_rest() || is_admin() ) {
		return;
	}

		$cookie_name = 'intro';

	if (
		'/' == $_SERVER['REQUEST_URI'] // you are visiting the main site.
		&& ! isset( $_COOKIE[ $cookie_name ] ) // you haven't visited the main site before.
		) {
		setcookie( $cookie_name, time(), time() + 1209600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true );
		$pageslug = 'home';
		wp_safe_redirect( get_site_url() . '/' . $pageslug );
		exit;
	}
}
add_action( 'init', 'cookie_update_redirect' );



function load_scripts_styles() {

	wp_enqueue_script( 'swiper', get_stylesheet_directory_uri() . '/node_modules/swiper/swiper-bundle.min.js', array(), '8.1', true );
	wp_enqueue_style( 'swiper-css', get_stylesheet_directory_uri() . '/node_modules/swiper/swiper.min.css', false, null );

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
	delete_transient( $feature_transient_name );
}

	/**
	 * @todo: we can probably think about a better way to posts and features (<-rename them!), so we don't fetch things so redundant.
	 *
	 * @param [type] $data
	 * @return void
	 */
function feature_collection( $data ) {
	global $feature_transient_name;

	$feature_collection = get_transient( $feature_transient_name );

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
	$features = array();

	if ( $query->have_posts() ) {

		while ( $query->have_posts() ) {
			global $post;
			$query->the_post();
			$location = get_field( 'location' );
			$priority = get_field( 'priority' );

			if ( ! empty( $location ) ) {
				array_push(
					$features,
					array(
						'type'       => 'Feature',
						'properties' => array(
							'post_id'             => get_the_ID(),
							'slug'                => $post->post_name,
							'title'               => get_the_title(),
							'full_name'            => get_field( 'full_name' ),
							'priority'            => intval( $priority ? $priority : 5 ),
							'thumbnail'           => get_the_post_thumbnail_url( null, 'medium' ),
							'location_name'       => get_field( 'location_name' ),
							'deadline'            => get_field( 'deadline' ),
							'website'			  => get_field( 'website' ),
							'contact'			  => get_field( 'contact' ),
							'erasmus_code'		  => get_field( 'erasmus_code' ),
							'department'		  => get_field( 'department' ),
							'link_relative'       => make_link_relative_to_blog( get_permalink() ),
						),
						'geometry'   => array(
							'type'        => 'Point',
							'coordinates' => array( $location['lng'], $location['lat'] ),
						),
					)
				);

			}
		}
	}

	$feature_collection =
	array(
		'type'     => 'FeatureCollection',
		'features' => $features,
	);
	$bool_response      = set_transient( $feature_transient_name, $feature_collection, 86400 );
	// Restore original Post Data.
	wp_reset_postdata();
	return $feature_collection;
}


function menu_callback( $data ) {
	wp_nav_menu( array( 'theme_location' => $data['type'] ) );
	die();
}

function get_full_name( $object ) {
	return get_field( 'full_name', $object['id'] );
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
 * @param WP_REST_Request $request Current request.
 *
 * @return mixed
 */
function slug_get_link_relative( $object, $field_name, $request ) {
	return make_link_relative_to_blog( $object['link'] );
	// return wp_make_link_relative( $object['link'] );
}

/**
 * Remove blog-specific things form a link.
 * If the link is http://wp.org/blogname/postname
 * this returns just /postname
 *
 * @param string $link absolute link.
 * @return string relative link.
 */
function make_link_relative_to_blog( $link ) {
	$site_url = get_site_url();
	$new_link = str_replace( $site_url, '', $link );
	// error_log( $new_link . ' | ' . $site_url . ' | ' . $link );
	return $new_link;
}


add_action( 'rest_api_init', 'register_routes' );
function register_routes() {
	register_rest_route(
		'map/v1',
		'/frontpage/',
		array(
			'methods'  => 'GET',
			'callback' => 'get_frontpage',
			'permission_callback' => '__return_true',
		)
	);

	// http://wp.local/wp-json/map/v1/features/solution
	register_rest_route(
		'map/v1',
		'/features/(?P<type>.+)',
		// '/features',
		array(
			'methods'  => 'GET',
			'callback' => 'feature_collection',
			'permission_callback' => '__return_true',
		)
	);

	// http://wp.local/wp-json/map/v1/menus/footer
	register_rest_route(
		'map/v1',
		'/menus/(?P<type>.+)',
		array(
			'methods'  => 'GET',
			'callback' => 'menu_callback',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_field(
		array( 'post', 'solution', 'page' ),
		'link_relative',
		array(
			'get_callback'    => 'slug_get_link_relative',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	register_rest_field(
		array( 'post', 'solution', 'page' ),
		'edit_url',
		array(
			'get_callback'    => 'get_edit_url',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	register_rest_field(
		array( 'post', 'solution', 'page' ),
		'feature',
		array(
			'get_callback'    => 'get_feature',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	register_rest_field(
		array( 'post', 'solution', 'page' ),
		'full_name',
		array(
			'get_callback'    => 'get_full_name',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	register_rest_field(
		array( 'solution' ),
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
