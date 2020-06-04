<?php

/**
 * Delete the local storage if there is new content.
 *
 * @todo: This can probably done more elegant in Vue.
 */
class MangeLocalStorage {

	private $option_name = 'last_content_update';
	private $cookie_name = 'content_version';

	public function __construct() {

		add_action( 'save_post', array( $this, 'set_last_updated' ) );
		add_action( 'update_post', array( $this, 'set_last_updated' ) );
		add_action( 'delete_post', array( $this, 'set_last_updated' ) );

		add_action( 'init', array( $this, 'maybe_update_cookie' ) );

	}

	public function set_last_updated() {
		update_option( $this->option_name, time(), true );
	}

	public function maybe_update_cookie() {

		// we are not interested in ajax/cron/rest/admin requests.
		if ( wp_doing_ajax() || wp_doing_cron() || is_rest() || is_admin()  ) {
			return;
		}

		$server_version = get_option( $this->option_name, 'init' );
		if ( isset( $_COOKIE[ $this->cookie_name ] ) ) {

			$client_version = $_COOKIE[ $this->cookie_name ];
			if ( $client_version == $server_version ) {
				return;
			}
		}

		wp_register_script( 'clear-local-storage', '', array(), '1.0', false );
		wp_enqueue_script( 'clear-local-storage' );
		wp_add_inline_script(
			'clear-local-storage',
			'localStorage.clear();
			caches.keys().then(function(names) {
				for (let name of names){
					caches.delete(name);
				}
			});
			console.log("new version, local storage & caches cleared.")'
		);

		/**
		 * Save a cookie with the most recent update time to the client.
		 */
		setcookie( $this->cookie_name, $server_version, time() + 1209600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true );

	}


}
