<?php
/**
 * Plugin Settings Initializer
 *
 * Initializer file for plugin settings.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * class-ims-settings.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/assets/settings/class-ims-settings.php' ) ) {
    require_once( IMS_BASE_DIR . '/assets/settings/class-ims-settings.php' );
}

if ( class_exists( 'WP_OSA' ) ) {

	// New Settings Menu.
	$ims_settings = new WP_OSA();

	/**
	 * Section: Basic Settings.
	 *
	 * @since 1.0.0
	 */
    $ims_settings->add_section(
    	array(
			'id'    => 'ims_basic_section',
			'title' => __( 'Basic Settings', 'inspiry-memberships' )
		)
    );

    // Field: Currency Code.
	$ims_settings->add_field(
		'ims_basic_section',
		array(
			'id'      => 'ims_currency_code',
			'type'    => 'text',
			'name'    => __( 'Currency Code', 'inspiry-memberships' ),
			'desc'    => __( 'Provide currency code that you want to use. Example: USD', 'inspiry-memberships' ),
			'default' => 'USD',
		)
	);

	// Field: Currency Symbol.
	$ims_settings->add_field(
		'ims_basic_section',
		array(
			'id'      => 'ims_currency_symbol',
			'type'    => 'text',
			'name'    => __( 'Currency Symbol', 'inspiry-memberships' ),
			'desc'    => __( 'Provide currency symbol that you want to use. Example: $', 'inspiry-memberships' ),
			'default' => '$',
		)
	);

	// Field: Currency Position.
	$ims_settings->add_field(
		'ims_basic_section',
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
	);

	/**
	 * Section: Stripe Settings.
	 *
	 * @since 1.0.0
	 */
    $ims_settings->add_section(
    	array(
			'id'    => 'ims_stripe_section',
			'title' => __( 'Stripe Settings', 'inspiry-memberships' )
		)
    );

    // Field: Test mode check
    $ims_settings->add_field(
		'ims_stripe_section',
		array(
			'id'   => 'ims_test_mode',
			'type' => 'checkbox',
			'name' => __( 'Test Mode', 'inspiry-memberships' ),
			'desc' => __( 'Check this to use the plugin in test mode.', 'inspiry-memberships' ),
		)
	);

    // Field: Live Secret Key.
	$ims_settings->add_field(
		'ims_stripe_section',
		array(
			'id'      => 'ims_live_secret',
			'type'    => 'text',
			'name'    => __( 'Live Secret Key', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your live secret key.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	// Field: Live Publishable Key.
	$ims_settings->add_field(
		'ims_stripe_section',
		array(
			'id'      => 'ims_live_publishable',
			'type'    => 'text',
			'name'    => __( 'Live Publishable Key', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your live publishable key.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	// Field: Test Secret Key.
	$ims_settings->add_field(
		'ims_stripe_section',
		array(
			'id'      => 'ims_test_secret',
			'type'    => 'text',
			'name'    => __( 'Test Secret Key', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your test secret key.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	// Field: Test Publishable Key.
	$ims_settings->add_field(
		'ims_stripe_section',
		array(
			'id'      => 'ims_test_publishable',
			'type'    => 'text',
			'name'    => __( 'Test Publishable Key', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your test publishable key.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

}
