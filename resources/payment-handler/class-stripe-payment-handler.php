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
		 * Single instance of this class.
		 *
		 * @var 	object
		 * @since 	1.0.0
		 */
		protected static $_instance;

		/**
		 * Stripe Secret Key.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		protected $secret_key;

		/**
		 * Stripe Publishable Key.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		protected $publishable_key;

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
		 * Method: Returns a single instance of this class.
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
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->set_variables();

			// Require Stripe library if it is not already exists.
			if ( ! class_exists( '\Stripe\Stripe' ) ) {
				include( IMS_BASE_DIR . '/resources/stripe/stripe-init.php' );
			}

			/**
			 * Action to run event on
			 * Doesn't need to be an existing WordPress action
			 *
			 * @param string - ims_stripe_schedule_membership_end
			 * @param string - schedule_normal_membership_end
			 */
			add_action( 'ims_stripe_schedule_membership_end', array( $this, 'schedule_normal_membership_end' ), 10, 3 );

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

			// Set the secret key.
			if ( isset( $stripe_settings[ 'ims_test_mode' ] ) && ( 'on' === $stripe_settings[ 'ims_test_mode' ] ) ) {

				if ( isset( $stripe_settings[ 'ims_test_secret' ] ) && ! empty( $stripe_settings[ 'ims_test_secret' ] ) ) {
					$this->secret_key 	= $stripe_settings[ 'ims_test_secret' ];
				}

			} elseif ( isset( $stripe_settings[ 'ims_test_mode' ] ) && ( 'off' === $stripe_settings[ 'ims_test_mode' ] ) ) {

				if ( isset( $stripe_settings[ 'ims_live_secret' ] ) && ! empty( $stripe_settings[ 'ims_live_secret' ] ) ) {
					$this->secret_key 	= $stripe_settings[ 'ims_live_secret' ];
				}

			}

			// Set the publishable key.
			if ( isset( $stripe_settings[ 'ims_test_mode' ] ) && ( 'on' === $stripe_settings[ 'ims_test_mode' ] ) ) {

				if ( isset( $stripe_settings[ 'ims_test_publishable' ] ) && ! empty( $stripe_settings[ 'ims_test_publishable' ] ) ) {
					$this->publishable_key 	= $stripe_settings[ 'ims_test_publishable' ];
				}

			} elseif ( isset( $stripe_settings[ 'ims_test_mode' ] ) && ( 'off' === $stripe_settings[ 'ims_test_mode' ] ) ) {

				if ( isset( $stripe_settings[ 'ims_live_publishable' ] ) && ! empty( $stripe_settings[ 'ims_live_publishable' ] ) ) {
					$this->publishable_key 	= $stripe_settings[ 'ims_live_publishable' ];
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
		 * Method: Display stripe button data through ajax call.
		 *
		 * @since 1.0.0
		 */
		public function ims_display_stripe_button() {

			// Check if the membership variable is set.
			if ( isset( $_POST[ 'nonce' ] )
    			&& wp_verify_nonce( $_POST[ 'nonce' ], 'membership-select-nonce' )
				&& isset( $_POST[ 'membership' ] ) ) {

				$this->stripe_routine_checks();

				// Set membership id.
				$membership_id = intval( $_POST[ 'membership' ] );

				if ( ! empty( $membership_id ) ) {

					// Get Stripe settings.
					$ims_stripe_settings 	= get_option( 'ims_stripe_settings' );

					// Strip button label.
					$ims_button_label 		= __( 'Pay with Card', 'inspiry-memberships' );
					if ( ! empty( $ims_stripe_settings[ 'ims_stripe_btn_label' ] ) ) {
						$ims_button_label	= $ims_stripe_settings[ 'ims_stripe_btn_label' ];
					}

					$membership_obj 	= ims_get_membership_object( $membership_id );

					$price 	= $membership_obj->get_price() * 100;
					if ( ! empty( $price ) ) {
						$payment_nonce	= wp_create_nonce( 'ims-stripe-nonce' );
					} else {
						$payment_nonce	= wp_create_nonce( 'ims-free-nonce' );
					}

					$stripe_button_arr	= apply_filters( 'ims_stripe_button_args', array(
						'success'			=> true,
						'blog_name'			=> get_bloginfo( 'name' ),
						'desc'				=> __( 'Membership Payment', 'inspiry-memberships' ),
						'ID'				=> get_the_ID(),
						'membership' 		=> get_the_title( $membership_id ),
						'membership_id'		=> $membership_id,
						'price'				=> $price,
						'publishable_key'	=> $this->publishable_key,
						'currency_code'		=> $this->currency_code,
						'button_label'		=> $ims_button_label,
						'payment_nonce'		=> $payment_nonce,
						'freeButtonLabel'	=> __( 'Subscribe', 'inspiry-memberships' )
					) );
					echo json_encode( $stripe_button_arr );

				} else {
					echo json_encode( array (
						'success'		=> false,
						'message'		=> __( 'Membership ID is empty.', 'inspiry-memberships' )
					) );
				}

			} else {
				echo json_encode( array (
					'success'		=> false,
					'message'		=> __( 'Membership ID is not valid.', 'inspiry-memberships' )
				) );
			}
			die();

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

				// Get membership details.
				$membership_id 		= ( isset( $_POST[ 'membership_id' ] ) ) ? intval( $_POST[ 'membership_id' ] ) : false;
				$membership_price 	= ( isset( $_POST[ 'membership_price' ] ) ) ? floatval( $_POST[ 'membership_price' ] ) : false;
				$redirect 	= ( isset( $_POST[ 'redirect' ] ) ) ? sanitize_text_field( $_POST[ 'redirect' ] ) : false;

				// Sanitize redirect url.
				$redirect 	= filter_var( $redirect, FILTER_SANITIZE_URL );

				// Get current user.
				$user 		= wp_get_current_user();
				$user_id 	= $user->ID;

				// Get stripe token.
				$this->stripe_token	= ( isset( $_POST[ 'stripeToken' ] ) ) ? sanitize_text_field( $_POST[ 'stripeToken' ] ) : false;

				// Customer Details
				$recurring 	= ( isset( $_POST[ 'ims_recurring' ] ) ) ? true : false;
				$email 		= ( isset( $_POST[ 'stripeEmail' ] ) ) ? sanitize_email( $_POST[ 'stripeEmail' ] ) : false;
				$name 		= ( isset( $_POST[ 'stripeBillingName' ] ) ) ? sanitize_text_field( $_POST[ 'stripeBillingName' ] ) : false;
				$address 	= ( isset( $_POST[ 'stripeBillingAddressLine1' ] ) ) ? sanitize_text_field( $_POST[ 'stripeBillingAddressLine1' ] ) : false;
				$zip 		= ( isset( $_POST[ 'stripeBillingAddressZip' ] ) ) ? sanitize_text_field( $_POST[ 'stripeBillingAddressZip' ] ) : false;
				$city 		= ( isset( $_POST[ 'stripeBillingAddressCity' ] ) ) ? sanitize_text_field( $_POST[ 'stripeBillingAddressCity' ] ) : false;
				$state 		= ( isset( $_POST[ 'stripeBillingAddressState' ] ) ) ? sanitize_text_field( $_POST[ 'stripeBillingAddressState' ] ) : false;
				$country 	= ( isset( $_POST[ 'stripeBillingAddressCountry' ] ) ) ? sanitize_text_field( $_POST[ 'stripeBillingAddressCountry' ] ) : false;

				$this->customer_details['email'] 		= ( ! empty( $email ) && is_email( $email ) ) ? $email : false;
				$this->customer_details['recurring']	= ( ! empty( $recurring ) ) ? $recurring : false;
				$this->customer_details['name'] 		= ( ! empty( $name )  ) ? $name : false;
				$this->customer_details['address'] 		= ( ! empty( $address ) ) ? $address : false;
				$this->customer_details['zip'] 			= ( ! empty( $zip ) ) ? $zip : false;
				$this->customer_details['city'] 		= ( ! empty( $city )  ) ? $city : false;
				$this->customer_details['state'] 		= ( ! empty( $state )  ) ? $state : false;
				$this->customer_details['country'] 		= ( ! empty( $country )  ) ? $country : false;

				/**
				 * Filter the values of $customer_details array
				 * for membership payment to extend its values.
				 *
				 * @param array $customer_details - Array of customer details
				 * @since 1.0.0
				 */
				$this->customer_details 	= apply_filters( 'ims_membership_customer_details', $this->customer_details );

				// Charge the card using stripe.
				if ( empty( $recurring ) ) {
					$this->stripe_charge( $user_id, $membership_id, $membership_price, $redirect );
				} elseif ( ! empty( $recurring ) ) {
					$this->stripe_recurring_charge( $user_id, $membership_id, $membership_price, $redirect );
				}


			}

		}

		/**
		 * Method: Create a simple charge on stripe.
		 *
		 * @param int $user_id - ID of the user purchasing membership
		 * @param int $membership_id - ID of the membership being purchased
		 * @param int $membership_price - Price of the membership
		 * @param string $redirect - Redirect URL
		 * @since 1.0.0
		 */
		public function stripe_charge( $user_id = 0, $membership_id = 0, $membership_price = 0, $redirect = NULL ) {

			if ( ! empty( $membership_id ) && ! empty( $this->stripe_token ) && ! empty( $user_id ) && ! empty( $redirect ) ) {

				try {

					\Stripe\Stripe::setApiKey( $this->secret_key );
					$stripe_charge_args	= apply_filters( 'ims_stripe_charge_args', array(
						'amount'	=> $membership_price,
						'currency'	=> $this->currency_code,
						'source'	=> $this->stripe_token
					) );
					$ims_stripe_membership_charge = \Stripe\Charge::create( $stripe_charge_args );

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
						$redirect_url	= add_query_arg( array( 'membership' => 'successful' ), $redirect );
						$redirect_url	= apply_filters( 'ims_membership_success_redirect', $redirect_url );

					} else {
						// Redirect on empty receipt id.
						$redirect_url 	= $redirect;
						$redirect_url	= apply_filters( 'ims_membership_failed_redirect', $redirect_url );
					}

					// Add action hook after stripe payment is done.
					do_action( 'ims_stripe_simple_payment_success', $user_id, $membership_id, $receipt_id );

				} catch ( Exception $e ) {

					// Redirect on empty token or membership id.
					$redirect_url	= add_query_arg( array( 'membership' => 'failed' ), $redirect );
					$redirect_url	= apply_filters( 'ims_membership_failed_redirect', $redirect_url );

					// Add action hook after stripe payment is done.
					do_action( 'ims_stripe_simple_payment_failed' );

				}

			} else {
				// Redirect on empty token or membership id.
				$redirect_url	= add_query_arg( array( 'membership' => 'failed' ), $redirect );
				$redirect_url	= apply_filters( 'ims_membership_failed_redirect', $redirect_url );
			}

			// Redirect back to our previous page with the added query variable.
			wp_redirect( $redirect_url );
			exit;

		}

		/**
		 * Method: Creates recurring charge on stripe.
		 *
		 * @param int $user_id - ID of the user purchasing membership
		 * @param int $membership_id - ID of the membership being purchased
		 * @param int $membership_price - Price of the membership
		 * @param string $redirect - Redirect URL
		 * @since 1.0.0
		 */
		public function stripe_recurring_charge( $user_id = 0, $membership_id = 0, $membership_price = 0, $redirect ) {

			// Redirect to payment failed if any of the parameters are empty.
			if ( empty( $user_id ) || empty( $membership_id ) || empty( $membership_price ) ) {

				// Redirect on empty token or membership id.
				$redirect_url	= add_query_arg( array( 'membership' => 'failed' ), $redirect );
				wp_redirect( $redirect_url );
				exit;

			}

			// Get Stripe plan ID for the membership.
			$membership_obj 			= ims_get_membership_object( $membership_id );
			$membership_stripe_plan 	= $membership_obj->get_stripe_plan_id();

			// Charge the customer.
			try {

				\Stripe\Stripe::setApiKey( $this->secret_key );

				$customer_args 	= apply_filters( 'ims_stripe_customer_args', array(
					'email'		=> $this->customer_details[ 'email' ],
					'source'	=> $this->stripe_token
				) );
				$customer 		= \Stripe\Customer::create( $customer_args );

				$subscription_args	= apply_filters( 'ims_stripe_subscription_args', array(
					"customer"		=> $customer->id,
					"plan"			=> $membership_stripe_plan
				) );
				$subscription 	= \Stripe\Subscription::create( $subscription_args );

				update_user_meta( $user_id, 'ims_stripe_customer_id', $customer->id ); // Stripe Customer ID.
				update_user_meta( $user_id, 'ims_stripe_subscription_id', $subscription->id ); // Stripe Subscription ID.
				update_user_meta( $user_id, 'ims_stripe_subscription_due', $subscription->current_period_end ); // Stripe Subscription End.

				$membership_methods	= new IMS_Membership_Method();

				$membership_methods->add_user_membership( $user_id, $membership_id, 'stripe' );

				// Redirect on successful payment.
				$redirect_url	= add_query_arg( array( 'membership' => 'successful' ), $redirect );
				$redirect_url	= apply_filters( 'ims_membership_success_redirect', $redirect_url );

				// Add action hook after stripe payment is done.
				do_action( 'ims_stripe_recurring_payment_success', $user_id, $membership_id );

			} catch ( Exception $e ) {

				// Redirect on empty token or membership id.
				$redirect_url	= add_query_arg( array( 'membership' => 'failed' ), $redirect );
				$redirect_url	= apply_filters( 'ims_membership_failed_redirect', $redirect_url );

				// Add action hook after stripe payment is done.
				do_action( 'ims_stripe_recurring_payment_failed' );

			}

			// Redirect back to our previous page with the added query variable.
			wp_redirect( $redirect_url );
			exit;

		}

		/**
		 * This function is used to schedule the end of
		 * non-recurring membership.
		 *
		 * @param int $user_id - ID of the user purchasing membership
		 * @param int $membership_id - ID of the membership being purchased
		 * @param int $receipt_id - Receipt ID of the purchased membership
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

			$schedule_args		= array( $user_id, $membership_id );

			/**
			 * Schedule the end of membership
			 *
			 * @param int - unix timestamp of when to run the event
			 * @param string - ims_stripe_schedule_membership_end
			 * @param array $schedule_args - arguments required by scheduling function
			 */
			wp_schedule_single_event( current_time( 'timestamp' ) + $time_duration, 'ims_stripe_schedule_membership_end', $schedule_args );

			// Membership schedulled action hook.
			do_action( 'ims_stripe_membership_schedulled', $user_id, $membership_id );

		}

		/**
		 * Method: Function to be called when ims_stripe_schedule_membership_end
		 * event is fired.
		 *
		 * @param int $user_id - ID of the user purchasing membership
		 * @param int $membership_id - ID of the membership being purchased
		 * @since 1.0.0
		 */
		public function schedule_normal_membership_end( $user_id, $membership_id ) {

			// Bail if user, membership or receipt id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) ) {
				return;
			}

			// $this->cancel_user_membership( $user_id );
			$membership_methods = new IMS_Membership_Method();
			$membership_methods->cancel_user_membership( $user_id, $membership_id );

		}

		/**
		 * Method: Cancels user subscription.
		 *
		 * @param int $user_id - User ID of the user making cancel request
		 * @since 1.0.0
		 */
		public static function cancel_stripe_membership( $user_id = 0 ) {

			// Bail if user id is empty.
			if ( empty( $user_id ) ) {
				return;
			}

			// Get basic settings.
			$basic_settings		= get_option( 'ims_basic_settings' );

			// Get stripe settings.
			$stripe_settings	= get_option( 'ims_stripe_settings' );

			$secret_key = '';

			// Check if we are using test mode.
			if ( isset( $stripe_settings[ 'ims_test_mode' ] ) && ( 'on' === $stripe_settings[ 'ims_test_mode' ] ) ) {

				if ( isset( $stripe_settings[ 'ims_test_secret' ] ) && ! empty( $stripe_settings[ 'ims_test_secret' ] ) ) {
					$secret_key	= $stripe_settings[ 'ims_test_secret' ];
				}

			} elseif ( isset( $stripe_settings[ 'ims_test_mode' ] ) && ( 'off' === $stripe_settings[ 'ims_test_mode' ] ) ) {

				if ( isset( $stripe_settings[ 'ims_live_secret' ] ) && ! empty( $stripe_settings[ 'ims_live_secret' ] ) ) {
					$secret_key	= $stripe_settings[ 'ims_live_secret' ];
				}

			}

			try {

				\Stripe\Stripe::setApiKey( $secret_key );

				$stripe_subscription 	= get_user_meta( $user_id, 'ims_stripe_subscription_id', true );
				if ( ! empty( $stripe_subscription ) ) {

					$subscription 		= \Stripe\Subscription::retrieve( $stripe_subscription );
					$subscription_cancel_args	= apply_filters( 'ims_subscription_cancel_args', array( 'at_period_end' => true ) );
					$subscription->cancel( $subscription_cancel_args );

				} else {

					$current_membership = get_user_meta( $user_id, 'ims_current_membership', true );
					$membership_methods = new IMS_Membership_Method();
					$membership_methods->cancel_user_membership( $user_id, $current_membership );

				}

			} catch ( Exception $e ) {
				// Redirect on failing request.
				$redirect = add_query_arg( 'request', 'failed', esc_url( home_url() ) );
				wp_redirect( $redirect );
				exit;
			}

			// Redirect on successful request.
			$redirect = add_query_arg( 'request', 'submitted', esc_url( home_url() ) );
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
				$stripe_customer_id = $event->data->object->customer;

				if ( 'customer.subscription.deleted' == $event->type ) {

					$customer_args	= array(
						'meta_key'		=> 'ims_stripe_customer_id',
						'meta_value'	=> $stripe_customer_id,
						'meta_compare'	=> '='
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
						'meta_value'	=> $stripe_customer_id,
						'meta_compare'	=> '='
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
						'meta_value'	=> $stripe_customer_id,
						'meta_compare'	=> '='
					);
					$customers 		= get_users( $customer_args );

					if ( ! empty( $customers ) ) {
						foreach ( $customers as $customer ) {

							// Update subscription end date.
							$subscription_id 	= get_user_meta( $customer->ID, 'ims_stripe_subscription_id', true );
							$subscription 		= \Stripe\Subscription::retrieve( $subscription_id );
							$subscription_due 	= $subscription->current_period_end;
							update_user_meta( $customer->ID, 'ims_stripe_subscription_due', $subscription_due );

							$membership_id 		= get_user_meta( $customer->ID, 'ims_current_membership', true );

							$membership_methods	= new IMS_Membership_Method();
							$membership_methods->update_membership_due_date( $membership_id, $customer->ID );
							$membership_methods->update_user_recurring_membership( $customer->ID, $membership_id );

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
						'meta_value'	=> $stripe_customer_id,
						'meta_compare'	=> '='
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


/**
 * Returns the main instance of IMS_Stripe_Payment_Handler.
 *
 * @since 1.0.0
 */
function IMS_Stripe_Payment_Handler() {
	return IMS_Stripe_Payment_Handler::instance();
}
