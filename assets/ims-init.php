<?php
/**
 * Plugin initialization file.
 *
 * This file initializes the core of the plugin.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * membership-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/membership/membership-init.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/membership/membership-init.php' );
}

/**
 * receipt-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/receipt/receipt-init.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/receipt/receipt-init.php' );
}

/**
 * admin-menu-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/admin-menu/admin-menu-init.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/admin-menu/admin-menu-init.php' );
}

/**
 * settings-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/settings/settings-init.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/settings/settings-init.php' );
}

/**
 * class-ims-email.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/email/class-ims-email.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/email/class-ims-email.php' );
}

/**
 * class-scripts.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/js/class-scripts.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/js/class-scripts.php' );
}

/**
 * ajax-functions.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/js/ajax-functions.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/js/ajax-functions.php' );
}

/**
 * class-functions.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/class-functions.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/class-functions.php' );
}

/**
 * payment-handler-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/payment-handler/payment-handler-init.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/payment-handler/payment-handler-init.php' );
}
