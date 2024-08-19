<?php

namespace AlfaomegaEbooks\Http\Controllers;

use AlfaomegaEbooks\Services\eBooks\Service;
use WP_REST_Request;
use WP_REST_Response;

class QueueController
{
    /**
     * Import eBooks from Alfaomega.
     *
     * This method is responsible for importing eBooks from Alfaomega.
     * It uses the eBooks service to import eBooks and returns a response with the status and message.
     *
     * @return WP_REST_Response The response containing the status and message.
     */
    public function queueStatus(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $queue = $request->get_param('queue');
            if (empty($queue)) {
                throw new \Exception(esc_html__('Queue ID is required', 'alfaomega-ebooks'));
            }

            return new WP_REST_Response([
                'status'  => 'success',
                'data' => Service::make()
                    ->queue()
                    ->status($queue),
            ]);
        } catch (\Exception $exception) {
            return new WP_REST_Response([
                'status'  => 'fail',
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Refreshes ebooks and returns the response.
     *
     * This method is responsible for refreshing eBooks. It first retrieves the post IDs from the request.
     * If no post IDs are specified, it throws an exception. Then, it makes a service call to refresh the eBooks,
     * and batches them. The number of refreshed eBooks is then checked.
     * If the number of refreshed eBooks is greater than 0, a success message is returned with the number of refreshed eBooks.
     * Otherwise, a message indicating that there are no new eBooks to refresh is returned.
     * The response from the service call and the message are then returned in a WP_REST_Response object.
     *
     * @param WP_REST_Request $request The request object, which should contain the post IDs to refresh.
     * @return WP_REST_Response A WP_REST_Response object containing the response from the service call and the message.
     * @throws \Exception If no post IDs are specified in the request.
     */
    public function queueClear(WP_REST_Request $request): WP_REST_Response
    {
        try {
            return new WP_REST_Response([
                'status'  => 'success',
                'data' => Service::make()->queue()->clear(),
            ]);
        } catch (\Exception $exception) {
            return new WP_REST_Response([
                'status'  => 'fail',
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
