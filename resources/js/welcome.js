/**
 * Welcome Page JS file
 *
 * @since 1.0.0
 */

jQuery( function( $ ) {

	$( document ).ready( function() {

		"use strict";

        if ( typeof jsData !== "undefined" ) {

        	var removeQueryStringParameters = function ( url ) {
                if ( url.indexOf ('?') >= 0 ) {
                    var urlParts = url.split('?');
                    return urlParts[0];
                }
                return url;
            };

            var ajaxSettingsAjaxCall = function( call_data ) {
            	return $.ajax( {
    				url			: ajaxURL,
    				type        : "POST",
                    data        : call_data,
                    dataType    : "json"
                } );
            };

            var scrollToNotice  = function() {
                $( 'html, body' ).animate( {
                    scrollTop: $( "#ims-notice" ).offset().top
                }, 400 );
            };

            var ajaxURL = removeQueryStringParameters( jsData.ajaxURL );
            var install	= $( '#ims-install' ); // Configure Button

            // Basic
            var basic		= $( '.ims-basic-settings' ); // Basic settings
            var basicBtn	= $( '#ims-basic-submit' ); // Basic settings submit

            // Stripe
            var stripe 		= $( '.ims-stripe-settings' );
            var stripeBtn	= $( '#ims-stripe-submit' ); // Basic settings submit

            // PayPal
            var paypal		= $( '.ims-paypal-settings' );
            var paypalBtn	= $( '#ims-paypal-submit' ); // Basic settings submit

            // Wire
            var wire 		= $( '.ims-wire-settings' );
            var wireBtn		= $( '#ims-wire-submit' ); // Basic settings submit

            // Step 1
            install.click( function( e ) {

            	e.preventDefault(); // Prevent the default event of the link.

            	var installGif 	= install.parent().find( '.ims-gif' );
            	installGif.show();

            	// basic.parent().find( 'h3' ).hide( "slow" );
            	stripe.parent().find( 'h3' ).hide( "slow" );
            	paypal.parent().find( 'h3' ).hide( "slow" );
            	wire.parent().find( 'h3' ).hide( "slow" );

            	basic.slideToggle( "slow" );
            	install.parent().hide( "slow" );

                scrollToNotice();

            } );

            // Step 2
            basicBtn.click( function( e ) {

            	e.preventDefault();

            	var error		= $( '.ims-error' );
            	error.empty();

            	var basicBtnGif	= $( this ).parent().find( '.ims-gif' );
            	basicBtnGif.show( "fast" );

            	var ims_membership_enable	= $( '#ims_membership_enable' );
            	ims_membership_enable		= ( ims_membership_enable.is( ':checked' ) ) ? true : false;

                var ims_recurring_membership_enable = $( '#ims_recurring_membership_enable' );
                ims_recurring_membership_enable		= ( ims_recurring_membership_enable.is( ':checked' ) ) ? true : false;

                var ims_currency_code				= $( '#ims_currency_code' ).val();
                var ims_currency_symbol				= $( '#ims_currency_symbol' ).val();
                var ims_currency_symbol_position	= $( '#ims_currency_symbol_position' ).val();

            	var ajaxCallData	= {
                    action				: "ims_basic_settings_ajax",
                    membership_enable 	: ims_membership_enable,
                    recurring_enable 	: ims_recurring_membership_enable,
                    currency_code 		: ims_currency_code,
                    currency_symbol 	: ims_currency_symbol,
                    currency_position 	: ims_currency_symbol_position
                };

            	var basicSettings 	= ajaxSettingsAjaxCall( ajaxCallData );

            	basicSettings.done( function( response ) {

            		basicBtnGif.hide( "slow" );

            		if ( response.success ) {
            			basic.parent().hide( "slow" );
	            		stripe.parent().find( 'h3' ).show( "slow" );
	            		stripe.slideToggle( "slow" );
                        scrollToNotice();
            		} else {
            			error.text( response.message );
            		}

            	} );

            	basicSettings.fail( function( jqXHR, textStatus ) {
                    error.text( "Request failed: " + textStatus );
                } );

            } );

			// Step 3
            stripeBtn.click( function( e ) {

            	e.preventDefault();

            	var error		= $( '.ims-error' );
            	error.empty();

            	var stripeBtnGif	= $( this ).parent().find( '.ims-gif' );
            	stripeBtnGif.show( "fast" );

            	var ims_stripe_enable	= $( '#ims_stripe_enable' );
            	ims_stripe_enable		= ( ims_stripe_enable.is( ':checked' ) ) ? true : false;

                var ims_stripe_test_mode 	= $( '#ims_stripe_test_mode' );
                ims_stripe_test_mode		= ( ims_stripe_test_mode.is( ':checked' ) ) ? true : false;

                var ims_live_secret			= $( '#ims_live_secret' ).val();
                var ims_live_publishable	= $( '#ims_live_publishable' ).val();
                var ims_test_secret			= $( '#ims_test_secret' ).val();
                var ims_test_publishable	= $( '#ims_test_publishable' ).val();
                var ims_stripe_webhook		= $( '#ims_stripe_webhook' ).val();

            	var ajaxCallData	= {
                    action				: "ims_stripe_settings_ajax",
                    stripe_enable 		: ims_stripe_enable,
                    stripe_test_mode 	: ims_stripe_test_mode,
                    live_secret 		: ims_live_secret,
                    live_publishable 	: ims_live_publishable,
                    test_secret 		: ims_test_secret,
                    test_publishable 	: ims_test_publishable,
                    stripe_webhook 		: ims_stripe_webhook
                };

            	var stripeSettings 	= ajaxSettingsAjaxCall( ajaxCallData );

            	stripeSettings.done( function( response ) {

            		stripeBtnGif.hide( "slow" );

            		if ( response.success ) {
            			stripe.parent().hide( "slow" );
	            		paypal.parent().find( 'h3' ).show( "slow" );
	            		paypal.slideToggle( "slow" );
                        scrollToNotice();
            		} else {
            			error.text( response.message );
            		}

            	} );

            	stripeSettings.fail( function( jqXHR, textStatus ) {
                    error.text( "Request failed: " + textStatus );
                } );

            } );

			// Step 4
            paypalBtn.click( function( e ) {

            	e.preventDefault();

            	var error		= $( '.ims-error' );
            	error.empty();

            	var paypalBtnGif	= $( this ).parent().find( '.ims-gif' );
            	paypalBtnGif.show( "fast" );

            	var ims_paypal_enable	= $( '#ims_paypal_enable' );
            	ims_paypal_enable		= ( ims_paypal_enable.is( ':checked' ) ) ? true : false;

                var ims_paypal_sandbox 	= $( '#ims_paypal_sandbox' );
                ims_paypal_sandbox		= ( ims_paypal_sandbox.is( ':checked' ) ) ? true : false;

                var ims_paypal_client_id		= $( '#ims_paypal_client_id' ).val();
                var ims_paypal_client_secret	= $( '#ims_paypal_client_secret' ).val();
                var ims_paypal_api_username		= $( '#ims_paypal_api_username' ).val();
                var ims_paypal_api_password		= $( '#ims_paypal_api_password' ).val();
                var ims_paypal_api_signature	= $( '#ims_paypal_api_signature' ).val();
                var ims_paypal_ipn_url			= $( '#ims_paypal_ipn_url' ).val();

            	var ajaxCallData	= {
                    action				: "ims_paypal_settings_ajax",
                    paypal_enable 		: ims_paypal_enable,
                    paypal_sandbox 		: ims_paypal_sandbox,
                    paypal_client_id 		: ims_paypal_client_id,
                    paypal_client_secret	: ims_paypal_client_secret,
                    paypal_api_username		: ims_paypal_api_username,
                    paypal_api_password 	: ims_paypal_api_password,
                    paypal_api_signature	: ims_paypal_api_signature,
                    paypal_ipn_url 			: ims_paypal_ipn_url
                };

            	var paypalSettings 	= ajaxSettingsAjaxCall( ajaxCallData );

            	paypalSettings.done( function( response ) {

            		paypalBtnGif.hide( "slow" );

            		if ( response.success ) {
            			paypal.parent().hide( "slow" );
	            		wire.parent().find( 'h3' ).show( "slow" );
	            		wire.slideToggle( "slow" );
                        scrollToNotice();
            		} else {
            			error.text( response.message );
            		}

            	} );

            	paypalSettings.fail( function( jqXHR, textStatus ) {
                    error.text( "Request failed: " + textStatus );
                } );

            } );

			// Step 5
            wireBtn.click( function( e ) {

            	e.preventDefault();

            	var error		= $( '.ims-error' );
            	error.empty();

            	var wireBtnGif	= $( this ).parent().find( '.ims-gif' );
            	wireBtnGif.show( "fast" );

            	var ims_wire_enable	= $( '#ims_wire_enable' );
            	ims_wire_enable		= ( ims_wire_enable.is( ':checked' ) ) ? true : false;

                var ims_wire_instructions	= $( '#ims_wire_instructions' ).val();
                var ims_wire_account_name	= $( '#ims_wire_account_name' ).val();
                var ims_wire_account_number	= $( '#ims_wire_account_number' ).val();

            	var ajaxCallData	= {
                    action				: "ims_wire_settings_ajax",
                    wire_enable 		: ims_wire_enable,
                    wire_instructions	: ims_wire_instructions,
                    wire_account_name	: ims_wire_account_name,
                    wire_account_number	: ims_wire_account_number,
                };

            	var wireSettings 	= ajaxSettingsAjaxCall( ajaxCallData );

            	wireSettings.done( function( response ) {

            		wireBtnGif.hide( "slow" );

            		if ( response.success ) {
                        $( '#ims-notice' ).hide();
            			wire.parent().hide( "slow" );
	            		error.html( response.message );
                        $( 'html, body' ).animate( {
                            scrollTop: $( "body" ).offset().top
                        }, 400 );
            		} else {
            			error.text( response.message );
            		}

            	} );

            	wireSettings.fail( function( jqXHR, textStatus ) {
                    error.text( "Request failed: " + textStatus );
                } );

            } );

        }

	});

} ); // EOF.
