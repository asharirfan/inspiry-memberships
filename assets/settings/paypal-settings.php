<?php
/**
 * PayPal Settings File
 *
 * File for adding paypal settings.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $ims_settings;

$ims_paypal_settings_arr	= apply_filters( 'ims_paypal_settings', array(
	array(
		'id'   => 'ims_paypal_enable',
		'type' => 'checkbox',
		'name' => __( 'Enable PayPal', 'inspiry-memberships' ),
		'desc' => __( 'Check this to enable PayPal payments.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_paypal_test_mode',
		'type'    => 'checkbox',
		'name'    => __( 'Sandbox Mode', 'inspiry-memberships' ),
		'desc'    => __( 'Check this option to use PayPal sandbox.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_paypal_client_id',
		'type'    => 'text',
		'name'    => __( 'Client ID', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your client ID here.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_paypal_client_secret',
		'type'    => 'text',
		'name'    => __( 'Client Secret', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your client secret here.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_paypal_api_username',
		'type'    => 'text',
		'name'    => __( 'API Username', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your API username here.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_paypal_api_password',
		'type'    => 'text',
		'name'    => __( 'API Password', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your API password here.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_paypal_api_signature',
		'type'    => 'text',
		'name'    => __( 'API Signature', 'inspiry-memberships' ),
		'desc'    => __( 'Paste your API signature here.', 'inspiry-memberships' ),
	),
	array(
		'id'      	=> 'ims_paypal_ipn_url',
		'type'    	=> 'text',
		'name'    	=> __( 'PayPal IPN URL', 'inspiry-memberships' ),
		'desc'		=> esc_url( add_query_arg( array( 'ims_paypal' => 'notification' ), home_url( '/' ) ) ),
		'default'	=> esc_url( add_query_arg( array( 'ims_paypal' => 'notification' ), home_url( '/' ) ) ),
	),
) );

if ( ! empty( $ims_paypal_settings_arr ) && is_array( $ims_paypal_settings_arr ) ) {
	foreach ( $ims_paypal_settings_arr as $ims_paypal_setting ) {
		$ims_settings->add_field( 'ims_paypal_settings', $ims_paypal_setting );
	}
}
