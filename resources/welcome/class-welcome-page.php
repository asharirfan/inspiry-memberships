<?php
/**
 * Welcome page class file
 *
 * Class file for welcome page of plugin.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Welcome_Page.
 *
 * Class for welcome page of plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Welcome_Page' ) ) :

	class IMS_Welcome_Page {

		/**
		 * Welcome Page.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		protected static $_welcome_page;

		/**
		 * Method: Redirect to welcome page on activation.
		 *
		 * @since 1.0.0
		 */
		public static function welcome_page_redirect() {

			// Bail if no activation redirect transient is present.
			if ( ! get_transient( '_welcome_redirect_ims' ) ) {
				return;
			}

			// Delete the redirect transient.
			delete_transient( '_welcome_redirect_ims' );

			// Bail if activating from network or bulk sites.
			if ( is_network_admin() || isset( $_GET[ 'activate-multi' ] ) ) {
				return;
			}

			// Redirects to Welcome Page.
			wp_safe_redirect( add_query_arg(
				array( 'page' => 'ims_welcome_page' ),
				admin_url( 'plugins.php' )
			) );

		}

		/**
		 * Method: Add welcome page to admin menu.
		 *
		 * @since 1.0.0
		 */
		public static function add_to_admin_menu() {

			$welcome_sub_menu	= add_submenu_page(
				'plugins.php', // The slug name for the parent menu.
				__( 'Inspiry Memberships', 'inspiry-memberships' ),
		    	__( 'Inspiry Memberships', 'inspiry-memberships' ), // The text to be used for the menu.
				'read', // The capability required for this menu to be displayed to the user.
				'ims_welcome_page', // The slug name to refer to this menu by (should be unique for this menu).
				array( __CLASS__, 'welcome_page_content' ) // The function to be called to output the content for this page.
			);
			self::$_welcome_page = $welcome_sub_menu;

		}

		/**
		 * Method: Welcome page content.
		 *
		 * @since 1.0.0
		 */
		public static function welcome_page_content() {

			/**
			 * welcome-page.
			 *
			 * @since 1.0.0
			 */
			if ( file_exists( IMS_BASE_DIR . '/resources/welcome/welcome-page.php' ) ) {
			    include_once( IMS_BASE_DIR . '/resources/welcome/welcome-page.php' );
			}

		}

		/**
		 * Method: Enqueue styles for welcome page.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_welcome_page_styles( $hook ) {

			// Add style to the welcome page only.
			if ( $hook !== self::$_welcome_page ) {
				return;
			}

			// Welcome page styles.
			wp_enqueue_style(
				'wpw_style',
				IMS_BASE_URL . '/resources/css/welcome.css',
				array(),
				IMS_VERSION,
				'all'
			);

		}

		/**
		 * Method: Enqueue scripts for welcome page.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_welcome_page_scripts( $hook ) {

			// Add script to the welcome page only.
			if ( $hook !== self::$_welcome_page ) {
				return;
			}

			// JS functions file.
            wp_register_script(
                'ims-welcome-js',
                IMS_BASE_URL . 'resources/js/welcome.js',
                array( 'jquery' ),
                IMS_VERSION,
                true
            );

            // data to print in JavaScript format above edit profile script tag in HTML
            $ims_js_data 	= array(
                'ajaxURL'	=> admin_url( 'admin-ajax.php' )
            );

            wp_localize_script( 'ims-welcome-js', 'jsData', $ims_js_data );
            wp_enqueue_script( 'ims-welcome-js' );

		}

	}

endif;
