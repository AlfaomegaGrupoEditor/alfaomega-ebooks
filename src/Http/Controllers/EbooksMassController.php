<?php

namespace AlfaomegaEbooks\Http\Controllers;

use AlfaomegaEbooks\Services\eBooks\Service;
use WP_REST_Request;
use WP_REST_Response;

class EbooksMassController
{
    /**
     * Import eBooks from Alfaomega.
     *
     * This method is responsible for importing eBooks from Alfaomega.
     * It uses the eBooks service to import eBooks and returns a response with the status and message.
     *
     * @return WP_REST_Response The response containing the status and message.
     */
    public function masImportEbooks(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $response = Service::make()->ebooks()
                ->importEbook()
                ->batch();

            $message = $response['imported'] > 0
                ? str_replace('%s', $response['imported'], esc_html__("%s new ebooks were added to the import scheduler successfully!", 'alfaomega-ebooks'))
                : esc_html__('No new eBooks to import', 'alfaomega-ebooks');

            return new WP_REST_Response([
                    'status'  => 'success',
                    'data'    => $response,
                    'message' => $message,
                ], 200);
        } catch (\Exception $exception) {
            return new WP_REST_Response([
                'status'  => 'fail',
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Links products and returns the response.
     *
     * This method is responsible for linking products. It first retrieves the post IDs from the request.
     * If no post IDs are specified, it throws an exception. Then, it makes a service call to link the products,
     * and batches them. The number of linked products is then checked.
     * If the number of linked products is greater than 0, a success message is returned with the number of linked products.
     * Otherwise, a message indicating that there are no products found to link is returned.
     * The response from the service call and the message are then returned in a WP_REST_Response object.
     *
     * @param WP_REST_Request $request The request object, which should contain the post IDs to link.
     * @return WP_REST_Response A WP_REST_Response object containing the response from the service call and the message.
     * @throws \Exception If no post IDs are specified in the request.
     */
    public function massLinkProducts(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $response = Service::make()->wooCommerce()
                ->linkProduct()
                ->batch();

            $message = $response['linked'] > 0
                ? str_replace('%s', $response['linked'], esc_html__("Linked %s products successfully!", 'alfaomega-ebooks'))
                : esc_html__('No products found to link', 'alfaomega-ebooks');

            return new WP_REST_Response([
                'status'  => 'success',
                'data'    => $response,
                'message' => $message,
            ], 200);
        } catch (\Exception $exception) {
            return new WP_REST_Response([
                'status'  => 'fail',
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Links ebooks and returns the response.
     *
     * This method is responsible for linking products. It first retrieves the post IDs from the request.
     * If no post IDs are specified, it throws an exception. Then, it makes a service call to link the products,
     * and batches them. The number of linked products is then checked.
     * If the number of linked products is greater than 0, a success message is returned with the number of linked products.
     * Otherwise, a message indicating that there are no products found to link is returned.
     * The response from the service call and the message are then returned in a WP_REST_Response object.
     *
     * @param WP_REST_Request $request The request object, which should contain the post IDs to link.
     * @return WP_REST_Response A WP_REST_Response object containing the response from the service call and the message.
     * @throws \Exception If no post IDs are specified in the request.
     */
    public function massLinkEbooks(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $response = Service::make()->wooCommerce()
                ->linkProduct()
                ->batch();

            $message = $response['linked'] > 0
                ? str_replace('%s', $response['linked'], esc_html__("Linked %s products successfully!", 'alfaomega-ebooks'))
                : esc_html__('No products found to link', 'alfaomega-ebooks');

            return new WP_REST_Response([
                'status'  => 'success',
                'data'    => $response,
                'message' => $message,
            ], 200);
        } catch (\Exception $exception) {
            return new WP_REST_Response([
                'status'  => 'fail',
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
