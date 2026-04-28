<?php
/**
 * Assign users to posts.
 * A user with an account can (un-)assign herself.
 * List all users who are assigned to a post.
 *
 * @package
 */
class Users_On_Post {

	public static function ajax(): void {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'wp_rest' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		$task    = isset( $_GET['task'] ) ? sanitize_key( $_GET['task'] ) : '';
		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
		$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;

		if ( ! $post_id ) {
			wp_send_json_error( 'Invalid post ID.' );
		}

		switch ( $task ) {
			case 'get_all_users':
				wp_send_json_success( self::get_all_users( $post_id ) );

			case 'add_user':
			case 'remove_user':
				if ( ! $user_id ) {
					wp_send_json_error( 'Missing user ID.' );
				}
				if ( 'add_user' === $task && $user_id !== get_current_user_id() ) {
					wp_send_json_error( 'You can only add yourself.' );
				}
				if ( 'remove_user' === $task && $user_id !== get_current_user_id() && ! is_super_admin() ) {
					wp_send_json_error( 'You can only remove yourself.' );
				}
				if ( 'add_user' === $task ) {
					self::add_user( $post_id, $user_id );
				} else {
					self::remove_user( $post_id, $user_id );
				}
				wp_send_json_success( self::get_all_users( $post_id ) );

			default:
				wp_send_json_error( 'Invalid task.' );
		}
	}

	private static function get_all_users( int $post_id ): array {
		$users = get_field( 'students', $post_id, false );
		if ( empty( $users ) ) {
			return array();
		}

		$user_ids = is_array( $users ) ? $users : array( $users );

		return array_values(
			array_filter(
				array_map(
					fn( $user_id ) => self::get_user_data( (int) $user_id ),
					$user_ids
				)
			)
		);
	}

	private static function get_user_data( int $user_id ): ?array {
		$user = get_userdata( $user_id );

		if ( ! $user instanceof WP_User ) {
			return null;
		}

		return array(
			'ID'               => $user->ID,
			'display_name'     => $user->display_name,
			'user_profile_url' => spaces()->blogs_profile->get_profile_url( $user->ID ),
			'user_avatar'      => get_avatar_url( $user->ID ),
		);
	}

	private static function add_user( int $post_id, int $user_id ): void {
		$users = get_field( 'students', $post_id, false ) ?: array();
		if ( ! in_array( $user_id, $users, true ) ) {
			$users[] = $user_id;
			update_field( 'students', $users, $post_id );
		}
	}

	private static function remove_user( int $post_id, int $user_id ): void {
		$users = get_field( 'students', $post_id, false ) ?: array();
		$users = array_values( array_filter( $users, fn( $id ) => $id !== $user_id ) );
		update_field( 'students', $users, $post_id );
	}
}
