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

        public function importEbooks(): array
        {
            // pull from panel all new ebooks
            // add each new ebook
            // link each related product

            return [
                'imported' => rand(0, 2)
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
