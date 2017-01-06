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
