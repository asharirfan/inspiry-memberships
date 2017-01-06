<?php
/**
 * Admin Menu CLass
 *
 * Class file for admin menu of plugin.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Admin_Menu.
 *
 * Class for admin menu of plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Admin_Menu' ) ) :

	class IMS_Admin_Menu {

		/**
		 * Cap for admin menu.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		public $menu_capability = 'manage_options';

		/**
		 * Register IMS Menu.
		 *
		 * Custom menu for IMS.
		 *
		 * @param string   $page_title Menu data attribute.
		 * @param string   $menu_title Menu data attribute.
		 * @param string   $capability Menu data attribute.
		 * @param string   $menu_slug Menu data attribute.
		 * @param callable $function = '' Menu data attribute.
		 * @param string   $icon_url = '' Menu data attribute.
		 * @param int      $position = null Menu data attribute.
		 * @since 1.0.0
		 */
		public function ims_menu() {
			// Add menu page.
			add_menu_page(
				__( 'Memberships', 'inspiry-memberships' ),
				__( 'Memberships', 'inspiry-memberships' ),
				$this->menu_capability,
				'inspiry_memberships',
				'',
				'dashicons-smiley',
				'9.08'
			);

			// Add all sub menus.
			$sub_menus = array(
				'addnew' => array(
					'inspiry_memberships',
					__( 'Add New Membership', 'inspiry-memberships' ),
					__( 'Add New', 'inspiry-memberships' ),
					'manage_options',
					'post-new.php?post_type=ims_membership',
				)
			);

			// Third-party can add more sub_menus.
			$sub_menu = apply_filters( 'ims_sub_menus', $sub_menus );

			/**
			 * Add Submenu.
			 *
			 * @param string $parent_slug
			 * @param string $page_title
			 * @param string $menu_title
			 * @param string $capability
			 * @param string $menu_slug
			 * @param callable $function = ''
			 * @since  1.0.0
			 */
			if ( $sub_menu ) {
				foreach ( $sub_menus as $sub_menu ) {
					call_user_func_array( 'add_submenu_page', $sub_menu );
				}
			}

		}

	}

endif;
