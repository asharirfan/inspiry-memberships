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

				// Bail if user id is empty.
				if ( ! isset( $_POST[ 'user_id' ] ) || empty( $_POST[ 'user_id' ] ) ) {
					return;
				}

				$user_id	= intval( $_POST[ 'user_id' ] );

				// Get current vendor.
				$vendor	= get_user_meta( $user_id, 'ims_current_vendor', true );

				if ( 'stripe' === $vendor ) {

					IMS_Stripe_Payment_Handler::cancel_stripe_membership( $user_id );

				} elseif ( 'paypal' === $vendor ) {

					$ims_paypal_payment_handler = new IMS_PayPal_Payment_Handler();
					$ims_paypal_payment_handler->cancel_paypal_membership( $user_id );

				} elseif ( 'wire' === $vendor ) {

					IMS_Wire_Transfer_Handler::cancel_wire_membership( $user_id );

				}

			}

		}

		/**
		 * Method: Handle free membership request.
		 *
		 * @since 1.0.0
		 */
		public function subscribe_free_membership() {

			if ( isset( $_POST[ 'action' ] )
					&& 'ims_subscribe_membership' == $_POST[ 'action' ]
					&& wp_verify_nonce( $_POST[ 'membership_select_nonce' ], 'membership-select-nonce' ) ) {

				// Bail if membership id is empty.
				if ( ! isset( $_POST[ 'ims-membership-select' ] ) || empty( $_POST[ 'ims-membership-select' ] ) ) {
					return;
				}

				$membership_id	= intval( $_POST[ 'ims-membership-select' ] );

				// Get current user.
				$user 		= wp_get_current_user();
				$user_id	= $user->ID;
				$user_email	= $user->user_email;

				$membership_methods	= new IMS_Membership_Method();
				$receipt_methods 	= new IMS_Receipt_Method();

				// Add membership.
				$membership_methods->add_user_membership( $user_id, $membership_id, 'wire' );

				// Generate receipt.
				$receipt_id	= $receipt_methods->generate_wire_transfer_receipt( $user_id, $membership_id, false );

				if ( ! empty( $receipt_id ) ) {

					// Mail the users.
					$membership_methods->mail_user( $user_id, $membership_id, 'wire' );
					$membership_methods->mail_admin( $membership_id, $receipt_id, 'wire' );

					// Update receipt meta.
					$prefix	= apply_filters( 'ims_receipt_meta_prefix', 'ims_receipt_' );

					update_post_meta( $receipt_id, "{$prefix}status", true );

				}

				$redirect_url 	= add_query_arg( array( 'membership' => 'purchased' ), esc_url( get_bloginfo( 'url' ) ) );
				$redirect_url	= apply_filters( 'ims_membership_success_redirect', $redirect_url );
				wp_redirect( $redirect_url );
				die();

			}

		}

	}

endif;
