/**
 * Plugin JS file
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

            var ajaxURL = removeQueryStringParameters( jsData.ajaxURL );

            $( '#ims-stripe' ).click( function( e ) {

                e.preventDefault(); // Prevent the default event of link.
                $( '#ims_select_membership > .error' ).text( 'Please select a membership.' );

            } );

    		/**
             * Generate Stripe Button for different memberships.
             *
             * @author Ashar Irfan
             * @since  1.0.0
             */
            $( '#ims-membership-select' ).change( function() {

    			var membership_id 	= $( this ).val(); // Membership ID.
    			var ims_stripe_load	= $( '.ims-membership_loader img' ); // Form Loader Image.
    			var stripe_button 	= $( '.ims-stripe-button' ); // Stripe button div.
                var error_div       = $( '#ims_select_membership > .error' ); // Error div.
                var select_nonce    = $( '#membership_select_nonce' ).val(); // Membership select nonce.

                error_div.empty(); // Empty the error div.
    			ims_stripe_load.show(); // Show the ajax loader GIF.

    			var membership_stripe_button_request = $.ajax({
    				url			: ajaxURL,
    				type        : "POST",
                    data        : {
                        membership		: membership_id,
                        action			: "ims_stripe_button",
                        nonce           : select_nonce
                    },
                    dataType    : "json"
                });

                membership_stripe_button_request.done( function( response ) {
                    ims_stripe_load.hide( 'fa-spin' ); // Hide ajax loader GIF.
                    if ( response.success ) {

                        stripe_button.empty(); // Empty the previous button.

                        if ( ! response.price ) {

                            stripe_button.hide();
                            $( '.ims-paypal-button' ).hide();

                            var freeButton  = jQuery( '<button></button>' );
                            freeButton.attr( 'id', 'ims-free-button' );
                            freeButton.attr( 'type', 'submit' );
                            freeButton.text( response.freeButtonLabel );
                            stripe_button.append( freeButton );
                            stripe_button.show();

                            var free_nonce = jQuery( '<input />' );
                            free_nonce.attr( 'type', 'hidden' );
                            free_nonce.attr( 'id', 'ims_free_check' );
                            free_nonce.attr( 'name', 'ims_free_check' );
                            free_nonce.attr( 'value', true );
                            stripe_button.append( free_nonce );

                        } else {

                            $( '.ims-paypal-button' ).show();

                            // Stripe Payment Button
                            var stripe_pay  = jQuery( '<script></script>' );
                            stripe_pay.attr( 'src', 'https://checkout.stripe.com/checkout.js' );
                            stripe_pay.addClass( 'stripe-button' );
                            stripe_pay.attr( 'data-key', response.publishable_key );
                            stripe_pay.attr( 'data-amount', response.price );
                            stripe_pay.attr( 'data-name', response.blog_name );
                            stripe_pay.attr( 'data-currency', response.currency_code );
                            stripe_pay.attr( 'data-description', response.desc + ' for ' + response.membership );
                            stripe_pay.attr( 'data-locale', 'auto' );
                            stripe_pay.attr( 'data-billing-address', 'true' );
                            stripe_pay.attr( 'data-label', response.button_label );
                            stripe_button.append( stripe_pay );

                            var stripe_action = jQuery( '<input />' );
                            stripe_action.attr( 'type', 'hidden' );
                            stripe_action.attr( 'name', 'action' );
                            stripe_action.attr( 'value', 'ims_stripe_membership_payment' );
                            stripe_button.append( stripe_action );

                            var stripe_nonce = jQuery( '<input />' );
                            stripe_nonce.attr( 'type', 'hidden' );
                            stripe_nonce.attr( 'name', 'ims_stripe_nonce' );
                            stripe_nonce.attr( 'value', response.payment_nonce );
                            stripe_button.append( stripe_nonce );

                            var stripe_price = jQuery( '<input />' );
                            stripe_price.attr( 'type', 'hidden' );
                            stripe_price.attr( 'name', 'membership_price' );
                            stripe_price.attr( 'value', response.price );
                            stripe_button.append( stripe_price );

                            var stripe_membership_id = jQuery( '<input />' );
                            stripe_membership_id.attr( 'type', 'hidden' );
                            stripe_membership_id.attr( 'name', 'membership_id' );
                            stripe_membership_id.attr( 'value', response.membership_id );
                            stripe_button.append( stripe_membership_id );
                        }

                    } else {
                        error_div.text( response.message );
                    }
                });

                membership_stripe_button_request.fail( function( jqXHR, textStatus ) {
                    stripe_button.text( "Request failed: " + textStatus );
                });

    		} );

            /**
             * Make request to PayPal for payment of membership.
             *
             * @author Ashar Irfan
             * @since  1.0.0
             */
            $( '#ims-paypal' ).click( function( e ) {

                e.preventDefault(); // Prevent the default event of link.
                var recurring           = $( '#ims_recurring' );

                if ( ! recurring.is( ':checked' ) ) {
                    ims_paypal_simple_payment_request();
                } else {
                    ims_paypal_recurring_payment_request();
                }

            } );

            var ims_paypal_simple_payment_request = function() {

                var membershipSelect    = $( '#ims-membership-select' ); // Membership select option.
                var membership          = membershipSelect.val(); // Getting selected membership id.
                var form_loader         = $( '.ims-membership_loader img' ); // Form Loader Image.
                var error_div           = $( '#ims_select_membership > .error' ); // Error div.
                var paypal_nonce        = $( '#membership_paypal_nonce' ).val(); // Membership paypal nonce.
                error_div.empty();

                form_loader.show(); // Show ajax loader GIF.

                var simple_paypal_payment_request = $.ajax({
                    url         : ajaxURL,
                    type        : "POST",
                    data        : {
                        membership_id   : membership,
                        action          : "ims_paypal_simple_payment",
                        nonce           : paypal_nonce
                    },
                    dataType    : "json"
                });

                simple_paypal_payment_request.done( function( response ) {
                    form_loader.hide(); // Hide ajax loader GIF.
                    if ( response.success ) {
                        window.location.href    = response.url; // Redirect to URL returned by PayPal.
                    } else {
                        error_div.text( response.message );
                    }
                } );

                simple_paypal_payment_request.fail( function( jqXHR, textStatus ) {
                    error_div.text( "Request failed: " + textStatus );
                });

            }

            var ims_paypal_recurring_payment_request = function() {

                var membershipSelect    = $( '#ims-membership-select' ); // Membership select option.
                var membership          = membershipSelect.val(); // Getting selected membership id.
                var form_loader         = $( '.ims-membership_loader img' ); // Form Loader Image.
                var error_div           = $( '#ims_select_membership > .error' ); // Error div.
                var paypal_nonce        = $( '#membership_paypal_nonce' ).val(); // Membership paypal nonce.
                error_div.empty();

                form_loader.show(); // Show ajax loader GIF.

                var recurring_paypal_payment_request = $.ajax({
                    url         : ajaxURL,
                    type        : "POST",
                    data        : {
                        membership_id   : membership,
                        action          : "ims_paypal_recurring_payment",
                        nonce           : paypal_nonce
                    },
                    dataType    : "json"
                });

                recurring_paypal_payment_request.done( function( response ) {
                    form_loader.hide(); // Hide ajax loader GIF.
                    if ( response.success ) {
                        // console.log(response);
                        window.location.href    = response.url; // Redirect to URL returned by PayPal.
                    } else {
                        // console.log(response);
                        error_div.text( response.message );
                    }
                } );

                recurring_paypal_payment_request.fail( function( jqXHR, textStatus ) {
                    error_div.text( "Request failed: " + textStatus );
                });

            }

            /**
             * Make request to send membership receipt to user.
             *
             * @author Ashar Irfan
             * @since  1.0.0
             */
            $( '#ims-receipt' ).click( function( e ) {

                e.preventDefault(); // Prevent the default event of link.

                var membershipSelect    = $( '#ims-membership-select' ); // Membership select option.
                var membership          = membershipSelect.val(); // Getting selected membership id.
                var form_loader         = $( '.ims-membership_loader img' ); // Form Loader Image.
                var response_div        = $( '#ims_select_membership > .error' ); // Error div.
                var wire_nonce          = $( '#membership_wire_nonce' ).val(); // Wire Transfer nonce.
                response_div.empty();

                form_loader.show(); // Show ajax loader GIF.

                var send_receipt_request = $.ajax({
                    url         : ajaxURL,
                    type        : "POST",
                    data        : {
                        membership_id   : membership,
                        action          : "ims_send_wire_receipt",
                        nonce           : wire_nonce
                    },
                    dataType    : "json"
                });

                send_receipt_request.done( function( response ) {
                    form_loader.hide(); // Hide ajax loader GIF.
                    if ( response.success ) {
                        // console.log(response);
                        response_div.text( response.message );
                    } else {
                        // console.log(response);
                        response_div.text( response.message );
                    }
                } );

                send_receipt_request.fail( function( jqXHR, textStatus ) {
                    response_div.text( "Request failed: " + textStatus );
                });

            } );

        }

	} );

} ); // EOF.
