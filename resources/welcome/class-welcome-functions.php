<?php
/**
 * Welcome Page Functions
 *
 * Class file for welcome page functions.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Welcome_Functions.
 *
 * Class for welcome page functions.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Welcome_Functions' ) ) :

	class IMS_Welcome_Functions {

		/**
		 * Method: Save basic settings.
		 *
		 * @since 1.0.0
		 */
		public static function save_basic_settings() {

			if ( isset( $_POST[ 'action' ] ) && 'ims_basic_settings_ajax' === $_POST[ 'action' ] ) {

				// Get the data.
				$membership_enable	= ( isset( $_POST[ 'membership_enable' ] ) && ! empty( $_POST[ 'membership_enable' ] ) ) ? sanitize_text_field( $_POST[ 'membership_enable' ] ) : false;
				$membership_enable	= ( 'true' === $membership_enable ) ? 'on' : 'off';
				$recurring_enable	= ( isset( $_POST[ 'recurring_enable' ] ) && ! empty( $_POST[ 'recurring_enable' ] ) ) ? sanitize_text_field( $_POST[ 'recurring_enable' ] ) : false;
				$recurring_enable	= ( 'true' === $recurring_enable ) ? 'on' : 'off';
				$currency_code		= ( isset( $_POST[ 'currency_code' ] ) && ! empty( $_POST[ 'currency_code' ] ) ) ? sanitize_text_field( $_POST[ 'currency_code' ] ) : false;
				$currency_symbol	= ( isset( $_POST[ 'currency_symbol' ] ) && ! empty( $_POST[ 'currency_symbol' ] ) ) ? sanitize_text_field( $_POST[ 'currency_symbol' ] ) : false;
				$currency_position	= ( isset( $_POST[ 'currency_position' ] ) && ! empty( $_POST[ 'currency_position' ] ) ) ? sanitize_text_field( $_POST[ 'currency_position' ] ) : false;

				$basic_settings 	= array(
					'ims_memberships_enable'			=> $membership_enable,
					'ims_recurring_memberships_enable'	=> $recurring_enable,
					'ims_currency_code'					=> $currency_code,
					'ims_currency_symbol'				=> $currency_symbol,
					'ims_currency_position'				=> $currency_position,
				);

				if ( update_option( 'ims_basic_settings', $basic_settings ) ) {
					echo json_encode( array(
						'success'	=> true
					) );
				} else {
					echo json_encode( array(
						'success'	=> false,
						'message'	=> __( 'Settings not saved.', 'inspiry-memberships' )
					) );
				}

			} else {
				echo json_encode( array(
					'success'	=> false,
					'message'	=> __( 'Some error occurred while saving settings', 'inspiry-memberships' )
				) );
			}
			die();

		}

		/**
		 * Method: Save stripe settings.
		 *
		 * @since 1.0.0
		 */
		public static function save_stripe_settings() {

			if ( isset( $_POST[ 'action' ] ) && 'ims_stripe_settings_ajax' === $_POST[ 'action' ] ) {

				// Get the data.
				$stripe_enable		= ( isset( $_POST[ 'stripe_enable' ] ) && ! empty( $_POST[ 'stripe_enable' ] ) ) ? sanitize_text_field( $_POST[ 'stripe_enable' ] ) : false;
				$stripe_enable		= ( 'true' === $stripe_enable ) ? 'on' : 'off';
				$stripe_test_mode	= ( isset( $_POST[ 'stripe_test_mode' ] ) && ! empty( $_POST[ 'stripe_test_mode' ] ) ) ? sanitize_text_field( $_POST[ 'stripe_test_mode' ] ) : false;
				$stripe_test_mode	= ( 'true' === $stripe_test_mode ) ? 'on' : 'off';
				$live_secret		= ( isset( $_POST[ 'live_secret' ] ) && ! empty( $_POST[ 'live_secret' ] ) ) ? sanitize_text_field( $_POST[ 'live_secret' ] ) : false;
				$live_publishable	= ( isset( $_POST[ 'live_publishable' ] ) && ! empty( $_POST[ 'live_publishable' ] ) ) ? sanitize_text_field( $_POST[ 'live_publishable' ] ) : false;
				$test_secret		= ( isset( $_POST[ 'test_secret' ] ) && ! empty( $_POST[ 'test_secret' ] ) ) ? sanitize_text_field( $_POST[ 'test_secret' ] ) : false;
				$test_publishable	= ( isset( $_POST[ 'test_publishable' ] ) && ! empty( $_POST[ 'test_publishable' ] ) ) ? sanitize_text_field( $_POST[ 'test_publishable' ] ) : false;
				$stripe_webhook		= ( isset( $_POST[ 'stripe_webhook' ] ) && ! empty( $_POST[ 'stripe_webhook' ] ) ) ? sanitize_text_field( $_POST[ 'stripe_webhook' ] ) : false;

				$stripe_settings 	= array(
					'ims_stripe_enable'			=> $stripe_enable,
					'ims_test_mode'				=> $stripe_test_mode,
					'ims_live_secret'			=> $live_secret,
					'ims_live_publishable'		=> $live_publishable,
					'ims_test_secret'			=> $test_secret,
					'ims_test_publishable'		=> $test_publishable,
					'ims_stripe_webhook_url'	=> $stripe_webhook
				);

				if ( update_option( 'ims_stripe_settings', $stripe_settings ) ) {
					echo json_encode( array(
						'success'	=> true
					) );
				} else {
					echo json_encode( array(
						'success'	=> false,
						'message'	=> __( 'Settings not saved.', 'inspiry-memberships' )
					) );
				}

			} else {
				echo json_encode( array(
					'success'	=> false,
					'message'	=> __( 'Some error occurred while saving settings', 'inspiry-memberships' )
				) );
			}
			die();

		}

		/**
		 * Method: Save paypal settings.
		 *
		 * @since 1.0.0
		 */
		public static function save_paypal_settings() {

			if ( isset( $_POST[ 'action' ] ) && 'ims_paypal_settings_ajax' === $_POST[ 'action' ] ) {

				// Get the data.
				$paypal_enable			= ( isset( $_POST[ 'paypal_enable' ] ) && ! empty( $_POST[ 'paypal_enable' ] ) ) ? sanitize_text_field( $_POST[ 'paypal_enable' ] ) : false;
				$paypal_enable			= ( 'true' === $paypal_enable ) ? 'on' : 'off';
				$paypal_sandbox			= ( isset( $_POST[ 'paypal_sandbox' ] ) && ! empty( $_POST[ 'paypal_sandbox' ] ) ) ? sanitize_text_field( $_POST[ 'paypal_sandbox' ] ) : false;
				$paypal_sandbox			= ( 'true' === $paypal_sandbox ) ? 'on' : 'off';
				$paypal_client_id		= ( isset( $_POST[ 'paypal_client_id' ] ) && ! empty( $_POST[ 'paypal_client_id' ] ) ) ? sanitize_text_field( $_POST[ 'paypal_client_id' ] ) : false;
				$paypal_client_secret	= ( isset( $_POST[ 'paypal_client_secret' ] ) && ! empty( $_POST[ 'paypal_client_secret' ] ) ) ? sanitize_text_field( $_POST[ 'paypal_client_secret' ] ) : false;
				$paypal_api_username	= ( isset( $_POST[ 'paypal_api_username' ] ) && ! empty( $_POST[ 'paypal_api_username' ] ) ) ? sanitize_text_field( $_POST[ 'paypal_api_username' ] ) : false;
				$paypal_api_password	= ( isset( $_POST[ 'paypal_api_password' ] ) && ! empty( $_POST[ 'paypal_api_password' ] ) ) ? sanitize_text_field( $_POST[ 'paypal_api_password' ] ) : false;
				$paypal_api_signature	= ( isset( $_POST[ 'paypal_api_signature' ] ) && ! empty( $_POST[ 'paypal_api_signature' ] ) ) ? sanitize_text_field( $_POST[ 'paypal_api_signature' ] ) : false;
				$paypal_ipn_url			= ( isset( $_POST[ 'paypal_ipn_url' ] ) && ! empty( $_POST[ 'paypal_ipn_url' ] ) ) ? sanitize_text_field( $_POST[ 'paypal_ipn_url' ] ) : false;

				// inspiry_log( get_option( 'ims_basic_settings' ) );
				$paypal_settings	= array(
					'ims_paypal_enable'			=> $paypal_enable,
					'ims_paypal_test_mode'		=> $paypal_sandbox,
					'ims_paypal_client_id'		=> $paypal_client_id,
					'ims_paypal_client_secret'	=> $paypal_client_secret,
					'ims_paypal_api_username'	=> $paypal_api_username,
					'ims_paypal_api_password'	=> $paypal_api_password,
					'ims_paypal_api_signature'	=> $paypal_api_signature,
					'ims_paypal_ipn_url'		=> $paypal_ipn_url,
				);

				if ( update_option( 'ims_paypal_settings', $paypal_settings	) ) {
					echo json_encode( array(
						'success'	=> true
					) );
				} else {
					echo json_encode( array(
						'success'	=> false,
						'message'	=> __( 'Settings not saved.', 'inspiry-memberships' )
					) );
				}

			} else {
				echo json_encode( array(
					'success'	=> false,
					'message'	=> __( 'Some error occurred while saving settings', 'inspiry-memberships' )
				) );
			}
			die();

		}

		/**
		 * Method: Save wire settings.
		 *
		 * @since 1.0.0
		 */
		public static function save_wire_settings() {

			if ( isset( $_POST[ 'action' ] ) && 'ims_wire_settings_ajax' === $_POST[ 'action' ] ) {

				// Get the data.
				$wire_enable			= ( isset( $_POST[ 'wire_enable' ] ) && ! empty( $_POST[ 'wire_enable' ] ) ) ? sanitize_text_field( $_POST[ 'wire_enable' ] ) : false;
				$wire_enable			= ( 'true' === $wire_enable ) ? 'on' : 'off';
				$wire_instructions		= ( isset( $_POST[ 'wire_instructions' ] ) && ! empty( $_POST[ 'wire_instructions' ] ) ) ? sanitize_text_field( $_POST[ 'wire_instructions' ] ) : false;
				$wire_account_name		= ( isset( $_POST[ 'wire_account_name' ] ) && ! empty( $_POST[ 'wire_account_name' ] ) ) ? sanitize_text_field( $_POST[ 'wire_account_name' ] ) : false;
				$wire_account_number	= ( isset( $_POST[ 'wire_account_number' ] ) && ! empty( $_POST[ 'wire_account_number' ] ) ) ? sanitize_text_field( $_POST[ 'wire_account_number' ] ) : false;

				// inspiry_log( get_option( 'ims_basic_settings' ) );
				$wire_settings	= array(
					'ims_wire_enable'					=> $wire_enable,
					'ims_wire_transfer_instructions'	=> $wire_instructions,
					'ims_wire_account_name'				=> $wire_account_name,
					'ims_wire_account_number'			=> $wire_account_number,
				);

				if ( update_option( 'ims_wire_settings', $wire_settings	) ) {
					echo json_encode( array(
						'success'	=> true,
						'message'	=> __( 'All done ', 'inspiry-memberships' ) . '<span class="dashicons dashicons-smiley"></span>'
					) );
				} else {
					echo json_encode( array(
						'success'	=> false,
						'message'	=> __( 'Settings not saved.', 'inspiry-memberships' )
					) );
				}

			} else {
				echo json_encode( array(
					'success'	=> false,
					'message'	=> __( 'Some error occurred while saving settings', 'inspiry-memberships' )
				) );
			}
			die();

		}

	}

endif;
