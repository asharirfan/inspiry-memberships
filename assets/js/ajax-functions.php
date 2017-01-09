<?php
/**
 * Ajax Functions
 *
 * Ajax functions of the plugin.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ims_display_stripe_button' ) ) {

	/**
	 * ims_display_stripe_button.
	 *
	 * @since 1.0.0
	 */
	function ims_display_stripe_button() {

		// Check if the membership variable is set.
		if ( isset( $_POST[ 'membership' ] ) ) {

			// Set membership id.
			$membership_id = intval( $_POST[ 'membership' ] );

			if ( ! empty( $membership_id ) ) {

				// Get currency code.
				$ims_basic_settings 	= get_option( 'ims_basic_settings' );
				$ims_currency_code 		= $ims_basic_settings[ 'ims_currency_code' ];
				if ( empty( $ims_currency_code ) ) {
					$ims_currency_code 	= 'USD';
				}

				// Strip button label.
				$ims_button_label 		= 'Pay with Card';

				// Get Stripe settings.
				$ims_stripe_settings 	= get_option( 'ims_stripe_settings' );

				// Check if we are using test mode.
				if ( isset( $ims_stripe_settings[ 'ims_test_mode' ] ) && $ims_stripe_settings[ 'ims_test_mode' ] ) {
					$ims_publishable_key	= $ims_stripe_settings[ 'ims_test_publishable' ];
				} else {
					$ims_publishable_key 	= $ims_stripe_settings[ 'ims_live_publishable' ];
				}

				$membership_obj 	= ims_get_membership_object( $membership_id );

				echo json_encode( array(
					'success'			=> true,
					'blog_name'			=> get_bloginfo( 'name' ),
					'desc'				=> __( 'Membership Payment', 'inspiry-stripe' ),
					'ID'				=> get_the_ID(),
					'membership' 		=> get_the_title( $membership_id ),
					'membership_id'		=> $membership_id,
					'price'				=> $membership_obj->get_price() * 100,
					'publishable_key'	=> $ims_publishable_key,
					'currency_code'		=> $ims_currency_code,
					'button_label'		=> $ims_button_label,
					'payment_nonce'		=> wp_create_nonce( 'ims-stripe-nonce' )
				) );
			} else {
				echo json_encode( array (
					'success'		=> false,
					'message'		=> __( 'Membership ID is empty.', 'inspiry-memberships' )
				) );
			}

		} else {
			echo json_encode( array (
				'success'		=> false,
				'message'		=> __( 'Membership ID is not valid.', 'inspiry-memberships' )
			) );
		}
		die();

	}

	add_action( 'wp_ajax_ims_stripe_button', 'ims_display_stripe_button' );
}
