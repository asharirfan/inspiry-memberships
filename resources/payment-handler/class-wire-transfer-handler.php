<?php
/**
 * Wire Transfer Functions Class
 *
 * Class file for wire transfer payment functions.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Wire_Transfer_Handler.
 *
 * Class for wire transfer payment functions.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Wire_Transfer_Handler' ) ) :

	class IMS_Wire_Transfer_Handler {

		/**
		 * Method: Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Action to run event on
			 * Doesn't need to be an existing WordPress action
			 *
			 * @param string - ims_wire_membership_schedule_end
			 * @param string - wire_membership_schedule_end
			 */
			add_action( 'ims_wire_membership_schedule_end', array( $this, 'wire_membership_schedule_end' ), 10, 2 );

		}

		/**
		 * Method: Ajax callback to send receipt to user.
		 *
		 * @since 1.0.0
		 */
		public function send_wire_receipt() {

			if ( isset( $_POST[ 'action' ] ) && 'ims_send_wire_receipt' === $_POST[ 'action' ] &&
				isset( $_POST[ 'nonce' ] ) && wp_verify_nonce( $_POST[ 'nonce' ], 'membership-wire-nonce' ) ) {

				// Get membership id.
				$membership_id	= ( isset( $_POST[ 'membership_id' ] ) ) ? intval( $_POST[ 'membership_id' ] ) : false;

				// Get current user.
				$user 		= wp_get_current_user();
				$user_id	= $user->ID;
				$user_email	= $user->user_email;

				if ( empty( $membership_id ) || empty( $user_id ) ) {
					echo json_encode( array(
						'success'	=> false,
						'message'	=> __( 'Please select a membership to continue.', 'inspiry-memberships' )
					) );
					die();
				}

				$membership_title	= get_the_title( $membership_id );
				$membership_obj		= ims_get_membership_object( $membership_id ); // Membership object.
				$price 				= $membership_obj->get_price(); // Membership price (unformatted).
				$formatted_price	= IMS_Functions::get_formatted_price( $price ); // Membership price (formatted).

				$receipt_methods	= new IMS_Receipt_Method();
				$receipt_id 		= $receipt_methods->generate_wire_transfer_receipt( $user_id, $membership_id, false );

				// Membership Receipt Mail.
				$subject	= 	__( 'Membership Receipt.', 'inspiry-memberships' );

				$message 	= 	sprintf( __( 'You have successfully applied for %s package on our site.', 'inspiry-memberships' ), $membership_title ) . "<br/><br/>";
				$message 	.= 	__( 'Your chose to pay via Wire Transfer.', 'inspiry-memberships' ) . "<br/><br/>";
				$message 	.= 	sprintf( __( 'Please send a payment of %s to the account mentioned on the website.', 'inspiry-memberships' ), $formatted_price ) . "<br/><br/>";
				$message 	.= 	sprintf( __( 'Please include the receipt name, Receipt %s, in the payment details.', 'inspiry-memberships' ), $receipt_id ) . "<br/><br/>";
				$message 	.= 	__( 'We will activate your membership as soon as we get confirmation of your payment.', 'inspiry-memberships' );

				$message 	= apply_filters( 'ims_membership_receipt_email_message', $message, $user_id, $membership_id, $receipt_id );

				if ( is_email( $user_email ) ) {
					$email 	= IMS_Email::send_email( $user_email, $subject, $message );
				} else {
					echo json_encode( array(
						'success'	=> false,
						'message'	=> __( 'Your email address is not valid.', 'inspiry-memberships' )
					) );
					die();
				}

				if ( $email ) {
					echo json_encode( array(
						'success'	=> true,
						'message'	=> __( 'Email sent successfully.', 'inspiry-memberships' )
					) );
				} else {
					echo json_encode( array(
						'success'	=> false,
						'message'	=> __( 'Error occured while sending email.', 'inspiry-memberships' )
					) );
				}
				die();

			}

		}

		/**
		 * Method: Activate membership via Wire Transfer.
		 *
		 * @param int $post_id - Receipt ID from where membership is activated
		 * @param object $post - Receipt Post Object
		 * @since 1.0.0
		 */
		public function activate_membership_via_wire( $post_id, $post ) {

			// Verify the nonce before proceeding.
			if ( ! isset( $_POST[ 'receipt_meta_box_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'receipt_meta_box_nonce' ], 'receipt-meta-box-nonce' ) ) {
				return false;
			}

			// Get the post type object.
			$post_type 	= get_post_type_object( $post->post_type );

			// Check if the post type is membership.
			if ( 'ims_receipt' !== $post->post_type ) {
				return false;
			}

			// Check if the current user has permission to edit the post.
			if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
				return false;
			}

			// Get receipt object.
			$receipt_obj	= ims_get_receipt_object( $post_id );
			if ( empty( $receipt_obj ) ) {
				return false;
			}

			// Get meta value of status and vendor.
			$receipt_id		= $post_id;
			$membership_id	= intval( $receipt_obj->get_membership_id() );
			$user_id 		= intval( $receipt_obj->get_user_id() );
			$status 		= ( ! empty( $_POST[ 'status' ] ) && ( 'on' === $_POST[ 'status' ] ) ) ? true : false;
			$vendor 		= $receipt_obj->get_vendor();
			$payment_id	 	= ( isset( $_POST[ 'payment_id' ] ) && ! empty( $_POST[ 'payment_id' ] ) ) ? sanitize_text_field( $_POST[ 'payment_id' ] ) : false;

			// Check if user has membership already.
			$membership_methods	= new IMS_Membership_Method();
			if ( $membership_methods->user_has_membership( $user_id ) ) {
				return false;
			}

			if ( ! empty( $membership_id ) && ! empty( $user_id ) && ! empty( $status ) && ! empty( $vendor ) && 'wire' === $vendor ) {

				// Add membership and mail to users.
				$membership_methods->add_user_membership( $user_id, $membership_id, $vendor );
				$this->schedule_membership_end( $user_id, $membership_id );
				$membership_methods->mail_user( $user_id, $membership_id, 'wire' );
				$membership_methods->mail_admin( $membership_id, $receipt_id, 'wire' );

				// Update receipt meta.
				$prefix	= apply_filters( 'ims_receipt_meta_prefix', 'ims_receipt_' );

				update_post_meta( $receipt_id, "{$prefix}status", true );

				if ( ! empty( $payment_id ) ) {
					update_post_meta( $receipt_id, "{$prefix}payment_id", $payment_id );
				}

				// Add action hook after wire payment is done.
				do_action( 'ims_wire_payment_success', $user_id, $membership_id, $receipt_id );

				return true;
			}

			// Add action hook after wire payment failed.
			do_action( 'ims_wire_payment_failed' );
			return false;

		}

		/**
		 * Method: Schedule Wire membership end.
		 *
		 * @param int $user_id - User ID who purchased membership
		 * @param int $membership_id - ID of the membership purchased
		 * @since 1.0.0
		 */
		public function schedule_membership_end( $user_id, $membership_id ) {

			// Bail if user or membership id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) ) {
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
			 * Schedule the event
			 *
			 * @param int - unix timestamp of when to run the event
			 * @param string - ims_paypal_membership_schedule_end
			 */
			wp_schedule_single_event( time() + $time_duration, 'ims_wire_membership_schedule_end', $schedule_args );

			// Membership schedulled action hook.
			do_action( 'ims_wire_membership_schedulled', $user_id, $membership_id );

		}

		/**
		 * Method: Function to be called when ims_wire_membership_schedule_end
		 * event is fired.
		 *
		 * @param int $user_id - User ID who purchased membership
		 * @param int $membership_id - ID of the membership purchased
		 * @since 1.0.0
		 */
		public function wire_membership_schedule_end( $user_id, $membership_id ) {

			// Bail if user or membership id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) ) {
				return;
			}

			$ims_membership_methods	= new IMS_Membership_Method();
			$ims_membership_methods->cancel_user_membership( $user_id, $membership_id );

		}

		/**
		 * Method: Cancel membership when vendor is wire.
		 *
		 * @since 1.0.0
		 */
		public static function cancel_wire_membership( $user_id ) {

			// Bail if user id is empty.
			if ( empty( $user_id ) ) {
				return;
			}

			// Get current membership.
			$membership_id	= get_user_meta( $user_id, 'ims_current_membership', true );

			// Cancel it.
			$ims_membership_methods	= new IMS_Membership_Method();
			$ims_membership_methods->cancel_user_membership( $user_id, $membership_id );

			// Redirect on success.
			$redirect = esc_url( add_query_arg( array( 'request' => 'submitted' ), home_url() ) );
			wp_redirect( $redirect );
			exit;

		}

	}

endif;
