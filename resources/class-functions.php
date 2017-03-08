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
		 * Single Instance of Class.
		 *
		 * @var 	IMS_Functions
		 * @since 	1.0.0
		 */
		protected static $_instance;

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
		 * Method: Provides a single instance of the class.
		 *
		 * @since 1.0.0
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;

		}

		/**
		 * is_memberships.
		 *
		 * @since 1.0.0
		 */
		public static function is_memberships() {

			// Get settings.
			$plugin_settings 	= get_option( 'ims_basic_settings' );

			if ( empty( $plugin_settings ) ) {
				return false;
			} elseif ( 'on' === $plugin_settings[ 'ims_memberships_enable' ] ) {
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
				if ( 'after' === $currency_position ) {
					$formatted_price 	= esc_html( $price . $currency_settings[ 'ims_currency_symbol' ] );
				} else {
					$formatted_price 	= esc_html( $currency_settings[ 'ims_currency_symbol' ] . $price );
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
			$membership_args	= array(
				'post_type'			=> 'ims_membership',
				'post_status' 		=> 'publish',
				'posts_per_page'	=> -1
			);
			$memberships_query	= new WP_Query( $membership_args );

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
						'featured_prop'	=> $membership_obj->get_featured_properties(),
						'duration'		=> $membership_obj->get_duration(),
						'duration_unit'	=> $membership_obj->get_duration_unit()
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
			if ( ! is_object( $user ) ) {
				return false;
			}

			$user_id 	= $user->ID;

			// Get current membership details.
			$membership_id 			= get_user_meta( $user_id, 'ims_current_membership', true );
			$package_properties		= get_user_meta( $user_id, 'ims_package_properties', true );
			$current_properties 	= get_user_meta( $user_id, 'ims_current_properties', true );
			$package_featured_props	= get_user_meta( $user_id, 'ims_package_featured_props', true );
			$current_featured_props	= get_user_meta( $user_id, 'ims_current_featured_props', true );
			$membership_due_date 	= get_user_meta( $user_id, 'ims_membership_due_date', true );

			if ( ! empty( $membership_id ) ) {

				// Get membership object.
				$membership_id 		= intval( $membership_id );
				$membership_obj 	= ims_get_membership_object( $membership_id );

				$membership_data 	= array(
					'ID'				=> get_the_ID(),
					'title' 			=> get_the_title( $membership_id ),
					'format_price'		=> self::get_formatted_price( $membership_obj->get_price() ),
					'price'				=> $membership_obj->get_price(),
					'properties'		=> $package_properties,
					'current_props'		=> $current_properties,
					'featured_prop'		=> $package_featured_props,
					'current_featured'	=> $current_featured_props,
					'duration'			=> $membership_obj->get_duration(),
					'duration_unit'		=> $membership_obj->get_duration_unit(),
					'due_date'			=> $membership_due_date
				);
				return $membership_data;

			} else {
				return false;
			}

		}

		/**
		 * Display membership selection form.
		 *
		 * @since 1.0.0
		 */
		public static function ims_display_membership_form() {

			$ims_memberships 	= self::ims_get_all_memberships();

			// Get plugin settings.
			$basic_settings		= get_option( 'ims_basic_settings' );
			$stripe_settings	= get_option( 'ims_stripe_settings' );
			$paypal_settings	= get_option( 'ims_paypal_settings' );
			$wire_settings		= get_option( 'ims_wire_settings' );

			// Strip button label.
			$ims_button_label 		= 'Pay with Card';
			if ( ! empty( $stripe_settings[ 'ims_stripe_btn_label' ] ) ) {
				$ims_button_label	= $stripe_settings[ 'ims_stripe_btn_label' ];
			}

			if ( is_array( $ims_memberships ) && ! empty( $ims_memberships ) ) : ?>

				<form action="" method="POST" id="ims_select_membership" class="clearfix">

					<div class="form-option">

						<h4><?php esc_html_e( 'Select Membership', 'inspiry-memberships' ); ?></h4>

						<select name="ims-membership-select" id="ims-membership-select">

							<option selected disabled><?php esc_html_e( 'None', 'inspiry-memberships' ); ?></option>
							<?php foreach ( $ims_memberships as $ims_membership ) : ?>
								<option value="<?php echo esc_attr( $ims_membership[ 'ID' ] ); ?>">
									<?php esc_html_e( $ims_membership[ 'title' ], 'inspiry-memberships' ); ?>
								</option>
							<?php endforeach; ?>

						</select>

						<?php wp_nonce_field( 'membership-select-nonce', 'membership_select_nonce' ); ?>
						<input type="hidden" name="action" value="<?php echo esc_attr( 'ims_subscribe_membership' ); ?>"/>
						<input type="hidden" name="redirect" value="<?php echo esc_url( home_url() ); ?>"/>

						<?php if ( 'on' === $basic_settings[ 'ims_recurring_memberships_enable' ] ) : ?>
							<input type="checkbox" name="ims_recurring" id="ims_recurring" />
							<label for="ims_recurring" id="ims_recurring_label">
								<?php esc_html_e( 'Recurring Membership?', 'inspiry-memberships' ); ?>
							</label>
						<?php endif; ?>

					</div>
					<!-- /.form-option -->

					<div class="ims-membership_loader">
						<img src="<?php echo IMS_BASE_URL; ?>resources/img/ajax-loader.gif">
						<!-- Ajax Loader GIF -->
					</div>
					<!-- /.ims-membership_loader -->

					<?php if ( 'on' === $stripe_settings[ 'ims_stripe_enable' ] ) : ?>
						<div class="ims-button-option ims-stripe-button">
							<a href="#" id="ims-stripe"><?php esc_html_e( $ims_button_label, 'inspiry-memberships' ); ?></a>
						</div>
						<!-- /.form-option ims-stripe-button -->
					<?php endif; ?>

					<?php if ( 'on' === $paypal_settings[ 'ims_paypal_enable' ] ) : ?>
						<?php wp_nonce_field( 'membership-paypal-nonce', 'membership_paypal_nonce' ); ?>
						<div class="ims-button-option ims-paypal-button">
							<a href="#" id="ims-paypal">
								<img src="<?php echo IMS_BASE_URL; ?>resources/img/checkout-paypal.png">
							</a>
						</div>
						<!-- /.form-option ims-paypal-button -->
					<?php endif; ?>

					<?php if ( 'on' === $wire_settings[ 'ims_wire_enable' ] ) : ?>
						<div class="ims-wire-transfer">
							<h4><?php esc_html_e( 'Wire Transfer', 'inspiry-memberships' ); ?></h4>
							<?php
								if ( isset( $wire_settings[ 'ims_wire_transfer_instructions' ] )
									&& ! empty( $wire_settings[ 'ims_wire_transfer_instructions' ] ) ) {
									echo '<p>' . esc_html( $wire_settings[ 'ims_wire_transfer_instructions' ] ) . '</p>';
								}
								if ( isset( $wire_settings[ 'ims_wire_account_name' ] )
									&& ! empty( $wire_settings[ 'ims_wire_account_name' ] ) ) {
									echo '<p>' . __( 'Account Name: ', 'inspiry-memberships' );
									echo esc_html( $wire_settings[ 'ims_wire_account_name' ] ) . '</p>';
								}
								if ( isset( $wire_settings[ 'ims_wire_account_number' ] )
									&& ! empty( $wire_settings[ 'ims_wire_account_number' ] ) ) {
									echo '<p>' . __( 'Account Number: ', 'inspiry-memberships' );
									echo esc_html( $wire_settings[ 'ims_wire_account_number' ] ) . '</p>';
								}
								wp_nonce_field( 'membership-wire-nonce', 'membership_wire_nonce' );
							?>
						</div>
						<!-- /.ims-wire-transfer -->

						<div class="ims-button-option ims-receipt-button">
							<a href="#" id="ims-receipt"><?php esc_html_e( 'Send Receipt', 'inspiry-memberships' ); ?></a>
						</div>
						<!-- /.form-option ims-paypal-button -->
					<?php endif; ?>

					<div class="ims-button-option error"></div>
					<!-- /.ims-button-option error -->

				</form>

				<?php
			endif;

		}

		/**
		 * Method: Displays cancel membership form.
		 *
		 * @since 1.0.0
		 */
		public static function cancel_user_membership_form( $user ) {

			// Get user id.
			if ( is_object( $user ) ) {
				$user_id 	= $user->ID;
			} else {
				return;
			}

			if ( ! empty( $user_id ) ) : ?>

				<form action="" method="POST" id="ims-cancel-user-membership">
					<h4 class="title"><?php esc_html_e( 'Are you sure?', 'inspiry-memberships' ); ?></h4>
					<button class="ims-btn" id="ims-btn-confirm" type="submit"><?php esc_html_e( 'Confirm', 'inspiry-memberships' ); ?></button>
					<button class="ims-btn" id="ims-btn-close" type="button"><?php esc_html_e( 'Cancel', 'inspiry-memberships' ); ?></button>
					<input type="hidden" name="action" value="ims_cancel_user_membership" />
					<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>" />
					<input type="hidden" name="ims_cancel_membership_nonce" value="<?php echo wp_create_nonce( 'ims-cancel-membership-nonce' ); ?>" />
				</form>

				<?php
			endif;

		}

	}

endif;


/**
 * Returns the main instance of IMS_Functions.
 *
 * @since 1.0.0
 */
function IMS_Functions() {
	return IMS_Functions::instance();
}
IMS_Functions();
