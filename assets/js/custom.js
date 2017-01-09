jQuery( function( $ ) {

	"use strict";

	$( document ).ready( function() {

		$( '#rh-membership-select' ).change( function() {

			var membership_id 	= $( this ).val(); // Membership ID.
			var ims_stripe_load	= $( '.rh-membership_loader img' ); // Form Loader Image.
			var ims_adminAjax 	= $( this ).data( 'ajax-link' );
			var stripe_button 	= $( '.rh-stripe-button' ); // Stripe button div.

			stripe_button.empty();

			ims_stripe_load.show();

			var membership_stripe_button_request = $.ajax({
				url			: ims_adminAjax,
				type        : "POST",
                data        : {
                    membership		: membership_id,
                    action			: "ims_stripe_button"
                },
                dataType    : "json"
            });

            membership_stripe_button_request.done( function( response ) {
                ims_stripe_load.hide( 'fa-spin' );
                if( response.success ) {
                    stripe_button.html( '<script src="https://checkout.stripe.com/checkout.js" class="stripe-button" data-key="' + response.publishable_key + '" data-amount="' + response.price + '" data-name="' + response.blog_name + '" data-currency="' + response.currency_code + '" data-description="' + response.desc + ' for ' + response.membership + '" data-locale="auto" data-billing-address="true" data-label="' + response.button_label + '"> </script>' );
                    stripe_button.append( '<input type="hidden" name="action" value="ims_stripe_membership_payment"/>' );
                    stripe_button.append( '<input type="hidden" name="isp_nonce" value="' + response.payment_nonce + '"/>' );
                    stripe_button.append( '<input type="hidden" name="membership_price" value="' + response.price + '"/>' );
                    stripe_button.append( '<input type="hidden" name="membership_id" value="' + response.membership_id + '"/>' );
                } else {
                    stripe_button.html( '<p class="error">' + response.message + '</p>' );
                }
            });

            membership_stripe_button_request.fail( function( jqXHR, textStatus ) {
                stripe_button.text( "Request failed: " + textStatus );
            });

		} );

	} );

} ); // EOF.
