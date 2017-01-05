<?php
/**
 * `Membership` Class
 *
 * A class to handle membership related tasks.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Membership.
 *
 * This class handles `membership` related functionality.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Membership' ) ) :

	class IMS_Membership {

		/**
		 * IMS_Membership object.
		 *
		 * @var 	object
		 * @since 	1.0.0
		 */
		 public $membership;


		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->membership = new IMS_CPT_Membership();

		}

		/**
		 * create_membership.
		 *
		 * @since 1.0.0
		 */
		public function create_membership() {

			$this->membership->register();

		}

	}

endif;
