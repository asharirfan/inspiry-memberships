<?php
/**
 * Receipt Methods Class
 *
 * Class file for receipt methods used during
 * operations of the plugin.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Receipt_Method.
 *
 * Class for receipt methods used during the
 * operations of the plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Receipt_Method' ) ) :

	class IMS_Receipt_Method {

		/**
		 * Generate receipt for the membership subscription.
		 *
		 * @since 1.0.0
		 */
		public function generate_receipt( $user_id = 0, $membership_id = 0, $vendor = NULL, $payment_id = 0, $recurring = false ) {

			// Bail if user or membership id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) || empty( $payment_id ) ) {
				return false;
			}

			$receipt_args = array(
				'post_author' 	=> $user_id,
				'post_title'	=> __( 'Receipt', 'inspiry-memberships' ),
				'post_status'	=> 'publish',
				'post_type'		=> 'ims_receipt'
			);
			$receipt_id	= wp_insert_post( $receipt_args );

			if ( $receipt_id > 0 ) {
				$receipt_args	= array();
				$receipt_args 	= array(
					'ID'			=> $receipt_id,
					'post_type'		=> 'ims_receipt',
					'post_title'	=> __( 'Receipt ', 'inspiry-memberships' ) . $receipt_id,
				);
				$receipt_id 	= wp_update_post( $receipt_args );

				if ( $receipt_id > 0 ) {

					$receipt 	= get_post( $receipt_id );
					$prefix 	= 'ims_membership_';

					$membership_obj 	= ims_get_membership_object( $membership_id );

					$receipt_type		= __( 'Normal Membership', 'inspiry-memberships' );
					$receipt_type 		= ( ! empty( $recurring ) ) ? __( 'Recurring Membership', 'inspiry-memberships' ) : $receipt_type;

					update_post_meta( $receipt_id, "{$prefix}receipt_id", $receipt_id );
					update_post_meta( $receipt_id, "{$prefix}receipt_for", $receipt_type );
					update_post_meta( $receipt_id, "{$prefix}membership_id", $membership_id );
					update_post_meta( $receipt_id, "{$prefix}price", $membership_obj->get_price() );
					update_post_meta( $receipt_id, "{$prefix}purchase_date", $receipt->post_date );
					update_post_meta( $receipt_id, "{$prefix}user_id", $user_id );
					update_post_meta( $receipt_id, "{$prefix}payment_id", $payment_id );

					// Set vendor.
					if ( ! empty( $vendor ) && 'paypal' == $vendor ) {
						update_post_meta( $receipt_id, "{$prefix}vendor", 'paypal' );
					} elseif ( ! empty( $vendor ) && 'stripe' == $vendor ) {
						update_post_meta( $receipt_id, "{$prefix}vendor", 'stripe' );
					} elseif ( ! empty( $vendor ) && 'wire' == $vendor ) {
						update_post_meta( $receipt_id, "{$prefix}vendor", 'wire' );
					}

					// Updating user receipts.
					$user_receipts 		= get_user_meta( $user_id, 'ims_receipts', true );
					if ( is_string( $user_receipts ) && empty( $user_receipts ) ) {
						$user_receipts 		= array();
						$user_receipts[]	= $receipt_id;
					} elseif ( ! empty( $user_receipts ) && is_array( $user_receipts ) ) {
						$user_receipts[]	= $receipt_id;
					} else {
						$user_receipts 		= explode( ",", $user_receipts );
						$user_receipts 		= array();
						$user_receipts[]	= $receipt_id;
					}
					update_user_meta( $user_id, 'ims_receipts', $user_receipts );

					return $receipt_id;
				}
			}
			return false;
		}

	}

endif;
