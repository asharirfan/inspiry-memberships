<?php
/**
 * Method: Get Receipt
 *
 * Methods for IMS_Get_Receipt.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ims_get_receipt_object' ) ) {

	function ims_get_receipt_object( $receipt_id ) {
		if ( ! empty( $receipt_id ) ) {
			return new IMS_Get_Receipt( $receipt_id );
		}
		return false;
	}

}
