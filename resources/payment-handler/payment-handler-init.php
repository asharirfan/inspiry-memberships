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
if ( file_exists( IMS_BASE_DIR . '/resources/payment-handler/class-payment-handler.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/payment-handler/class-payment-handler.php' );
}

/**
 * class-stripe-payment-handler.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/payment-handler/class-stripe-payment-handler.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/payment-handler/class-stripe-payment-handler.php' );
}

/**
 * class-paypal-payment-handler.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/payment-handler/class-paypal-payment-handler.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/payment-handler/class-paypal-payment-handler.php' );
}

/**
 * class-wire-transfer-handler.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/payment-handler/class-wire-transfer-handler.php' ) ) {
    include_once( IMS_BASE_DIR . '/resources/payment-handler/class-wire-transfer-handler.php' );
}

if ( class_exists( 'IMS_Payment_Handler' ) ) {

	/**
	 * If IMS_Payment_Handler class exists then initialize it.
	 */
	$ims_payment_handler = new IMS_Payment_Handler();

	add_action( 'init', array( $ims_payment_handler, 'cancel_user_membership_request' ) ); // Cancel User Membership Request.

	add_action( 'init', array( $ims_payment_handler, 'subscribe_free_membership' ) );

}

if ( class_exists( 'IMS_Stripe_Payment_Handler' ) ) {

	/**
	 * If IMS_Stripe_Payment_Handler class exists then initialize it.
	 */
	$ims_stripe_payment_handler = IMS_Stripe_Payment_Handler();

	add_action( 'wp_ajax_ims_stripe_button', array( $ims_stripe_payment_handler, 'ims_display_stripe_button' ) );

	add_action( 'init', array( $ims_stripe_payment_handler, 'process_stripe_payment' ) ); // Stripe Payment Process Init.

	add_action( 'init', array( $ims_stripe_payment_handler, 'handle_stripe_subscription_event' ), 1 ); // Handle stripe events for memberships.

}

if ( class_exists( 'IMS_PayPal_Payment_Handler' ) ) {

	/**
	 * If IMS_PayPal_Payment_Handler class exists then initialize
	 * the class.
	 */
	$ims_paypal_payment_handler = new IMS_PayPal_Payment_Handler();

	add_action( 'wp_ajax_ims_paypal_simple_payment', array( $ims_paypal_payment_handler, 'process_simple_paypal_payment' ) );

	add_action( 'init', array( $ims_paypal_payment_handler, 'execute_paypal_payment' ) );

	add_action( 'wp_ajax_ims_paypal_recurring_payment', array( $ims_paypal_payment_handler, 'process_recurring_paypal_payment' ) );

	add_action( 'init', array( $ims_paypal_payment_handler, 'execute_recurring_paypal_payment' ) );

	add_action( 'init', array( $ims_paypal_payment_handler, 'handle_paypal_ipn_event' ) );

}

if ( class_exists( 'IMS_Wire_Transfer_Handler' ) ) {

	/**
	 * If IMS_Wire_Transfer_Handler class exist then initialize the
	 * class.
	 */
	$ims_wire_transfer_handler 	= new IMS_Wire_Transfer_Handler();

	add_action( 'wp_ajax_ims_send_wire_receipt', array( $ims_wire_transfer_handler, 'send_wire_receipt' ) );

	add_action( 'save_post', array( $ims_wire_transfer_handler, 'activate_membership_via_wire' ), 20, 2 );

}
