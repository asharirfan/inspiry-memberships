<?php
/**
 * PayPal Settings section
 *
 * PayPal settings section of installation form.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$api_keys			= 'https://support.stripe.com/questions/where-do-i-find-my-api-keys';
$api_credentials	= 'https://developer.paypal.com/docs/classic/api/apiCredentials/#creating-an-api-signature' ?>

<h3>
	<?php esc_html_e( 'Step 3: PayPal Settings', 'inspiry-memberships' ); ?>
</h3>

<div class="ims-paypal-settings">
	<p class="ims__description">
		<strong><?php esc_html_e( 'Important: ', 'inspiry-memberships' ); ?></strong>
		<?php esc_html_e( 'Please get your PayPal API keys from your PayPal account.', 'inspiry-memberships' ); ?>
		<a href="<?php echo esc_url( $api_keys ); ?>" target="__blank">
			<?php esc_html_e( 'How to get API keys?', 'inspiry-memberships' ); ?></a><br><a href="<?php echo esc_url( $api_credentials ); ?>" target="__blank">
			<?php esc_html_e( 'How to get API signature?', 'inspiry-memberships' ); ?>
		</a>
	</p>

	<table class="form-table">

		<tr>
			<th scope="row">
				<label for="ims_paypal_enable">
					<?php esc_html_e( 'Enable PayPal', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_paypal_enable">
						<input type="checkbox" id="ims_paypal_enable" name="ims_paypal_enable" />
						<?php esc_html_e( 'Check this box to enable PayPal payments.', 'inspiry-memberships' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_paypal_sandbox">
					<?php esc_html_e( 'PayPal Sandbox', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_paypal_sandbox">
						<input type="checkbox" id="ims_paypal_sandbox" name="ims_paypal_sandbox" />
						<?php esc_html_e( 'Check this box to enable sandbox of PayPal.', 'inspiry-memberships' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_paypal_client_id">
					<?php esc_html_e( 'Client ID', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_paypal_client_id">
						<input type="text" id="ims_paypal_client_id" class="regular-text" name="ims_paypal_client_id" placeholder="Client ID" />
						<p class="description">
							<?php esc_html_e( 'Paste your client ID here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_paypal_client_secret">
					<?php esc_html_e( 'Client Secret', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_paypal_client_secret">
						<input type="text" id="ims_paypal_client_secret" class="regular-text" name="ims_paypal_client_secret" placeholder="Client Secret" />
						<p class="description">
							<?php esc_html_e( 'Paste your client secret here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_paypal_api_username">
					<?php esc_html_e( 'API Username', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_paypal_api_username">
						<input type="text" id="ims_paypal_api_username" class="regular-text" name="ims_paypal_api_username" placeholder="API Username" />
						<p class="description">
							<?php esc_html_e( 'Paste your API username here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_paypal_api_password">
					<?php esc_html_e( 'API Password', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_paypal_api_password">
						<input type="text" id="ims_paypal_api_password" class="regular-text" name="ims_paypal_api_password" placeholder="API Password" />
						<p class="description">
							<?php esc_html_e( 'Paste your API password here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_paypal_api_signature">
					<?php esc_html_e( 'API Signature', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_paypal_api_signature">
						<input type="text" id="ims_paypal_api_signature" class="regular-text" name="ims_paypal_api_signature" placeholder="API Signature" />
						<p class="description">
							<?php esc_html_e( 'Paste your API signature here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_paypal_ipn_url">
					<?php esc_html_e( 'PayPal IPN URL', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_paypal_ipn_url">
						<?php $ipn 	= add_query_arg( array( 'ims_paypal' => 'notification' ), home_url( '/' ) ); ?>
						<input type="text" id="ims_paypal_ipn_url" class="regular-text" name="ims_paypal_ipn_url" value="<?php echo esc_url( $ipn ); ?>" />
						<p class="description">
							<?php esc_html_e( 'For example: ' . $ipn, 'inspiry-memberships' ); ?>
						</p>
						<p>
							<a href="https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNSetup/" target="__blank">
								<?php esc_html_e( 'Click here for more information.', 'inspiry-memberships' ); ?>
							</a>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

	</table>
	<!-- /.form-table -->

	<p class="ims__description">
		<a href="#" class="button button-primary" id="ims-paypal-submit">
			<?php esc_html_e( 'Next', 'inspiry-memberships' ); ?>
		</a>
		<img class="ims-gif" src="<?php echo IMS_BASE_URL; ?>resources/img/spinner.gif">
	</p>
	<!-- /.ims__description -->

</div>
