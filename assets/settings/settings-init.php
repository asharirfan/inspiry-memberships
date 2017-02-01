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
			'id'    => 'ims_basic_settings',
			'title' => __( 'Basic Settings', 'inspiry-memberships' )
		)
    );

    // Field: Membership Mode Check
    $ims_settings->add_field(
		'ims_basic_settings',
		array(
			'id'   => 'ims_memberships_enable',
			'type' => 'checkbox',
			'name' => __( 'Enable Memberships', 'inspiry-memberships' ),
			'desc' => __( 'Check this to enable memberships on your website.', 'inspiry-memberships' ),
		)
	);

	// Field: Recurring Membership Check
    $ims_settings->add_field(
		'ims_basic_settings',
		array(
			'id'   => 'ims_recurring_memberships_enable',
			'type' => 'checkbox',
			'name' => __( 'Enable Recurring Memberships', 'inspiry-memberships' ),
			'desc' => __( 'Check this to enable recurring memberships on your website.', 'inspiry-memberships' ),
		)
	);

    // Field: Currency Code.
	$ims_settings->add_field(
		'ims_basic_settings',
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
		'ims_basic_settings',
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
		'ims_basic_settings',
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
			'id'    => 'ims_stripe_settings',
			'title' => __( 'Stripe Settings', 'inspiry-memberships' )
		)
    );

    // Field: Enable Stripe
    $ims_settings->add_field(
		'ims_stripe_settings',
		array(
			'id'   => 'ims_stripe_enable',
			'type' => 'checkbox',
			'name' => __( 'Enable Stripe', 'inspiry-memberships' ),
			'desc' => __( 'Check this to enable Stripe payments.', 'inspiry-memberships' ),
		)
	);

	// Field: Stripe Button Label
    $ims_settings->add_field(
		'ims_stripe_settings',
		array(
			'id'   		=> 'ims_stripe_btn_label',
			'type' 		=> 'text',
			'name' 		=> __( 'Stripe Button Label', 'inspiry-memberships' ),
			'default'	=> 'Pay with Card',
			// 'desc' => __( 'Check this to enable Stripe payments.', 'inspiry-memberships' ),
		)
	);

    // Field: Test mode check
    $ims_settings->add_field(
		'ims_stripe_settings',
		array(
			'id'   => 'ims_test_mode',
			'type' => 'checkbox',
			'name' => __( 'Test Mode', 'inspiry-memberships' ),
			'desc' => __( 'Check this to use the plugin in test mode.', 'inspiry-memberships' ),
		)
	);

    // Field: Live Secret Key.
	$ims_settings->add_field(
		'ims_stripe_settings',
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
		'ims_stripe_settings',
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
		'ims_stripe_settings',
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
		'ims_stripe_settings',
		array(
			'id'      => 'ims_test_publishable',
			'type'    => 'text',
			'name'    => __( 'Test Publishable Key', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your test publishable key.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	/**
	 * Field: Stripe WebHook URL.
	 *
	 * @todo Make an option to let the user know how to register WebHook URL.
	 */
	$ims_settings->add_field(
		'ims_stripe_settings',
		array(
			'id'      => 'ims_stripe_webhook_url',
			'type'    => 'text',
			'name'    => __( 'Stripe WebHook URL', 'inspiry-memberships' ),
			'desc'    => esc_url( add_query_arg( array( 'ims_stripe' => 'membership_event' ), home_url( '/' ) ) ),
			'default' => esc_url( add_query_arg( array( 'ims_stripe' => 'membership_event' ), home_url( '/' ) ) ),
		)
	);

	/**
	 * Section: PayPal Settings.
	 *
	 * @since 1.0.0
	 */
    $ims_settings->add_section(
    	array(
			'id'    => 'ims_paypal_settings',
			'title' => __( 'PayPal Settings', 'inspiry-memberships' )
		)
    );

    // Field: Enable PayPal
    $ims_settings->add_field(
		'ims_paypal_settings',
		array(
			'id'   => 'ims_paypal_enable',
			'type' => 'checkbox',
			'name' => __( 'Enable PayPal', 'inspiry-memberships' ),
			'desc' => __( 'Check this to enable PayPal payments.', 'inspiry-memberships' ),
		)
	);

    // Field: Test Mode.
	$ims_settings->add_field(
		'ims_paypal_settings',
		array(
			'id'      => 'ims_paypal_test_mode',
			'type'    => 'checkbox',
			'name'    => __( 'Sandbox Mode', 'inspiry-memberships' ),
			'desc'    => __( 'Check this option to use PayPal sandbox.', 'inspiry-memberships' ),
		)
	);

    // Field: Client ID.
	$ims_settings->add_field(
		'ims_paypal_settings',
		array(
			'id'      => 'ims_paypal_client_id',
			'type'    => 'text',
			'name'    => __( 'Client ID', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your client ID here.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	// Field: Client Secret.
	$ims_settings->add_field(
		'ims_paypal_settings',
		array(
			'id'      => 'ims_paypal_client_secret',
			'type'    => 'text',
			'name'    => __( 'Client Secret', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your client secret here.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	// Field: API Username.
	$ims_settings->add_field(
		'ims_paypal_settings',
		array(
			'id'      => 'ims_paypal_api_username',
			'type'    => 'text',
			'name'    => __( 'API Username', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your API username here.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	// Field: API Password.
	$ims_settings->add_field(
		'ims_paypal_settings',
		array(
			'id'      => 'ims_paypal_api_password',
			'type'    => 'text',
			'name'    => __( 'API Password', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your API password here.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	// Field: API Signature.
	$ims_settings->add_field(
		'ims_paypal_settings',
		array(
			'id'      => 'ims_paypal_api_signature',
			'type'    => 'text',
			'name'    => __( 'API Signature', 'inspiry-memberships' ),
			'desc'    => __( 'Paste your API signature here.', 'inspiry-memberships' ),
			// 'default' => 'Default Text',
		)
	);

	/**
	 * Field: PayPal IPN URL.
	 *
	 * @todo Make an option to let the user know how to register IPN URL.
	 */
	$ims_settings->add_field(
		'ims_paypal_settings',
		array(
			'id'      	=> 'ims_paypal_ipn_url',
			'type'    	=> 'text',
			'name'    	=> __( 'PayPal IPN URL', 'inspiry-memberships' ),
			'desc'		=> esc_url( add_query_arg( array( 'ims_paypal' => 'notification' ), home_url( '/' ) ) ),
			'default'	=> esc_url( add_query_arg( array( 'ims_paypal' => 'notification' ), home_url( '/' ) ) ),
		)
	);

	/**
	 * Section: PayPal Settings.
	 *
	 * @since 1.0.0
	 */
    $ims_settings->add_section(
    	array(
			'id'    => 'ims_wire_settings',
			'title' => __( 'Wire Transfer Settings', 'inspiry-memberships' )
		)
    );

    // Field: Enable PayPal
    $ims_settings->add_field(
		'ims_wire_settings',
		array(
			'id'   => 'ims_wire_enable',
			'type' => 'checkbox',
			'name' => __( 'Enable Wire Transfer', 'inspiry-memberships' ),
			'desc' => __( 'Check this to enable wire transfer.', 'inspiry-memberships' ),
		)
	);

	// Field: Wire Transfer Instructions.
	$ims_settings->add_field(
		'ims_wire_settings',
		array(
			'id'      => 'ims_wire_transfer_instructions',
			'type'    => 'textarea',
			'name'    => __( 'Instructions for Wire Transfer', 'inspiry-memberships' ),
			'desc'    => __( 'Enter the instructions for wire transfer.', 'inspiry-memberships' ),
			'default' => 'Please include the following information on all wire transfers to our bank account:',
		)
	);

	// Field: Account Name
	$ims_settings->add_field(
		'ims_wire_settings',
		array(
			'id'      => 'ims_wire_account_name',
			'type'    => 'text',
			'name'    => __( 'Account Name', 'inspiry-memberships' ),
			'desc'    => __( 'Enter your account name.', 'inspiry-memberships' ),
			'default' => __( esc_html( get_bloginfo( 'name' ) ), 'inspiry-memberships' ),
		)
	);

	// Field: Account Number
	$ims_settings->add_field(
		'ims_wire_settings',
		array(
			'id'      => 'ims_wire_account_number',
			'type'    => 'text',
			'name'    => __( 'Account Number', 'inspiry-memberships' ),
			'desc'    => __( 'Enter your account number.', 'inspiry-memberships' ),
			'default' => __( '1111-2222-33333-44-5', 'inspiry-memberships' ),
		)
	);

}
