<?php
/**
 * Basic Settings File
 *
 * File for adding basic settings.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $ims_settings;

$ims_basic_settings_arr	= apply_filters( 'ims_basic_settings', array(
	array(
		'id'   => 'ims_memberships_enable',
		'type' => 'checkbox',
		'name' => __( 'Enable Memberships', 'inspiry-memberships' ),
		'desc' => __( 'Check this to enable memberships on your website.', 'inspiry-memberships' ),
	),
	array(
		'id'   => 'ims_recurring_memberships_enable',
		'type' => 'checkbox',
		'name' => __( 'Enable Recurring Memberships', 'inspiry-memberships' ),
		'desc' => __( 'Check this to enable recurring memberships on your website.', 'inspiry-memberships' ),
	),
	array(
		'id'      => 'ims_currency_code',
		'type'    => 'text',
		'name'    => __( 'Currency Code', 'inspiry-memberships' ),
		'desc'    => __( 'Provide currency code that you want to use. Example: USD', 'inspiry-memberships' ),
		'default' => 'USD',
	),
	array(
		'id'      => 'ims_currency_symbol',
		'type'    => 'text',
		'name'    => __( 'Currency Symbol', 'inspiry-memberships' ),
		'desc'    => __( 'Provide currency symbol that you want to use. Example: $', 'inspiry-memberships' ),
		'default' => '$',
	),
	array(
		'id'      => 'ims_currency_position',
		'type'    => 'select',
		'name'    => __( 'Currency Symbol Position', 'inspiry-membership' ),
		'desc'    => __( 'Default: Before', 'inspiry-membership' ),
		'default' => 'before',
		'options' => array(
			'before' => 'Before (E.g. $10)',
			'after'  => 'After (E.g. 10$)'
		)
	)
) );

if ( ! empty( $ims_basic_settings_arr ) && is_array( $ims_basic_settings_arr ) ) {
	foreach ( $ims_basic_settings_arr as $ims_basic_setting ) {
		$ims_settings->add_field( 'ims_basic_settings', $ims_basic_setting );
	}
}
