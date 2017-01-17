<?php
/**
 * Payment Handler Initialization
 *
 * Payment handling initialization file.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * class-payment-handler.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/payment-handler/class-payment-handler.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/payment-handler/class-payment-handler.php' );
}

if ( class_exists( 'IMS_Payment_Handler' ) ) {

	/**
	 * If IMS_Payment_Handler class exists then initialize
	 * it to make it available to init hook.
	 */
	$ims_payment_handler = new IMS_Payment_Handler();

	add_action( 'init', array( $ims_payment_handler, 'process_stripe_payment' ) ); // Stripe Payment Process Init.

	add_action( 'init', array( $ims_payment_handler, 'cancel_user_subscription_request' ) ); // Cancel User Membership Request.

	add_action( 'init', array( $ims_payment_handler, 'handle_stripe_subscription_event' ), 1 ); // Hande stripe events for memberships.

}
