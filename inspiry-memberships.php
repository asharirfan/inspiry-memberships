<?php
/**
 * Plugin Name:		Inspiry Memberships
 * Description: 	A simple plugin to create memberships portal on your WordPress site.
 * Plugin URI: 		https://github.com/InspiryThemes/inspiry-memberships
 * Author: 			mrasharirfan
 * Author URI: 		https://inspirythemes.com
 * Version: 		1.0.0
 * License: 		GPL-2.0+
 * Text Domain:		inspiry-memberships
 * Domain Path:		/languages/
 *
 * @link 			https://github.com/InspiryThemes/inspiry-memberships
 * @since 			1.0.0
 * @package 		IMS
 */

/*

    Copyright (C) 2017  mrasharirfan  mrasharirfan@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Constants and Globals
 *
 * @since 1.0.0
 */
if ( ! defined( 'IM_VERSION' ) ) {
    define( 'IM_VERSION', '1.0.0' );
}

if ( ! defined( 'IM_BASE_NAME' ) ) {
	define( 'IM_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'IM_BASE_URL' ) ) {
	define( 'IM_BASE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'IM_BASE_DIR' ) ) {
	define( 'IM_BASE_DIR', dirname( __FILE__ ) );
}


/**
 * activate_inspiry_memberships.
 *
 * @since 1.0.0
 */
function activate_inspiry_memberships() {

	/**
	 * class-im-activator.
	 *
	 * @since 1.0.0
	 */
	if ( file_exists( IM_BASE_DIR . '/assets/includes/class-im-activator.php' ) ) {
	    require_once( IM_BASE_DIR . '/assets/includes/class-im-activator.php' );
	}
	Inspiry_Memberships_Activator::activate();

}


/**
 * deactivate_inspiry_memberships.
 *
 * @since 1.0.0
 */
function deactivate_inspiry_memberships() {

	/**
	 * class-im-deactivator.
	 *
	 * @since 1.0.0
	 */
	if ( file_exists( IM_BASE_DIR . '/assets/includes/class-im-deactivator.php' ) ) {
	    require_once( IM_BASE_DIR . '/assets/includes/class-im-deactivator.php' );
	}
	Inspiry_Memberships_Deactivator::deactivate();

}


register_activation_hook( __FILE__, 'activate_inspiry_memberships' );
register_deactivation_hook( __FILE__, 'deactivate_inspiry_memberships' );


/**
 * im-init.php.
 *
 * @since 1.0.0
 */
if ( file_exists( IM_BASE_DIR . '/assets/im-init.php' ) ) {
    require_once( IM_BASE_DIR . '/assets/im-init.php' );
}
