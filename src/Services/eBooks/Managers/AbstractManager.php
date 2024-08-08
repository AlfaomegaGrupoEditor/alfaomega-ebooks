<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use AlfaomegaEbooks\Services\Alfaomega\Api;

class AbstractManager
{
    /**
     * AbstractManager constructor.
     *
     * @param Api $api
     * @param array $settings
     */
    public function __construct(
        protected Api $api,
        protected array $settings
    ) {}

    /**
     * Update the settings.
     *
     * This method is used to update the settings of the AbstractManager.
     * It accepts an array of settings, assigns it to the $settings property, and returns the instance of the class.
     *
     * @param array $settings The new settings.
     * @return self The instance of the AbstractManager class.
     */
    public function updateSettings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Get the WooCommerce constants.
     *
     * This method is used to get the WooCommerce constants from the wp-config.php file.
     * It reads the content of the wp-config.php file, extracts the WooCommerce constants, and returns them as an array.
     *
     * @return array|null The WooCommerce constants.
     */
    public function getWoocommerceConstants(): ?array
    {
        $rootFolder = ABSPATH;
        $content = @file_get_contents( "$rootFolder/wp-config.php" );
        if( ! $content ) {
            return null;
        }

        $params = [
            'WOOCOMMERCE_API_KEY'    => "/define.+?'WOOCOMMERCE_API_KEY'.+?'(.*?)'.+/",
            'WOOCOMMERCE_API_SECRET' => "/define.+?'WOOCOMMERCE_API_SECRET'.+?'(.*?)'.+/",
            'WCPAY_DEV_MODE'         => "/define.+?'WCPAY_DEV_MODE'.+?'(.*?)'.+/",
        ];

        $return = [];
        foreach( $params as $key => $value ) {
            $found = preg_match_all( $value, $content, $result );
            $return[ $key ] = $found ? $result[ 1 ][ 0 ] : false;
        }

        return $return;
    }
}
