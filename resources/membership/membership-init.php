<?php
/**
 * Membership initialization file
 *
 * This file initializes all the related functionality of memberships.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Membership Custom Post Type.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/membership/cpt-membership.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/membership/cpt-membership.php' );
}

/**
 * Membership Class.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/membership/class-membership.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/membership/class-membership.php' );
}

/**
 * Membership Meta boxes.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/membership/class-membership-metaboxes.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/membership/class-membership-metaboxes.php' );
}

/**
 * Membership Custom Columns.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/membership/class-membership-custom-columns.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/membership/class-membership-custom-columns.php' );
}

/**
 * Get Membership Class.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/membership/class-get-membership.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/membership/class-get-membership.php' );
}

/**
 * Functions to get Membership Object.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/membership/methods-get-membership.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/membership/methods-get-membership.php' );
}

/**
 * Membership methods class file.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/membership/class-membership-methods.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/membership/class-membership-methods.php' );
}

if ( class_exists( 'IMS_Membership' ) ) {

	$ims_membership_init	= new IMS_Membership(); // IMS_Membership object

	add_action( 'init', array( $ims_membership_init, 'create_membership' ), 5 ); // Creating Membership Post Type.

	add_filter( 'cron_schedules', array( $ims_membership_init, 'create_schedules' ) ); // Adding custom time schedules.

}

if ( class_exists( 'IMS_Membership_Meta_Boxes' ) ) {

	$ims_membership_meta_boxes_init	= new IMS_Membership_Meta_Boxes(); // IMS_Membership object

	add_action( 'load-post.php', array( $ims_membership_meta_boxes_init, 'setup_meta_box' ) );
	add_action( 'load-post-new.php', array( $ims_membership_meta_boxes_init, 'setup_meta_box' ) );

	add_action( 'admin_print_styles-post.php', array( $ims_membership_meta_boxes_init, 'add_styles' ) );
	add_action( 'admin_print_styles-post-new.php', array( $ims_membership_meta_boxes_init, 'add_styles' ) );

}

if ( class_exists( 'IMS_Membership_Custom_Columns' ) ) {

	if ( is_admin() ) {

		global $pagenow;

		if ( 'edit.php' === $pagenow && isset( $_GET[ 'post_type' ] ) && 'ims_membership' === esc_attr( $_GET[ 'post_type' ] ) ) {

			// Object: IMS_Membership_Custom_Columns class.
			$ims_membership_custom_columns = new IMS_Membership_Custom_Columns();

			// Add custom columns.
			add_filter( 'manage_edit-ims_membership_columns', array( $ims_membership_custom_columns, 'register_columns' ) );

			// Display custom columns values.
			add_action( 'manage_ims_membership_posts_custom_column', array( $ims_membership_custom_columns, 'display_column_values' ) );

		}

	}

}
