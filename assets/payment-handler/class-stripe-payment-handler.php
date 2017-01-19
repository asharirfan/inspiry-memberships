<?php
/**
 * Payment Handling Class for Stripe
 *
 * Class for handling payment functions for stripe.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Stripe_Payment_Handler.
 *
 * Class for handling payment functions.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Stripe_Payment_Handler' ) ) :

	class IMS_Stripe_Payment_Handler {

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

			$this->set_variables();

			// Require Stripe library.
			include( IMS_BASE_DIR . '/assets/stripe/stripe-init.php' );

		}

		/**
		 * Method: Set the customer details variable.
		 *
		 * @since 1.0.0
		 */
		public function set_variables() {

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
		 * Method: Perform routine checks to keep the plugin
		 * updated about the latest user settings.
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
		 * Method: Starting point of Stripe Payment processing.
		 *
		 * @since 1.0.0
		 */
		public function process_stripe_payment() {

			if ( isset( $_POST[ 'action' ] )
					&& 'ims_stripe_membership_payment' == $_POST[ 'action' ]
					&& wp_verify_nonce( $_POST[ 'ims_stripe_nonce' ], 'ims-stripe-nonce' ) ) {

				$this->stripe_routine_checks();

				// Get recurring payment check.
				$is_recurring 		= ( isset( $_POST[ 'ims_recurring' ] ) ) ? $_POST[ 'ims_recurring' ]: false;

				// Get membership details.
				$membership_id 		= intval( $_POST[ 'membership_id' ] );
				$membership_price 	= floatval( $_POST[ 'membership_price' ] );

				// Get current user.
				$user 		= wp_get_current_user();
				$user_id 	= $user->ID;

				// Get stripe token.
				$this->stripe_token	= $_POST[ 'stripeToken' ];

				// Customer Details
				$recurring 	= $is_recurring;
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
				if ( empty( $is_recurring ) ) {
					$this->stripe_charge( $user_id, $membership_id, $membership_price );
				} elseif ( ! empty( $is_recurring ) ) {
					$this->stripe_recurring_charge( $user_id, $membership_id, $membership_price );
				}


			}

		}

		/**
		 * Method: Create a simple charge on stripe.
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

					// Redirect on empty token or membership id.
					$redirect = add_query_arg( 'payment', 'paid', $_POST[ 'redirect' ] );

				} catch ( Exception $e ) {

					// Redirect on empty token or membership id.
					$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

				}

			} else {

				// Redirect on empty token or membership id.
				$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

			}

			// Redirect back to our previous page with the added query variable.
			wp_redirect( $redirect );
			exit;

		}

		/**
		 * Method: Creates recurring charge on stripe.
		 *
		 * @since 1.0.0
		 */
		public function stripe_recurring_charge( $user_id = 0, $membership_id = 0, $membership_price = 0 ) {

			// Redirect to payment failed if any of the parameters are empty.
			if ( empty( $user_id ) || empty( $membership_id ) || empty( $membership_price ) ) {
				// Redirect on empty token or membership id.
				$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

				// Redirect back to our previous page with the added query variable.
				wp_redirect( $redirect );
				exit;
			}

			// Get Stripe plan ID for the membership.
			$membership_obj 			= ims_get_membership_object( $membership_id );
			$membership_stripe_plan 	= $membership_obj->get_stripe_plan_id();

			// Charge the customer.
			try {

				\Stripe\Stripe::setApiKey( $this->secret_key );

				$customer_args 	= array(
					'email'		=> $this->customer_details[ 'email' ],
					'source'	=> $this->stripe_token
				);
				$customer 		= \Stripe\Customer::create( $customer_args );

				$subscription_args	= array(
					"customer"		=> $customer->id,
					"plan"			=> $membership_stripe_plan
				);

				$subscription 	= \Stripe\Subscription::create( $subscription_args );

				update_user_meta( $user_id, 'ims_stripe_customer_id', $customer->id ); // Stripe Customer ID.
				update_user_meta( $user_id, 'ims_stripe_subscription_id', $subscription->id ); // Stripe Subscription ID.
				update_user_meta( $user_id, 'ims_stripe_subscription_due', $subscription->current_period_end ); // Stripe Subscription End.

				$this->add_membership_subscription( $user_id, $membership_id ); // Add membership to user.
				$receipt_id 	= $this->generate_receipt( $user_id, $membership_id ); // Generate receipt of the membership.
				$this->mail_user( $user_id, $membership_id, $receipt_id ); // Mail the user about the membership.
				$this->mail_admin( $membership_id, $receipt_id ); // Mail the admin about the membership sale.

				// Redirect on empty token or membership id.
				$redirect = add_query_arg( 'payment', 'paid', $_POST[ 'redirect' ] );

			} catch ( Exception $e ) {

				// Redirect on empty token or membership id.
				$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

			}

			// Redirect back to our previous page with the added query variable.
			wp_redirect( $redirect );
			exit;

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
							'post_status'	=> 'pending'
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

					$receipt_type		= __( 'Normal', 'inspiry-memberships' );
					$receipt_type 		= ( isset( $this->customer_details[ 'recurring' ] ) && ! empty( $this->customer_details[ 'recurring' ] ) ) ? __( 'Recurring', 'inspiry-memberships' ) : $receipt_type;

					update_post_meta( $receipt_id, "{$prefix}receipt_id", $receipt_id );
					update_post_meta( $receipt_id, "{$prefix}receipt_for", $receipt_type );
					update_post_meta( $receipt_id, "{$prefix}membership_id", $membership_id );
					update_post_meta( $receipt_id, "{$prefix}price", $membership_obj->get_price() );
					update_post_meta( $receipt_id, "{$prefix}purchase_date", $receipt->post_date );
					update_post_meta( $receipt_id, "{$prefix}user_id", $user_id );
					update_post_meta( $receipt_id, "{$prefix}vendor", 'stripe' );

					// Updating user receipts.
					$user_receipts 		= get_user_meta( $user_id, 'ims_receipts', true );
					if ( is_string( $user_receipts ) ) {
						$user_receipts 	= explode( ",", $user_receipts );
					}

					if ( ! empty( $user_receipts ) ) {
						$user_receipts[]	= $receipt_id;
					} else {
						$user_receipts 		= array();
						$user_receipts[]	= $receipt_id;
					}
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
			 * Action to run event on
			 * Doesn't need to be an existing WordPress action
			 *
			 * @param string - ims_schedule_membership_end
			 * @param string - schedule_normal_membership_end
			 */
			add_action( 'ims_schedule_membership_end', array( $this, 'schedule_normal_membership_end' ), 10, 3 );

			inspiry_log( 'single_event' );
			/**
			 * Schedule the event
			 *
			 * @param int - unix timestamp of when to run the event
			 * @param string - ims_schedule_membership_end
			 */
			wp_schedule_single_event( time() + $time_duration, 'ims_schedule_membership_end', $schedule_args );

		}

		/**
		 * Method: Function to be called when ims_schedule_membership_end
		 * event is fired.
		 *
		 * @since 1.0.0
		 */
		public function schedule_normal_membership_end( $user_id, $membership_id, $receipt_id ) {

			// Bail if user, membership or receipt id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) || empty( $receipt_id ) ) {
				return;
			}

			$this->cancel_user_membership( $user_id );
			inspiry_log( 'cancelled user membership' );
		}

		/**
		 * Method: Send email to user after selected period of time.
		 *
		 * @since 1.0.0
		 */
		public function membership_reminder_email( $user_id, $membership_id ) {

			// Bail if user, membership or receipt id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) ) {
				return;
			}

			// Get user.
			$user	= get_user_by( 'id', $user_id );
			if ( ! empty( $user ) ) {
				$user_email	= $user->user_email;
			}

			$site_name 		= get_bloginfo( 'name' );
			$site_url		= get_bloginfo( 'url' );

			$subject		= __( 'Membership is about to end.', 'inspiry-memberships' );

			$message 	= sprintf( __( 'Your membership package on %s is about to end.', 'inspiry-memberships' ), $site_name ) . "<br/><br/>";
			$message 	.= __( 'Please make sure that you renew your membership within due date via Stripe.', 'inspiry-memberships' ) . "<br/><br/>";
			$message 	.= __( 'Otherwise your membership will be cancelled.', 'inspiry-memberships' ) . "<br/><br/>";
			$message 	.= '<a target="_blank" href="' . $site_url . '">' . $site_name . '</a>';

			if ( is_email( $user_email ) ) {
				IMS_Email::send_email( $user_email, $subject, $message );
			}

		}

		/**
		 * Method: Cancels user subscription.
		 *
		 * @since 1.0.0
		 */
		public function cancel_user_membership_manual( $user_id = 0 ) {

			// Bail if user id is empty.
			if ( empty( $user_id ) ) {
				return;
			}

			$this->stripe_routine_checks();
			\Stripe\Stripe::setApiKey( $this->secret_key );

			$stripe_subscription 	= get_user_meta( $user_id, 'ims_stripe_subscription_id', true );
			if ( ! empty( $stripe_subscription ) ) {
				$subscription 		= \Stripe\Subscription::retrieve( $stripe_subscription );
				$subscription->cancel( array( 'at_period_end' => true ) );
			} else {
				$this->cancel_user_membership( $user_id );
			}

		}

		/**
		 * Method: This function process membership cancel request.
		 *
		 * @since 1.0.0
		 */
		public function cancel_user_subscription_request() {

			if ( isset( $_POST[ 'action' ] )
					&& 'ims_cancel_user_membership' == $_POST[ 'action' ]
					&& wp_verify_nonce( $_POST[ 'ims_cancel_membership_nonce' ], 'ims-cancel-membership-nonce' ) ) {

				// Get user and membership id.
				$user_id		= $_POST[ 'user_id' ];

				// Bail if user id is empty.
				if ( empty( $user_id ) ) {
					return;
				}

				$this->cancel_user_membership_manual( $user_id );

			}

		}

		/**
		 * Method: Stripe cancel event function.
		 *
		 * @since 1.0.0
		 */
		public function cancel_user_membership( $user_id ) {

			// Bail if user id is empty.
			if ( empty( $user_id ) ) {
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
			delete_user_meta( $user_id, 'ims_stripe_subscription_id' );
			delete_user_meta( $user_id, 'ims_stripe_subscription_due' );
			delete_user_meta( $user_id, 'ims_stripe_customer_id' );

		}

		/**
		 * Method: To detect and handle stripe membership events.
		 *
		 * @since 1.0.0
		 */
		public function handle_stripe_subscription_event() {

			if ( isset( $_GET[ 'ims_stripe' ] ) && 'membership_event' == $_GET[ 'ims_stripe' ] ) {

				$this->stripe_routine_checks();
				\Stripe\Stripe::setApiKey( $this->secret_key );

				$input 		= @file_get_contents( "php://input" );
				$event_json	= json_decode( $input );

				$event 		= \Stripe\Event::retrieve( $event_json->id );

				// Get stripe customer id.
				$customer_arr		= get_object_vars( $event->data );
				if ( ! empty( $customer_arr ) ) {
					foreach ( $customer_arr as $customer_data => $value ) {
						$cus_stripe_id 		= $value->customer;
					}
				}

				if ( 'customer.subscription.deleted' == $event->type ) {

					$customer_args 	= array(
						'meta_key'		=> 'ims_stripe_customer_id',
						'meta_value'	=> $cus_stripe_id
					);
					$customers 		= get_users( $customer_args );

					// Cancel subscription.
					if ( ! empty( $customers ) ) {
						foreach ( $customers as $customer ) {
							$this->cancel_user_membership( $customer->ID );
						}
					}

				} elseif ( 'customer.subscription.created' == $event->type ) {

					$user_reminder 	= 0;

					$customer_args 	= array(
						'meta_key'		=> 'ims_stripe_customer_id',
						'meta_value'	=> $cus_stripe_id
					);
					$customers 		= get_users( $customer_args );

					// Cancel subscription.
					if ( ! empty( $customers ) ) {
						foreach ( $customers as $customer ) {
							update_user_meta( $customer->ID, 'ims_user_reminder_mail', $user_reminder );
						}
					}


				} elseif ( 'invoice.payment_succeeded' == $event->type ) {

					$customer_args 	= array(
						'meta_key'		=> 'ims_stripe_customer_id',
						'meta_value'	=> $cus_stripe_id
					);
					$customers 		= get_users( $customer_args );

					if ( ! empty( $customers ) ) {
						foreach ( $customers as $customer ) {
							// Update subscription end date.
							$stripe_subscription 	= get_user_meta( $customer->ID, 'ims_stripe_subscription_id', true );
							$subscription 			= \Stripe\Subscription::retrieve( $stripe_subscription );
							$subscription_due 		= $subscription->current_period_end;
							update_user_meta( $customer->ID, 'ims_stripe_subscription_due', $subscription_due );
						}
					}

				} elseif ( 'invoice.created' == $event->type ) {

					$customer_args 	= array(
						'meta_key'		=> 'ims_stripe_customer_id',
						'meta_value'	=> $cus_stripe_id
					);
					$customers 		= get_users( $customer_args );

					if ( ! empty( $customers ) ) {
						foreach ( $customers as $customer ) {
							// Send reminder email.
							$reminder_user 	= get_user_meta( $customer->ID, 'ims_user_reminder_mail', true );
							$membership_id 	= get_user_meta( $customer->ID, 'ims_current_membership', true );
							if ( ! empty( $membership_id ) && ! empty( $reminder_user ) ) {
								$this->membership_reminder_email( $customer->ID, $membership_id );
							}
							update_user_meta( $customer->ID, 'ims_user_reminder_mail', 1 );
						}
					}

				}

				http_response_code( 200 );
				exit();

			}

		}

	}

endif;
