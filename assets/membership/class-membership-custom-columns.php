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

			$columns = array(
	            'cb' 			=> "<input type=\"checkbox\" />",
	            'title' 		=> __( 'Membership Title', 'inspiry-stripe' ),
	            'properties'	=> __( 'Properties Allowed', 'inspiry-stripe' ),
	            'featured' 		=> __( 'Featured Properties', 'inspiry-stripe' ),
	            'price' 		=> __('Price', 'inspiry-stripe')
	        );

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

			switch ( $column ) {
				case 'properties':
					$properties = get_post_meta( $post->ID, 'ims_allowed_properties', true );
					if ( ! empty( $properties ) ) {
						echo esc_html( $properties );
					} else {
						_e( 'Not Available', 'inspiry-stripe' );
					}
					break;

				case 'featured':
					$featured = get_post_meta( $post->ID, 'ims_featured_properties', true );
					if ( ! empty( $featured ) ) {
						echo esc_html( $featured );
					} else {
						_e( 'Not Available', 'inspiry-stripe' );
					}
					break;

				case 'price':
					$price = get_post_meta( $post->ID, 'ims_price', true );
					if ( ! empty( $price ) ) {
						echo esc_html( $price );
					} else {
						_e( 'Not Available', 'inspiry-stripe' );
					}
					break;
			}

		}

		/**
		 * sortable_price.
		 *
		 * @since 1.0.0
		 */
		public function sortable_price( $columns ) {

			$columns['properties']	= 'properties';
			$columns['featured'] 	= 'featured';
			$columns['price'] 		= 'price';
			return $columns;

		}

	}

endif;
