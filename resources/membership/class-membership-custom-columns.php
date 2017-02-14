<?php
/**
 * Custom Columns for `Membership` Post Type
 *
 * Creates and manages custom columns for `Membership` post type.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Membership_Custom_Columns.
 *
 * This class creates and manages custom columns for `Membership` post type.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Membership_Custom_Columns' ) ) :

	class IMS_Membership_Custom_Columns {

		/**
		 * register_columns.
		 *
		 * @since 1.0.0
		 */
		public function register_columns( $columns ) {

			$columns = apply_filters( 'ims_membership_custom_column_name', array(
	            'cb' 			=> "<input type=\"checkbox\" />",
	            'title' 		=> __( 'Membership Title', 'inspiry-memberships' ),
	            'properties'	=> __( 'Allowed Properties', 'inspiry-memberships' ),
	            'featured' 		=> __( 'Featured Properties', 'inspiry-memberships' ),
	            'price' 		=> __( 'Price', 'inspiry-memberships' ),
	            'duration' 		=> __( 'Billing Period', 'inspiry-memberships' )
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
			$prefix = apply_filters( 'ims_membership_meta_prefix', 'ims_membership_' );

			switch ( $column ) {

				case 'properties':
					$properties = get_post_meta( $post->ID, "{$prefix}allowed_properties", true );
					if ( ! empty( $properties ) ) {
						echo esc_html( $properties );
					} else {
						esc_html_e( 'Not Available', 'inspiry-memberships' );
					}
					break;

				case 'featured':
					$featured = get_post_meta( $post->ID, "{$prefix}featured_properties", true );
					if ( ! empty( $featured ) ) {
						echo esc_html( $featured );
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

				case 'duration':
					$duration 		= get_post_meta( $post->ID, "{$prefix}duration", true );
					$duration_unit	= get_post_meta( $post->ID, "{$prefix}duration_unit", true );
					if ( ! empty( $duration ) && ( $duration > 1 ) ) {
						echo esc_html( $duration . ' ' . $duration_unit );
					} elseif ( ! empty( $duration ) && ( $duration == 1 ) ) {
						echo esc_html( $duration . ' ' . rtrim( $duration_unit, "s" ) );
					} else {
						esc_html_e( 'Not Available', 'inspiry-memberships' );
					}
					break;

				default:
					break;

			}

		}

	}

endif;
