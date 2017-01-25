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

			/**
			 * Action to run event on
			 * Doesn't need to be an existing WordPress action
			 *
			 * @param string - ims_schedule_membership_end
			 * @param string - schedule_normal_membership_end
			 */
			add_action( 'ims_schedule_membership_end', array( $this, 'schedule_normal_membership_end' ), 10, 3 );

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
					&& 'ims_stripe_membership_payment' === $_POST[ 'action' ]
					&& wp_verify_nonce( $_POST[ 'ims_stripe_nonce' ], 'ims-stripe-nonce' ) ) {

				$this->stripe_routine_checks();

				// Get recurring payment check.
				$is_recurring 		= ( isset( $_POST[ 'ims_recurring' ] ) ) ? true : false;

				// Get membership details.
				$membership_id 		= intval( $_POST[ 'membership_id' ] );
				$membership_price 	= floatval( $_POST[ 'membership_price' ] );

				// Get redirect url.
				$redirect 	= filter_var( $_POST[ 'redirect' ], FILTER_SANITIZE_URL );

				// Get current user.
				$user 		= wp_get_current_user();
				$user_id 	= $user->ID;

				// Get stripe token.
				$this->stripe_token	= sanitize_text_field( $_POST[ 'stripeToken' ] );

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
				$this->customer_details['email'] 		= ( is_email( $email ) ) ? sanitize_email( $email ) : false;
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
					$this->stripe_charge( $user_id, $membership_id, $membership_price, $redirect );
				} elseif ( ! empty( $is_recurring ) ) {
					$this->stripe_recurring_charge( $user_id, $membership_id, $membership_price, $redirect );
				}


			}

		}

		/**
		 * Method: Create a simple charge on stripe.
		 *
		 * @since 1.0.0
		 */
		public function stripe_charge( $user_id = 0, $membership_id = 0, $membership_price = 0, $redirect = NULL ) {

			if ( ! empty( $membership_id ) && ! empty( $this->stripe_token ) && ! empty( $user_id ) && ! empty( $redirect ) ) {

				try {

					\Stripe\Stripe::setApiKey( $this->secret_key );
					$ims_stripe_membership_charge = \Stripe\Charge::create( array(
						'amount'	=> $membership_price,
						'currency'	=> $this->currency_code,
						'source'	=> $this->stripe_token
					) );

					$membership_methods	= new IMS_Membership_Method();
					$receipt_methods 	= new IMS_Receipt_Method();

					// Add membership.
					$membership_methods->add_user_membership( $user_id, $membership_id, 'stripe' );
					// Generate receipt.
					$receipt_id	= $receipt_methods->generate_receipt( $user_id, $membership_id, 'stripe', $ims_stripe_membership_charge->id );

					if ( ! empty( $receipt_id ) ) {

						// Schedule membership to end.
						$this->schedule_end_membership( $user_id, $membership_id, $receipt_id );

						// Mail the users.
						$membership_methods->mail_user( $user_id, $membership_id, 'stripe' );
						$membership_methods->mail_admin( $membership_id, $receipt_id, 'stripe' );

						// Redirect on sucessful membership.
						$redirect_url	= add_query_arg( 'membership', 'successful', $redirect );

					} else {
						// Redirect on empty token or membership id.
						$redirect_url 	= $redirect;
					}

				} catch ( Exception $e ) {
					// Redirect on empty token or membership id.
					$redirect_url	= add_query_arg( 'membership', 'failed', $redirect );
				}

			} else {
				// Redirect on empty token or membership id.
				$redirect_url	= add_query_arg( 'membership', 'failed', $redirect );
			}

			// Redirect back to our previous page with the added query variable.
			wp_redirect( $redirect_url );
			exit;

		}

		/**
		 * Method: Creates recurring charge on stripe.
		 *
		 * @since 1.0.0
		 */
		public function stripe_recurring_charge( $user_id = 0, $membership_id = 0, $membership_price = 0, $redirect ) {

			// Redirect to payment failed if any of the parameters are empty.
			if ( empty( $user_id ) || empty( $membership_id ) || empty( $membership_price ) ) {

				// Redirect on empty token or membership id.
				$redirect_url = add_query_arg( 'payment', 'failed', $redirect );
				wp_redirect( $redirect_url );
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

				$membership_methods	= new IMS_Membership_Method();
				// $receipt_methods 	= new IMS_Receipt_Method();

				$membership_methods->add_user_membership( $user_id, $membership_id, 'stripe' );
				// $receipt_id 	= $receipt_methods->generate_receipt( $user_id, $membership_id, 'stripe', $subscription->id, true );

				// Redirect on empty token or membership id.
				$redirect_url	= add_query_arg( 'payment', 'paid', $redirect );

				// if ( ! empty( $receipt_id ) ) {

				// 	$membership_methods->mail_user( $user_id, $membership_id, 'stripe' );
				// 	$membership_methods->mail_admin( $membership_id, $receipt_id, 'stripe' );

				// 	// Redirect on empty token or membership id.
				// 	$redirect_url	= add_query_arg( 'payment', 'paid', $redirect );

				// } else {
				// 	$redirect_url 	= $redirect;
				// }

			} catch ( Exception $e ) {

				// Redirect on empty token or membership id.
				$redirect_url	= add_query_arg( 'payment', 'failed', $redirect );

			}

			// Redirect back to our previous page with the added query variable.
			wp_redirect( $redirect_url );
			exit;

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
			} elseif ( 'weeks' == $time_unit ) {
				$seconds 		= 7 * 24 * 60 * 60;
			} elseif ( 'months' == $time_unit ) {
				$seconds 		= 30 * 24 * 60 * 60;
			} elseif ( 'years' == $time_unit ) {
				$seconds 		= 365 * 24 * 60 * 60;
			}

			$time_duration		= $time_duration * $seconds;

			$schedule_args		= array( $user_id, $membership_id, $receipt_id );

			/**
			 * Schedule the end of membership
			 *
			 * @param int - unix timestamp of when to run the event
			 * @param string - ims_schedule_membership_end
			 * @param array $schedule_args - arguments required by scheduling function
			 */
			wp_schedule_single_event( current_time( 'timestamp' ) + $time_duration, 'ims_schedule_membership_end', $schedule_args );

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

			// $this->cancel_user_membership( $user_id );
			$membership_methods = new IMS_Membership_Method();
			$membership_methods->cancel_user_membership( $user_id, $membership_id );

		}

		/**
		 * Method: Cancels user subscription.
		 *
		 * @since 1.0.0
		 */
		public function cancel_stripe_membership( $user_id = 0 ) {

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

				$current_membership = get_user_meta( $user_id, 'ims_current_membership', true );
				$membership_methods = new IMS_Membership_Method();
				$membership_methods->cancel_user_membership( $user_id, $current_membership );

			}

			// Redirect on empty token or membership id.
			$redirect = add_query_arg( 'request', 'submitted', esc_url( get_bloginfo( 'url' ) ) );
			wp_redirect( $redirect );
			exit;

		}

		/**
		 * Method: To detect and handle stripe membership events.
		 *
		 * @since 1.0.0
		 */
		public function handle_stripe_subscription_event() {

			// Get stripe settings.
			$stripe_settings 	= get_option( 'ims_stripe_settings' );

			if ( isset( $stripe_settings[ 'ims_stripe_webhook_url' ] ) && ! empty( $stripe_settings[ 'ims_stripe_webhook_url' ] ) ) {

				// Extract URL parameters.
				$webhook_url 			= $stripe_settings[ 'ims_stripe_webhook_url' ];
				$webhook_url_params		= parse_url( $webhook_url, PHP_URL_QUERY );
				$webhook_url_params 	= explode( '=', $webhook_url_params );

			} else {
				return false;
			}

			if ( isset( $_GET[ $webhook_url_params[0] ] ) && ( $webhook_url_params[1] === $_GET[ $webhook_url_params[0] ] ) ) {

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

							// $this->cancel_user_membership( $customer->ID );
							$current_membership = get_user_meta( $customer->ID, 'ims_current_membership', true );
							$membership_methods = new IMS_Membership_Method();
							$membership_methods->cancel_user_membership( $customer->ID, $current_membership );

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

							$membership_id 			= get_user_meta( $customer->ID, 'ims_current_membership', true );
							$subscription_id 		= get_user_meta( $customer->ID, 'ims_stripe_subscription_id', true );

							$membership_methods		= new IMS_Membership_Method();
							$membership_methods->update_membership_due_date( $membership_id, $customer->ID );

							$receipt_methods	= new IMS_Receipt_Method();
							$receipt_id 		= $receipt_methods->generate_receipt( $customer->ID, $membership_id, 'stripe', $subscription_id, true );

							if ( ! empty( $receipt_id ) ) {

								$membership_methods->mail_user( $customer->ID, $membership_id, 'stripe' );
								$membership_methods->mail_admin( $membership_id, $receipt_id, 'stripe' );

							}

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
								// $this->membership_reminder_email( $customer->ID, $membership_id );
								$membership_methods	= new IMS_Membership_Method();
								$membership_methods->membership_reminder_email( $customer->ID, $membership_id );
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
