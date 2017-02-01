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

if ( ! function_exists( 'inspiry_log' ) ) {
    /**
     * Function to help in debugging
     *
     * @param $message
     */
    function inspiry_log( $message ) {
        if ( WP_DEBUG === true ) {
            if ( is_array( $message ) || is_object( $message ) ) {
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }
}

/**
 * welcome-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/welcome/welcome-init.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/welcome/welcome-init.php' );
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
 * class-scripts.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/js/class-scripts.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/js/class-scripts.php' );
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
