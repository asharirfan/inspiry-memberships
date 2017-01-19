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
 * class-stripe-payment-handler.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/payment-handler/class-stripe-payment-handler.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/payment-handler/class-stripe-payment-handler.php' );
}

/**
 * class-paypal-payment-handler.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/payment-handler/class-paypal-payment-handler.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/payment-handler/class-paypal-payment-handler.php' );
}

if ( class_exists( 'IMS_Stripe_Payment_Handler' ) ) {

	/**
	 * If IMS_Stripe_Payment_Handler class exists then initialize
	 * it to make it available to init hook.
	 */
	$ims_stripe_payment_handler = new IMS_Stripe_Payment_Handler();

	add_action( 'init', array( $ims_stripe_payment_handler, 'process_stripe_payment' ) ); // Stripe Payment Process Init.

	add_action( 'init', array( $ims_stripe_payment_handler, 'cancel_user_subscription_request' ) ); // Cancel User Membership Request.

	add_action( 'init', array( $ims_stripe_payment_handler, 'handle_stripe_subscription_event' ), 1 ); // Hande stripe events for memberships.

}

if ( class_exists( 'IMS_PayPal_Payment_Handler' ) ) {

	/**
	 * If IMS_PayPal_Payment_Handler class exists then initialize
	 * the class.
	 */
	$ims_paypal_payment_handler = new IMS_PayPal_Payment_Handler();

	add_action( 'wp_ajax_ims_paypal_simple_payment', array( $ims_paypal_payment_handler, 'process_paypal_payment' ) );

}
