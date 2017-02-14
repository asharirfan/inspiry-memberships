<?php
/**
 * Welcome page initialization file
 *
 * Initialization file of plugin welcome page.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * class-welcome.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/welcome/class-welcome-page.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/welcome/class-welcome-page.php' );
}

/**
 * class-welcome-functions.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/welcome/class-welcome-functions.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/welcome/class-welcome-functions.php' );
}

if ( class_exists( 'IMS_Welcome_Page' ) ) {

	add_action( 'admin_init', array( 'IMS_Welcome_Page', 'welcome_page_redirect' ) );

	add_action( 'admin_menu', array( 'IMS_Welcome_Page', 'add_to_admin_menu' ) );

	add_action( 'admin_enqueue_scripts', array( 'IMS_Welcome_Page', 'enqueue_welcome_page_styles' ), 10, 1 );

	add_action( 'admin_enqueue_scripts', array( 'IMS_Welcome_Page', 'enqueue_welcome_page_scripts' ), 10, 1 );

}

if ( class_exists( 'IMS_Welcome_Functions' ) ) {

	add_action( 'wp_ajax_ims_basic_settings_ajax', array( 'IMS_Welcome_Functions', 'save_basic_settings' ) );

	add_action( 'wp_ajax_ims_stripe_settings_ajax', array( 'IMS_Welcome_Functions', 'save_stripe_settings' ) );

	add_action( 'wp_ajax_ims_paypal_settings_ajax', array( 'IMS_Welcome_Functions', 'save_paypal_settings' ) );

	add_action( 'wp_ajax_ims_wire_settings_ajax', array( 'IMS_Welcome_Functions', 'save_wire_settings' ) );

}
