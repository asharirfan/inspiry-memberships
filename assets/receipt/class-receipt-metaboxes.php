<?php
/**
 * `Receipt` Metabox
 *
 * Class to create and manage `Receipt` metaboxes.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Receipt_Meta_Boxes.
 *
 * This class creates and manage `Receipt` meta boxes.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Receipt_Meta_Boxes' ) ) :

	class IMS_Receipt_Meta_Boxes {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// add_action( 'load-post.php', array( $this, 'setup_meta_box' ) );
			// add_action( 'load-post-new.php', array( $this, 'setup_meta_box' ) );

		}

		/**
		 * setup_meta_box.
		 *
		 * @since 1.0.0
		 */
		public function setup_meta_box() {

			add_action( 'add_meta_boxes', array( $this, 'add_receipt_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );

		}

		/**
		 * add_receipt_meta_box.
		 *
		 * @since 1.0.0
		 */
		public function add_receipt_meta_box() {

			add_meta_box(
				'receipt-details-metabox',      								// Unique ID
				esc_html__( 'Receipt Details', 'inspiry-membership' ),	    	// Title
				array( $this, 'meta_box_content' ),   							// Callback function
				'ims_receipt',         											// Post Type
				'normal',         												// Context
				'high'        											 		// Priority
			);

			do_action( 'ims_receipt_extend_meta_boxes' );

		}

		/**
		 * meta_box_content.
		 *
		 * @since 1.0.0
		 */
		public function meta_box_content( $object, $box ) {

			wp_nonce_field( basename( __FILE__ ), 'receipt_meta_box_nonce' );
			$prefix = 'ims_membership_'; ?>

			<table class="form-table">

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="receipt_id">
							<?php _e( 'Receipt ID', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<?php $receipt_id = get_post_meta( $object->ID, "{$prefix}receipt_id", true ); ?>
						<p><?php echo ( ! empty( $receipt_id ) ) ? $receipt_id : __( 'Receipt ID is not generated yet!' ); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="receipt_for">
							<?php _e( 'Receipt For', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<?php $receipt_for = get_post_meta( $object->ID, "{$prefix}receipt_for", true ); ?>
						<p class=""><?php echo ( ! empty( $receipt_for ) ) ? $receipt_for : __( 'Data not available!' ); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="membership_id">
							<?php _e( 'Membership ID', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="membership_id"
								id="membership_id"
								placeholder="Membership ID"
								value="<?php echo esc_attr( get_post_meta( $object->ID, "{$prefix}membership_id", true ) ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="price">
							<?php _e( 'Price', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="price"
								id="price"
								placeholder="Price"
								value="<?php echo esc_attr( get_post_meta( $object->ID, "{$prefix}price", true ) ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="purchase_date">
							<?php _e( 'Date of Purchase', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="purchase_date"
								id="purchase_date"
								placeholder="Date of Purchase"
								value="<?php echo esc_attr( get_post_meta( $object->ID, "{$prefix}purchase_date", true ) ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="user_id">
							<?php _e( 'User ID', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="user_id"
								id="user_id"
								placeholder="User ID"
								value="<?php echo esc_attr( get_post_meta( $object->ID, "{$prefix}user_id", true ) ); ?>"
						/>
					</td>
				</tr>

				<?php do_action( 'ims_receipt_add_meta_boxes', $object->ID ); ?>

			</table>

			<?php

		}

		/**
		 * save_meta_box.
		 *
		 * @since 1.0.0
		 */
		public function save_meta_box( $post_id, $post ) {

			// Verify the nonce before proceeding.
			if ( ! isset( $_POST[ 'receipt_meta_box_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'receipt_meta_box_nonce' ], basename( __FILE__ ) ) ) {
				return $post_id;
			}

			// Get the post type object.
			$post_type 	= get_post_type_object( $post->post_type );

			// Check if the post type is membership.
			if ( 'ims_receipt' != $post->post_type ) {
				return $post_id;
			}

			// Check if the current user has permission to edit the post.
			if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
				return $post_id;
			}


			// Get the posted data and sanitize it for use as an HTML class.
			$ims_meta_value 					= array();
			$ims_meta_value[ 'receipt_id' ] 	= ( ! empty( $post_id ) ) ? intval( $post_id ) : '';
			$ims_meta_value[ 'receipt_for' ]	= ( isset( $_POST[ 'receipt_for' ] ) ) ? sanitize_text_field( $_POST[ 'receipt_for' ] ) : '';
			$ims_meta_value[ 'membership_id' ] 	= ( isset( $_POST[ 'membership_id' ] ) ) ? intval( $_POST[ 'membership_id' ] ) : '';
			$ims_meta_value[ 'price' ] 			= ( isset( $_POST[ 'price' ] ) ) ? floatval( $_POST[ 'price' ] ) : '';
			$ims_meta_value[ 'purchase_date' ] 	= ( isset( $_POST[ 'purchase_date' ] ) ) ? sanitize_text_field( $_POST[ 'purchase_date' ] ) : '';
			$ims_meta_value[ 'user_id' ] 		= ( isset( $_POST[ 'user_id' ] ) ) ? intval( $_POST[ 'user_id' ] ) : '';

			// Meta data prefix.
			$prefix = 'ims_membership_';

			// Save the meta values.
			$this->save_meta_value( $post_id, "{$prefix}receipt_id", $ims_meta_value[ 'receipt_id' ] );
			$this->save_meta_value( $post_id, "{$prefix}receipt_for", $ims_meta_value[ 'receipt_for' ] );
			$this->save_meta_value( $post_id, "{$prefix}membership_id", $ims_meta_value[ 'membership_id' ] );
			$this->save_meta_value( $post_id, "{$prefix}price", $ims_meta_value[ 'price' ] );
			$this->save_meta_value( $post_id, "{$prefix}purchase_date", $ims_meta_value[ 'purchase_date' ] );
			$this->save_meta_value( $post_id, "{$prefix}user_id", $ims_meta_value[ 'user_id' ] );

			do_action( 'ims_receipt_save_meta_boxes', $post_id, $_POST );

		}

		/**
		 * save_meta_value.
		 *
		 * @since 1.0.0
		 */
		public function save_meta_value( $post_id, $meta_key, $new_meta_value ) {

			// Get the old meta value of the meta key.
			$old_meta_value	= get_post_meta( $post_id, $meta_key, true );

			if ( $new_meta_value && '' == $old_meta_value ) {

				// If a new meta value was added and there was no previous value, add it.
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			} elseif ( $new_meta_value && $old_meta_value != $new_meta_value ) {

				// If the new meta value does not match the old value, update it.
				update_post_meta( $post_id, $meta_key, $new_meta_value );

			} elseif ( '' == $new_meta_value && $old_meta_value ) {

				// If there is no new meta value but an old value exists, delete it.
				delete_post_meta( $post_id, $meta_key, $old_meta_value );

			}

		}

	}

endif;
