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

	<div class="feature-section one-col">
		<!-- <h2>What's Inside?</h2>
		<div class="headline-feature feature-video">
			<div class='embed-container'>
				<iframe src='https://www.youtube.com/embed/3RLE_vWJ73c' frameborder='0' allowfullscreen></iframe>
			</div>
		</div> -->
	</div>

	<div class="feature-section two-col">
		<div class="col">
			<img src="<?php echo IMS_BASE_URL; ?>assets/img/stripe.png" />
			<h3><?php _e( 'Stripe' ); ?></h3>
			<p><?php _e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras sed sapien quam. Sed dapibus est id enim facilisis, at posuere turpis adipiscing. Quisque sit amet dui dui.' ); ?></p>
		</div>

		<div class="col">
			<img src="<?php echo IMS_BASE_URL; ?>assets/img/paypal.png" />
			<h3><?php _e( 'Some Feature' ); ?></h3>
			<p><?php _e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras sed sapien quam. Sed dapibus est id enim facilisis, at posuere turpis adipiscing. Quisque sit amet dui dui.' ); ?></p>
		</div>
	</div>

	<div class="feature-section two-col">
		<div class="col">
			<img src="http://placehold.it/600x180/0092F9/fff?text=WELCOME" />
			<h3><?php _e( 'PayPal' ); ?></h3>
			<p><?php _e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras sed sapien quam. Sed dapibus est id enim facilisis, at posuere turpis adipiscing. Quisque sit amet dui dui.' ); ?></p>
		</div>
		<div class="col">
			<img src="http://placehold.it/600x180/0092F9/fff?text=WELCOME" />
			<h3><?php _e( 'Some Feature' ); ?></h3>
			<p><?php _e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras sed sapien quam. Sed dapibus est id enim facilisis, at posuere turpis adipiscing. Quisque sit amet dui dui.' ); ?></p>
		</div>
	</div>
</div>
<!-- HTML Ended! -->
