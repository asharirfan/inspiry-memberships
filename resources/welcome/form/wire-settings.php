<?php
/**
 * Wire Transfer Settings section
 *
 * Wire Transfer settings section of installation form.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<h3>
	<?php esc_html_e( 'Step 4: Wire Transfer Settings', 'inspiry-memberships' ); ?>
</h3>

<div class="ims-wire-settings">
	<table class="form-table">

		<tr>
			<th scope="row">
				<label for="ims_wire_enable">
					<?php esc_html_e( 'Enable Wire Transfer', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_wire_enable">
						<input type="checkbox" id="ims_wire_enable" name="ims_wire_enable" />
						<?php esc_html_e( 'Check this box to enable wire transfer.', 'inspiry-memberships' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_wire_instructions">
					<?php esc_html_e( 'Instructions for Wire Transfer', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_wire_instructions">
						<textarea rows="5" cols="55" type="text" id="ims_wire_instructions" class="regular-text" name="ims_wire_instructions" placeholder="Instructions for Wire Transfer"></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter the instructions for wire transfer.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_wire_account_name">
					<?php esc_html_e( 'Account Name', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_wire_account_name">
						<input type="text" id="ims_wire_account_name" class="regular-text" name="ims_wire_account_name" placeholder="Account Name" />
						<p class="description">
							<?php esc_html_e( 'Enter your account name.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_wire_account_number">
					<?php esc_html_e( 'Account Number', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_wire_account_number">
						<input type="text" id="ims_wire_account_number" class="regular-text" name="ims_wire_account_number" placeholder="Account Number" />
						<p class="description">
							<?php esc_html_e( 'Enter your account number.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

	</table>
	<!-- /.form-table -->

	<p class="ims__description">
		<a href="#" class="button button-primary" id="ims-wire-submit">
			<?php esc_html_e( 'Finish', 'inspiry-memberships' ); ?>
		</a>
		<img class="ims-gif" src="<?php echo IMS_BASE_URL; ?>resources/img/spinner.gif">
	</p>
	<!-- /.ims__description -->

</div>
<!-- /.ims-wire-settings -->
