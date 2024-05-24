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

        /**
         * Constructor
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function process(): void
        {
            try {
                header('Content-Type: application/json; charset=utf-8');

                if( isset( $_POST['alfaomega_ebook_nonce'] ) ){
                    if( ! wp_verify_nonce( $_POST['alfaomega_ebook_nonce'], 'alfaomega_ebook_nonce' ) ){
                        throw new Exception(esc_html__('Access denied', 'alfaomega-ebooks'), 403);
                    }
                } else {
                    throw new Exception(esc_html__('Access denied', 'alfaomega-ebooks'), 403);
                }

                if( ! current_user_can( 'edit_post') ){
                    throw new Exception(esc_html__('Access denied', 'alfaomega-ebooks'), 403);
                }

                $this->request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
                if (empty($this->request['action']) || !method_exists($this, $this->request['action'])) {
                    throw new Exception(esc_html__('Bad request', 'alfaomega-ebooks'), 400);
                }

                echo json_encode([
                    'status' => 'fail',
                    'error'  => $this->{$this->request['action']}(),
                ]);

            } catch (\Exception $exception) {
                http_response_code($exception->getCode() || 400);
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
        public function alfaomega_ebooks_import_ebooks(): array
        {
            return [
                'status' => 'success',
                'data' => [
                    'key' => 'value'
                ]
            ];
        }
    }
}
