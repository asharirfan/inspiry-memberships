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

	}

endif;
