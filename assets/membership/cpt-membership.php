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

			if ( post_type_exists( 'ims_membership' ) ) {
				return;
			}

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

			$rewrite = array(
				'slug'       => apply_filters( 'ims_membership_post_type_slug', __( 'membership', 'inspiry-memberships' ) ),
				'with_front' => true,
				'pages'      => true,
				'feeds'      => true,
			);

			$args = array(
				'labels'              => apply_filters( 'ims_membership_post_type_labels', $labels ),
				'hierarchical'        => false,
				'description'         => __( 'Represents a membership package.', 'inspiry-memberships' ),
				// 'taxonomies'          => array(),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'inspiry_memberships',
				'show_in_admin_bar'   => true,
				'menu_position'       => 10,
				'menu_icon'           => 'dashicons-smiley',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => apply_filters( 'ims_membership_post_type_rewrite', $rewrite ),
				'capability_type'     => 'post',
				'supports'            => apply_filters( 'ims_membership_post_type_supports', array( 'title', 'thumbnail' ) )
			);

			register_post_type( 'ims_membership', apply_filters( 'ims_membership_post_type_args', $args ) );

			// Membership post type registered action hook.
			do_action( 'ims_membership_post_type_registered' );

		}

	}

endif;
