<?php
/**
 * Membership initialization file
 *
 * This file initializes all the related functionality of receipts.
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
if ( file_exists( IMS_BASE_DIR . '/assets/receipt/cpt-receipt.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/receipt/cpt-receipt.php' );
}

/**
 * Membership Class.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/receipt/class-receipt.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/receipt/class-receipt.php' );
}

/**
 * Membership Meta boxes.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/receipt/class-receipt-metaboxes.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/receipt/class-receipt-metaboxes.php' );
}

/**
 * Membership Custom Columns.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/receipt/class-receipt-custom-columns.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/receipt/class-receipt-custom-columns.php' );
}

/**
 * Get Membership Class.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/receipt/class-get-receipt.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/receipt/class-get-receipt.php' );
}

/**
 * Functions to get Membership Object.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/receipt/methods-get-receipt.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/receipt/methods-get-receipt.php' );
}

if ( class_exists( 'IMS_Membership' ) ) {

	$ims_receipt_init	= new IMS_Receipt(); // IMS_Receipt object

	add_action( 'init', array( $ims_receipt_init, 'create_receipt' ), 5 );

}

if ( class_exists( 'IMS_Receipt_Meta_Boxes' ) ) {

	$ims_receipt_meta_boxes_init	= new IMS_Receipt_Meta_Boxes(); // IMS_Receipt_Meta_Boxes object

	add_action( 'load-post.php', array( $ims_receipt_meta_boxes_init, 'setup_meta_box' ) );
	add_action( 'load-post-new.php', array( $ims_receipt_meta_boxes_init, 'setup_meta_box' ) );

}

if ( class_exists( 'IMS_Receipt_Custom_Columns' ) ) {

	// Object: IMS_Receipt_Custom_Columns class.
	$ims_receipt_custom_columns = new IMS_Receipt_Custom_Columns();

	// Add custom columns.
	add_filter( 'manage_edit-ims_receipt_columns', array( $ims_receipt_custom_columns, 'register_columns' ) );

	// Display custom columns values.
	add_action( 'manage_ims_receipt_posts_custom_column', array( $ims_receipt_custom_columns, 'display_column_values' ) );

	// // Sortable Columns.
	// add_filter( 'manage_edit-ims_receipt_sortable_columns', array( $ims_receipt_custom_columns, 'sortable_price' ) );

}
