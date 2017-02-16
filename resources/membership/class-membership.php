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

		/**
		 * Method: Create custom schedules for memberships.
		 *
		 * @since 1.0.0
		 */
		public function create_schedules( $schedules ) {

			$schedules[ 'weekly' ]	= array(
				'interval'	=> 7 * 24 * 60 * 60, // 7 days * 24 hours * 60 minutes * 60 seconds
				'display' 	=> __( 'Once Weekly', 'inspiry-memberships' )
			);

			$schedules[ 'monthly' ]	= array(
				'interval'	=> 30 * 24 * 60 * 60, // 30 days * 24 hours * 60 minutes * 60 seconds
				'display' 	=> __( 'Once Monthly', 'inspiry-memberships' )
			);

			$schedules[ 'yearly' ]	= array(
				'interval'	=> 365 * 24 * 60 * 60, // 365 days * 24 hours * 60 minutes * 60 seconds
				'display' 	=> __( 'Once Yearly', 'inspiry-memberships' )
			);

			return apply_filters( 'ims_create_crons_scedules', $schedules );

		}

	}

endif;
