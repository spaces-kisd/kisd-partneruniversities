<?php

require_once 'functions-custom.php';

// Remove all default WP template redirects/lookups.
remove_action( 'template_redirect', 'redirect_canonical' );

// Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown.
function remove_redirects() {
	add_rewrite_rule( '^/(.+)/?', 'index.php', 'top' );
}
add_action( 'init', 'remove_redirects' );

/* function v_include_scripts( $name, $subdir_path ){} */

// Load scripts.
function load_vue_scripts() {
	$subdir_file = '/dist/js/chunk-vendors.js';
	wp_enqueue_script(
		'vuejs-js-chunk-vendors',
		get_stylesheet_directory_uri() . $subdir_file,
		array(),
		filemtime( get_stylesheet_directory() . $subdir_file ),
		true
	);

	wp_deregister_script( 'vuejs' ); // make sure vue is not otherwise included.

	$subdir_file = '/dist/js/app.js';
	wp_enqueue_script(
		'vuejs-js-app',
		get_stylesheet_directory_uri() . $subdir_file,
		array(),
		filemtime( get_stylesheet_directory() . $subdir_file ),
		true
	);

	$path = '/';
	if ( is_multisite() ) {
		$blog_details = get_blog_details();
		$path = $blog_details->path;
	}

	// The nonce is used by axios when accessing the REST-API. If it's not present, your uid is 0.
	wp_localize_script(
		'vuejs-js-app',
		'vueWp',
		array(
			'apiNonce' => wp_create_nonce( 'wp_rest' ),
			'siteUrl'  => get_site_url(),
			'path'     => $path,
		)
	);

	$subdir_file = '/dist/css/app.css';
	wp_enqueue_style(
		'vuejs-app',
		get_stylesheet_directory_uri() . $subdir_file,
		null,
		filemtime( get_stylesheet_directory() . $subdir_file )
	);

	$subdir_file = '/dist/css/chunk-vendors.css';
	wp_enqueue_style(
		'vuejs-chunk-vendors',
		get_stylesheet_directory_uri() . $subdir_file,
		null,
		filemtime( get_stylesheet_directory() . $subdir_file )
	);

	wp_enqueue_style( 'material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons#asyncload', array() );
}
add_action( 'wp_enqueue_scripts', 'load_vue_scripts', 100 );
