<?php
/**
 * IMS Scriptions Class
 *
 * Class file for loading plugin scripts.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Scripts.
 *
 * Class file for loading plugin scripts.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Scripts' ) ) :

	class IMS_Scripts {

		/**
		 * load_scripts.
		 *
		 * @since 1.0.0
		 */
		public static function load_scripts() {

			if ( ! is_admin() ) {

				// JS functions file.
                wp_register_script(
                    'ims-custom-js',
                    IMS_BASE_URL . 'assets/js/custom.js',
                    array( 'jquery' ),
                    IMS_VERSION,
                    true
                );
                wp_enqueue_script( 'ims-custom-js' );

			}

		}

	}

endif;


if ( class_exists( 'IMS_Scripts' ) ) {
	add_action( 'wp_enqueue_scripts', array( 'IMS_Scripts' , 'load_scripts' ) );
}
