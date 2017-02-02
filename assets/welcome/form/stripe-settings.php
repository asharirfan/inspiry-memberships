<?php
/**
 * Stripe Settings section
 *
 * Stripe settings section of installation form.
 *
 * @since 	1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<h3>
	<?php _e( 'Step 2: Stripe Settings', 'inspiry-memberships' ); ?>
</h3>

<div class="ims-stripe-settings">
	<p class="ims__description">
		<strong><?php _e( 'Important: ', 'inspiry-memberships' ); ?></strong>
		<?php _e( 'Please get your Stripe API keys from the Stripe Dashboard.', 'inspiry-memberships' ); ?>
		<a href="https://support.stripe.com/questions/where-do-i-find-my-api-keys" target="__blank">
			<?php _e( 'Click here for more information.', 'inspiry-memberships' ); ?>
		</a>
	</p>

	<table class="form-table">

		<tr>
			<th scope="row">
				<label for="ims_stripe_enable">
					<?php _e( 'Enable Stripe', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_stripe_enable">
						<input type="checkbox" id="ims_stripe_enable" name="ims_stripe_enable" />
						<?php _e( 'Check this box to enable Stripe payments.', 'inspiry-memberships' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_stripe_test_mode">
					<?php _e( 'Test Mode', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_stripe_test_mode">
						<input type="checkbox" id="ims_stripe_test_mode" name="ims_stripe_test_mode" />
						<?php _e( 'Check this box to enable test mode of Stripe payments.', 'inspiry-memberships' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_live_secret">
					<?php _e( 'Live Secret Key', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_live_secret">
						<input type="text" id="ims_live_secret" class="regular-text" name="ims_live_secret" placeholder="Live Secret Key" />
						<p class="description">
							<?php _e( 'Paste your live secret key here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_live_publishable">
					<?php _e( 'Live Publishable Key', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_live_publishable">
						<input type="text" id="ims_live_publishable" class="regular-text" name="ims_live_publishable" placeholder="Live Publishable Key" />
						<p class="description">
							<?php _e( 'Paste your live publishable key here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_test_secret">
					<?php _e( 'Test Secret Key', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_test_secret">
						<input type="text" id="ims_test_secret" class="regular-text" name="ims_test_secret" placeholder="Test Secret Key" />
						<p class="description">
							<?php _e( 'Paste your test secret key here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_test_publishable">
					<?php _e( 'Test Publishable Key', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_test_publishable">
						<input type="text" id="ims_test_publishable" class="regular-text" name="ims_test_publishable" placeholder="Test Publishable Key" />
						<p class="description">
							<?php _e( 'Paste your test publishable key here.', 'inspiry-memberships' ); ?>
						</p>
					</label>
				</fieldset>
			</td>
		</tr>
		<!-- tr -->

		<tr>
			<th scope="row">
				<label for="ims_stripe_webhook">
					<?php _e( 'Stripe Webhook URL', 'inspiry-memberships' ); ?>
				</label>
			</th>
			<td>
				<fieldset>
					<label for="ims_stripe_webhook">
						<?php $webhook 	= add_query_arg( array( 'ims_stripe' => 'membership_event' ), home_url( '/' ) ); ?>
						<input type="text" id="ims_stripe_webhook" class="regular-text" name="ims_stripe_webhook" value="<?php echo esc_url( $webhook ); ?>" />
						<p class="description">
							<?php _e( 'For example: ' . $webhook, 'inspiry-memberships' ); ?>
						</p>
						<p>
							<a href="https://dashboard.stripe.com/account/webhooks" target="__blank">
								<?php _e( 'Click here to register your webhook.', 'inspiry-memberships' ); ?>
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
		<a href="#" class="button button-primary" id="ims-stripe-submit">
			<?php _e( 'Next', 'inspiry-memberships' ); ?>
		</a>
		<img class="ims-gif" src="<?php echo IMS_BASE_URL; ?>assets/img/spinner.gif">
	</p>
	<!-- /.ims__description -->

</div>
