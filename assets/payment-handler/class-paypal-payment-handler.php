<?php
/**
 * PayPal Payments Handling Class
 *
 * Class for handling PayPal payments.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_PayPal_Payment_Handler.
 *
 * Class for handling PayPal payments.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_PayPal_Payment_Handler' ) ) :

	class IMS_PayPal_Payment_Handler {

		/**
		 * PayPal Client ID.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $client_ID;

		/**
		 * PayPal Client Secret.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $client_secret;

		/**
		 * PayPal Sandbox URI.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 public $uri_sandbox;

		/**
		 * PayPal Live URI.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 public $uri_live;

		/**
		 * PayPal Access Token.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $access_token;

		/**
		 * PayPal Token Type.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $token_type;

		/**
		 * PayPal API Username.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $api_username;

		/**
		 * PayPal API Password.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $api_password;

		/**
		 * PayPal API Signature.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $api_signature;

		/**
		 * PayPal API EndPoint.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $api_endpoint;

		/**
		 * PayPal Express Checkout URL.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 private $express_checkout_url;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Get PayPal settings.
			$paypal_settings		= get_option( 'ims_paypal_settings' );

			// Set the variables.
			if ( isset( $paypal_settings[ 'ims_paypal_client_id' ] ) && ! empty( $paypal_settings[ 'ims_paypal_client_id' ] ) ) {
				$this->client_ID		= $paypal_settings[ 'ims_paypal_client_id' ];
			}

			if ( isset( $paypal_settings[ 'ims_paypal_client_secret' ] ) && ! empty( $paypal_settings[ 'ims_paypal_client_secret' ] ) ) {
				$this->client_secret	= $paypal_settings[ 'ims_paypal_client_secret' ];
			}

			$this->uri_live		= "https://api.paypal.com/v1/";
			$this->uri_sandbox	= "https://api.sandbox.paypal.com/v1/";

			if ( isset( $paypal_settings[ 'ims_paypal_api_username' ] ) && ! empty( $paypal_settings[ 'ims_paypal_api_username' ] ) ) {
				$this->api_username		= $paypal_settings[ 'ims_paypal_api_username' ];
			}

			if ( isset( $paypal_settings[ 'ims_paypal_api_password' ] ) && ! empty( $paypal_settings[ 'ims_paypal_api_password' ] ) ) {
				$this->api_password		= $paypal_settings[ 'ims_paypal_api_password' ];
			}

			if ( isset( $paypal_settings[ 'ims_paypal_api_signature' ] ) && ! empty( $paypal_settings[ 'ims_paypal_api_signature' ] ) ) {
				$this->api_signature	= $paypal_settings[ 'ims_paypal_api_signature' ];
			}

			/**
			 * Action to run event on
			 * Doesn't need to be an existing WordPress action
			 *
			 * @param string - ims_paypal_membership_schedule_end
			 * @param string - paypal_membership_schedule_end
			 */
			add_action( 'ims_paypal_membership_schedule_end', array( $this, 'paypal_membership_schedule_end' ), 10, 3 );

		}

		/**
		 * Method: Set PayPal setting variables.
		 *
		 * @since 1.0.0
		 */
		private function paypal_routine_check() {

			// Get PayPal settings.
			$paypal_settings		= get_option( 'ims_paypal_settings' );

			// Set the variables.
			if ( isset( $paypal_settings[ 'ims_paypal_client_id' ] ) && ! empty( $paypal_settings[ 'ims_paypal_client_id' ] ) ) {
				$this->client_ID		= $paypal_settings[ 'ims_paypal_client_id' ];
			}

			if ( isset( $paypal_settings[ 'ims_paypal_client_secret' ] ) && ! empty( $paypal_settings[ 'ims_paypal_client_secret' ] ) ) {
				$this->client_secret	= $paypal_settings[ 'ims_paypal_client_secret' ];
			}

			$this->uri_live		= "https://api.paypal.com/v1/";
			$this->uri_sandbox	= "https://api.sandbox.paypal.com/v1/";

			if ( isset( $paypal_settings[ 'ims_paypal_api_username' ] ) && ! empty( $paypal_settings[ 'ims_paypal_api_username' ] ) ) {
				$this->api_username		= $paypal_settings[ 'ims_paypal_api_username' ];
			}

			if ( isset( $paypal_settings[ 'ims_paypal_api_password' ] ) && ! empty( $paypal_settings[ 'ims_paypal_api_password' ] ) ) {
				$this->api_password		= $paypal_settings[ 'ims_paypal_api_password' ];
			}

			if ( isset( $paypal_settings[ 'ims_paypal_api_signature' ] ) && ! empty( $paypal_settings[ 'ims_paypal_api_signature' ] ) ) {
				$this->api_signature	= $paypal_settings[ 'ims_paypal_api_signature' ];
			}

		}

		/**
		 * Method: Start processing simple PayPal payment.
		 *
		 * @since 1.0.0
		 */
		public function process_simple_paypal_payment() {

			// Get membership id.
			$membership_id 	= intval( $_POST[ 'membership_id' ] );

			// Get current user.
			$user 		= wp_get_current_user();
			$user_id 	= $user->ID;

			if ( ! empty( $membership_id ) && ! empty( $user_id ) ) {

				$this->paypal_routine_check();

				// Get currency code.
				$ims_basic_settings 	= get_option( 'ims_basic_settings' );
				$currency_code 			= $ims_basic_settings[ 'ims_currency_code' ];
				if ( empty( $currency_code ) ) {
					$currency_code 	= 'USD';
				}

				// Get membership object.
				$membership 	= ims_get_membership_object( $membership_id );
				$price 			= $membership->get_price();

				// Get PayPal Settings.
				$paypal_settings 	= get_option( 'ims_paypal_settings' );
				$sandbox_mode 		= $paypal_settings[ 'ims_paypal_test_mode' ];

				if ( ! empty( $sandbox_mode ) && ( 'on' === $sandbox_mode ) ) {
					$paypal_url	= $this->uri_sandbox;
				} else {
					$paypal_url	= $this->uri_live;
				}

				$postVal 		= "grant_type=client_credentials";
        		$paypal_uri		= $paypal_url . "oauth2/token";

        		// Call to PayPal API to generate access token.
        		$auth_response	= $this->generate_token( $paypal_uri, $postVal );

        		if ( ! empty( $auth_response ) ) {

        			$this->access_token	= $auth_response->access_token;
        			$this->token_type	= $auth_response->token_type;

        			$paypal_payment_uri	= $paypal_url . "payments/payment";

        			$return_url 		= esc_url( add_query_arg( 'paypal_payment', 'success', get_bloginfo( 'url' ) ) );
        			$cancel_url 		= esc_url( add_query_arg( 'paypal_payment', 'failed', get_bloginfo( 'url' ) ) );
        			$memberships_title 	= esc_html( get_the_title( $membership_id ) );

        			$payment_args 	= array(
        				"intent"		=> "sale",
        				"redirect_urls"	=> array(
        					"return_url"	=> $return_url,
        					"cancel_url"	=> $return_url
        				),
        				"payer"			=> array(
        					"payment_method"	=> "paypal"
        				),
        				"transactions"	=> array(
        					0	=> array(
        						"amount"		=> array(
        							"total"			=> $price,
        							"currency"		=> $currency_code,
        							"details"		=> array(
										"subtotal"		=> $price,
										"tax"			=> "0.00",
										"shipping"		=> "0.00",
										"insurance"		=> "0.00"
									)
        						),
        						"description"	=> __( 'Payment for ', 'inspiry-memberships' ) . $memberships_title,
        						"item_list"	=> array(
        							"items" 		=> array(
        								0 				=> array(
        									"name" 			=> $memberships_title,
        									"description"	=> __( 'Payment for ', 'inspiry-memberships' ) . $memberships_title,
        									"quantity" 		=> "1",
	                                        "price"			=> $price,
	                                        "tax"			=> "0.00",
	                                        "sku" 			=> $memberships_title,
	                                        "currency"		=> $currency_code
        								)
        							)
        						)
        					)
        				)
        			);

					$payment_json_args	= json_encode( $payment_args );

					$payment_response 	= $this->make_payment_call( $paypal_payment_uri, $payment_json_args, $this->access_token );

					if ( ! empty( $payment_response ) ) {

						foreach ( $payment_response[ 'links' ] as $response_link ) {

							if ( 'approval_url' == $response_link[ 'rel' ] ) {
								$payment_return_url		= $response_link[ 'href' ]; // Approved payment URL to redirect member.
							} elseif ( 'execute' == $response_link[ 'rel' ] ) {
								$payment_execute_url 	= $response_link[ 'href' ]; // Use this URL to execute payment in future.
							}

						}

						$user_paypal_payment 	= array(
							'execute_url'	=> $payment_execute_url,
							'access_token'	=> $this->access_token,
							'membership_id'	=> $membership_id
						);
						$user_payment_details 	= array(
							$user_id	=> $user_paypal_payment
						);
						update_option( 'ims_paypal_payment_details', $user_payment_details );

						echo json_encode( array (
							'success'	=> true,
							'url'		=> $payment_return_url,
							'response'	=> $payment_response
						) );

					} else {
						echo json_encode( array (
							'success'	=> false,
							'message'	=> __( 'We were unable to authorize payment from PayPal. Please try again.', 'inspiry-memberships' )
						) );
					}


        		} else {
        			echo json_encode( array (
						'success'	=> false,
						'message'	=> __( 'We were not able to connect to PayPal. Please try again.', 'inspiry-memberships' )
					) );
        		}

			} else {
				echo json_encode( array (
					'success'	=> false,
					'message'	=> __( 'Please select a membership to continue.', 'inspiry-memberships' )
				) );
			}

			die();

		}

		/**
		 * Method: Generate customer token.
		 *
		 * @since 1.0.0
		 */
		public function generate_token( $url, $postVals ) {

			// Bail if any of the variables are empty.
			if ( empty( $url ) || empty( $postVals ) ) {
				return false;
			}

			$ch 	= curl_init();

			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_USERPWD, $this->client_ID . ":" . $this->client_secret );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postVals );

			$result	= curl_exec( $ch );
			curl_close( $ch );

			if ( empty( $result ) ) {
				return false;
			} else {
			    $token 	= json_decode( $result );
			    return $token;
			}

		}

		/**
		 * Method: Make Payment POST call.
		 *
		 * @since 1.0.0
		 */
		public function make_payment_call( $url, $postVals, $token ) {

			// Bail if any of the variables are empty.
			if ( empty( $url ) || empty( $postVals ) || empty( $token ) ) {
				return false;
			}

			$ch 	= curl_init( $url );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt(
				$ch,
				CURLOPT_HTTPHEADER,
				array(
					'Authorization: Bearer ' . $token,
					'Accept: application/json',
					'Content-Type: application/json'
				)
			);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postVals );

			$result	= curl_exec( $ch );
			curl_close( $ch );

			if ( empty( $result ) ) {
				return false;
			} else {
			    $response	= json_decode( $result, true );
			    return $response;
			}

		}

		/**
		 * Method: Get PayPal PayerID and execute PayPal Payment.
		 *
		 * @since 1.0.0
		 */
		public function execute_paypal_payment() {

			if ( isset( $_GET[ 'paypal_payment' ] ) && ( 'success' === $_GET[ 'paypal_payment' ] )
					&& isset( $_GET[ 'PayerID' ] ) && isset( $_GET[ 'token' ] ) ) {

				// Get PayerID sent by PayPal.
				$payerID 	= wp_kses( $_GET[ 'PayerID' ], array() );

				// Get current user.
				$current_user 	= wp_get_current_user();
				$user_id 		= $current_user->ID;

				if ( ! empty( $user_id ) ) {

					// Get payment data.
					$payment_data 	= get_option( 'ims_paypal_payment_details' );
					$token 			= $payment_data[ $user_id ][ 'access_token' ];
					$execute_url	= $payment_data[ $user_id ][ 'execute_url' ];
					$membership_id	= $payment_data[ $user_id ][ 'membership_id' ];

					$payment_args 	= array(
						'payer_id'	=> $payerID
					);
					$payment_json_args	= json_encode( $payment_args );

					$payment 		= $this->make_payment_call( $execute_url, $payment_json_args, $token );

					if ( 'approved' == $payment[ 'state' ] ) {

						// Clear the general option.
						$payment_data 	= array(
							$user_id	= array()
						);
						update_option( 'ims_paypal_payment_details', $payment_data );

						// Update user meta with the payment id.
						$ims_paypal_payments		= get_user_meta( $current_user->ID, 'ims_paypal_payments', true );
						if ( is_string( $ims_paypal_payments ) && empty( $ims_paypal_payments ) ) {
							$ims_paypal_payments 	= array();
							$ims_paypal_payments[]	= $payment[ 'id' ];
						} else {
							$ims_paypal_payments[]	= $payment[ 'id' ];
						}
						update_user_meta( $current_user->ID, 'ims_paypal_payments', $ims_paypal_payments );

						$membership_methods	= new IMS_Membership_Method();
						$receipt_methods 	= new IMS_Receipt_Method();

						$membership_methods->add_user_membership( $current_user->ID, $membership_id, 'paypal' );
						$receipt_id			= $receipt_methods->generate_receipt( $current_user->ID, $membership_id, 'paypal', $payment[ 'id' ] );

						if ( ! empty( $receipt_id ) ) {
							$membership_methods->mail_user( $current_user->ID, $membership_id, 'paypal' );
							$membership_methods->mail_admin( $current_user->ID, $receipt_id, 'paypal' );
						}

						$this->paypal_user_membership_end_schedule( $current_user->ID, $membership_id );

						$redirect_url 	= add_query_arg( 'membership', 'purchased', esc_url( get_bloginfo( 'url' ) ) );
						wp_redirect( $redirect_url );
						exit();

					} else {

						$redirect_url 	= add_query_arg( 'payment', 'failed', esc_url( get_bloginfo( 'url' ) ) );
						wp_redirect( $redirect_url );
						exit();

					}

				}

			}

		}

		/**
		 * Method: Schedule PayPal membership end.
		 *
		 * @since 1.0.0
		 */
		public function paypal_user_membership_end_schedule( $user_id = 0, $membership_id = 0 ) {

			// Bail if user or membership id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) ) {
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

			$schedule_args		= array( $user_id, $membership_id );

			/**
			 * Schedule the event
			 *
			 * @param int - unix timestamp of when to run the event
			 * @param string - ims_paypal_membership_schedule_end
			 */
			wp_schedule_single_event( time() + $time_duration, 'ims_paypal_membership_schedule_end', $schedule_args );

		}

		/**
		 * Method: Function to be called when ims_paypal_membership_schedule_end
		 * event is fired.
		 *
		 * @since 1.0.0
		 */
		public function paypal_membership_schedule_end( $user_id, $membership_id ) {

			// Bail if user or membership id is empty.
			if ( empty( $user_id) || empty( $membership_id ) ) {
				return;
			}

			$ims_membership_methods	= new IMS_Membership_Method();
			$ims_membership_methods->cancel_user_membership( $user_id, $membership_id );

		}

		/**
		 * Method: Start processing recurring PayPal payment.
		 *
		 * @since 1.0.0
		 */
		public function process_recurring_paypal_payment() {

			// Get membership id.
			$membership_id 	= intval( $_POST[ 'membership_id' ] );

			// Get current user.
			$user 		= wp_get_current_user();
			$user_id 	= $user->ID;

			if ( ! empty( $membership_id ) && ! empty( $user_id ) ) {

				$this->paypal_routine_check();

				// Get currency code.
				$ims_basic_settings	= get_option( 'ims_basic_settings' );
				$currency_code		= $ims_basic_settings[ 'ims_currency_code' ];
				if ( empty( $currency_code ) ) {
					$currency_code 	= 'USD';
				}

				// Get membership object.
				$membership 	= ims_get_membership_object( $membership_id );
				$price 			= $membership->get_price();
				$title 			= get_the_title( $membership_id );
				$description 	= __( 'Payment for ', 'inspiry-memberships' ) . $title;

				// Get PayPal Settings.
				$paypal_settings 	= get_option( 'ims_paypal_settings' );
				$sandbox_mode 		= $paypal_settings[ 'ims_paypal_test_mode' ];

				$return_url		= esc_url( add_query_arg( 'paypal_recurring_payment', 'success', get_bloginfo( 'url' ) ) );
        		$cancel_url		= esc_url( add_query_arg( 'paypal_recurring_payment', 'failed', get_bloginfo( 'url' ) ) );

				$response 	= $this->callShortcutExpressCheckout( $price, $currency_code, "Sale", $return_url, $cancel_url, $description );
				if ( ! empty( $response ) ) {

					// Redirect to PayPal with token.
					$token 	= urldecode( $response[ "TOKEN" ] );
					$redirect_paypal	= $this->express_checkout_url . $token;

					$user_paypal_payment 	= array(
						'token'			=> $token,
						'membership_id'	=> $membership_id
					);
					$user_payment_details 	= array(
						$user_id	=> $user_paypal_payment
					);
					update_option( 'ims_paypal_recurring_payment_details', $user_payment_details );

					echo json_encode( array (
						'success'	=> true,
						'url'		=> $redirect_paypal
					) );

				} else {
					echo json_encode( array (
						'success'	=> false,
						'message'	=> __( 'Error occured while connecting to PayPal.', 'inspiry-memberships' )
					) );
				}

			} else {
				echo json_encode( array (
					'success'	=> false,
					'message'	=> __( 'Please select a membership to continue.', 'inspiry-memberships' )
				) );
			}

			die();

		}

		/**
		 * Method: Get PayPal PayerID and execute recurring payment.
		 *
		 * @since 1.0.0
		 */
		public function execute_recurring_paypal_payment() {

			if ( isset( $_GET[ 'paypal_recurring_payment' ] ) && ( 'success' === $_GET[ 'paypal_recurring_payment' ] )
					&& isset( $_GET[ 'PayerID' ] ) && isset( $_GET[ 'token' ] ) ) {

				// Get PayerID and token sent by PayPal.
				$payerID	= wp_kses( $_GET[ 'PayerID' ], array() );
				$token 		= wp_kses( $_GET[ 'token' ], array() );

				// Get current user.
				$current_user 	= wp_get_current_user();
				$user_id 		= $current_user->ID;

				// Get payment data.
				$payment_data 	= get_option( 'ims_paypal_recurring_payment_details' );

				if ( isset( $payment_data[ $user_id ][ 'membership_id' ] ) && ! empty( $payment_data[ $user_id ][ 'membership_id' ] ) ) {
					$membership_id	= $payment_data[ $user_id ][ 'membership_id' ];
				}

				if ( ! empty( $user_id ) && ! empty( $membership_id ) ) {

					$shipping_details	= $this->getShippingDetails( $token );
					// $billing_agreement 	= $this->confirm_payment( $token, $payerID, $membership_id );
					$profile 			= $this->createRecurringPaymentsProfile( $shipping_details, $membership_id, $user_id );

					if ( ! empty( $profile ) ) {

						$profile_id	= ( isset( $profile[ "PROFILEID" ] ) && ! empty( $profile[ "PROFILEID" ] ) ) ? $profile[ "PROFILEID" ] : false;

						// Store the profile id in user meta.
						update_user_meta( $user_id, 'ims_paypal_profile_id', $profile_id );
						$redirect_url 	= add_query_arg( 'membership', 'success', esc_url( get_bloginfo( 'url' ) ) );
						wp_redirect( $redirect_url );
						exit();

					}

				} else {

					$redirect_url 	= add_query_arg( 'membership', 'failed', esc_url( get_bloginfo( 'url' ) ) );
					wp_redirect( $redirect_url );
					exit();

				}

			}

		}

		private function callShortcutExpressCheckout( $amount, $currency_code, $type, $returnURL, $cancelURL, $description ) {

			// Bail if parameters are empty.
			if ( empty( $amount ) ||
					empty( $currency_code ) ||
					empty( $type ) ||
					empty( $returnURL ) ||
					empty( $cancelURL ) ||
					empty( $description ) ) {
				return false;
			}

			/**
			 * Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation.
			 */
			$nvpstr	= "PAYMENTREQUEST_0_AMT=" . $amount;
			$nvpstr = $nvpstr . "&ReturnUrl=" . $returnURL;
			$nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
			$nvpstr = $nvpstr . "&PAYMENTACTION=" . urlencode( $type );
			$nvpstr = $nvpstr . "&CURRENCYCODE=" . $currency_code;
			$nvpstr = $nvpstr . "&L_BILLINGTYPE0=RecurringPayments";
			$nvpstr = $nvpstr . "&L_BILLINGAGREEMENTDESCRIPTION0=" . urlencode( $description );

			/**
			 * Make the API call to PayPal.
			 * If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
			 * If an error occured, show the resulting errors.
			 */
			$result				= $this->hash_call( "SetExpressCheckout", $nvpstr );
			$acknowledgement	= strtoupper( $result[ "ACK" ] );
			if ( "SUCCESS" == $acknowledgement || "SUCCESSWITHWARNING" == $acknowledgement ) {
				return $result;
			}
		    return false;

		}

		/**
		 * Method: Get Shipping Details from PayPal.
		 * Prepares the parameters for the
		 * GetExpressCheckoutDetails API Call.
		 *
		 * @return array The NVP Collection object of the GetExpressCheckoutDetails Call Response.
		 * @since 1.0.0
		 */
		private function getShippingDetails( $token ) {

			// Bail if token is empty.
			if ( empty( $token ) ) {
				return false;
			}

		    /**
		     * Build a second API request to PayPal, using the token
		     * as the ID to get the details on the payment authorization.
		     */
		    $nvpstr 	= "TOKEN=" . $token;

		    $resArray 	= $this->hash_call( "GetExpressCheckoutDetails", $nvpstr );
		    $ack 		= strtoupper( $resArray[ "ACK" ] );
			if ( "SUCCESS" == $ack || "SUCCESSWITHWARNING" == $ack ) {
				return $resArray;
			}
			return false;

		}

		/**
		 * Method: Prepares the parameters for the GetExpressCheckoutDetails API Call.
		 *
		 * @return array The NVP Collection object of the GetExpressCheckoutDetails Call Response.
		 * @since  1.0.0
		 */
		private function confirm_payment( $token, $payer_id, $membership_id ) {

			// Bail if parameters are empty.
			if ( empty( $token ) || empty( $payer_id) || empty( $membership_id ) ) {
				return false;
			}

			// Get membership object.
			$membership 	= ims_get_membership_object( $membership_id );
			$price	 		= $membership->get_price();

			// Get currency code.
			$ims_basic_settings	= get_option( 'ims_basic_settings' );
			$currency_code		= $ims_basic_settings[ 'ims_currency_code' ];
			if ( empty( $currency_code ) ) {
				$currency_code 	= 'USD';
			}

			$token 			= urlencode( $token );
			$paymentType 	= urlencode( "Sale" );
			$currency_code 	= urlencode( $currency_code );
			$payer_id 		= urlencode( $payer_id );
			$serverName 	= urlencode( $_SERVER[ 'SERVER_NAME' ] );
			$nvpstr = 	'TOKEN=' . $token;
			$nvpstr .= 	'&PAYERID=' . $payer_id;
			$nvpstr .= 	'&PAYMENTACTION=' . $paymentType;
			$nvpstr .= 	'&AMT=' . $price;
			$nvpstr .= 	'&CURRENCYCODE=' . $currency_code . '&IPADDRESS=' . $serverName;

			// Make the call to PayPal to finalize payment.
			$resArray	= $this->hash_call( "DoExpressCheckoutPayment", $nvpstr );
			$ack 		= strtoupper( $resArray[ "ACK" ] );
			if ( "SUCCESS" == $ack || "SUCCESSWITHWARNING" == $ack ) {
				return $resArray;
			}
			return false;

		}

		/**
		 * Method: Creates a profile that charges the customer.
		 *
		 * @since  1.0.0
		 */
		private function createRecurringPaymentsProfile( $shipping_details, $membership_id, $user_id ) {

			// Bail if parameters are empty.
			if ( empty( $shipping_details ) || empty( $membership_id ) || empty( $user_id ) ) {
				return false;
			}

			// Get shipping details obtained from GetShippingDetails API call.
			if ( array_key_exists( 'TOKEN', $shipping_details ) && ! empty( $shipping_details[ 'TOKEN' ] ) ) {
				$token 			= urlencode( $shipping_details[ 'TOKEN' ] );
			}
			if ( array_key_exists( 'PAYERID', $shipping_details ) && ! empty( $shipping_details[ 'PAYERID' ] ) ) {
				$payer_id 		= urlencode( $shipping_details[ 'PAYERID' ] );
			}
			if ( array_key_exists( 'SHIPTONAME', $shipping_details ) && ! empty( $shipping_details[ 'SHIPTONAME' ] ) ) {
				$shipToName 	= urlencode( $shipping_details[ 'SHIPTONAME' ] );
			}
			if ( array_key_exists( 'SHIPTOSTREET', $shipping_details ) && ! empty( $shipping_details[ 'SHIPTOSTREET' ] ) ) {
				$shipToStreet 	= urlencode( $shipping_details[ 'SHIPTOSTREET' ] );
			}
			if ( array_key_exists( 'SHIPTOCITY', $shipping_details ) && ! empty( $shipping_details[ 'SHIPTOCITY' ] ) ) {
				$shipToCity 	= urlencode( $shipping_details[ 'SHIPTOCITY' ] );
			}
			if ( array_key_exists( 'SHIPTOSTATE', $shipping_details ) && ! empty( $shipping_details[ 'SHIPTOSTATE' ] ) ) {
				$shipToState 	= urlencode( $shipping_details[ 'SHIPTOSTATE' ] );
			}
			if ( array_key_exists( 'SHIPTOZIP', $shipping_details ) && ! empty( $shipping_details[ 'SHIPTOZIP' ] ) ) {
				$shipToZip 		= urlencode( $shipping_details[ 'SHIPTOZIP' ] );
			}
			if ( array_key_exists( 'SHIPTOCOUNTRYCODE', $shipping_details ) && ! empty( $shipping_details[ 'SHIPTOCOUNTRYCODE' ] ) ) {
				$shipToCountry 	= urlencode( $shipping_details[ 'SHIPTOCOUNTRYCODE' ] );
			}

			// Get membership object.
			$membership 	= ims_get_membership_object( $membership_id );
			$title 			= get_the_title( $membership_id );
			$description	= __( 'Payment for ', 'inspiry-memberships' ) . $title;
			$price 			= $membership->get_price();
			$duration 		= $membership->get_duration();
			$time_period 	= $membership->get_duration_unit();
			if ( ! empty( $time_period ) && ( 'days' == $time_period ) ) {
				$period 	= "Day";
			} elseif ( ! empty( $time_period ) && ( 'weeks' == $time_period ) ) {
				$period 	= "Week";
			} elseif ( ! empty( $time_period ) && ( 'months' == $time_period ) ) {
				$period 	= "Month";
			} elseif ( ! empty( $time_period ) && ( 'years' == $time_period ) ) {
				$period 	= "Year";
			}

			// Get user email.
			$user	= get_user_by( 'id', $user_id );
			if ( ! empty( $user ) ) {
				$user_email	= $user->user_email;
			}

			// Get currency code.
			$ims_basic_settings	= get_option( 'ims_basic_settings' );
			$currency_code		= $ims_basic_settings[ 'ims_currency_code' ];
			if ( empty( $currency_code ) ) {
				$currency_code 	= 'USD';
			}

		    /**
		     * Build a second API request to PayPal, using the token
		     * as the ID to get the details on the payment authorization.
		     */
			$nvpstr =	"TOKEN=" . $token;
			$nvpstr .=	"&SUBSCRIBERNAME=" . urlencode( get_bloginfo( 'name' ) );
			$nvpstr	.=	"&PAYERID=" . $payer_id;
			$nvpstr	.=	"&EMAIL=" . urlencode( $user_email );
			$nvpstr	.=	"&SHIPTONAME=" . $shipToName;
			$nvpstr	.=	"&SHIPTOSTREET=" . $shipToStreet;
			$nvpstr	.=	"&SHIPTOCITY=" . $shipToCity;
			$nvpstr	.=	"&SHIPTOSTATE=" . $shipToState;
			$nvpstr	.=	"&SHIPTOZIP=" . $shipToZip;
			$nvpstr	.=	"&SHIPTOCOUNTRY=" . $shipToCountry;
			$nvpstr	.=	"&PROFILESTARTDATE=" . urlencode( date( 'Y-m-d H:i:s', time() ) );
			$nvpstr	.=	"&DESC=" . urlencode( $description );
			$nvpstr	.=	"&BILLINGPERIOD=" . urlencode( $period );
			$nvpstr	.=	"&BILLINGFREQUENCY=" . urlencode( $duration );
			$nvpstr	.=	"&AMT=" . urlencode( $price );
			$nvpstr .= 	"&INITAMT=" . urlencode( $price );
			$nvpstr	.=	"&CURRENCYCODE=" . urlencode( $currency_code );
			$nvpstr	.=	"&IPADDRESS=" . $_SERVER['REMOTE_ADDR'];
			$nvpstr .= 	"&MAXFAILEDPAYMENTS=1";
			$nvpstr .= 	"&AUTOBILLOUTAMT=AddToNextBilling";

			/**
			 * Make the API call and store the results in an array.
			 * If the call was a success, show the authorization
			 * details, and provide an action to complete the
			 * payment. If failed, then return false.
			 */
			$resArray 	= $this->hash_call( "CreateRecurringPaymentsProfile", $nvpstr );
			$ack 		= strtoupper( $resArray[ "ACK" ] );
			if ( "SUCCESS" == $ack || "SUCCESSWITHWARNING" == $ack ) {
				return $resArray;
			}
			return false;

		}

		/**
		 * Method: Function to perform the API call to PayPal using API signature.
		 *
		 * @param  string    $methodName 	Name of API method
		 * @param  string    $nvpStr 		nvp string
		 * @return array
		 * @since  1.0.0
		 */
		private function hash_call( $methodName, $nvpStr ) {

			// Setting the PayPal setting variables.
			$API_UserName 	= $this->api_username;
			$API_Password 	= $this->api_password;
			$API_Signature 	= $this->api_signature;

			// Get PayPal Settings.
			$paypal_settings 	= get_option( 'ims_paypal_settings' );
			$sandbox_mode 		= $paypal_settings[ 'ims_paypal_test_mode' ];

			if ( ! empty( $sandbox_mode ) && ( 'on' === $sandbox_mode ) ) {

				$this->api_endpoint = "https://api-3t.sandbox.paypal.com/nvp";
				$API_Endpoint 		= $this->api_endpoint;
				$this->express_checkout_url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
				$PAYPAL_URL 		= $this->express_checkout_url;

			} else {

				$this->api_endpoint = "https://api-3t.paypal.com/nvp";
				$API_Endpoint 		= $this->api_endpoint;
				$this->express_checkout_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
				$PAYPAL_URL 		= $this->express_checkout_url;

			}

			$version 	= urlencode( '64' );

			// Setting the curl parameters.
			$ch 	= curl_init();
			curl_setopt( $ch, CURLOPT_URL, $API_Endpoint );
			curl_setopt( $ch, CURLOPT_VERBOSE, 1 );

			// Turning off the server and peer verification ( TrustManager Concept ).
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_POST, 1 );

			// NVPRequest for submitting to server.
			$nvpreq = 	"METHOD=" . urlencode( $methodName );
			$nvpreq .= 	"&VERSION=" . urlencode( $version );
			$nvpreq	.=	"&PWD=" . urlencode( $API_Password );
			$nvpreq .= 	"&USER=" . urlencode( $API_UserName );
			$nvpreq .= 	"&SIGNATURE=" . urlencode( $API_Signature );
			$nvpreq .= 	"&" . $nvpStr;

			// Setting the nvpreq as POST FIELD to curl.
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $nvpreq );

			// Getting response from server.
			$result	= curl_exec( $ch );

			// Closing the curl.
			curl_close( $ch );

			if ( empty( $result ) ) {
				return false;
			} else {
			  	// Converting NVPResponse to an Associative Array.
				$nvpResArray = $this->deformatNVP( $result );
			}

			return $nvpResArray;

		}

		/**
		 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
	  	 * It is useful to search for a particular key and displaying arrays.
		 *
		 * @param  string    $nvpstr
		 * @return array
		 * @since  1.0.0
		 */
		private function deformatNVP( $nvpstr ) {

			$intial 	= 0;
		 	$nvpArray 	= array();
			while ( strlen( $nvpstr ) ) {

				// Postion of Key.
				$keypos = strpos( $nvpstr, '=' );

				// Position of value.
				$valuepos	= ( strpos( $nvpstr,'&' ) ) ? strpos( $nvpstr,'&' ) : strlen( $nvpstr );

				// Getting the Key and Value values and storing in a Associative Array.
				$keyval		= substr( $nvpstr, $intial, $keypos );
				$valval		= substr( $nvpstr, $keypos + 1, $valuepos - $keypos - 1 );

				// Decoding the response.
				$nvpArray[ urldecode( $keyval ) ] = urldecode( $valval );
				$nvpstr 	= substr( $nvpstr, $valuepos + 1, strlen( $nvpstr ) );
		    }

			return $nvpArray;

		}

	}

endif;
