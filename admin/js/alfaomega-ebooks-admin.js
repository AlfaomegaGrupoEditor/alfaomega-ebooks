/**
 * This is a self-invoking function that uses jQuery to handle the admin-facing JavaScript source code.
 * It includes handlers for DOM-ready and window-load events.
 * It also includes AJAX calls for form submission and queue clearing, as well as a function to check the queue status.
 */
(function( $ ) {
	'use strict';

	/**
	 * This function is executed when the DOM is ready.
	 * It sets up event handlers for form submission and queue clearing.
	 * It also initiates the queue checking process.
	 */
	$(function() {
		// Define variables for form, clear queue button, form submit button, and queue status
		const alfaomegaEbooksForm = $('#alfaomega_ebooks_form');
		const clearQueue = $('#clear-queue');
		const formSubmit = $('#form_submit');
		const queueStatus = $("#queue_status")
		const endpointValue = alfaomegaEbooksForm.find("input[name=endpoint]").val();

		/**
		 * This is the event handler for form submission.
		 * It prevents the default form submission action and makes an AJAX call instead.
		 */
		alfaomegaEbooksForm.submit(function(event) {
			// Prevent default form submission
			event.preventDefault();

			// Make AJAX call
			$.ajax({
				url: php_vars.api_url + '/' +  endpointValue,
				type: 'GET',
				dataType: 'JSON',
				timeout: 0,
				beforeSend: function(xhr) {
					xhr.setRequestHeader('X-WP-Nonce', php_vars.nonce);

					// Disable form submit button and update queue status before sending the request
					formSubmit.prop("disabled", true);
					queueStatus.html('Scheduling jobs');
					checkQueue();
					$('#wpfooter')
						.append('<div class="alfaomega_ebooksLoading">Loading&#8230;</div>')
						.show();
				},
				error: function(error) {
					// Remove loading indicator and show error message on error
					$('.alfaomega_ebooksLoading').remove();
					showError(error?.responseJSON?.error ? error?.responseJSON?.error : '');
				},
				success: function(response) {
					// Remove loading indicator and show success or error message on success
					$('.alfaomega_ebooksLoading').remove();

					if ('success' === response.status) {
						showInfo(response.message);
					} else {
						showError(response.error);
					}
					checkQueue(true);
				},

			});
		});

		/**
		 * This is the event handler for the clear queue button click event.
		 * It prevents the default action and makes an AJAX call instead.
		 */
		clearQueue.click(function(event){
			// Prevent default action
			event.preventDefault();

			// Prepare data for AJAX call
			const endpoint = alfaomegaEbooksForm.find("input[name=endpoint]");
			let data = alfaomegaEbooksForm.serialize().replace(endpoint.val(), 'clear_queue');

			// Make AJAX call
			$.ajax({
				url: php_vars.admin_post_url,
				type: 'POST',
				timeout: 0,
				data: data,
				error: function(error) {
					// Show error message on error
					showError(error?.responseJSON?.error ? error?.responseJSON?.error : '');
				},
				success: function(response) {
					// Show success or error message on success
					if ('success' === response.status) {
						showInfo(response.message);
					} else {
						showError(response.error);
					};
					checkQueue(true);
				},

			});
		});

		// Initiate queue checking process
		checkQueue();
	});

	// Define interval variable
	let interval;

	/**
	 * This function shows an error message.
	 * @param {string} msg - The error message to show. Defaults to a generic error message.
	 */
	function showError( msg = 'Something went wrong. Please try again') {
		$('.alfaomega_ebooks-error-msg')
			.html(msg)
			.fadeIn('fast')
			.delay(5000)
			.fadeOut('slow');
	}

	/**
	 * This function shows an info message.
	 * @param {string} msg - The info message to show. Defaults to a generic info message.
	 */
	function showInfo( msg = 'Good Jod!!') {
		$('.alfaomega_ebooks-success-msg')
			.html(msg)
			.fadeIn('fast')
			.delay(5000)
			.fadeOut('slow');
	}

	/**
	 * This function checks the queue status.
	 * It makes an AJAX call to get the queue status and updates the UI accordingly.
	 * @param {boolean} force - Whether to force the queue checking process. Defaults to false.
	 */
	function checkQueue(force= false) {
		// Define variables for form, form submit button, and queue status elements
		const alfaomegaEbooksForm = $('#alfaomega_ebooks_form');
		const formSubmit = $('#form_submit');
		const queueCompleted = $("#queue-complete")
		const queueFailed = $("#queue-failed")
		const queuePending = $("#queue-pending")
		const queueStatus = $("#queue_status")

		// Check if form exists and queue checking process should be initiated
		if (alfaomegaEbooksForm.length > 0 && (force || (queuePending.html() && queuePending.html().trim() !== '0'))) {
			// Update queue status and start interval to check queue status
			queueStatus.html('Working');
			interval = setInterval(function() {
				// Prepare data for AJAX call
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

				// Make AJAX call
				$.ajax({
					url: php_vars.admin_post_url,
					type: 'GET',
					dataType: 'JSON',
					timeout: 0,
					data: data + `&queue=${queue}`,
					error: function(error) {
					},
					success: function(response) {
						// Update UI based on response
						if (response.status === 'success') {
							formSubmit.prop("disabled", response.data.pending > 0)
							queueCompleted.html(response.data.complete);
							queueFailed.html(response.data.failed);
							queuePending.html(response.data.pending);

							if (response.data.pending === 0) {
								// Stop interval and show info message if all jobs are complete
								clearInterval(interval);
								showInfo('Queue status updated!');
								formSubmit.prop("disabled", false);
								$('queue_status').html('Idle');
							}
						}
					}
				});

			}, 6000);
		}
	}
})( jQuery );
