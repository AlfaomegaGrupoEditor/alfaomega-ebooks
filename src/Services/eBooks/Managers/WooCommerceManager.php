<?php

namespace AlfaomegaEbooks\Services\Managers;

use AlfaomegaEbooks\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\Attribute;
use AlfaomegaEbooks\Services\Process\LinkProduct;
use Automattic\WooCommerce\Client;

class WooCommerceManager extends AbstractManager
{
    const bool WOOCOMMERCE_WP_API = true;
    const string WOOCOMMERCE_VERSION = 'wc/v3';
    const bool WOOCOMMERCE_VERIFY_SSL = false;
    const int WOOCOMMERCE_TIMEOUT = 180; // 3 minutes

    /**
     * @var Client|null $woocommerce
     * This protected property holds an instance of the WooCommerce Client class.
     * It is used to interact with the WooCommerce API.
     * It is nullable, meaning it can also hold a null value.
     */
    protected ?Client $client = null;

    /**
     * @var Attribute $format
     * This protected property holds an instance of the Attribute class.
     * It is used to interact with the WooCommerce product attributes.
     */
    protected Attribute $format;

    /**
     * @var LinkProduct $linkProduct
     * This protected property holds an instance of the LinkProduct class.
     * It is used to link products to the WooCommerce store.
     */
    protected LinkProduct $linkProduct;

    /**
     * Initialize the WooCommerce client.
     *
     * @return self
     */
    public function __construct(Api $api, array $settings) {
        parent::__construct($api, $settings);

        $this->format = new Attribute($this->client);
        //$this->linkProduct = new LinkProduct($this->client);
    }

    /**
     * Initialize the WooCommerce client.
     *
     * @return self
     */
    public function init(): self {
        $this->client = new Client(
            get_site_url(),
            WOOCOMMERCE_API_KEY,
            WOOCOMMERCE_API_SECRET,
            [
                'wp_api'           => self::WOOCOMMERCE_WP_API,
                'version'          => self::WOOCOMMERCE_VERSION,
                'verify_ssl'       => self::WOOCOMMERCE_VERIFY_SSL,
                'timeout'          => self::WOOCOMMERCE_TIMEOUT,
            ]
        );

        return $this;
    }

    /**
     * Get the format attribute.
     *
     * This method is used to get the format attribute of the WooCommerce product.
     * The format attribute is an instance of the Attribute class and is used to interact with the WooCommerce product attributes.
     *
     * @return Attribute The format attribute of the WooCommerce product.
     */
    public function format(): Attribute
    {
        return $this->format;
    }
}
