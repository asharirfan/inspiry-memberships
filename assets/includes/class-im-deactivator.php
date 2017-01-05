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
 * Inspiry_Memberships_Deactivator.
 *
 * This class contains functions which run on deactivating the plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'Inspiry_Memberships_Deactivator' ) ) :

	class Inspiry_Memberships_Deactivator {

		/**
		 * activate.
		 *
		 * @since 1.0.0
		 */
		public static function deactivate() {

		}

	}

endif;
