<?php
/**
 * Plugin Name:     Inspiry Memberships
 * Plugin URI:      https://github.com/InspiryThemes/inspiry-memberships
 * Description:     Provides functionality to create membership packages for real estate themes by Inspiry Themes
 * Version:         1.0.1
 * Author:          Inspiry Themes
 * Author URI:      https://inspirythemes.com
 * Contributors:	inspirythemes, mrasharirfan, saqibsarwar
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     inspiry-memberships
 * Domain Path:     /languages/
 *
 * GitHub Plugin URI: https://github.com/InspiryThemes/inspiry-memberships
 *
 * @since            1.0.0
 * @package          IMS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Inspiry_Memberships' ) ) :

	/**
	 * Inspiry_Memberships.
	 *
	 * Plugin Core Class.
	 *
	 * @since 1.0.0
	 */
	class Inspiry_Memberships {

		/**
		 * Version.
		 *
		 * @var    string
		 * @since    1.0.0
		 */
		public $version = '1.0.0';

		/**
		 * Inspiry Memberships Instance.
		 *
		 * @var    Inspiry_Memberships
		 * @since    1.0.0
		 */
		protected static $_instance;

		/**
		 * Method: Creates an instance of the class.
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

			// Plugin version.
			if ( ! defined( 'IMS_VERSION' ) ) {
				define( 'IMS_VERSION', $this->version );
			}

			// Plugin Name.
			if ( ! defined( 'IMS_BASE_NAME' ) ) {
				define( 'IMS_BASE_NAME', plugin_basename( __FILE__ ) );
			}

			// Plugin Directory URL.
			if ( ! defined( 'IMS_BASE_URL' ) ) {
				define( 'IMS_BASE_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Directory Path.
			if ( ! defined( 'IMS_BASE_DIR' ) ) {
				define( 'IMS_BASE_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Docs URL.
			if ( ! defined( 'IMS_DOCS_URL' ) ) {
				define( 'IMS_DOCS_URL', 'http://realhomes.inspirythemes.biz/doc/#ims-installation' );
			}

			// Plugin Issue Reporting URL.
			if ( ! defined( 'IMS_ISSUE_URL' ) ) {
				define( 'IMS_ISSUE_URL', 'https://github.com/InspiryThemes/inspiry-memberships/issues' );
			}

		}

		/**
		 * Method: Include files.
		 *
		 * @since 1.0.0
		 */
		public function include_files() {

			/**
			 * Class-ims-install.
			 *
			 * @since 1.0.0
			 */
			if ( file_exists( IMS_BASE_DIR . '/resources/includes/class-ims-install.php' ) ) {
				include_once( IMS_BASE_DIR . '/resources/includes/class-ims-install.php' );
			}

			/**
			 * Class-ims-uninstall.
			 *
			 * @since 1.0.0
			 */
			if ( file_exists( IMS_BASE_DIR . '/resources/includes/class-ims-uninstall.php' ) ) {
				include_once( IMS_BASE_DIR . '/resources/includes/class-ims-uninstall.php' );
			}

			/**
			 * IMS-init.php.
			 *
			 * @since 1.0.0
			 */
			if ( file_exists( IMS_BASE_DIR . '/resources/ims-init.php' ) ) {
				include_once( IMS_BASE_DIR . '/resources/ims-init.php' );
			}

		}

		/**
		 * Method: Initialization hooks.
		 *
		 * @since 1.0.0
		 */
		public function init_hooks() {

			register_activation_hook( __FILE__, array( 'IMS_Install', 'install' ) );

			add_filter( 'plugin_action_links_' . IMS_BASE_NAME, array( $this, 'settings_action_link' ) );

			register_deactivation_hook( __FILE__, array( 'IMS_Uninstall', 'uninstall' ) );
		}

		/**
		 * Add plugin settings link
		 *
		 * @param string $links - links related to plugin.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function settings_action_link( $links ) {
			$links[] = '<a href="' . get_admin_url( null, 'admin.php?page=ims_settings' ) . '">' . esc_html__( 'Settings', 'inspiry-memberships' ) . '</a>';

			return $links;
		}
	}

endif;


/**
 * Returns the main instance of Inspiry_Memberships.
 *
 * @since 1.0.0
 */
function ims() {
	return Inspiry_Memberships::instance();
}

ims();
