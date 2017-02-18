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

			// Receipt meta box added action hook.
			do_action( 'ims_receipt_meta_box_added' );

		}

		/**
		 * meta_box_content.
		 *
		 * @since 1.0.0
		 */
		public function meta_box_content( $receipt, $box ) {

			wp_nonce_field( 'receipt-meta-box-nonce', 'receipt_meta_box_nonce' );
			$prefix = apply_filters( 'ims_receipt_meta_prefix', 'ims_receipt_' ); ?>

			<table class="form-table">

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="receipt_id">
							<?php esc_html_e( 'Receipt ID', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<?php $receipt_id = get_post_meta( $receipt->ID, "{$prefix}receipt_id", true ); ?>
						<input 	type="text"
								name="receipt_id"
								id="receipt_id"
								disabled
								value="<?php echo ( ! empty( $receipt_id ) ) ? esc_attr( $receipt_id ) : __( 'Receipt ID is not generated yet!', 'inspiry-memberships' ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="receipt_for">
							<?php esc_html_e( 'Receipt For', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<?php $receipt_for = get_post_meta( $receipt->ID, "{$prefix}receipt_for", true ); ?>
						<input 	type="text"
								name="receipt_for"
								id="receipt_for"
								disabled
								value="<?php echo ( ! empty( $receipt_for ) ) ? esc_attr( $receipt_for ) : __( 'Data not available!', 'inspiry-memberships' ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="membership_id">
							<?php esc_html_e( 'Membership ID', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="membership_id"
								id="membership_id"
								placeholder="Membership ID"
								value="<?php echo esc_attr( get_post_meta( $receipt->ID, "{$prefix}membership_id", true ) ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="price">
							<?php esc_html_e( 'Price', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="price"
								id="price"
								placeholder="Price"
								value="<?php echo esc_attr( get_post_meta( $receipt->ID, "{$prefix}price", true ) ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="purchase_date">
							<?php esc_html_e( 'Date of Purchase', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="purchase_date"
								id="purchase_date"
								placeholder="Date of Purchase"
								value="<?php echo esc_attr( get_post_meta( $receipt->ID, "{$prefix}purchase_date", true ) ); ?>"
						/>
						<p class="description"><?php esc_html_e( 'Format: YYYY-MM-DD H:M:S', 'inspiry-memberships' ); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="user_id">
							<?php esc_html_e( 'User ID', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="user_id"
								id="user_id"
								placeholder="User ID"
								value="<?php echo esc_attr( get_post_meta( $receipt->ID, "{$prefix}user_id", true ) ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="user_id">
							<?php esc_html_e( 'Vendor', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<?php $vendor = get_post_meta( $receipt->ID, "{$prefix}vendor", true ); ?>
						<select	name="vendor" id="vendor">
							<option value="" <?php echo ( '' == $vendor ) ? 'selected' : ''; ?> disabled>
								<?php esc_html_e( 'None', 'inspiry-memberships' ); ?>
							</option>
							<option value="stripe" <?php echo ( 'stripe' == $vendor ) ? 'selected' : ''; ?> >
								<?php esc_html_e( 'Stripe', 'inspiry-memberships' ); ?>
							</option>
							<option value="paypal" <?php echo ( 'paypal' == $vendor ) ? 'selected' : ''; ?> >
								<?php esc_html_e( 'PayPal', 'inspiry-memberships' ); ?>
							</option>
							<option value="wire" <?php echo ( 'wire' == $vendor ) ? 'selected' : ''; ?> >
								<?php esc_html_e( 'Wire Transfer', 'inspiry-memberships' ); ?>
							</option>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="payment_id">
							<?php esc_html_e( 'Payment ID', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="text"
								name="payment_id"
								id="payment_id"
								placeholder="Payment ID"
								value="<?php echo esc_attr( get_post_meta( $receipt->ID, "{$prefix}payment_id", true ) ); ?>"
						/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="status">
							<?php esc_html_e( 'Membership Status', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<?php
							// Get membership status.
							$status 	= esc_attr( get_post_meta( $receipt->ID, "{$prefix}status", true ) );
							// Get user id and current user membership id.
							$user_id	= get_post_meta( $receipt->ID, "{$prefix}user_id", true );
							$current_membership	= get_user_meta( $user_id, "ims_current_membership", true );
							$membership_id 		= get_post_meta( $receipt->ID, "{$prefix}membership_id", true );
						?>
						<?php if ( empty( $status ) ) : ?>
							<input type="checkbox" name="status" id="status" />
							<p class="description"><?php esc_html_e( 'Select to activate the membership.', 'inspiry-membership' ); ?></p>
						<?php elseif ( $current_membership !== $membership_id ) : ?>
							<input type="hidden" name="status" id="status" value="true" />
							<p class="description"><?php esc_html_e( 'Membership expired.', 'inspiry-membership' ); ?></p>
						<?php else : ?>
							<input type="hidden" name="status" id="status" value="true" />
							<p class="description"><?php esc_html_e( 'Membership is active.', 'inspiry-membership' ); ?></p>
						<?php endif; ?>
					</td>
				</tr>

				<?php

					/**
					 * `ims_receipt_add_meta_boxes`
					 *
					 * This hook can be used to extend the meta-boxes
					 * of receipt post type.
					 *
					 * @param int $receipt->ID Receipt Post ID.
					 * @since 1.0.0
					 */
					do_action( 'ims_receipt_add_meta_boxes', $receipt->ID );
				?>

			</table>

			<?php

		}

		/**
		 * save_meta_box.
		 *
		 * @since 1.0.0
		 */
		public function save_meta_box( $receipt_id, $receipt ) {

			// Verify the nonce before proceeding.
			if ( ! isset( $_POST[ 'receipt_meta_box_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'receipt_meta_box_nonce' ], 'receipt-meta-box-nonce' ) ) {
				return $receipt_id;
			}

			// Get the post type object.
			$post_type 	= get_post_type_object( $receipt->post_type );

			// Check if the post type is membership.
			if ( 'ims_receipt' !== $receipt->post_type ) {
				return $receipt_id;
			}

			// Check if the current user has permission to edit the post.
			if ( ! current_user_can( $post_type->cap->edit_post, $receipt_id ) ) {
				return $receipt_id;
			}

			// Get the posted data and sanitize it for use as an HTML class.
			$ims_meta_value 					= array();
			$ims_meta_value[ 'receipt_id' ] 	= ( ! empty( $receipt_id ) ) ? intval( $receipt_id ) : '';
			$ims_meta_value[ 'receipt_for' ]	= ( isset( $_POST[ 'receipt_for' ] ) && ! empty( $_POST[ 'receipt_for' ] ) ) ? sanitize_text_field( $_POST[ 'receipt_for' ] ) : 'Normal Membership';
			$ims_meta_value[ 'membership_id' ] 	= ( isset( $_POST[ 'membership_id' ] ) && ! empty( $_POST[ 'membership_id' ] ) ) ? intval( $_POST[ 'membership_id' ] ) : '';
			$ims_meta_value[ 'price' ] 			= ( isset( $_POST[ 'price' ] ) && ! empty( $_POST[ 'price' ] ) ) ? floatval( $_POST[ 'price' ] ) : '';
			$ims_meta_value[ 'purchase_date' ] 	= ( isset( $_POST[ 'purchase_date' ] ) && ! empty( $_POST[ 'purchase_date' ] ) ) ? sanitize_text_field( $_POST[ 'purchase_date' ] ) : $receipt->post_date;
			$ims_meta_value[ 'user_id' ] 		= ( isset( $_POST[ 'user_id' ] ) && ! empty( $_POST[ 'user_id' ] ) ) ? intval( $_POST[ 'user_id' ] ) : '';
			$ims_meta_value[ 'vendor' ] 		= ( isset( $_POST[ 'vendor' ] ) ) && ! empty( $_POST[ 'vendor' ] ) ? sanitize_text_field( $_POST[ 'vendor' ] ) : '';
			$ims_meta_value[ 'payment_id' ] 	= ( isset( $_POST[ 'payment_id' ] ) && ! empty( $_POST[ 'payment_id' ] ) ) ? sanitize_text_field( $_POST[ 'payment_id' ] ) : '';
			$ims_meta_value[ 'status' ] 		= ( ! empty( $_POST[ 'status' ] ) && ( 'on' === $_POST[ 'status' ] ) ) ? true : false;

			$membership_status	= get_post_meta( $receipt_id, 'ims_membership_status', true );

			// Filter the values of meta data being saved by receipt post type.
			$ims_meta_value		= apply_filters( 'ims_receipt_before_save_meta_values', $ims_meta_value, $receipt_id );

			// Meta data prefix.
			$prefix = apply_filters( 'ims_receipt_meta_prefix', 'ims_receipt_' );

			// Save the meta values.
			$this->save_meta_value( $receipt_id, "{$prefix}receipt_id", $ims_meta_value[ 'receipt_id' ] );
			$this->save_meta_value( $receipt_id, "{$prefix}receipt_for", $ims_meta_value[ 'receipt_for' ] );
			$this->save_meta_value( $receipt_id, "{$prefix}membership_id", $ims_meta_value[ 'membership_id' ] );
			$this->save_meta_value( $receipt_id, "{$prefix}price", $ims_meta_value[ 'price' ] );
			$this->save_meta_value( $receipt_id, "{$prefix}purchase_date", $ims_meta_value[ 'purchase_date' ] );
			$this->save_meta_value( $receipt_id, "{$prefix}user_id", $ims_meta_value[ 'user_id' ] );
			$this->save_meta_value( $receipt_id, "{$prefix}vendor", $ims_meta_value[ 'vendor' ] );
			$this->save_meta_value( $receipt_id, "{$prefix}payment_id", $ims_meta_value[ 'payment_id' ] );

			if ( empty( $membership_status ) ) {
				$this->save_meta_value( $receipt_id, "{$prefix}status", $ims_meta_value[ 'status' ] );
			}

			// After save meta box values action hook.
			$ims_meta_value		= apply_filters( 'ims_receipt_after_save_meta_values', $ims_meta_value, $receipt_id );

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

		/**
		 * add_styles.
		 *
		 * @since 1.0.0
		 */
		public function add_styles() {

			global $post_type;
    		if ( 'ims_receipt' === $post_type ) {
    			wp_enqueue_style( 'ims-admin-styles', IMS_BASE_URL . 'resources/css/receipt.css', array(), IMS_VERSION );
    		}

		}

	}

endif;
