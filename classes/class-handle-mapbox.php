<?php

/**
 * Mapbox requires an access token to work.
 * This includes styles & scripts for mapbox and adds a small backend
 * (in settings->general) for the access token and some mapbox settings.
 */
class HandleMapbox {

	/**
	 * Holds the API Key.
	 *
	 * @var string
	 */
	private $mapbox_settings;


	/**
	 * Constructor
	 */
	public function __construct() {

		$this->check_mapbox_settings();

		add_action( 'admin_init', [ $this, 'register_fields' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts_styles' ], 100 );

	}

	public function check_mapbox_settings() {

		// Determines whether the current request is for an administrative interface page.
		if ( ! is_admin() ) {
			return;
		}

		$this->mapbox_settings = json_decode( get_option( 'mapbox_api_key', false ) );

		if ( isset( $this->mapbox_settings->accessToken ) && ! empty( $this->mapbox_settings->accessToken ) ) {
			return;
		}

		add_action( 'admin_notices', [ $this, 'notice_add_api_key' ] );

	}

	public function load_scripts_styles() {

		wp_enqueue_script( 'mapbox-gl-js', 'https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/0.53.1/mapbox-gl.js', array(), '0.53.1', true );
		// wp_add_inline.
		wp_enqueue_style( 'mapbox-gl-css', get_template_directory_uri() . '/mapbox-gl.css#asyncload', false, null );
		wp_localize_script(
			'mapbox-gl-js',
			'mapboxThemeSettings',
			(array) json_decode( get_option( 'mapbox_api_key', [] ) )
		);
	}

	/**
	 * Add new fields to wp-admin/options-general.php page
	 */
	public function register_fields() {

		register_setting(
			'general',
			'mapbox_api_key',
			[
				'type'              => 'string',
				'sanitize_callback' => [ $this, 'sanitize_mapbox_settings' ],
				'default'           => null,
			]
		);

		add_settings_field(
			'mapbox_api_key',
			'<label for="mapbox_api_key">' . __( 'Mapbox Settings & API Key', 'blankslate' ) . '</label>',
			array( $this, 'fields_html' ),
			'general'
		);
	}

	public function sanitize_mapbox_settings( $value ) {
		return $value;
	}

	/**
	 * HTML for extra settings.
	 */
	public function fields_html() {
		$json_value = get_option( 'mapbox_api_key', '' );

		$defaults = [
			'accessToken' => '',
			'container'   => 'map',
			'style'       => 'mapbox://styles/mapbox/light-v9',
			'center'      => [ 0, 0 ],
			'zoom'        => 5,
		];

		$val_with_defaults = stripslashes(
			wp_json_encode(
				wp_parse_args(
					json_decode( $json_value ),
					$defaults
				),
				JSON_PRETTY_PRINT
			)
		);

		$description = esc_html__(
			'Add a JSON string with following keys: accessToken(necesary), container, style, center, zoom.'
		);

		echo "
			<textarea type='text'
				rows='10'
				id='mapbox_api_key' 
				name='mapbox_api_key' 
				class='regular-text ltr'
				aria-describedby='mapbox-description'
			>$val_with_defaults</textarea>
			<p class='description' id='mapbox-description'>
				$description
			</p>
		";
	}

	/**
	 * Triggered by the action 'admin_notices'.
	 * Add a warning if there is not Google maps API key.
	 *
	 * @return void
	 */
	public function notice_add_api_key() {
		$msg = esc_html( __( 'Please set an Acess Token for Mapbox (In Settings->General)', 'blankslate' ) );
		echo "
			<div class='notice notice-warning is-dismissible'>
				<p>$msg</p>
			</div>
		";

	}

}
