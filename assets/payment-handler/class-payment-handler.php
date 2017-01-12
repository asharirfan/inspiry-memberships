<?php
/**
 * Payment Handling Class
 *
 * Class for handling payment functions.
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
 * Class for handling payment functions.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Payment_Handler' ) ) :

	class IMS_Payment_Handler {

		/**
		 * Stripe Secret Key.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 protected $secret_key;

		/**
		 * $currency_code.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 protected $currency_code;

		/**
		 * Stripe Token.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 protected $stripe_token;

		/**
		 * Customer Details Array.
		 *
		 * @var 	array
		 * @since 	1.0.0
		 */
		 protected $customer_details;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->set_stripe_variables();

			// Require Stripe library.
			include( IMS_BASE_DIR . '/assets/stripe/stripe-init.php' );

		}

		/**
		 * set_variables.
		 *
		 * @since 1.0.0
		 */
		public function set_stripe_variables() {

			// Set customer details.
			$this->customer_details	= array(
				'recurring'	=> '',
				'user_id'	=> '',
				'email' 	=> '',
				'name' 		=> '',
				'address'	=> '',
				'zip' 		=> '',
				'city' 		=> '',
				'state' 	=> '',
				'country' 	=> ''
			);

		}

		/**
		 * Check test mode of Stripe.
		 *
		 * @since 1.0.0
		 */
		public function stripe_routine_checks() {

			// Get basic settings.
			$basic_settings 		= get_option( 'ims_basic_settings' );

			// Get stripe settings.
			$stripe_settings 		= get_option( 'ims_stripe_settings' );

			// Check if we are using test mode.
			if ( isset( $stripe_settings[ 'ims_test_mode' ] ) && ! empty( $stripe_settings[ 'ims_test_mode' ] ) ) {

				if ( isset( $stripe_settings[ 'ims_test_secret' ] ) && ! empty( $stripe_settings[ 'ims_test_secret' ] ) ) {
					$this->secret_key 	= $stripe_settings[ 'ims_test_secret' ];
				}

			} elseif ( ! isset( $stripe_settings[ 'ims_test_mode' ] ) && empty( $stripe_settings[ 'ims_test_mode' ] ) ) {

				if ( isset( $stripe_settings[ 'ims_live_secret' ] ) && ! empty( $stripe_settings[ 'ims_live_secret' ] ) ) {
					$this->secret_key 	= $stripe_settings[ 'ims_live_secret' ];
				}

			}

			// Set currency code.
			if ( isset( $basic_settings[ 'ims_currency_code' ] ) && ! empty( $basic_settings[ 'ims_currency_code' ] ) ) {
				$this->currency_code 	= $basic_settings[ 'ims_currency_code' ];
			} else {
				$this->currency_code 	= 'USD';
			}

		}

		/**
		 * This function starts processing stripe payment.
		 *
		 * @since 1.0.0
		 */
		public function process_stripe_payment() {

			if ( isset( $_POST[ 'action' ] )
					&& 'ims_stripe_membership_payment' == $_POST[ 'action' ]
					&& wp_verify_nonce( $_POST[ 'ims_stripe_nonce' ], 'ims-stripe-nonce' ) ) {

				$this->stripe_routine_checks();

				// Get membership details.
				$membership_id 		= intval( $_POST[ 'membership_id' ] );
				$membership_price 	= floatval( $_POST[ 'membership_price' ] );

				// Get current user.
				$user 		= wp_get_current_user();
				$user_id 	= $user->ID;

				// Get stripe token.
				$this->stripe_token	= $_POST[ 'stripeToken' ];

				// Customer Details
				$recurring 	= $_POST[ 'ims_recurring' ];
				$email 		= $_POST[ 'stripeEmail' ];
				$name 		= $_POST[ 'stripeBillingName' ];
				$address 	= $_POST[ 'stripeBillingAddressLine1' ];
				$zip 		= $_POST[ 'stripeBillingAddressZip' ];
				$city 		= $_POST[ 'stripeBillingAddressCity' ];
				$state 		= $_POST[ 'stripeBillingAddressState' ];
				$country 	= $_POST[ 'stripeBillingAddressCountry' ];

				$this->customer_details['recurring']	= ( ! empty( $recurring ) ) ? $recurring : false;
				$this->customer_details['email'] 		= ( is_email( $email ) ) ? $email : false;
				$this->customer_details['name'] 		= ( ! empty( $name )  ) ? sanitize_text_field( $name ) : false;
				$this->customer_details['address'] 		= ( ! empty( $address ) ) ? sanitize_text_field( $address ) : false;
				$this->customer_details['zip'] 			= ( ! empty( $zip ) ) ? sanitize_text_field( $zip ) : false;
				$this->customer_details['city'] 		= ( ! empty( $city )  ) ? sanitize_text_field( $city ) : false;
				$this->customer_details['state'] 		= ( ! empty( $state )  ) ? sanitize_text_field( $state ) : false;
				$this->customer_details['country'] 		= ( ! empty( $country )  ) ? sanitize_text_field( $country ) : false;

				/**
				 * Filter the values of $customer_details array
				 * for membership payment to extend its values.
				 */
				$this->customer_details 	= apply_filters( 'ims_membership_customer_details', $this->customer_details );

				// Charge the card using stripe.
				$this->stripe_charge( $user_id, $membership_id, $membership_price );

			}

		}

		/**
		 * This function creates charge for stripe.
		 *
		 * @since 1.0.0
		 */
		public function stripe_charge( $user_id = 0, $membership_id = 0, $membership_price = 0 ) {

			if ( ! empty( $membership_id ) && ! empty( $this->stripe_token ) && ! empty( $user_id ) ) {

				try {

					\Stripe\Stripe::setApiKey( $this->secret_key );
					$ims_stripe_membership_charge = \Stripe\Charge::create( array(
						'amount'	=> $membership_price,
						'currency'	=> $this->currency_code,
						'source'	=> $this->stripe_token
					) );

					$this->add_membership_subscription( $user_id, $membership_id );
					$receipt_id 	= $this->generate_receipt( $user_id, $membership_id );
					$this->schedule_end_membership( $user_id, $membership_id, $receipt_id );
					$this->mail_user( $user_id, $membership_id, $receipt_id );
					$this->mail_admin( $membership_id, $receipt_id );

				} catch ( Exception $e ) {

					// Redirect on empty token or membership id.
					$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

					// Redirect back to our previous page with the added query variable.
					wp_redirect( $redirect );
					exit;

				}

			} else {

				// Redirect on empty token or membership id.
				$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

				// Redirect back to our previous page with the added query variable.
				wp_redirect( $redirect );
				exit;

			}

		}

		/**
		 * Add membership subscription details to user meta.
		 *
		 * @since 1.0.0
		 */
		public function add_membership_subscription( $user_id = 0, $membership_id = 0 ) {

			// Bail if membership or user id is empty.
			if ( empty( $membership_id ) || empty( $user_id ) ) {
				return;
			}

			// Get membership object.
			$membership_obj		= ims_get_membership_object( $membership_id );

			// Get current membership of user.
			$current_membership	= get_user_meta( $user_id, 'ims_current_membership', true );

			if ( empty( $current_membership ) ) {

				// Add membership id to user meta.
				update_user_meta( $user_id, 'ims_current_membership', $membership_id );

				// Add number of properties available.
				update_user_meta( $user_id, 'ims_package_properties', $membership_obj->get_properties() );
				update_user_meta( $user_id, 'ims_current_properties', $membership_obj->get_properties() );
				update_user_meta( $user_id, 'ims_package_featured_props', $membership_obj->get_featured_properties() );
				update_user_meta( $user_id, 'ims_current_featured_props', $membership_obj->get_featured_properties() );
				update_user_meta( $user_id, 'ims_current_duration', $membership_obj->get_duration() );
				update_user_meta( $user_id, 'ims_current_duration_unit', $membership_obj->get_duration_unit() );
				update_user_meta( $user_id, 'ims_current_stripe_plan_id', $membership_obj->get_stripe_plan_id() );

			} else {
				// Update membership package if there is another package present before.
				$this->update_membership_subscription( $user_id, $membership_id );
			}

		}

		/**
		 * Update membership subscription of user.
		 *
		 * @since 1.0.0
		 */
		public function update_membership_subscription( $user_id = 0, $membership_id = 0 ) {

			// Bail if membership or user id is empty.
			if ( empty( $membership_id ) || empty( $user_id ) ) {
				return;
			}

			// Get current membership details.
			$current_membership 		= get_user_meta( $user_id, 'ims_current_membership', true );
			$current_membership_id 		= intval( $current_membership ); // Current Membership ID.

			$current_membership_obj	= ims_get_membership_object( $current_membership_id ); // Current Membership Object.

			$current_properties 	= get_user_meta( $user_id, 'ims_current_properties', true );
			$current_properties 	= intval( $current_properties ); // Current Properties.

			$current_featured_props	= get_user_meta( $user_id, 'ims_current_featured_props', true );
			$current_featured_props	= intval( $current_properties ); // Current Featured Properties.

			// Get new membership details.
			$new_membership_id 	= intval( $membership_id ); // New Membership ID.
			$new_membership_obj	= ims_get_membership_object( $new_membership_id ); // Current Membership Object.
			$new_properties 	= $new_membership_obj->get_properties(); // Current Properties.
			$new_featured_props	= $new_membership_obj->get_featured_properties(); // Current Featured Properties.

			if ( ! empty( $new_properties ) && ! empty( $new_featured_props ) ) {

				// Update membership id to user meta.
				update_user_meta( $user_id, 'ims_current_membership', $new_membership_id );

				// Update number of properties available.
				update_user_meta( $user_id, 'ims_package_properties', $new_properties );
				update_user_meta( $user_id, 'ims_current_properties', $new_properties 	);
				update_user_meta( $user_id, 'ims_package_featured_props', $new_featured_props );
				update_user_meta( $user_id, 'ims_current_featured_props', $new_featured_props );
				update_user_meta( $user_id, 'ims_current_duration', $new_membership_obj->get_duration() );
				update_user_meta( $user_id, 'ims_current_duration_unit', $new_membership_obj->get_duration_unit() );
				update_user_meta( $user_id, 'ims_current_stripe_plan_id', $new_membership_obj->get_stripe_plan_id() );

				/**
				 * The WordPress Query class.
				 * @link http://codex.wordpress.org/Function_Reference/WP_Query
				 *
				 */
				$properties_args		= array(
					'author'      		=> $user_id, // Author Parameters
					'post_type'   		=> 'property', // Type & Status Parameters
					'post_status' 		=> 'publish',
					'posts_per_page'	=> -1 // Pagination Parameters
				);

				$properties 	= get_posts( $properties_args );
				if ( ! empty( $properties ) ) {
					foreach ( $properties as $property ) {
						$property_args 		= array(
							'ID' 			=> $property->ID,
							'post_status'	=> 'draft'
						);
						wp_update_post( $property_args );
					}
				}

			}

		}

		/**
		 * Generate receipt for the membership subscription.
		 *
		 * @since 1.0.0
		 */
		public function generate_receipt( $user_id = 0, $membership_id = 0 ) {

			// Bail if user or membership id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) ) {
				return;
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

					update_post_meta( $receipt_id, "{$prefix}receipt_id", $receipt_id );
					update_post_meta( $receipt_id, "{$prefix}receipt_for", __( 'Normal', 'inspiry-memberships' ) );
					update_post_meta( $receipt_id, "{$prefix}membership_id", $membership_id );
					update_post_meta( $receipt_id, "{$prefix}price", $membership_obj->get_price() );
					update_post_meta( $receipt_id, "{$prefix}purchase_date", $receipt->post_date );
					update_post_meta( $receipt_id, "{$prefix}user_id", $user_id );
					update_post_meta( $receipt_id, "{$prefix}vendor", 'stripe' );

					$user_receipts 		= get_user_meta( $user_id, 'ims_receipts', false );
					$user_receipts[]	= $receipt_id;
					update_user_meta( $user_id, 'ims_receipts', $user_receipts );

					return $receipt_id;
				}
			}
			return false;
		}

		/**
		 * Mail User to notify about membership purchase.
		 *
		 * @since 1.0.0
		 */
		public function mail_user( $user_id = 0, $membership_id = 0, $receipt_id = 0 ) {

			// Bail if user, membership or receipt id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) || empty( $receipt_id ) ) {
				return;
			}

			// Get user.
			$user	= get_user_by( 'id', $user_id );
			if ( ! empty( $user ) ) {
				$user_email	= $user->user_email;
			}

			$membership = get_post( $membership_id );

			$subject	= __( 'Membership Purchased.', 'inspiry-memberships' );

			$message 	= sprintf( __( 'You have successfully purchased %s membership package on our site.', 'inspiry-memberships' ), $membership->post_title ) . "<br/><br/>";
			$message 	.= __( 'Your payment has been received successfully via Stripe.', 'inspiry-memberships' ) . "<br/><br/>";
			$message 	.= __( 'To view the details, please visit your profile page on the website.', 'inspiry-memberships' );

			if ( is_email( $user_email ) ) {
				IMS_Email::send_email( $user_email, $subject, $message );
			}

		}

		/**
		 * Mail Admin to notify about membership purchase.
		 *
		 * @since 1.0.0
		 */
		public function mail_admin( $membership_id = 0, $receipt_id = 0 ) {

			// Bail if membership or receipt id is empty.
			if ( empty( $membership_id ) || empty( $receipt_id ) ) {
				return;
			}

			// Get admin email.
			$admin_email 	= get_bloginfo( 'admin_email' );

			// Get receipt edit link.
			$receipt_link 	= get_edit_post_link( $receipt_id );
			$receipt 		= get_post( $receipt_id );

			$membership 	= get_post( $membership_id );

			$subject		= __( 'Membership Purchased.', 'inspiry-memberships' );

			$message 	= sprintf( __( 'A user successfully purchased %s membership package on your site.', 'inspiry-memberships' ), $membership->post_title ) . "<br/><br/>";
			$message 	.= __( 'Payment has been submitted via Stripe.', 'inspiry-memberships' ) . "<br/><br/>";
			$message 	.= __( 'To view the details, please visit : ', 'inspiry-memberships' );
			$message 	.= '<a target="_blank" href="' . $receipt_link . '">' . $receipt->post_title . '</a>';

			if ( is_email( $admin_email ) ) {
				IMS_Email::send_email( $admin_email, $subject, $message );
			}

		}

		/**
		 * This function is used to schedule the end of
		 * non-recurring membership.
		 *
		 * @since 1.0.0
		 */
		public function schedule_end_membership( $user_id = 0, $membership_id = 0, $receipt_id = 0 ) {

			// Bail if user, membership or receipt id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) || empty( $receipt_id ) ) {
				return;
			}

			$membership_obj 	= ims_get_membership_object( $membership_id );
			$time_duration		= $membership_obj->get_duration();
			$time_unit			= $membership_obj->get_duration_unit();

			if ( 'days' == $time_unit ) {
				$seconds		= 24 * 60 * 60;
			} elseif ( 'months' == $time_unit ) {
				$seconds 		= 30 * 24 * 60 * 60;
			} elseif ( 'years' == $time_unit ) {
				$seconds 		= 365 * 24 * 60 * 60;
			}

			$time_duration		= $time_duration * $seconds;

			$schedule_args		= array( $user_id, $membership_id, $receipt_id );

			/**
			 * Schedule the event
			 *
			 * @param int - unix timestamp of when to run the event
			 * @param string - action to fire at the timestamp
			 */
			wp_schedule_single_event( time() + $time_duration, 'ims_schedule_membership_end', $schedule_args );

			/**
			 * Action to run event on
			 * Doesn't need to be an existing WordPress action
			 *
			 * @param string - name of action
			 * @param string - name of function to run on this action
			 */
			add_action( 'ims_schedule_membership_end', array( $this, 'ims_schedule_membership_end_function' ), 10, 3 );

		}

		/**
		 * Function to be called when ims_schedule_membership_end event is fired
		 *
		 * @since 1.0.0
		 */
		public function ims_schedule_membership_end_function( $user_id, $membership_id, $receipt_id ) {

			// Bail if user, membership or receipt id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) || empty( $receipt_id ) ) {
				return;
			}

			// Delete membership details from user meta.
			delete_user_meta( $user_id, 'ims_current_membership' );

			// Add number of properties available.
			delete_user_meta( $user_id, 'ims_package_properties' );
			delete_user_meta( $user_id, 'ims_current_properties' );
			delete_user_meta( $user_id, 'ims_package_featured_props' );
			delete_user_meta( $user_id, 'ims_current_featured_props' );
			delete_user_meta( $user_id, 'ims_current_duration' );
			delete_user_meta( $user_id, 'ims_current_duration_unit' );
			delete_user_meta( $user_id, 'ims_current_stripe_plan_id' );

		}

	}

endif;
