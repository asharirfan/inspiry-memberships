<?php
/**
 * Get membership class object
 *
 * Class to return common methods to interact with `membership` post type.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Get_Membership.
 *
 * Class to return common methods to interact with `membership` post type.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Get_Membership' ) ) :

	class IMS_Get_Membership {

		/**
		 * The Membership ID.
		 *
		 * @var 	int
		 * @since 	1.0.0
		 */
		 public $the_membership_id;

		/**
		 * Membership Meta Data.
		 *
		 * @var 	array
		 * @since 	1.0.0
		 */
		 public $the_meta_data;

		/**
		 * Membership Meta Keys.
		 *
		 * @var 	array
		 * @since 	1.0.0
		 */
		 public $meta_keys;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $the_membership_id = NULL ) {

			$this->set_meta_keys();

			// Check if $the_membership_id is not empty.
			if ( ! empty( $the_membership_id ) ) {
				$the_membership_id	= intval( $the_membership_id );
			} else {
				$the_membership_id	= get_the_id();
			}

			if ( $the_membership_id > 0 ) {
				$this->the_membership_id	= $the_membership_id;
				$this->the_meta_data		= get_post_custom( $the_membership_id );
			}

		}

		/**
		 * Method: Set meta keys.
		 *
		 * @since 1.0.0
		 */
		public function set_meta_keys() {

			$prefix 	= apply_filters( 'ims_membership_meta_prefix', 'ims_membership_' );
			$this->meta_keys	= array(
				'properties'			=> "{$prefix}allowed_properties",
			 	'featured_properties'	=> "{$prefix}featured_properties",
			 	'price'					=> "{$prefix}price",
			 	'duration'				=> "{$prefix}duration",
			 	'duration_unit'			=> "{$prefix}duration_unit",
			 	'stripe_plan_id'		=> "{$prefix}stripe_plan_id"
			);

		}

		/**
		 * Get Membership: Meta.
		 *
		 * Gets the membership meta_value if passed
		 * a meta_key through argument.
		 *
		 * @since 1.0.0
		 */
		public function get_meta( $meta_key ) {
			// Solves undefined index problem.
			$the_meta = isset( $this->the_meta_data[ $meta_key ] ) ? $this->the_meta_data[ $meta_key ] : false;
			// Array or not?
			if ( is_array( $the_meta ) ) {
				// Check 0th element of array
				// If meta is set then return value else return false.
				if ( isset( $the_meta[0] ) ) {
					// Returns the value of meta.
					return $the_meta[0];
				} else {
				    return false;
				}
			} else {
				// If meta is set then return value else return false.
				if ( isset( $the_meta ) ) {
					// Returns the value of meta.
					return $the_meta[0];
				} else {
				    return false;
				}
			}
		}

		/**
		 * Get Membership: ID.
		 *
		 * @since 1.0.0
		 */
		public function get_ID() {
			return $this->the_membership_id;
		}

		/**
		 * Get Membership: Price.
		 *
		 * @since 1.0.0
		 */
		public function get_price() {
			// Returns false if ID is not present.
			if ( ! $this->the_membership_id ) {
			    return false;
			}
			return $this->get_meta( $this->meta_keys[ 'price' ] );
		}

		/**
		 * Get Membership: Properties.
		 *
		 * @since 1.0.0
		 */
		public function get_properties() {
			// Returns false if ID is not present.
			if ( ! $this->the_membership_id ) {
			    return false;
			}
			return $this->get_meta( $this->meta_keys[ 'properties' ] );
		}

		/**
		 * Get Membership: Featured Properties.
		 *
		 * @since 1.0.0
		 */
		public function get_featured_properties() {
			// Returns false if ID is not present.
			if ( ! $this->the_membership_id ) {
			    return false;
			}
			return $this->get_meta( $this->meta_keys[ 'featured_properties' ] );
		}

		/**
		 * Get Membership: Duration.
		 *
		 * @since 1.0.0
		 */
		public function get_duration() {
			// Returns false if ID is not present.
			if ( ! $this->the_membership_id ) {
			    return false;
			}
			return $this->get_meta( $this->meta_keys[ 'duration' ] );
		}

		/**
		 * Get Membership: Duration Unit.
		 *
		 * @since 1.0.0
		 */
		public function get_duration_unit() {
			// Returns false if ID is not present.
			if ( ! $this->the_membership_id ) {
			    return false;
			}
			return $this->get_meta( $this->meta_keys[ 'duration_unit' ] );
		}

		/**
		 * Get Membership: Stripe Plan ID.
		 *
		 * @since 1.0.0
		 */
		public function get_stripe_plan_id() {
			// Returns false if ID is not present.
			if ( ! $this->the_membership_id ) {
			    return false;
			}
			return $this->get_meta( $this->meta_keys[ 'stripe_plan_id' ] );
		}

	}

endif;
