<?php
/**
 * Admin Menu Initializer
 *
 * Initializer file for admin menu of plugin.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * class-ims-admin-menu.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/admin-menu/class-ims-admin-menu.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/admin-menu/class-ims-admin-menu.php' );
}

if ( class_exists( 'IMS_Admin_Menu' ) ) {

	$ims_admin_menu_init = new IMS_Admin_Menu();

	// Admin menu.
	add_action( 'admin_menu', array( $ims_admin_menu_init, 'ims_menu' ), 10 );

	// Current menu when clicked on a tab.
	add_action( 'admin_footer', array( $ims_admin_menu_init, 'open_menu' ) );

}
