<?php

/**
 * The Alfaomega_Ebooks_Controller class.
 * This class is responsible for processing requests, handling bulk actions, and interacting with the
 * Alfaomega_Ebooks_Service. It provides methods for importing ebooks, refreshing ebooks, linking products, linking
 * ebooks, downloading ebooks, checking the queue status, and clearing the queue. It also provides a method for
 * processing requests, which includes checking the request method, verifying the nonce, checking the user
 * capabilities, and calling the appropriate endpoint method. The class uses the Alfaomega_Ebooks_Service class to
 * perform these actions.
 *
 * @since      1.0.0
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/includes
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
if( ! class_exists( 'Alfaomega_Ebooks_Controller' )){
    class Alfaomega_Ebooks_Controller{
        /**
         * @var array $request
         * Holds the request data. This could be either POST or GET data.
         */
        protected array $request = [];

        /**
         * @var Alfaomega_Ebooks_Service $service
         * An instance of the Alfaomega_Ebooks_Service class. This service class is used to perform various actions such
         * as importing ebooks, refreshing ebooks, linking products, linking ebooks, downloading ebooks, checking the
         * queue status, and clearing the queue.
         */
        protected Alfaomega_Ebooks_Service $service;

        /**
         * The constructor for the Alfaomega_Ebooks_Controller class.
         * This method is called when an object of the Alfaomega_Ebooks_Controller class is created. It initializes the
         * $service property with a new instance of the Alfaomega_Ebooks_Service class.
         *
         * @since 1.0
         */
        public function __construct()
        {
            $this->service = new Alfaomega_Ebooks_Service();
        }

        public function process(): void
        {
            try {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->request = $_POST;
                } else {
                    $this->request = $_GET;
                }

                header('Content-Type: application/json; charset=utf-8');

                if( isset( $this->request['alfaomega_ebook_nonce'] ) ){
                    if( ! wp_verify_nonce( $this->request['alfaomega_ebook_nonce'], 'alfaomega_ebook_nonce' ) ){
                        throw new Exception(esc_html__('Access denied', 'alfaomega-ebooks'), 403);
                    }
                } else {
                    throw new Exception(esc_html__('Access denied', 'alfaomega-ebooks'), 403);
                }

                if( ! current_user_can( 'install_plugins') ){
                    throw new Exception(esc_html__('Access denied', 'alfaomega-ebooks'), 403);
                }

                if (empty($this->request['endpoint']) || !method_exists($this, $this->request['endpoint'])) {
                    throw new Exception(esc_html__('Bad request', 'alfaomega-ebooks'), 400);
                }

                $response = $this->{$this->request['endpoint']}();
                echo json_encode([
                    'status'  => 'success',
                    'message' => !empty($response['message']) ? $response['message'] : esc_html__('God Job!!', 'alfaomega-ebooks'),
                    'data'    => $response['data'],
                ]);

            } catch (\Exception $exception) {
                status_header($exception->getCode());
                echo json_encode([
                    'status' => 'fail',
                    'error'  => $exception->getMessage(),
                ]);
            }
        }

        /**
         * Performs a bulk action on a set of ebooks.
         * This method takes a redirect URL, an action, and an array of post IDs. It performs the specified action on the posts
         * with the given IDs. The actions can be 'update-meta', 'link-product', or 'link-ebook'. For each action, it calls the
         * appropriate method, updates the redirect URL with the result, and returns the updated URL. If an error occurs during
         * the action, it adds 'fail' to the redirect URL.
         *
         * @param string $redirect_url The URL to redirect to after the action is performed.
         * @param string $action       The action to perform. Can be 'update-meta', 'link-product', or 'link-ebook'.
         * @param array $post_ids      The IDs of the posts to perform the action on.
         *
         * @return string The updated redirect URL.
         * @throws Exception If an error occurs during the action.
         * @since 1.0
         */
        public function bulk_action_alfaomega_ebooks(string $redirect_url, string $action, array $post_ids): string
        {
            switch ($action) {
                case 'update-meta':
                    try {
                        $redirect_url = remove_query_arg('link-product', $redirect_url);
                        $result = $this->refresh_ebooks($post_ids);
                        $redirect_url = add_query_arg('updated-meta', $result['data']['refreshed'], $redirect_url);
                    } catch (Exception $exception) {
                        $redirect_url = add_query_arg('updated-meta', 'fail', $redirect_url);
                    }
                    break;
                case 'link-product':
                    try {
                        $redirect_url = remove_query_arg('updated-meta', $redirect_url);
                        $result = $this->link_products($post_ids);
                        $redirect_url = add_query_arg('link-product', $result['data']['linked'], $redirect_url);
                    } catch (Exception $exception) {
                        $redirect_url = add_query_arg('link-product', 'fail', $redirect_url);
                    }
                    break;
                case 'link-ebook':
                    try {
                        //$redirect_url = remove_query_arg('link-product', $redirect_url);
                        $result = $this->link_ebooks($post_ids);
                        $redirect_url = add_query_arg('link-ebook', $result['data']['linked'], $redirect_url);
                    } catch (Exception $exception) {
                        $redirect_url = add_query_arg('link-ebook', 'fail', $redirect_url);
                    }
                    break;
            }

            return $redirect_url;
        }

        /**
         * Imports ebooks and returns the response.
         * This method calls the importEbooks method of the service class, which imports ebooks and returns a response.
         * The response contains the number of imported ebooks. If the number of imported ebooks is greater than 0,
         * it generates a success message. Otherwise, it generates a message indicating that no new ebooks were imported.
         *
         * @return array An associative array containing the response data and the message.
         * @throws \Exception
         * @since 1.0
         */
        public function import_ebooks(): array
        {
            $response = $this->service->importEbooks();

            $message = $response['imported'] > 0
                ? str_replace('%s', $response['imported'], esc_html__("%s new ebooks were added to the import scheduler successfully!", 'alfaomega-ebooks'))
                : esc_html__('No new eBooks to import', 'alfaomega-ebooks');

            return [
                'data'    => $response,
                'message' => $message,
            ];
        }

        /**
         * Refreshes ebooks and returns the response.
         * This method calls the refreshEbooks method of the service class, which refreshes ebooks and returns a response.
         * The response contains the number of refreshed ebooks. If the number of refreshed ebooks is greater than 0,
         * it generates a success message. Otherwise, it generates a message indicating that no ebooks were found to refresh.
         *
         * @param array|null $postIds The IDs of the posts to refresh. If null, all posts are refreshed.
         *
         * @return array An associative array containing the response data and the message.
         * @throws \Exception
         * @since 1.0
         */
        public function refresh_ebooks(array $postIds = null): array
        {
            $response = $this->service->refreshEbooks($postIds);

            $message = $response['refreshed'] > 0
                ? str_replace('%s', $response['refreshed'], esc_html__("Scheduled to refresh %s ebooks successfully!", 'alfaomega-ebooks'))
                : esc_html__('No eBooks found to refresh', 'alfaomega-ebooks');

            return [
                'data'    => $response,
                'message' => $message,
            ];
        }

        /**
         * Links products and returns the response.
         * This method calls the linkProducts method of the service class, which links products and returns a response.
         * The response contains the number of linked products. If the number of linked products is greater than 0,
         * it generates a success message. Otherwise, it generates a message indicating that no products were found to link.
         *
         * @param array|null $postIds The IDs of the posts to link. If null, all posts are linked.
         *
         * @return array An associative array containing the response data and the message.
         * @since 1.0
         */
        public function link_products(array $postIds = null): array
        {
            $response = $this->service
                ->initWooCommerceClient()
                ->linkProducts($postIds);

            $message = $response['linked'] > 0
                ? str_replace('%s', $response['linked'], esc_html__("Linked %s products successfully!", 'alfaomega-ebooks'))
                : esc_html__('No products found to link', 'alfaomega-ebooks');

            return [
                'data'    => $response,
                'message' => $message,
            ];
        }

        /**
         * Links ebooks and returns the response.
         * This method calls the linkEbooks method of the service class, which links ebooks and returns a response.
         * The response contains the number of linked ebooks. If the number of linked ebooks is greater than 0,
         * it generates a success message. Otherwise, it generates a message indicating that no ebooks were found to link.
         *
         * @param array|null $postIds The IDs of the posts to link. If null, all posts are linked.
         *
         * @return array An associative array containing the response data and the message.
         * @since 1.0
         */
        public function link_ebooks($postIds = null): array
        {
            $response = $this->service
                ->initWooCommerceClient()
                ->linkEbooks($postIds);

            $message = $response['linked'] > 0
                ? str_replace('%s', $response['linked'], esc_html__("Linked %s ebooks successfully!", 'alfaomega-ebooks'))
                : esc_html__('No ebooks found to link, please check the product ISBN tag', 'alfaomega-ebooks');

            return [
                'data'    => $response,
                'message' => $message,
            ];
        }

        /**
         * Downloads an ebook.
         * This method calls the downloadEbook method of the service class, which downloads an ebook.
         * The product and download ID are retrieved from the request data.
         *
         * @throws Exception If an error occurs during the download.
         * @since 1.0
         */
        public function download_ebook(): void
        {
            try {
                $product = $this->request['product'];
                $download_id = $this->request['download_id']; // the unique download ID
                $this->service->downloadEbook($product, $download_id);
                // show success message
            } catch (Exception $exception) {
                // show error message
            }
        }

        /**
         * Returns the status of a queue.
         * This method calls the queueStatus method of the service class, which returns the status of a queue.
         * The queue is retrieved from the request data.
         *
         * @return array An associative array containing the queue status.
         * @since 1.0
         */
        public function queue_status(): array
        {
            $queue = $this->request['queue'];

            return [
                'data' => $this->service->queueStatus($queue),
            ];
        }

        /**
         * Clears a queue and returns the response.
         * This method calls the clearQueue method of the service class, which clears a queue and returns a response.
         * The response contains a success message.
         *
         * @return array An associative array containing the response data and the message.
         * @since 1.0
         */
        public function clear_queue(): array
        {
            return [
                'data'    => $this->service->clearQueue(),
                'message' => esc_html__('Queue cleared successfully', 'alfaomega-ebooks'),
            ];
        }
    }
}
