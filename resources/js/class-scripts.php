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
		 * Instance.
		 *
		 * @var 	object
		 * @since 	1.0.0
		 */
		protected static $_instance;

		/**
		 * Method: Create and return instance of the class.
		 *
		 * @since 1.0.0
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;

		}

		/**
		 * Method: Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		}

		/**
		 * load_scripts.
		 *
		 * @since 1.0.0
		 */
		public function load_scripts() {

			if ( ! is_admin() ) {

				// JS functions file.
                wp_register_script(
                    'ims-custom-js',
                    IMS_BASE_URL . 'resources/js/custom.js',
                    array( 'jquery' ),
                    IMS_VERSION,
                    true
                );

                // data to print in JavaScript format above edit profile script tag in HTML
                $ims_js_data 	= array(
                    'ajaxURL'	=> admin_url( 'admin-ajax.php' )
                );

                wp_localize_script( 'ims-custom-js', 'jsData', $ims_js_data );
                wp_enqueue_script( 'ims-custom-js' );

			}

		}

	}

endif;


/**
 * Returns the instance of IMS_Scripts.
 *
 * @since 1.0.0
 */
function IMS_Scripts() {
	return IMS_Scripts::instance();
}
IMS_Scripts();
