<?php

/**
 * This class defines the general plugin settings.
 *
 * @since      1.0.0
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/includes
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
if( ! class_exists( 'Alfaomega_Ebooks_Controller' )){
    class Alfaomega_Ebooks_Controller{

        protected array $request = [];
        protected Alfaomega_Ebooks_Service $service;

        public function __construct() {
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

        public function bulk_action_alfaomega_ebooks($redirect_url, $action, $post_ids): string
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

        public function refresh_ebooks($postIds = null): array
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

        public function link_products($postIds = null): array
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

        public function download_ebook()
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

        public function queue_status(): array
        {
            $queue = $this->request['queue'];
            return [
                'data' => $this->service->queueStatus($queue)
            ];
        }

        public function clear_queue(): array
        {
            return [
                'data'    => $this->service->clearQueue(),
                'message' => esc_html__('Queue cleared successfully', 'alfaomega-ebooks'),
            ];
        }
    }
}
