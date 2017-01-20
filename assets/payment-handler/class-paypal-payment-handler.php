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
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Get PayPal settings.
			$paypal_settings		= get_option( 'ims_paypal_settings' );

			// Set the variables.
			if ( isset( $paypal_settings[ 'ims_paypal_client_id' ] ) && ! empty( $paypal_settings[ 'ims_paypal_client_id' ] ) ) {
				$this->client_ID	= $paypal_settings[ 'ims_paypal_client_id' ];
			}

			if ( isset( $paypal_settings[ 'ims_paypal_client_secret' ] ) && ! empty( $paypal_settings[ 'ims_paypal_client_secret' ] ) ) {
				$this->client_secret	= $paypal_settings[ 'ims_paypal_client_secret' ];
			}

			$this->uri_live		= "https://api.paypal.com/v1/";
			$this->uri_sandbox	= "https://api.sandbox.paypal.com/v1/";

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
		 * Method: Start processing PayPal payment.
		 *
		 * @since 1.0.0
		 */
		public function process_paypal_payment() {

			// Get membership id.
			$membership_id 	= intval( $_POST[ 'membership_id' ] );

			// Get current user.
			$user 		= wp_get_current_user();
			$user_id 	= $user->ID;

			if ( ! empty( $membership_id ) && ! empty( $user_id ) ) {

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

				if ( ! empty( $sandbox_mode ) && ( 'on' == $sandbox_mode ) ) {
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

	}

endif;
