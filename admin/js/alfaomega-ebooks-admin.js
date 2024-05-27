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
		const alfaomegaEbooksForm = $('#alfaomega_ebooks_form');

		alfaomegaEbooksForm.submit(function(event) {
			event.preventDefault();
			$.ajax({
				url: php_vars.admin_post_url,
				type: 'POST',
				dataType: 'JSON',
				timeout: 0,
				data: $(this).serialize(),
				beforeSend: function() {
					$('#wpfooter')
						.append('<div class="alfaomega_ebooksLoading">Loading&#8230;</div>')
						.show();
				},
				error: function(error) {
					$('.alfaomega_ebooksLoading').remove();
					showError(error?.responseJSON?.error ? error?.responseJSON?.error : '');
				},
				success: function(response) {
					$('.alfaomega_ebooksLoading').remove();

					if ('success' === response.status) {
						showInfo(response.message);
					} else {
						showError(response.error);
					}
					checkQueue();
				},

			});
		});

		checkQueue();
	});

	let interval;

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

	function checkQueue() {
		const alfaomegaEbooksForm = $('#alfaomega_ebooks_form');
		const formSubmit = $('#form_submit');
		const queueCompleted = $("#queue-completed")
		const queueFailed = $("#queue-failed")
		const queuePending = $("#queue-pending")

		if (alfaomegaEbooksForm.length > 0) {
			interval = setInterval(function() {
				const endpoint = alfaomegaEbooksForm.find("input[name=endpoint]");
				let queue = '';
				switch (endpoint.val()) {
					case 'import_ebooks':
						queue = 'alfaomega_ebooks_queue_import';
						break;
					case 'refresh_ebooks':
						queue = 'alfaomega_ebooks_queue_refresh';
						break;
					case 'link_ebooks':
						queue = 'alfaomega_ebooks_queue_link';
						break;
				}
				let data = alfaomegaEbooksForm.serialize().replace(endpoint.val(), 'queue_status');

				$.ajax({
					url: php_vars.admin_post_url,
					type: 'GET',
					dataType: 'JSON',
					timeout: 0,
					data: data + `&queue=${queue}`,
					error: function(error) {
					},
					success: function(response) {
						if (response.status === 'success') {
							formSubmit.prop("disabled", response.data.pending > 0)
							queueCompleted.html(response.data.completed);
							queueFailed.html(response.data.failed);
							queuePending.html(response.data.pending);

							if (response.data.pending === 0) {
								clearInterval(interval);
							}
						}
					}
				});

			}, 6000);
		}
	}
})( jQuery );
