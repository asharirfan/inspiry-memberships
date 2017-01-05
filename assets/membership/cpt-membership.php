<?php
/**
 * `Membership` Post Type
 *
 * Class to create `membership` post type.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_CPT_Membership.
 *
 * Class to create `membership` post type.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_CPT_Membership' ) ) :

	class IMS_CPT_Membership {

		/**
		* Registers a new post type
		* @uses $wp_post_types Inserts new post type object into the list
		*
		* @param string  Post type key, must not exceed 20 characters
		* @param array|string  See optional args description above.
		* @return object|WP_Error the registered post type object, or an error object
		*/
		public function register() {

			$labels = array(
				'name'                => __( 'Memberships', 'inspiry-memberships' ),
				'singular_name'       => __( 'Membership', 'inspiry-memberships' ),
				'add_new'             => _x( 'Add New Membership', 'inspiry-memberships', 'inspiry-memberships' ),
				'add_new_item'        => __( 'Add New Membership', 'inspiry-memberships' ),
				'edit_item'           => __( 'Edit Membership', 'inspiry-memberships' ),
				'new_item'            => __( 'New Membership', 'inspiry-memberships' ),
				'view_item'           => __( 'View Membership', 'inspiry-memberships' ),
				'search_items'        => __( 'Search Memberships', 'inspiry-memberships' ),
				'not_found'           => __( 'No Memberships found', 'inspiry-memberships' ),
				'not_found_in_trash'  => __( 'No Memberships found in Trash', 'inspiry-memberships' ),
				'parent_item_colon'   => __( 'Parent Membership:', 'inspiry-memberships' ),
				'menu_name'           => __( 'Memberships', 'inspiry-memberships' ),
			);

			$args = array(
				'labels'              => $labels,
				'hierarchical'        => false,
				'description'         => __( 'Memberships', 'inspiry-memberships' ),
				// 'taxonomies'          => array(),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 10,
				'menu_icon'           => 'dashicons-smiley',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => array( 'slug' => __( 'membership', 'inspiry-memberships' ) ),
				'capability_type'     => 'post',
				'supports'            => array( 'title', 'thumbnail', 'excerpt' )
			);

			register_post_type( 'ims_membership', $args );
		}

	}

endif;
