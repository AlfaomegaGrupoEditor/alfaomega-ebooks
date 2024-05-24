(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {
		$('#alfaomega_ebooks_import_ebooks').submit(function(event) {
			event.preventDefault();
			$.ajax({
				url: 'https://reqres.in/api/users?page=2',
				type: 'get',
				dataType: 'JSON',
				//data: {action: n, nonce: wp_dummy_content_generator_backend_ajax_object.nonce},
				beforeSend: function() {
					$('#wpfooter')
						.append('<div class="alfaomega_ebooksLoading">Loading&#8230;</div>')
						.show();
				},
				error: function(error) {
					showError();
				},
				success: function(response) {
					$('.alfaomega_ebooksLoading').remove();

					if ('success' === response.status) {
						showInfo();
					}
				},

			});
		});
	});

	function showError( msg = 'Something went wrong. Please try again') {
		$('.alfaomega_ebooks-error-msg')
			.html(msg)
			.fadeIn('fast')
			.delay(5000)
			.fadeOut('slow');
	}

	function showInfo( msg = 'Good Jod!!') {
		$('.alfaomega_ebooks-success-msg')
			.html(msg)
			.fadeIn('fast')
			.delay(5000)
			.fadeOut('slow');
	}

})( jQuery );
