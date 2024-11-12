// the woo global styles. adding uikit classes to woocommerce markup where necessary
// this needs firing on document.ready & after shop filtering etc
wooGlobalStyles = function() {
	jQuery(function($) {
	
		// form login
		$(".woocommerce-form-login .woocommerce-form-login__submit").addClass("uk-button-primary uk-margin-top");
		
		// buttons
		$(".button").addClass("uk-button");
		$("input.submit").addClass("uk-button uk-button-primary");
		$(".woocommerce-message .button").addClass("uk-button-primary uk-button-small");
		// $(".tease-buttons .button").addClass("uk-button-small uk-button-primary uk-button-small uk-text-boldest uk-text-capitalize");
		$(".tease-buttons .button").addClass("uk-button uk-button-small uk-button-primary uk-button-small uk-text-boldest uk-text-capitalize");
		
		// forms
		$(".woocommerce-form__input-checkbox").addClass("uk-checkbox");
		$("input#wp-comment-cookies-consent").addClass("uk-checkbox");
		$(".input-radio").addClass("uk-radio");
		$(".input-text").addClass("uk-input");
		$(".input-checkbox").addClass("uk-checkbox");
		$(".comment-form input").addClass("uk-input");
		$("input#wp-comment-cookies-consent").removeClass("uk-input");
		// $("input.qty").addClass("uk-input");
		$("form label").addClass("uk-form-label");
		$(".woocommerce-form").addClass("uk-form-stacked");
		$(".comment-form").addClass("uk-form-stacked");
		$("form").addClass("uk-form-stacked");
		$("em").addClass("uk-text-danger");
		$("select").addClass("uk-select");
		$("textarea").addClass("uk-textarea");
		// $("label").addClass("uk-form-label");
		
		// tables
		$("table").addClass("uk-table");
		
		// onsale
		$(".onsale").addClass("uk-card-badge uk-label uk-position-top-right uk-position-small uk-background-primary");
		
		// woo errors
		$("ul.woocommerce-error").addClass("uk-list");
		$("ul.woocommerce-error .uk-button").addClass("uk-button-primary");
		
		// login/register grid
		$(".col2-set").attr("uk-grid", "");
		$(".col2-set").addClass("uk-child-width-1-2@m");
	
	});
};

// what to do to Woo when the document is ready (woo globals)
jQuery(document).ready(function($) {
	
	// do stuff on added_to_cart event
	$(document).on('added_to_cart', function(e, fragments, cart_hash, this_button) {
		UIkit.modal("#MiniCartModal").show(); // display the minicart modal
		$(".added_to_cart").addClass("uk-button uk-button-small uk-text-emphasis"); // style added_to_cart button
  });
	
	wooGlobalStyles();
	mailchimp4WpStyles();
	// headerSize();
	
	// whenNodesChange('#MiniCartModal', wooGlobalStyles); // do the WooGlobalStyles again when MiniCartModal changes. only when doc is fully loaded & modal is created
});