<?php
/**
 * `Receipt` Class
 *
 * A class to handle `receipt` related tasks.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Receipt
 *
 * This class handles `receipt` related functionality.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Receipt' ) ) :

	class IMS_Receipt {

		/**
		 * IMS_Receipt object.
		 *
		 * @var 	object
		 * @since 	1.0.0
		 */
		public $receipt;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->receipt = new IMS_CPT_Receipt();

		}

		/**
		 * create_receipt.
		 *
		 * @since 1.0.0
		 */
		public function create_receipt() {

			$this->receipt->register();

		}

	}

endif;
