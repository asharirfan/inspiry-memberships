<?php
/**
 * Basic Settings section
 *
 * Basic settings section of installation form.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<h3>
	<?php _e( 'Step 1: Basic Settings', 'inspiry-memberships' ); ?>
</h3>

<form action="#" class="ims-basic-settings">
	<table class="form-table">

		<tr>
			<th scope="row">
				<label for="ims_membership_enable">
					<?php _e( 'Enable Memberships', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_membership_enable">
						<input type="checkbox" id="ims_membership_enable" name="ims_membership_enable" />
						<?php _e( 'Check this box to enable memberships on your website', 'inspiry-memberships' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_recurring_membership_enable">
					<?php _e( 'Enable Recurring Memberships', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_recurring_membership_enable">
						<input type="checkbox" id="ims_recurring_membership_enable" name="ims_recurring_membership_enable" />
						<?php _e( 'Check this box to enable recurring memberships on your website', 'inspiry-memberships' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_currency_code">
					<?php _e( 'Currency Code', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_currency_code">
						<input type="text" id="ims_currency_code" name="ims_currency_code" value="USD" />
						<p class="description">
							<?php _e( 'Provide currency code that you want to use. Example: USD', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_currency_symbol">
					<?php _e( 'Currency Symbol', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_currency_symbol">
						<input type="text" id="ims_currency_symbol" name="ims_currency_symbol" />
						<p class="description">
							<?php _e( 'Provide currency symbol that you want to use. Example: $', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_currency_symbol_position">
					<?php _e( 'Currency Symbol Position', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_currency_symbol_position">
						<select name="ims_currency_symbol_position" id="ims_currency_symbol_position">
							<option value="before"><?php _e( 'Before (E.g. $10)' ); ?></option>
							<option value="after"><?php _e( 'After (E.g. 10$)' ); ?></option>
						</select>
						<p class="description">
							<?php _e( 'Default: Before', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

	</table>
	<!-- /.form-table -->

	<p class="ims__description">
		<a href="#" class="button button-primary" id="ims-basic-submit">
			<?php _e( 'Next', 'inspiry-memberships' ); ?>
		</a>
		<img class="ims-gif" src="<?php echo IMS_BASE_URL; ?>assets/img/spinner.gif">
	</p>
	<!-- /.ims__description -->

</form>
<!-- /.ims-basic-settings -->
