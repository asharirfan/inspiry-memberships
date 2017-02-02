<?php
/**
 * Welcome Page Content
 *
 * Contents of welcome page.
 *
 * @since 	1.0.0
 * @package WP
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

	<h1><?php printf( __( 'Inspiry Memberships &nbsp;%s', 'inspiry-memberships' ), $the_version ); ?></h1>

	<div class="about-text">
		<?php printf( __( "A membership plugin for Real-Estate websites by Inspiry Themes.", 'inspiry-memberships' ), $the_version ); ?>
	</div>

	<div class="ims__logo"></div>
	<!-- Logo -->

	<div class="feature-section one-col">
		<h2><?php _e( 'Get Started', 'inspiry-memberships' ); ?></h2>
		<ul>
			<li>
				<?php $settings = get_option( 'ims_basic_settings' ); ?>
				<?php if ( empty( $settings ) ) : ?>
					<p class="ims__description" id="ims-notice">
						<?php _e( 'The following settings need to be configured.', 'inspiry-memberships' ); ?>
					</p>
				<?php endif; ?>
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
					<?php if ( empty( $settings ) ) : ?>
						<a id="ims-install" class="button button-primary"><?php _e( 'Configure', 'inspiry-memberships' ); ?></a>
						<img class="ims-gif" src="<?php echo IMS_BASE_URL; ?>assets/img/spinner.gif">
					<?php endif; ?>
				</p>
				<p class="ims-error ims__description"></p>
			</li>
		</ul>
	</div>

	<hr>

	<h2><?php _e( 'Features', 'inspiry-memberships' ); ?></h2>
	<p class="lead-description">
		<?php _e( 'Inspiry Memberships lets you create membership packages for your Real-Estate website so that you can take your Real-Estate website to the next level.', 'inspiry-memberships' ); ?>
	</p>
	<!-- /.lead-description -->

	<div class="feature-section two-col">
		<div class="col">
			<h3><?php _e( 'Easy to configure', 'inspiry-memberships' ); ?></h3>
			<p><?php _e( 'Easily configure Inspiry Memberships to start selling membership packages on your Real-Estate website. The plugin comes with out of the box installation setup to help users get started. You can add Stripe, PayPal and Wire Transfers methods to accept payments. You can also provide your customers with recurring memberships. Now you do not need to update all the memberships of your users manually. Inspiry Memberships do this for you automatically.', 'inspiry-memberships' ); ?></p>
		</div>
		<!-- /.col -->
		<div class="col">
			<div class="wp-video" style="width:640px;">
				<iframe src="//giphy.com/embed/l3q2LECdz0sO9JqE0" width="100%" height="300" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>
			</div>
			<!-- /.wp-video -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.feature-section one-col -->

	<div class="feature-section two-col">
		<div class="col">
			<div class="wp-video" style="width:640px;">
				<iframe src="//giphy.com/embed/d3mm1QcY5cjsbYHu" width="100%" height="300" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>
			</div>
			<!-- /.wp-video -->
		</div>
		<!-- /.col -->
		<div class="col">
			<h3><?php _e( 'One Menu to Rule Them All', 'inspiry-memberships' ); ?></h3>
			<p><?php _e( 'As the heading says, there is only one menu of Inspiry Memberships. No more fuss about finding where the settings of the plugin are, where to add a new membership, or where you can find your customer receipts because it is all together in one place.', 'inspiry-memberships' ); ?></p>
		</div>
		<!-- /.col -->
	</div>
	<!-- /.feature-section one-col -->

	<div class="feature-section two-col">

		<div class="col">
			<img src="<?php echo IMS_BASE_URL; ?>assets/img/stripe.png" />
			<h3><?php _e( 'Stripe' ); ?></h3>
			<p><?php _e( 'Use your Stripe account to receive payments for your membership packages. You can also create subscriptions in your Stripe account and integrate it with a membership on your website using its Stripe Plan ID.', 'inspiry-memberships' ); ?></p>
		</div>

		<div class="col">
			<img src="<?php echo IMS_BASE_URL; ?>assets/img/paypal.png" />
			<h3><?php _e( 'PayPal' ); ?></h3>
			<p><?php _e( 'Use PayPal to receive payments for your memberships. You can also accept payments through Master, Visa and other credit cards supported by PayPal.' ); ?></p>
		</div>

	</div>

	<div class="feature-section two-col">
		<div class="col">
			<img src="http://placehold.it/600x237/418EDA/fff?text=Wire&nbsp;Transfers" />
			<h3><?php _e( 'Wire Transfers' ); ?></h3>
			<p><?php _e( 'Inspiry Memberships can also be configured to use Wire Transfer to receive payments. It emails your customers the details of the selected membership so that they can purchase it via Wire Transfer.', 'inspiry-memberships' ); ?></p>
		</div>
		<div class="col">
			<img src="http://placehold.it/600x237/418EDA/fff?text=Recurring&nbsp;Memberships" />
			<h3><?php _e( 'Recurring Memberships' ); ?></h3>
			<p><?php _e( 'Inspiry Memberships allows you to create recurring memberships so that you can receive payments recursively from your customers. You can use this feature with both Stripe and PayPal. Every time you receive a payment, a receipt generates against it, and both you and your customer gets an email of the payment made.', 'inspiry-memberships' ); ?></p>
		</div>
	</div>

	<hr>

	<div class="return-to-dashboard">
		<p>
			<?php _e( 'With love, from', 'inspiry-memberships' ); ?><a href="<?php echo esc_url( "https://inspirythemes.com" ); ?>"><?php _e( 'Inspiry Themes', 'inspiry-memberships' ); ?></a>
		</p>
	</div>
	<!-- /.return-to-dashboard -->

</div>
<!-- HTML Ended! -->
