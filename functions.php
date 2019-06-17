<?php

require_once 'functions-custom.php';

// Remove all default WP template redirects/lookups.
remove_action( 'template_redirect', 'redirect_canonical' );

// Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown.
function remove_redirects() {
	add_rewrite_rule( '^/(.+)/?', 'index.php', 'top' );
}
add_action( 'init', 'remove_redirects' );

// Load scripts.
function load_vue_scripts() {

	wp_enqueue_script(
		'vuejs-wordpress-theme-starter-js',
		get_stylesheet_directory_uri() . '/dist/scripts/index.min.bundle.js',
		array(),
		filemtime( get_stylesheet_directory() . '/dist/scripts/index.min.bundle.js' ),
		true
	);

	$path = '/';
	if ( is_multisite() ){
		$blog_details = get_blog_details();
		$path = $blog_details->path;
	}

	// The nonce is used by axios when accessing the REST-API. If it's not present, your uid is 0.
	wp_localize_script(
		'vuejs-wordpress-theme-starter-js',
		'vueWp',
		array(
			'apiNonce' => wp_create_nonce( 'wp_rest' ),
			'siteUrl'  => get_site_url(),
			'path'     => $path
		)
	);
	wp_enqueue_style(
		'vuejs-wordpress-theme-starter-css',
		get_stylesheet_directory_uri() . '/dist/styles.css',
		null,
		filemtime( get_stylesheet_directory() . '/dist/styles.css' )
	);
	wp_enqueue_style( 'material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons#asyncload', array() );
}
add_action( 'wp_enqueue_scripts', 'load_vue_scripts', 100 );
