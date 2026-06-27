<?php

class AddPerformance {

	public function __construct() {
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
}
