<?php
/**
 * General Payment Class
 *
 * Class file for general payment related functions.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Payment_Handler.
 *
 * Class file for general payment related functions.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Payment_Handler' ) ) :

	class IMS_Payment_Handler {

		/**
		 * Method: Cancel user membership manually.
		 *
		 * @since 1.0.0
		 */
		public function cancel_user_membership_request() {

			if ( isset( $_POST[ 'action' ] )
					&& 'ims_cancel_user_membership' == $_POST[ 'action' ]
					&& wp_verify_nonce( $_POST[ 'ims_cancel_membership_nonce' ], 'ims-cancel-membership-nonce' ) ) {

				// Get user and membership id.
				$user_id		= $_POST[ 'user_id' ];

				// Bail if user id is empty.
				if ( empty( $user_id ) ) {
					return;
				}

				// Get current vendor.
				$vendor = get_user_meta( $user_id, 'ims_current_vendor', true );

				if ( 'stripe' === $vendor ) {

					$ims_stripe_payment_handler = new IMS_Stripe_Payment_Handler();
					$ims_stripe_payment_handler->cancel_stripe_membership( $user_id );

				} elseif ( 'paypal' === $vendor ) {

					$ims_paypal_payment_handler = new IMS_PayPal_Payment_Handler();
					$ims_paypal_payment_handler->cancel_paypal_membership( $user_id );

				}

				// $this->cancel_user_membership_manual( $user_id );

			}

		}

	}

endif;
