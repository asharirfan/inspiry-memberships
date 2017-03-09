<?php
/**
 * Custom Columns for `Receipt` Post Type
 *
 * Creates and manages custom columns for `Receipt` post type.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Receipt_Custom_Columns.
 *
 * This class creates and manages custom columns for `Receipt` post type.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Receipt_Custom_Columns' ) ) :

	class IMS_Receipt_Custom_Columns {

		/**
		 * register_columns.
		 *
		 * @since 1.0.0
		 */
		public function register_columns( $columns ) {

			$columns = apply_filters( 'ims_receipt_custom_column_names', array(
	            'cb' 			=> "<input type=\"checkbox\" />",
	            'title' 		=> __( 'Receipt', 'inspiry-memberships' ),
	            'receipt_for'	=> __( 'Receipt For', 'inspiry-memberships' ),
	            'membership'	=> __( 'Membership', 'inspiry-memberships' ),
	            'price'			=> __( 'Price', 'inspiry-memberships' ),
	            'user_id' 		=> __( 'User', 'inspiry-memberships' ),
	            'vendor' 		=> __( 'Vendor', 'inspiry-memberships' ),
	            'purchase_date'	=> __( 'Date of Purchase', 'inspiry-memberships' )
	        ) );

	        /**
		     * Reverse the array for RTL
		     */
	        if ( is_rtl() ) {
	            $columns = array_reverse( $columns );
	        }

	        return $columns;

		}

		/**
		 * display_column_values.
		 *
		 * @since 1.0.0
		 */
		public function display_column_values( $column ) {

			global $post;

			// Meta data prefix
			$prefix = apply_filters( 'ims_receipt_meta_prefix', 'ims_receipt_' );

			switch ( $column ) {

				case 'receipt_for':
					$receipt_for = get_post_meta( $post->ID, "{$prefix}receipt_for", true );
					if ( ! empty( $receipt_for ) ) {
						echo esc_html( $receipt_for );
					} else {
						esc_html_e( 'Not Available', 'inspiry-memberships' );
					}
					break;

				case 'membership':
					$membership_id 		= get_post_meta( $post->ID, "{$prefix}membership_id", true );
					$membership_obj 	= get_post( $membership_id );
					$membership_title 	= $membership_obj->post_title;
					if ( ! empty( $membership_title ) ) {
						echo '<a href="' . esc_url( get_edit_post_link( $membership_id ) ) . '">' . esc_html( $membership_title ) . '</a>';
					} else {
						esc_html_e( 'Not Available', 'inspiry-memberships' );
					}
					break;

				case 'price':
					$currency_settings 	= get_option( 'ims_basic_settings' );
					$price 				= get_post_meta( $post->ID, "{$prefix}price", true );
					$currency_position	= $currency_settings[ 'ims_currency_position' ];
					$formatted_price 	= '';
					if ( 'after' == $currency_position ) {
						$formatted_price 	= $price . $currency_settings[ 'ims_currency_symbol' ];
					} else {
						$formatted_price 	= $currency_settings[ 'ims_currency_symbol' ] . $price;
					}
					if ( ! empty( $price ) ) {
						echo esc_html( $formatted_price );
					} else {
						esc_html_e( 'Not Available', 'inspiry-memberships' );
					}
					break;

				case 'user_id':
					$user_id 	= intval( get_post_meta( $post->ID, "{$prefix}user_id", true ) );
					$user 		= get_user_by( 'id', $user_id );
					if ( ! empty( $user ) ) {
						$user_name 	= $user->user_login;
					}
					if ( ! empty( $user_name ) ) {
						echo '<a href="' . esc_url( get_edit_user_link( $user_id ) ) . '">' . esc_html( $user_name ) . '</a>';
					} else {
						esc_html_e( 'Not Available', 'inspiry-memberships' );
					}
					break;

				case 'vendor':
					$vendor = get_post_meta( $post->ID, "{$prefix}vendor", true );
					if ( ! empty( $vendor ) && ( 'stripe' == $vendor ) ) {
						esc_html_e( 'Stripe', 'inspiry-memberships' );
					} elseif ( ! empty( $vendor ) && ( 'paypal' == $vendor ) ) {
						esc_html_e( 'PayPal', 'inspiry-memberships' );
					} elseif ( ! empty( $vendor ) && ( 'wire' == $vendor ) ) {
						esc_html_e( 'Wire Transfer', 'inspiry-memberships' );
					} else {
						esc_html_e( 'Not Available', 'inspiry-memberships' );
					}
					break;

				case 'purchase_date':
					$purchase_date = get_post_meta( $post->ID, "{$prefix}purchase_date", true );
					if ( ! empty( $purchase_date ) ) {
						echo esc_html( $purchase_date );
					} else {
						esc_html_e( 'Not Available', 'inspiry-memberships' );
					}
					break;

				default :
					break;

			}

		}

	}

endif;
