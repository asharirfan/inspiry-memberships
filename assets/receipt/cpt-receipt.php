<?php
/**
 * `Receipt` Post Type
 *
 * Class to create `receipt` post type.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_CPT_Receipt.
 *
 * Class to create `receipt` post type.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_CPT_Receipt' ) ) :

	class IMS_CPT_Receipt {

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
				'name'                => __( 'Receipts', 'inspiry-memberships' ),
				'singular_name'       => __( 'Receipt', 'inspiry-memberships' ),
				'add_new'             => _x( 'Add New Receipt', 'inspiry-memberships', 'inspiry-memberships' ),
				'add_new_item'        => __( 'Add New Receipt', 'inspiry-memberships' ),
				'edit_item'           => __( 'Edit Receipt', 'inspiry-memberships' ),
				'new_item'            => __( 'New Receipt', 'inspiry-memberships' ),
				'view_item'           => __( 'View Receipt', 'inspiry-memberships' ),
				'search_items'        => __( 'Search Receipts', 'inspiry-memberships' ),
				'not_found'           => __( 'No Receipts found', 'inspiry-memberships' ),
				'not_found_in_trash'  => __( 'No Receipts found in Trash', 'inspiry-memberships' ),
				'parent_item_colon'   => __( 'Parent Receipt:', 'inspiry-memberships' ),
				'menu_name'           => __( 'Receipts', 'inspiry-memberships' ),
			);

			$rewrite = array(
				'slug'       => apply_filters( 'ims_receipt_post_type_slug', __( 'receipt', 'inspiry-memberships' ) ),
				'with_front' => true,
				'pages'      => true,
				'feeds'      => true,
			);

			$args = array(
				'labels'              => apply_filters( 'ims_receipt_post_type_labels', $labels ),
				'hierarchical'        => false,
				'description'         => __( 'Represents a receipt of membership.', 'inspiry-memberships' ),
				// 'taxonomies'          => array(),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_admin_bar'   => true,
				'menu_position'       => 10,
				// 'menu_icon'           => 'dashicons-smiley',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => false,
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => apply_filters( 'ims_receipt_post_type_rewrite', $rewrite ),
				'capability_type'     => 'post',
				'supports'            => apply_filters( 'ims_receipt_post_type_supports', array( 'title' ) )
			);

			register_post_type( 'ims_receipt', apply_filters( 'ims_receipt_post_type_args', $args ) );

			// Membership post type registered action hook.
			do_action( 'ims_receipt_post_type_registered' );

		}

	}

endif;
