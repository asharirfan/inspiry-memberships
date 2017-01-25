<?php
/**
 * Membership Methods Class
 *
 * Class file for membership methods used during
 * operations of the plugin.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Membership_Method.
 *
 * Class for membership methods used during
 * operations of the plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Membership_Method' ) ) :

	class IMS_Membership_Method {

		/**
		 * Method: Add membership details to user meta.
		 *
		 * @since 1.0.0
		 */
		public function add_user_membership( $user_id = 0, $membership_id = 0, $vendor = NULL ) {

			// Bail if membership or user id is empty.
			if ( empty( $membership_id ) || empty( $user_id ) ) {
				return;
			}

			// Get membership object.
			$membership_obj		= ims_get_membership_object( $membership_id );

			// Get current membership of user.
			$current_membership	= get_user_meta( $user_id, 'ims_current_membership', true );

			if ( empty( $current_membership ) ) {

				$time_due 	= $this->get_membership_due_date( $membership_id, current_time( 'timestamp' ) );
				$due_date 	= date( 'Y-m-d H:i:s', $time_due );

				// Add membership id to user meta.
				update_user_meta( $user_id, 'ims_current_membership', $membership_id );

				// Add number of properties available.
				update_user_meta( $user_id, 'ims_package_properties', $membership_obj->get_properties() );
				update_user_meta( $user_id, 'ims_current_properties', $membership_obj->get_properties() );
				update_user_meta( $user_id, 'ims_package_featured_props', $membership_obj->get_featured_properties() );
				update_user_meta( $user_id, 'ims_current_featured_props', $membership_obj->get_featured_properties() );
				update_user_meta( $user_id, 'ims_current_duration', $membership_obj->get_duration() );
				update_user_meta( $user_id, 'ims_current_duration_unit', $membership_obj->get_duration_unit() );
				update_user_meta( $user_id, 'ims_membership_due_date', $due_date );

				if ( ! empty( $vendor ) && 'stripe' == $vendor ) {

					// Current vendor is Stripe.
					update_user_meta( $user_id, 'ims_current_vendor', 'stripe' );
					update_user_meta( $user_id, 'ims_current_stripe_plan_id', $membership_obj->get_stripe_plan_id() );

				} elseif ( ! empty( $vendor ) && 'paypal' == $vendor ) {

					// Current vendor is PayPal.
					update_user_meta( $user_id, 'ims_current_vendor', 'paypal' );

				} elseif ( ! empty( $vendor ) && 'wire' == $vendor ) {

					// Current vendor is Wire Transfer.
					update_user_meta( $user_id, 'ims_current_vendor', 'wire' );

				}

			} else {
				// Update membership package if there is another package present before.
				self::update_user_membership( $user_id, $membership_id, $vendor );
			}

		}

		/**
		 * Method: Update membership of user.
		 *
		 * @since 1.0.0
		 */
		public function update_user_membership( $user_id = 0, $membership_id = 0, $vendor = NULL ) {

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

				$time_due 	= $this->get_membership_due_date( $new_membership_id, current_time( 'timestamp' ) );
				$due_date 	= date( 'Y-m-d H:i:s', $time_due );

				// Update membership id to user meta.
				update_user_meta( $user_id, 'ims_current_membership', $new_membership_id );

				// Update number of properties available.
				update_user_meta( $user_id, 'ims_package_properties', $new_properties );
				update_user_meta( $user_id, 'ims_current_properties', $new_properties 	);
				update_user_meta( $user_id, 'ims_package_featured_props', $new_featured_props );
				update_user_meta( $user_id, 'ims_current_featured_props', $new_featured_props );
				update_user_meta( $user_id, 'ims_current_duration', $new_membership_obj->get_duration() );
				update_user_meta( $user_id, 'ims_current_duration_unit', $new_membership_obj->get_duration_unit() );
				update_user_meta( $user_id, 'ims_membership_due_date', $due_date );

				if ( ! empty( $vendor ) && 'stripe' == $vendor ) {

					// Current vendor is Stripe.
					update_user_meta( $user_id, 'ims_current_vendor', 'stripe' );
					update_user_meta( $user_id, 'ims_current_stripe_plan_id', $new_membership_obj->get_stripe_plan_id() );

				} elseif ( ! empty( $vendor ) && 'paypal' == $vendor ) {

					// Current vendor is PayPal.
					update_user_meta( $user_id, 'ims_current_vendor', 'paypal' );

				} elseif ( ! empty( $vendor ) && 'wire' == $vendor ) {

					// Current vendor is Wire Transfer.
					update_user_meta( $user_id, 'ims_current_vendor', 'wire' );

				}

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

				/**
				 * Get all published properties and convert
				 * them to draft either on update or new
				 * membership.
				 */
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
		 * Method: Mail User to notify about membership purchase.
		 *
		 * @since 1.0.0
		 */
		public function mail_user( $user_id = 0, $membership_id = 0, $vendor = NULL, $recurring = false ) {

			// Bail if user, membership or receipt id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) ) {
				return;
			}

			// Set vendor.
			if ( ! empty( $vendor ) && 'paypal' == $vendor ) {
				$vendor	= 'via PayPal';
			} elseif ( ! empty( $vendor ) && 'stripe' == $vendor ) {
				$vendor	= 'via Stripe';
			} elseif ( ! empty( $vendor ) && 'wire' == $vendor ) {
				$vendor	= 'via Wire Transfer';
			}

			// Get user.
			$user	= get_user_by( 'id', $user_id );
			if ( ! empty( $user ) ) {
				$user_email	= $user->user_email;
			}

			$membership = get_post( $membership_id );

			if ( empty( $recurring ) ) {

				// Purchase Membership Mail.
				$subject	= __( 'Membership Purchased.', 'inspiry-memberships' );

				$message 	= sprintf( __( 'You have successfully purchased %s membership package on our site.', 'inspiry-memberships' ), $membership->post_title ) . "<br/><br/>";
				$message 	.= sprintf( __( 'Your payment has been received successfully %s.', 'inspiry-memberships' ), $vendor ) . "<br/><br/>";
				$message 	.= __( 'To view the details, please visit your profile page on our website.', 'inspiry-memberships' );

			} elseif ( ! empty( $recurring ) ) {

				// Update Membership Mail.
				$subject	= __( 'Membership Updated Successfully.', 'inspiry-memberships' );

				$message 	= sprintf( __( 'Your membership package: %s has been successfully updated on our site.', 'inspiry-memberships' ), $membership->post_title ) . "<br/><br/>";
				$message 	.= sprintf( __( 'Your payment has been received successfully %s.', 'inspiry-memberships' ), $vendor ) . "<br/><br/>";
				$message 	.= __( 'To view the details, please visit your profile page on our website.', 'inspiry-memberships' );

			}

			if ( is_email( $user_email ) ) {
				IMS_Email::send_email( $user_email, $subject, $message );
			}

		}

		/**
		 * Method: Mail Admin to notify about membership purchase.
		 *
		 * @since 1.0.0
		 */
		public function mail_admin( $membership_id = 0, $receipt_id = 0, $vendor = NULL, $recurring = false ) {

			// Bail if membership or receipt id is empty.
			if ( empty( $membership_id ) || empty( $receipt_id ) ) {
				return;
			}

			// Set vendor.
			if ( ! empty( $vendor ) && 'paypal' == $vendor ) {
				$vendor	= 'via PayPal';
			} elseif ( ! empty( $vendor ) && 'stripe' == $vendor ) {
				$vendor	= 'via Stripe';
			} elseif ( ! empty( $vendor ) && 'wire' == $vendor ) {
				$vendor	= 'via Wire Transfer';
			}

			// Get admin email.
			$admin_email 	= get_bloginfo( 'admin_email' );

			// Get receipt edit link.
			$receipt_link 	= get_edit_post_link( $receipt_id );
			$receipt 		= get_post( $receipt_id );

			$membership 	= get_post( $membership_id );

			if ( empty( $recurring ) ) {

				$subject		= __( 'Membership Purchased.', 'inspiry-memberships' );

				$message 	= sprintf( __( 'A user successfully purchased %s membership package on your site.', 'inspiry-memberships' ), $membership->post_title ) . "<br/><br/>";
				$message 	.= sprintf( __( 'Payment has been submitted %s.', 'inspiry-memberships' ), $vendor ) . "<br/><br/>";
				$message 	.= __( 'To view the details, please visit : ', 'inspiry-memberships' );
				$message 	.= '<a target="_blank" href="' . $receipt_link . '">' . $receipt->post_title . '</a>';

			} elseif ( ! empty( $recurring ) ) {

				$subject		= __( 'Membership Updated.', 'inspiry-memberships' );

				$message 	= sprintf( __( 'A user successfully updated %s membership package on your site.', 'inspiry-memberships' ), $membership->post_title ) . "<br/><br/>";
				$message 	.= sprintf( __( 'Payment has been submitted %s.', 'inspiry-memberships' ), $vendor ) . "<br/><br/>";
				$message 	.= __( 'To view the details, please visit : ', 'inspiry-memberships' );
				$message 	.= '<a target="_blank" href="' . $receipt_link . '">' . $receipt->post_title . '</a>';

			}

			if ( is_email( $admin_email ) ) {
				IMS_Email::send_email( $admin_email, $subject, $message );
			}

		}

		/**
		 * Method: Cancel membership function.
		 *
		 * @since 1.0.0
		 */
		public function cancel_user_membership( $user_id = 0, $membership_id = 0 ) {

			// Bail if user id is empty.
			if ( empty( $user_id ) || empty( $membership_id ) ) {
				return;
			}

			$membership_id 	= intval( $membership_id );

			// Check membership id to confirm.
			$current_membership_id	= get_user_meta( $user_id, 'ims_current_membership', true );
			$current_membership_id 	= intval( $current_membership_id );

			if ( $current_membership_id !== $membership_id ) {
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
			delete_user_meta( $user_id, 'ims_membership_due_date' );

			// Delete meta related to vendors.
			$vendor 	= get_user_meta( $user_id, 'ims_current_vendor', true );

			if ( 'stripe' === $vendor ) {
				delete_user_meta( $user_id, 'ims_current_vendor' );
				delete_user_meta( $user_id, 'ims_current_stripe_plan_id' );
				delete_user_meta( $user_id, 'ims_stripe_subscription_id' );
				delete_user_meta( $user_id, 'ims_stripe_subscription_due' );
				delete_user_meta( $user_id, 'ims_stripe_customer_id' );
			} elseif ( 'paypal' === $vendor ) {
				delete_user_meta( $user_id, 'ims_current_vendor' );
				delete_user_meta( $user_id, 'ims_paypal_profile_id' );
			} elseif ( 'wire' === $vendor ) {
				delete_user_meta( $user_id, 'ims_current_vendor' );
			}

		}

		/**
		 * Method: Get user by PayPal Profile ID.
		 *
		 * @since 1.0.0
		 */
		public function get_user_by_paypal_profile( $profile_id ) {

			// Bail if user id is empty.
			if ( empty( $profile_id ) ) {
				return false;
			}

			$user_args 	= array(
				'meta_key'		=> 'ims_paypal_profile_id',
				'meta_value'	=> $profile_id,
				'meta_compare'	=> '='
			);
			$users 		= get_users( $user_args );

			if ( ! empty( $users ) ) {

				foreach ( $users as $user ) {
					$user_id 	= $user->ID;
				}
				return $user_id;

			}
			return false;

		}

		/**
		 * Method: Calculate Membership Due date.
		 *
		 * @since 1.0.0
		 */
		public function get_membership_due_date( $membership_id, $current_time ) {

			// Bail if paramters are empty.
			if ( empty( $membership_id ) || empty( $current_time ) ) {
				return false;
			}

			$membership 	= ims_get_membership_object( $membership_id );
			$time_duration	= $membership->get_duration();
			$time_unit 		= $membership->get_duration_unit();
			$seconds 		= 0;

			if ( 'days' == $time_unit ) {
				$seconds		= 24 * 60 * 60;
			} elseif ( 'months' == $time_unit ) {
				$seconds 		= 30 * 24 * 60 * 60;
			} elseif ( 'years' == $time_unit ) {
				$seconds 		= 365 * 24 * 60 * 60;
			}

			$time_duration		= $time_duration * $seconds;

			return $current_time + $time_duration;

		}

		/**
		 * Method: Update membership due date.
		 *
		 * @since 1.0.0
		 */
		public function update_membership_due_date( $membership_id, $user_id ) {

			// Bail if paramters are empty.
			if ( empty( $membership_id ) || empty( $user_id ) ) {
				return false;
			}

			$membership 	= ims_get_membership_object( $membership_id );
			$time_duration	= $membership->get_duration();
			$time_unit 		= $membership->get_duration_unit();
			$seconds 		= 0;

			if ( 'days' == $time_unit ) {
				$seconds		= 24 * 60 * 60;
			} elseif ( 'months' == $time_unit ) {
				$seconds 		= 30 * 24 * 60 * 60;
			} elseif ( 'years' == $time_unit ) {
				$seconds 		= 365 * 24 * 60 * 60;
			}

			$time_duration		= $time_duration * $seconds;
			$current_time 		= current_time( 'timestamp' );
			$due_time 			= $current_time + $time_duration;
			$due_date 			= date( 'Y-m-d H:i:s', $due_time );

			if ( update_user_meta( $user_id, 'ims_membership_due_date', $due_date ) ) {
				return true;
			}
			return false;

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

			$site_name 		= esc_html( get_bloginfo( 'name' ) );
			$site_url		= esc_url( get_bloginfo( 'url' ) );

			$subject		= __( 'Membership is about to end.', 'inspiry-memberships' );

			$message 	= sprintf( __( 'Your membership package on %s is about to end.', 'inspiry-memberships' ), $site_name ) . "<br/><br/>";
			$message 	.= __( 'Please make sure that you renew your membership within due date via Stripe.', 'inspiry-memberships' ) . "<br/><br/>";
			$message 	.= __( 'Otherwise your membership will be cancelled.', 'inspiry-memberships' ) . "<br/><br/>";
			$message 	.= '<a target="_blank" href="' . $site_url . '">' . $site_name . '</a>';

			if ( is_email( $user_email ) ) {
				IMS_Email::send_email( $user_email, $subject, $message );
			}

		}

	}

endif;
