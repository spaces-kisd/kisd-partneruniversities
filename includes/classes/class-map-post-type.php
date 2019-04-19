<?php

/**
 * CPT and Custom Taxonomy.
 * Initially created with the great Plugin "Custom Post Type UI Tools".
 *
 * @todo: move to: on theme install hook ?!
 */
class MapPostType {

	public function __construct() {
		add_action( 'init', [ $this, 'old_cptui_register_my_cpts' ] ); // delete when done.
		//add_action( 'init', [ $this, 'new_cptui_register_my_cpts' ] );

		add_action( 'init', [ $this, 'old_cptui_register_my_taxes_solutions' ] ); // delete when done.
		//add_action( 'init', [ $this, 'new_cptui_register_my_taxes_solutions' ] );
	}

	public function old_cptui_register_my_cpts() {

		/**
		 * Post Type: Solutions.
		 */
		$labels = array(
			'name'          => __( 'Solutions', 'blankslate' ),
			'singular_name' => __( 'Solution', 'blankslate' ),
			'all_items'     => __( 'All Solutions', 'blankslate' ),
		);

		$args = array(
			'label'                 => __( 'Solutions', 'blankslate' ),
			'labels'                => $labels,
			'description'           => 'Lösungen für Plastikdinge',
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'delete_with_user'      => false,
			'show_in_rest'          => true,
			'rest_base'             => 'solutions',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'has_archive'           => false,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'exclude_from_search'   => false,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'hierarchical'          => false,
			'rewrite'               => array(
				'slug'       => 'solution',
				'with_front' => false,
			),
			'query_var'             => true,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'author' ),
			'taxonomies'            => array( 'solutions' ),
		);

		register_post_type( 'solution', $args );
	}

	public function new_cptui_register_my_cpts() {

		/**
		 * Post Type: Solutions.
		 */
		$labels = array(
			'name'          => __( 'Solutions', 'blankslate' ),
			'singular_name' => __( 'Solution', 'blankslate' ),
			'all_items'     => __( 'All Solutions', 'blankslate' ),
		);

		$args = array(
			'label'                 => __( 'Solutions', 'blankslate' ),
			'labels'                => $labels,
			'description'           => 'Lösungen für Plastikdinge',
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'delete_with_user'      => false,
			'show_in_rest'          => true,
			'rest_base'             => 'solutions',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'has_archive'           => false,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'exclude_from_search'   => false,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'hierarchical'          => false,
			'rewrite'               => array(
				'slug'       => 'solution',
				'with_front' => false,
			),
			'query_var'             => true,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'author' ),
			'taxonomies'            => array( 'solutions' ),
		);

		register_post_type( 'solution', $args );
	}

	public function old_cptui_register_my_taxes_solutions() {

		/**
		 * Taxonomy: Solution Categories.
		 */

		$labels = array(
			'name'          => __( 'Solution Categories', 'blankslate' ),
			'singular_name' => __( 'Solution Category', 'blankslate' ),
		);

		$args = array(
			'label'                 => __( 'Solution Categories', 'blankslate' ),
			'labels'                => $labels,
			'public'                => true,
			'publicly_queryable'    => true,
			'hierarchical'          => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'query_var'             => true,
			'rewrite'               => array(
				'slug'         => 'solutions',
				'with_front'   => true,
				'hierarchical' => true,
			),
			'show_admin_column'     => true,
			'show_in_rest'          => true,
			'rest_base'             => 'solution_categories',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_in_quick_edit'    => false,
			'meta_box_cb'           => 'post_categories_meta_box',
		);
		register_taxonomy( 'solutions', array( 'solution' ), $args );
	}

	public function new_cptui_register_my_taxes_solutions() {

		/**
		 * Taxonomy: Solution Categories.
		 */

		$labels = array(
			'name'          => __( 'Solution Categories', 'blankslate' ),
			'singular_name' => __( 'Solution Category', 'blankslate' ),
		);

		$args = array(
			'label'                 => __( 'Solution Categories', 'blankslate' ),
			'labels'                => $labels,
			'public'                => true,
			'publicly_queryable'    => true,
			'hierarchical'          => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'query_var'             => true,
			'rewrite'               => array(
				'slug'         => 'solutions',
				'with_front'   => true,
				'hierarchical' => true,
			),
			'show_admin_column'     => true,
			'show_in_rest'          => true,
			'rest_base'             => 'solution_categories',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_in_quick_edit'    => false,
			'meta_box_cb'           => 'post_categories_meta_box',
		);
		register_taxonomy( 'solutions', array( 'solution' ), $args );
	}




}


