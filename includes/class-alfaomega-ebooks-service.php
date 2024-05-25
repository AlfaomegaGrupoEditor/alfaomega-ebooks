<?php

/**
 * This class defines the general plugin settings.
 *
 * @since      1.0.0
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/includes
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
if( ! class_exists( 'Alfaomega_Ebooks_Service' )){
    class Alfaomega_Ebooks_Service{

        protected Alfaomega_Ebooks_Api $api;
        protected array $settings = [];

        public function __construct()
        {
            $this->getSettings();
            $this->api = new Alfaomega_Ebooks_Api($this->settings);
        }

        public function getSettings(): void
        {
            $this->settings = array_merge(
                (array) get_option('alfaomega_ebooks_general_options'),
                (array) get_option('alfaomega_ebooks_platform_options'),
                (array) get_option('alfaomega_ebooks_api_options')
            );
        }

        public function importEbooks(): array
        {
            // TODO HERE
            // get the latest ebook
            // search ebooks with pagination
            // create or update the ebook
            // search the ebook related product
            // update the product variant
            $response = $this->api->get('/book/all');
            if ($response['response']['code'] !== 200) {
                throw new Exception($response['response']['message']);
            }
            $data = json_decode($response['body'], true)['data'];

            // pull from panel all new ebooks
            // add each new ebook
            // link each related product

            return [
                'imported' => count($data)
            ];
        }

        public function refreshEbooks(): array
        {
            // pull from panel the information of each ebook already imported
            // update or create the ebook information
            // refresh the link of each related product

            return [
                'refreshed' => rand(0, 2)
            ];
        }

        public function linkProducts(): array
        {
            // pull the eBook information of each linked product
            // update or create the eBook information
            // refresh de link of each product

            return [
                'linked' => rand(0, 2)
            ];
        }
    }
}
