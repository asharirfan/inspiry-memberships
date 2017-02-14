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
if ( file_exists( IMS_BASE_DIR . '/resources/welcome/welcome-init.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/welcome/welcome-init.php' );
}

/**
 * class-ims-email.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/email/class-ims-email.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/email/class-ims-email.php' );
}

/**
 * membership-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/membership/membership-init.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/membership/membership-init.php' );
}

/**
 * receipt-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/receipt/receipt-init.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/receipt/receipt-init.php' );
}

/**
 * admin-menu-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/admin-menu/admin-menu-init.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/admin-menu/admin-menu-init.php' );
}

/**
 * settings-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/settings/settings-init.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/settings/settings-init.php' );
}

/**
 * class-scripts.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/js/class-scripts.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/js/class-scripts.php' );
}

/**
 * class-functions.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/class-functions.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/class-functions.php' );
}

/**
 * payment-handler-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/payment-handler/payment-handler-init.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/payment-handler/payment-handler-init.php' );
}
