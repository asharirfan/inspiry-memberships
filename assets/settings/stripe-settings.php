<?php
/**
 * Stripe Settings File
 *
 * File for adding stripe settings.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $ims_settings;

$ims_stripe_settings_arr	= apply_filters( 'ims_stripe_settings', array(
	array(
		'id'   => 'ims_stripe_enable',
		'type' => 'checkbox',
		'name' => __( 'Enable Stripe', 'inspiry-memberships' ),
		'desc' => __( 'Check this to enable Stripe payments.', 'inspiry-memberships' ),
	),
	array(
		'id'   		=> 'ims_stripe_btn_label',
		'type' 		=> 'text',
		'name' 		=> __( 'Stripe Button Label', 'inspiry-memberships' ),
		'default'	=> 'Pay with Card',
	),
	array(
		'id'   => 'ims_test_mode',
		'type' => 'checkbox',
		'name' => __( 'Test Mode', 'inspiry-memberships' ),
		'desc' => __( 'Check this to use the plugin in test mode.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_live_secret',
		'type'    => 'text',
		'name'    => __( 'Live Secret Key', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your live secret key.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_live_publishable',
		'type'    => 'text',
		'name'    => __( 'Live Publishable Key', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your live publishable key.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_test_secret',
		'type'    => 'text',
		'name'    => __( 'Test Secret Key', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your test secret key.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_test_publishable',
		'type'    => 'text',
		'name'    => __( 'Test Publishable Key', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your test publishable key.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_stripe_webhook_url',
		'type'    => 'text',
		'name'    => __( 'Stripe WebHook URL', 'inspiry-memberships' ),
		'desc'    => esc_url( add_query_arg( array( 'ims_stripe' => 'membership_event' ), home_url( '/' ) ) ),
		'default' => esc_url( add_query_arg( array( 'ims_stripe' => 'membership_event' ), home_url( '/' ) ) ),
	),
) );

if ( ! empty( $ims_stripe_settings_arr ) && is_array( $ims_stripe_settings_arr ) ) {
	foreach ( $ims_stripe_settings_arr as $ims_stripe_setting ) {
		$ims_settings->add_field( 'ims_stripe_settings', $ims_stripe_setting );
	}
}
