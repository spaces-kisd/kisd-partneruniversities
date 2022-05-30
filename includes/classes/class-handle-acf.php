<?php

/**
 * We use ACF for creating custom fields in the backend.
 *
 * Makes sure ACF is present and there is an API-Key.
 *
 * Don't use it as a frontend-dependency!
 */
class HandleAcf {

	/**
	 * Holds the API Key.
	 *
	 * @var string
	 */
	private $api_key;


	/**
	 * Constructor
	 */
	public function __construct( $fields ) {

		// Determines whether the current request is for an administrative interface page.
		if ( is_admin() ) {

			$this->api_key = get_option( 'google_api_key', false );
			if ( ! $this->api_key ) {
				// add_action( 'admin_notices', array( $this, 'notice_add_api_key' ) );
			}
			add_filter( 'acf/fields/google_map/api', array( $this, 'my_acf_google_map_api' ) );
		}

		/**
		 * If the ACF-Plugin is not installed we manually add it.
		 *
		 * @see https://www.advancedcustomfields.com/resources/including-acf-within-a-plugin-or-theme/
		 */
		if ( ! function_exists( 'acf_add_local_field_group' ) && is_admin() ) {
			$subdir = '/vendor/advancedcustomfields/acf/';
			// Define path and URL to the ACF plugin.
			define( 'MY_ACF_PATH', get_stylesheet_directory() . $subdir );
			define( 'MY_ACF_URL', get_stylesheet_directory_uri() . $subdir );

			// Customize the url setting to fix incorrect asset URLs.
			add_filter( 'acf/settings/url', array( $this, 'my_acf_settings_url' ) );

			// Include the ACF plugin.
			if ( file_exists( MY_ACF_PATH . 'acf.php' ) ) {
				require_once MY_ACF_PATH . 'acf.php';
			} else {
				error_log( "ACF is not available. It can (also) be installed via composer" );
			}
		}

		add_filter( 'option_active_plugins', array( $this, 'disable_acf_on_frontend' ) );
		add_action( 'admin_init', array( $this, 'register_fields' ) );

	}

	public function missing_acf_message() {
		$msg = 'Make sure the plugin "Advanced Custom Fields" is installed and active.';
		if ( is_admin() || 'wp-login.php' === $GLOBALS['pagenow'] ) {
			add_action(
				'admin_notices',
				function() use ( $msg ) {
					echo "<div class='notice notice-error'><p>$msg</p></div>";
				}
			);
		} else {
			wp_die( $msg );
		}
	}

	/**
	 * Add new fields to wp-admin/options-general.php page
	 */
	public function register_fields() {

		register_setting( 'general', 'google_api_key', 'esc_attr' );

		add_settings_field(
			'google_api_key',
			'<label for="google_api_key">' . __( 'ACF - Google Maps API Key', 'blankslate' ) . '</label>',
			array( $this, 'fields_html' ),
			'general'
		);
	}

	/**
	 * HTML for extra settings.
	 */
	public function fields_html() {
		$value = get_option( 'google_api_key', '' );
		echo '<input type="text" id="google_api_key" name="google_api_key" class="regular-text ltr" value="' . esc_attr( $value ) . '" />';
	}

	/**
	 * Don't use ACF on the frontend.
	 *
	 * @param array $plugins All plugins.
	 * @return array The filtered plugins.
	 */
	public function disable_acf_on_frontend( $plugins ) {
		if ( is_admin() ) {
			return $plugins;
		}

		foreach ( $plugins as $i => $plugin ) {
			if ( 'advanced-custom-fields/acf.php' === $plugin ) {
				unset( $plugins[ $i ] );
			}
		}
		return $plugins;
	}

	/**
	 * Triggered by the filter 'acf/settings/url'.
	 *
	 * @param string $url The URL to ACF settings.
	 * @return string
	 */
	public function my_acf_settings_url( $url ) {
		return MY_ACF_URL;
	}

	/**
	 * Triggered by the action 'admin_notices'.
	 * Add a warning if there is not Google maps API key.
	 *
	 * @return void
	 */
	public function notice_add_api_key() {
		$msg = esc_html( __( 'Please set an API-Key for ACF (In Settings->General)', 'blankslate' ) );
		echo "
			<div class='notice notice-warning is-dismissible'>
				<p>$msg</p>
			</div>
		";
	}

	/**
	 * Triggered by the filter 'acf/fields/google_map/api'.
	 *
	 * @param array $api
	 * @return void
	 */
	public function my_acf_google_map_api( $api ) {
		$api['key'] = get_option( 'google_api_key', '' );
		return $api;
	}
}
