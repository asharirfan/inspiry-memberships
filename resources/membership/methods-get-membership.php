<?php
/**
 * Method Get Membership
 *
 * Methods for IMS_Get_Membership.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ims_get_membership_object' ) ) {

	function ims_get_membership_object( $membership_id ) {
		if ( ! empty( $membership_id ) ) {
			return new IMS_Get_Membership( $membership_id );
		}
		return false;
	}

}
