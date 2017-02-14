<?php
/**
 * Activator Class
 *
 * Activator class for Inspiry Memberships plugin.
 *
 * @since 	1.0.0
 * @package inspiry_memberships
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Install.
 *
 * This class contains functions which run on activating the plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Install' ) ) :

	class IMS_Install {

		/**
		 * activate.
		 *
		 * @since 1.0.0
		 */
		public static function install() {

			// Transient max age is 60 seconds.
			set_transient( '_welcome_redirect_ims', true, 60 );

		}

	}

endif;
