<?php
/**
 * Welcome Page Content
 *
 * Contents of welcome page.
 *
 * @since    1.0.0
 * @package  WP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Version.
$the_version = IMS_VERSION;

// Logo image.
// $logo_img = IMS_BASE_URL . '/welcome/img/logo.png'; ?>

<!-- HTML Started! -->
<div class="wrap about-wrap">

	<h1><?php printf( __( 'Inspiry Memberships %s', 'inspiry-memberships' ), $the_version ); ?></h1>

	<div class="about-text">
		<?php printf( __( "A membership plugin for real estate themes by Inspiry Themes.", 'inspiry-memberships' ), $the_version ); ?>
	</div>

	<div class="ims__logo"></div>
	<!-- Logo -->

	<?php $settings = get_option( 'ims_basic_settings' ); ?>
	<?php if ( empty( $settings ) ) : ?>
		<div class="feature-section one-col">
			<h2><?php _e( 'Get Started', 'inspiry-memberships' ); ?></h2>
			<ul>
				<li>
					<p class="ims__description" id="ims-notice">
						<?php _e( 'You need to configure following settings.', 'inspiry-memberships' ); ?>
					</p>
				</li>
				<li>
					<div class="ims__basic_settings">
						<?php
						/**
						 * Basic Settings Form.
						 *
						 * @since 1.0.0
						 */
						if ( file_exists( IMS_BASE_DIR . '/assets/welcome/form/basic-settings.php' ) ) {
							require_once( IMS_BASE_DIR . '/assets/welcome/form/basic-settings.php' );
						}
						?>
					</div>
					<!-- /.ims__basic_settings -->
				</li>
				<li>
					<div class="ims__stripe_settings">
						<?php
						/**
						 * Stripe Settings Form.
						 *
						 * @since 1.0.0
						 */
						if ( file_exists( IMS_BASE_DIR . '/assets/welcome/form/stripe-settings.php' ) ) {
							require_once( IMS_BASE_DIR . '/assets/welcome/form/stripe-settings.php' );
						}
						?>
					</div>
					<!-- /.ims__stripe_settings -->
				</li>
				<li>
					<div class="ims__paypal_settings">
						<?php
						/**
						 * PayPal Settings Form.
						 *
						 * @since 1.0.0
						 */
						if ( file_exists( IMS_BASE_DIR . '/assets/welcome/form/paypal-settings.php' ) ) {
							require_once( IMS_BASE_DIR . '/assets/welcome/form/paypal-settings.php' );
						}
						?>
					</div>
					<!-- /.ims__paypal_settings -->
				</li>
				<li>
					<div class="ims__wire_settings">
						<?php
						/**
						 * Wire Settings Form.
						 *
						 * @since 1.0.0
						 */
						if ( file_exists( IMS_BASE_DIR . '/assets/welcome/form/wire-settings.php' ) ) {
							require_once( IMS_BASE_DIR . '/assets/welcome/form/wire-settings.php' );
						}
						?>
					</div>
					<!-- /.ims__wire_settings -->
				</li>
				<li>
					<p class="ims__description">
						<a id="ims-install" class="button button-primary"><?php _e( 'Start Configuring', 'inspiry-memberships' ); ?></a>
						<img class="ims-gif" src="<?php echo IMS_BASE_URL; ?>assets/img/spinner.gif">
					</p>
					<p class="ims-error ims__description"></p>
				</li>
			</ul>
		</div>

	<?php endif; ?>

	<hr>

	<h2><?php _e( 'Features', 'inspiry-memberships' ); ?></h2>
	<p class="lead-description">
		<?php _e( 'This plugin facilitates you to create membership packages for your real estate website.', 'inspiry-memberships' ); ?>
	</p>
	<!-- /.lead-description -->

	<div class="feature-section one-col">

		<h3><?php _e( 'Easy to Use', 'inspiry-memberships' ); ?></h3>
		<p class="ims__description"><?php _e( 'This plugin provides simple and straight forward settings to get you started on adding membership packages in your real estate website.', 'inspiry-memberships' ); ?></p>


		<h3><?php _e( 'One Menu to Rule Them All', 'inspiry-memberships' ); ?></h3>
		<p class="ims__description"><?php _e( 'This plugin offers only one menu to cover its settings and features. So, no trouble in finding where the settings are, where to add a new membership or where to find customer receipts. As all this is provided in one place.', 'inspiry-memberships' ); ?></p>


		<h3><?php _e( 'Stripe' ); ?></h3>
		<p class="ims__description"><?php _e( 'Using this plugin you can use your Stripe account to receive payments for membership packages. You can also create subscriptions in your Stripe account and integrate it with a membership on your website using related Stripe Plan ID.', 'inspiry-memberships' ); ?></p>


		<h3><?php _e( 'PayPal' ); ?></h3>
		<p class="ims__description"><?php _e( 'You can also use PayPal to receive payments for memberships. You can accept payments through Master, Visa and other credit cards supported by PayPal.' ); ?></p>

		<h3><?php _e( 'Wire Transfers' ); ?></h3>
		<p class="ims__description"><?php _e( 'This plugin can also be configured to receive Wire Transfers. It emails your customer the details of the selected membership so that they can pay it via Wire Transfer.', 'inspiry-memberships' ); ?></p>

		<h3><?php _e( 'Recurring Memberships' ); ?></h3>
		<p class="ims__description"><?php _e( 'This plugin allows you to create recurring memberships. So that you can receive recurring payments from your customers. You can use this feature with both Stripe and PayPal. Every time you receive a payment, a receipt is generated against it and both you and your customer gets an email notification.', 'inspiry-memberships' ); ?></p>

	</div>

	<hr>

	<div class="return-to-dashboard">
		<p>
			<?php _e( 'With love, from', 'inspiry-memberships' ); ?>
			<a target="_blank" href="<?php echo esc_url( "https://inspirythemes.com" ); ?>"><?php _e( 'Inspiry Themes', 'inspiry-memberships' ); ?></a>
		</p>
	</div>
	<!-- /.return-to-dashboard -->

</div>
<!-- HTML Ended! -->
