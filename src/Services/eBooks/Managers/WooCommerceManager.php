<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\Attribute;
use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\Product;
use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\Tag;
use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\Variant;
use AlfaomegaEbooks\Services\eBooks\Process\LinkEbook;
use AlfaomegaEbooks\Services\eBooks\Process\LinkProduct;
use Automattic\WooCommerce\Client;
use \Exception;

class WooCommerceManager extends AbstractManager
{
    const WOOCOMMERCE_WP_API = true;
    const WOOCOMMERCE_VERSION = 'wc/v3';
    const WOOCOMMERCE_VERIFY_SSL = false;
    const WOOCOMMERCE_TIMEOUT = 180; // 3 minutes
    protected string $WOOCOMMERCE_API_KEY;
    protected string $WOOCOMMERCE_API_SECRET;

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
     * @var Attribute $ebook
     * This protected property holds an instance of the Attribute class.
     * It is used to interact with the WooCommerce product attributes.
     */
    protected Attribute $ebook;

    /**
     * @var Tag $tag
     * This protected property holds an instance of the Tag class.
     * It is used to interact with the WooCommerce product tags.
     */
    protected Tag $tag;

    /**
     * @var LinkProduct $linkProduct
     * This protected property holds an instance of the LinkProduct class.
     * It is used to link the product with the ebook.
     */
    protected LinkProduct $linkProduct;

    /**
     * @var LinkEbook $linkEbook
     * This protected property holds an instance of the LinkEbook class.
     * It is used to link the ebook with the product.
     */
    protected LinkEbook $linkEbook;

    /**
     * @var Product $product
     * This protected property holds an instance of the Product class.
     * It is used to interact with the WooCommerce product.
     */
    protected Product $product;

    /**
     * Initialize the WooCommerce client.
     * @param \AlfaomegaEbooks\Services\Alfaomega\Api $api
     * @param array $settings
     *
     * @throws Exception
     */
    public function __construct(Api $api, array $settings)
    {
        parent::__construct($api, $settings);
        $woocommerceCredentials = $this->getWoocommerceConstants();
        if (empty($woocommerceCredentials)) {
            throw new Exception('WooCommerce credentials are not set.');
        }

        $this->init($woocommerceCredentials);

        $this->format = new Attribute($this->client, $settings);
        $this->ebook = new Attribute($this->client, $settings);
        $this->tag = new Tag($this->client, $settings);
        $this->product = new Product(
            $this->client,
            $settings,
            new Variant($this->client, $settings)
        );
        $this->linkProduct = new LinkProduct($settings, $this->product);
        $this->linkEbook = new LinkEbook($settings, $this->product);
    }

    /**
     * Initialize the WooCommerce client.
     *
     * @param array $credentials
     *
     * @return self
     */
    public function init(array $credentials): self {
        $this->client = new Client(
            get_site_url(scheme: 'https'),
            $credentials['WOOCOMMERCE_API_KEY'],
            $credentials['WOOCOMMERCE_API_SECRET'],
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

    /**
     * Get the ebook attribute.
     *
     * This method is used to get the ebook attribute of the WooCommerce product.
     * The format attribute is an instance of the Attribute class and is used to interact with the WooCommerce product attributes.
     *
     * @return Attribute The ebook attribute of the WooCommerce product.
     */
    public function ebook(): Attribute
    {
        return $this->ebook;
    }

    /**
     * Get the tag attribute.
     * This method is used to get the tag attribute of the WooCommerce product.
     * The tag attribute is an instance of the Tag class and is used to interact with the WooCommerce product tags.
     *
     * @return Tag The tag attribute of the WooCommerce product.
     */
    public function tag(): Tag
    {
        return $this->tag;
    }

    /**
     * Get the link product process.
     * This method is used to get the instance of the LinkProduct class.
     * The LinkProduct class is used to link the product with the ebook.
     *
     * @return LinkProduct The instance of the LinkProduct class.
     */
    public function linkProduct(): LinkProduct
    {
        return $this->linkProduct;
    }

    /**
     * Get the link ebook process.
     * This method is used to get the instance of the LinkEbook class.
     * The LinkEbook class is used to link the ebook with the product.
     *
     * @return LinkEbook The instance of the LinkEbook class.
     */
    public function linkEbook(): LinkEbook
    {
        return $this->linkEbook;
    }

    /**
     * Get the downloads for a specific customer.
     *
     * This method is used to get the downloads for a specific customer from the WooCommerce API.
     * It takes a customer object and an optional download key as parameters.
     * The customer object should be an instance of the stdClass class and should contain the ID of the customer.
     * The download key is a string and is used to filter the downloads by download ID.
     * If the download key is not provided, the method returns all downloads for the customer.
     * The method returns an array with the downloads, or null if there are no downloads for the customer.
     *
     * @param int $customer The customer to get the downloads for.
     * @param string $key The download key to filter the downloads by.
     * @return array|null The downloads for the customer, or null if there are no downloads.
     */
    public function getCustomerDownloads(int $customerId, string $key=''): ?array
    {
        return (array) $this->client
            ->get("customers/$customerId/downloads", [
                'download_id' => $key,
            ]);
    }
}
