<?php
/**
 * Functions Class
 *
 * Class for general plugin functions.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Functions.
 *
 * Class for general plugin functions.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Functions' ) ) :

	class IMS_Functions {

		/**
		 * $basic_settings.
		 *
		 * @var 	array
		 * @since 	1.0.0
		 */
		public $basic_settings;

		/**
		 * $stripe_settings.
		 *
		 * @var 	array
		 * @since 	1.0.0
		 */
		 public $stripe_settings;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->basic_settings  	= get_option( 'ims_basic_settings' );
			$this->stripe_settings 	= get_option( 'ims_stripe_settings' );

		}

		/**
		 * is_memberships.
		 *
		 * @since 1.0.0
		 */
		public function is_memberships() {

			if ( 'on' == $this->basic_settings[ 'ims_memberships_enable' ] ) {
				return true;
			}
			return false;

		}

		/**
		 * get_formatted_price.
		 *
		 * @since 1.0.0
		 */
		public static function get_formatted_price( $price ) {

			// Get settings.
			$currency_settings 	= get_option( 'ims_basic_settings' );
			$currency_position	= $currency_settings[ 'ims_currency_position' ]; // Currency Symbol Position.
			$formatted_price 	= '';

			if ( ! empty( $price ) ) {
				if ( 'after' == $currency_position ) {
					$formatted_price 	= $price . $currency_settings[ 'ims_currency_symbol' ];
				} else {
					$formatted_price 	= $currency_settings[ 'ims_currency_symbol' ] . $price;
				}
			} else {
				return __( 'Price not available', 'inspiry-memberships' );
			}
			return $formatted_price;

		}

		/**
		 * Get all memberships.
		 *
		 * @return array Array of Memberships data.
		 * @since 1.0.0
		 */
		public static function ims_get_all_memberships() {

			/**
			 * The WordPress Query class.
			 * @link http://codex.wordpress.org/Function_Reference/WP_Query
			 *
			 */
			$membership_args = array(
				'post_type'=> 'ims_membership',
				'post_status' => 'publish',
				'posts_per_page'=> -1
			);
			$memberships_query = new WP_Query( $membership_args );

			// Membership Data array.
			$memberships_data 	= array();

			if ( $memberships_query->have_posts() ) {
				while ( $memberships_query->have_posts() ) {
					$memberships_query->the_post();
					$membership_obj 	= ims_get_membership_object( get_the_ID() );

					// Memberships data.
					$memberships_data[]	= array(
						'ID'			=> get_the_ID(),
						'title' 		=> get_the_title(),
						'format_price'	=> self::get_formatted_price( $membership_obj->get_price() ),
						'price'			=> $membership_obj->get_price(),
						'properties'	=> $membership_obj->get_properties(),
						'featured_prop'	=> $membership_obj->get_featured_properties()
					);
				}

				return $memberships_data;
			} else {
				return false;
			}

		}

		/**
		 * Get membership by user.
		 *
		 * @since 1.0.0
		 */
		public static function ims_get_membership_by_user( $user ) {

			// Get user id.
			if ( is_object( $user ) ) {
				$user_id 	= $user->ID;
			}

			// Get membership id.
			$membership_id 	= get_user_meta( $user_id, 'ims_current_membership', true );

			// Membership Data array.
			$membership_data 	= array();

			if ( ! empty( $membership_id ) ) {
				// Get membership object.
				$membership_id 		= intval( $membership_id );
				$membership_obj 	= ims_get_membership_object( $membership_id );

				$membership_data 	= array(
					'ID'			=> get_the_ID(),
					'title' 		=> get_the_title( $membership_id ),
					'format_price'	=> self::get_formatted_price( $membership_obj->get_price() ),
					'price'			=> $membership_obj->get_price(),
					'properties'	=> $membership_obj->get_properties(),
					'featured_prop'	=> $membership_obj->get_featured_properties()
				);
			} else {
				return false;
			}

			return $membership_data;

		}

		/**
		 * Display Stripe Buy button form memberships.
		 *
		 * @since 1.0.0
		 */
		public static function ims_display_stripe_form( $membership ) {

			// Bail if $membership is not an array.
			if ( ! is_array( $membership ) ) {
				return;
			}

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

			?>

				<script
					src="https://checkout.stripe.com/checkout.js" class="stripe-button"
					data-key="<?php echo esc_attr( $ims_publishable_key ); ?>"
					data-amount="<?php echo esc_attr( $membership[ 'price' ] ); ?>"
					data-name="<?php echo get_bloginfo( 'name' ); ?>"
					data-currency="<?php echo esc_attr( $ims_currency_code ); ?>"
					data-description="<?php _e( 'Membership Payment', 'inspiry-stripe' ); ?>"
					data-locale="auto"
					data-billing-address="true"
					data-label="<?php _e( $ims_button_label, 'inspiry-stripe' ); ?>">
				</script>

			<?php

		}

	}

endif;


if ( ! function_exists( 'ims_functions_obj' ) ) {

	/**
	 * Get an object of IMS_Functions.
	 *
	 * @since 1.0.0
	 */
	function ims_functions_obj() {
		return new IMS_Functions();
	}

}
