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
 * IMS_Uninstall.
 *
 * This class contains functions which run on deactivating the plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Uninstall' ) ) :

	class IMS_Uninstall {

		/**
		 * activate.
		 *
		 * @since 1.0.0
		 */
		public static function uninstall() {

			// Delete the welcome transient.
			delete_transient( '_welcome_redirect_ims' );

		}

	}

endif;
