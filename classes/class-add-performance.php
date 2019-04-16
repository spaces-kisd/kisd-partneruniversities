<?php

class AddPerformance {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'my_dns_prefetch' ), 0 );
		add_filter( 'clean_url', array( $this, 'async_scripts' ), 11, 1 );
	}

	public function async_scripts( $url ) {
		if ( strpos( $url, '#asyncload' ) === false ) {
			return $url;
		} elseif ( is_admin() ) {
			return str_replace( '#asyncload', '', $url );
		} else {
			return str_replace( '#asyncload', '', $url ) . "' async='async";
		}
	}

	/**
	 * Set custom "dns-prefetch" and "preconnect" headers at top of <head>.
	 *
	 * @return void
	 */
	public function my_dns_prefetch() {
		// List of domains to set prefetching for.
		$prefetch_domains = [
			'https://cdnjs.cloudflare.com',
			'https://fonts.googleapis.com',
		];

		$prefetch_domains = array_unique( $prefetch_domains );
		$result           = '';

		foreach ( $prefetch_domains as $domain ) {
			$domain  = esc_url( $domain );
			$result .= '<link rel="dns-prefetch" href="' . $domain . '" crossorigin />';
			$result .= '<link rel="preconnect" href="' . $domain . '" crossorigin />';
		}

		echo $result;
	}
}
