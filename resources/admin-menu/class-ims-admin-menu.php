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
				'dashicons-id',
				'9'
			);

			// Add all sub menus.
			$sub_menus = array(
				'addnew' => array(
					'inspiry_memberships',
					__( 'Add New Membership', 'inspiry-memberships' ),
					__( 'New Membership', 'inspiry-memberships' ),
					'manage_options',
					'post-new.php?post_type=ims_membership',
				),
				'receipts' => array(
					'inspiry_memberships',
					__( 'Receipts', 'inspiry-memberships' ),
					__( 'Receipts', 'inspiry-memberships' ),
					'manage_options',
					'edit.php?post_type=ims_receipt',
				),
				'addnewreceipt' => array(
					'inspiry_memberships',
					__( 'Add New Receipt', 'inspiry-memberships' ),
					__( 'New Receipt', 'inspiry-memberships' ),
					'manage_options',
					'post-new.php?post_type=ims_receipt',
				),
			);

			// Third-party can add more sub_menus.
			$sub_menu = apply_filters( 'ims_sub_menus', $sub_menus, 20 );

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

		/**
		 * WP menu open.
		 *
		 * Open IMS menu when clicked on a tab.
		 *
		 * @since 1.0.0
		 */
		public function open_menu() {
			// Get Current Screen.
			$screen 	= get_current_screen();
			$menu_arr 	= apply_filters( 'ims_open_menus_slugs', [
				'ims_membership',
				'edit-ims_membership',
				'ims_receipt',
				'edit-ims_receipt',
			] );
			// Check if the current screen's ID has any of the above menu array items.
			if ( in_array( $screen->id, $menu_arr ) ) { ?>
				<script type="text/javascript">
					jQuery( "body" ).removeClass( "sticky-menu" );
					jQuery( "#toplevel_page_inspiry_memberships" ).addClass( 'wp-has-current-submenu wp-menu-open' ).removeClass( 'wp-not-current-submenu' );
					jQuery( "#toplevel_page_inspiry_memberships > a" ).addClass( 'wp-has-current-submenu wp-menu-open' ).removeClass( 'wp-not-current-submenu' );
				</script>
				<?php
			}
		}

	}

endif;
