<?php
/**
 * Deactivator Class
 *
 * Deactivator class for Inspiry Memberships plugin.
 *
 * @since 	1.0.0
 * @package inspiry_memberships
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Deactivator.
 *
 * This class contains functions which run on deactivating the plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Deactivator' ) ) :

	class IMS_Deactivator {

		/**
		 * activate.
		 *
		 * @since 1.0.0
		 */
		public static function deactivate() {

			// Delete the welcome transient.
			delete_transient( '_welcome_redirect_ims' );

			// Delete plugin options.
			delete_option( 'ims_basic_settings' );
			delete_option( 'ims_stripe_settings' );
			delete_option( 'ims_paypal_settings' );
			delete_option( 'ims_wire_settings' );

		}

	}

endif;
