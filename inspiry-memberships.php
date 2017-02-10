<?php
/**
 * Plugin Name:     Inspiry Memberships
 * Plugin URI:      https://github.com/InspiryThemes/inspiry-memberships
 * Description:     Provides functionality to create membership packages for real estate themes by Inspiry Themes
 * Version:         1.0.0
 * Author:          Inspiry Themes
 * Author URI:      https://inspirythemes.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     inspiry-memberships
 * Domain Path:     languages
 *
 * @link             https://github.com/InspiryThemes/inspiry-memberships
 * @since            1.0.0
 * @package          IMS
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Inspiry_Memberships.
 *
 * Plugin Core Class.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'Inspiry_Memberships' ) ) :

	class Inspiry_Memberships {

		/**
		 * Version.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 public $version = '1.0.0';

		/**
		 * Inspiry Memberships Instance.
		 *
		 * @var 	Inspiry_Memberships
		 * @since 	1.0.0
		 */
		 protected static $_instance;

		/**
		 * Method: Creates an instance of the class.
		 *
		 * @since 1.0.0
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance	= new self();
			}
			return self::$_instance;

		}

		/**
		 * Method: Contructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Get started here.
			$this->define_constants();
			$this->include_files();
			$this->init_hooks();

			// Plugin is loaded.
			do_action( 'ims_loaded' );

		}

		/**
		 * Method: Define constants.
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {

			// Plugin version
			if ( ! defined( 'IMS_VERSION' ) ) {
			    define( 'IMS_VERSION', $this->version );
			}

			// Plugin Name
			if ( ! defined( 'IMS_BASE_NAME' ) ) {
				define( 'IMS_BASE_NAME', plugin_basename( __FILE__ ) );
			}

			// Plugin Directory URL
			if ( ! defined( 'IMS_BASE_URL' ) ) {
				define( 'IMS_BASE_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Directory Path
			if ( ! defined( 'IMS_BASE_DIR' ) ) {
				define( 'IMS_BASE_DIR', plugin_dir_path( __FILE__ ) );
			}

		}

		/**
		 * Method: Include files.
		 *
		 * @since 1.0.0
		 */
		public function include_files() {

			/**
			 * class-ims-install.
			 *
			 * @since 1.0.0
			 */
			if ( file_exists( IMS_BASE_DIR . '/assets/includes/class-ims-install.php' ) ) {
			    require_once( IMS_BASE_DIR . '/assets/includes/class-ims-install.php' );
			}

			/**
			 * class-ims-uninstall.
			 *
			 * @since 1.0.0
			 */
			if ( file_exists( IMS_BASE_DIR . '/assets/includes/class-ims-uninstall.php' ) ) {
			    require_once( IMS_BASE_DIR . '/assets/includes/class-ims-uninstall.php' );
			}

			/**
			 * ims-init.php.
			 *
			 * @since 1.0.0
			 */
			if ( file_exists( IMS_BASE_DIR . '/assets/ims-init.php' ) ) {
			    require_once( IMS_BASE_DIR . '/assets/ims-init.php' );
			}

		}

		/**
		 * Method: Initialization hooks.
		 *
		 * @since 1.0.0
		 */
		public function init_hooks() {

			register_activation_hook( __FILE__, array( 'IMS_Install', 'install' ) );
			register_deactivation_hook( __FILE__, array( 'IMS_Uninstall', 'uninstall' ) );

			// add_action( 'init', array( __CLASS__, 'init' ) );

		}

	}

endif;


/**
 * Returns the main instance of Inspiry_Memberships.
 *
 * @since 1.0.0
 */
function IMS() {
	return Inspiry_Memberships::instance();
}
IMS();
