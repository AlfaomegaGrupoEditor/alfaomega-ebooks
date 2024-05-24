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
                $this->request = $_POST;
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
                    'message' => $response['message'],
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

        public function import_ebooks(): array
        {
            $response = $this->service->importEbooks();

            $message = $response['imported'] > 0
                ? str_replace('%s', $response['imported'], esc_html__("Imported %s new ebooks successfully!", 'alfaomega-ebooks'))
                : esc_html__('No new eBooks to import', 'alfaomega-ebooks');

            return [
                'data'    => $response,
                'message' => $message,
            ];
        }

        public function refresh_ebooks(): array
        {
            $response = $this->service->refreshEbooks();

            $message = $response['refreshed'] > 0
                ? str_replace('%s', $response['refreshed'], esc_html__("Refreshed %s ebooks successfully!", 'alfaomega-ebooks'))
                : esc_html__('No eBooks found to refresh', 'alfaomega-ebooks');

            return [
                'data'    => $response,
                'message' => $message,
            ];
        }

        public function link_products(): array
        {
            $response = $this->service->linkProducts();

            $message = $response['linked'] > 0
                ? str_replace('%s', $response['linked'], esc_html__("Linked %s products successfully!", 'alfaomega-ebooks'))
                : esc_html__('No products found to link', 'alfaomega-ebooks');

            return [
                'data'    => $response,
                'message' => $message,
            ];
        }
    }
}
