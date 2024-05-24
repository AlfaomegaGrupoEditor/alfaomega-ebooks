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

        /**
         * Render the username field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function import_ebooks(): array
        {
            $imported = rand(0, 2);

            // TODO pull ebooks from panel
            //  add the ebook to wp
            //  link the ebook to products with the same isbn

            $message = $imported > 0
                ? str_replace('%s', $imported, esc_html__("Imported %s new ebooks successfully!", 'alfaomega-ebooks'))
                : esc_html__('No new eBooks to import', 'alfaomega-ebooks');

            return [
                'data' => [
                    'imported' => $imported
                ],
                'message' => $message
            ];
        }
    }
}
