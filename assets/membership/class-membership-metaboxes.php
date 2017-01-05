<?php
/**
 * `Membership` Metabox
 *
 * Class to create and manage `Membership` metaboxes.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_Membership_Meta_Boxes.
 *
 * This class creates and manage `Membership` meta boxes.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_Membership_Meta_Boxes' ) ) :

	class IMS_Membership_Meta_Boxes {

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

			add_action( 'add_meta_boxes', array( $this, 'add_membership_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );

		}

		/**
		 * add_membership_meta_box.
		 *
		 * @since 1.0.0
		 */
		public function add_membership_meta_box() {

			add_meta_box(
				'membership-settings-metabox',      							// Unique ID
				esc_html__( 'Membership Settings', 'inspiry-membership' ),    	// Title
				array( $this, 'meta_box_content' ),   							// Callback function
				'ims_membership',         										// Post Type
				'normal',         												// Context
				'high'        											 		// Priority
			);

			do_action( 'ims_membership_extend_meta_boxes' );

		}

		/**
		 * meta_box_content.
		 *
		 * @since 1.0.0
		 */
		public function meta_box_content( $object, $box ) {

			wp_nonce_field( basename( __FILE__ ), 'membership_meta_box_nonce' ); ?>

			<table class="form-table">

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="allowed_properties">
							<?php _e( 'Number of Properties', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="number"
								name="allowed_properties"
								id="allowed_properties"
								value="<?php echo esc_attr( get_post_meta( $object->ID, 'ims_allowed_properties', true ) ); ?>"
						/>
						<p class="description"><?php _e( 'Enter the number of properties allowed in this membership. Example: 50', 'inspiry-membership' ); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="featured_properties">
							<?php _e( 'Number of Featured Properties', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="number"
								name="featured_properties"
								id="featured_properties"
								value="<?php echo esc_attr( get_post_meta( $object->ID, 'ims_featured_properties', true ) ); ?>"
						/>
						<p class="description"><?php _e( 'Enter the number of featured properties allowed in this membership. Example: 20', 'inspiry-membership' ); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" valign="top">
						<label for="price">
							<?php _e( 'Price', 'inspiry-membership' ); ?>
						</label>
					</th>
					<td>
						<input 	type="number"
								name="price"
								id="price"
								value="<?php echo esc_attr( get_post_meta( $object->ID, 'ims_price', true ) ); ?>"
						/>
						<p class="description"><?php _e( 'Enter the price of this membership. Example: 20', 'inspiry-membership' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'ims_membership_add_meta_boxes', $object->ID ); ?>

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
			if ( ! isset( $_POST[ 'membership_meta_box_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'membership_meta_box_nonce' ], basename( __FILE__ ) ) ) {
				return $post_id;
			}

			// Get the post type object.
			$post_type 	= get_post_type_object( $post->post_type );

			// Check if the post type is membership.
			if ( 'ims_membership' != $post->post_type ) {
				return $post_id;
			}

			// Check if the current user has permission to edit the post.
			if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
				return $post_id;
			}

			// Get the posted data and sanitize it for use as an HTML class.
			$ims_meta_value 							= array();
			$ims_meta_value[ 'allowed_properties' ] 	= ( isset( $_POST[ 'allowed_properties' ] ) ) ? sanitize_html_class( $_POST[ 'allowed_properties' ] ) : '';
			$ims_meta_value[ 'featured_properties' ]	= ( isset( $_POST[ 'featured_properties' ] ) ) ? sanitize_html_class( $_POST[ 'featured_properties' ] ) : '';
			$ims_meta_value[ 'price' ] 					= ( isset( $_POST[ 'price' ] ) ) ? sanitize_html_class( $_POST[ 'price' ] ) : '';

			// Save the meta values.
			$this->save_meta_value( $post_id, 'ims_allowed_properties', $ims_meta_value[ 'allowed_properties' ] );
			$this->save_meta_value( $post_id, 'ims_featured_properties', $ims_meta_value[ 'featured_properties' ] );
			$this->save_meta_value( $post_id, 'ims_price', $ims_meta_value[ 'price' ] );

			do_action( 'ims_membership_save_meta_boxes', $post_id, $_POST );

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
