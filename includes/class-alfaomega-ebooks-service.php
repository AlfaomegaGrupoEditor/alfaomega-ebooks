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
            return [
                'imported' => rand(0, 2)
            ];
        }

        public function refreshEbooks(): array
        {
            return [
                'refreshed' => rand(0, 2)
            ];
        }

        public function linkProducts(): array
        {
            return [
                'linked' => rand(0, 2)
            ];
        }
    }
}
