<?php

require_once 'functions-custom.php';

// Remove all default WP template redirects/lookups.
remove_action( 'template_redirect', 'redirect_canonical' );

// Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown.
function kpu_remove_redirects() {
	add_rewrite_rule( '^/(.+)/?', 'index.php', 'top' );
}
add_action( 'init', 'kpu_remove_redirects' );

/* function v_include_scripts( $name, $subdir_path ){} */

// Load scripts.
function kpu_load_vue_scripts() {
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
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'restUrl'  => rest_url(),
			'path'     => $path,
			'isLoggedIn'     => is_user_logged_in(),
			'currentUserId'  => get_current_user_id(),
			'isSuperAdmin'   => is_super_admin(),
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

	// Self-hosted Material Icons — no request to fonts.googleapis.com (DSGVO/Drittland).
	$mi_css = '/includes/css/material-icons.css';
	wp_enqueue_style(
		'material-icons',
		get_stylesheet_directory_uri() . $mi_css,
		array(),
		filemtime( get_stylesheet_directory() . $mi_css )
	);
}
add_action( 'wp_enqueue_scripts', 'kpu_load_vue_scripts', 100 );

//Workaround for Spaces Editor: Should be changend
add_action('wp_enqueue_scripts', 'kpu_disable_pluginsjs_selectively', 100 );
function kpu_disable_pluginsjs_selectively( ) {
   wp_deregister_script( 'vuejs');
}