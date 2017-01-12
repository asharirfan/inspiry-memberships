<?php
/**
 * Email Class File
 *
 * This class is used to send emails by this plugin.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Email.
 *
 * This class is used to send emails by this plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Email' ) ) :

	class IMS_Email {

		/**
		 * Method: Send Email.
		 *
		 * @since 1.0.0
		 */
		public static function send_email( $to_email, $subject, $message ) {

			/**
			 * The blogname option is escaped with esc_html on the way into the database in sanitize_option
			 * we want to reverse this for the plain text arena of emails.
			 */
			$website_name	= wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

			/**
		     * Email Headers ( Reply To and Content Type )
		     */
			$headers 		= array();
			$headers[] 		= "Content-Type: text/html; charset=UTF-8";
			$headers 		= apply_filters( 'ims_email_header', $headers );

			$email_message	= sprintf( '%s', $subject ) . "<br/><br/>";
			$email_message	.= $message;

			wp_mail( $to_email, $subject, $email_message, $headers );

		}

	}

endif;
