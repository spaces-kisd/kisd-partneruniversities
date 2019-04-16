<?php


class CustomizeTheme {

	public function __construct() {

		/**
		 * This enables Post Thumbnails
		 *
		 * @see https://codex.wordpress.org/Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		// Add Theme Support for wide and full - width images .
		add_theme_support( 'align-wide' );

		/**
		 * Hide the admin bar.
		 */
		add_filter( 'show_admin_bar', '__return_false' );

		add_action( 'after_setup_theme', array( $this, 'action_after_setup_theme' ) );
		add_action( 'after_switch_theme', array( $this, 'action_after_setup_theme' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'style_backend' ), 100 );

		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts_styles' ], 100 );

	}

	/** add a custom stylesheet from /static/theme.css if it exists. */
	public function load_scripts_styles() {
		$file = get_template_directory_uri() . '/static/theme.css';
		if ( file_exists( $file ) ) {
			wp_enqueue_style( 'custom-theme', $file, array(), filemtime( $file ), 'all' );
		}

	}

	/** Add grey background for gutenberg */
	public function style_backend() {
		$sheet = 'body { background-color:#eee !important; }';
		wp_add_inline_style( 'wp-components', $sheet );
	}

	/**
	 * @action after_setup_theme
	 * @action after_switch_theme
	 */
	public function action_after_setup_theme() {

		// set default thumbnail size in settings > media
		update_option( 'thumbnail_size_w', 256 );
		update_option( 'thumbnail_size_h', 144 );

		update_option( 'medium_size_w', 426 );
		update_option( 'medium_size_h', 240 );

		update_option( 'large_size_w', 800 );
		update_option( 'large_size_h', 9999 );

	}


}
