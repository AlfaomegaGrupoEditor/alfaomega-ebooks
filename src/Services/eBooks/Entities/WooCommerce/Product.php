<?php
namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\ProductEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Automattic\WooCommerce\Client;
use WC_Product;

class Product extends WooAbstractEntity implements ProductEntity
{
    /**
     * Product constructor.
     *
     * @param Client $client
     * @param array $settings
     * @param Variant $variant
     */
    public function __construct(
        Client $client,
        array $settings,
        Protected Variant $variant
    ) {
        parent::__construct($client, $settings);
    }

    /**
     * Retrieves a product from WooCommerce.
     *
     * This method retrieves a product from WooCommerce by searching for the product with the specified tag ID.
     * If the product is not found, it returns null.
     *
     * @param int $postId The tag ID to search for.
     *
     * @return array|null Returns an associative array containing the product data if the product is found, or null if the product is not found.
     */
    public function get(int $postId): ?array
    {
        $product = (array) $this->client->get("products/$postId");

        return !empty($product) ? $product : null;
    }

    /**
     * Finds a product in WooCommerce.
     *
     * This method finds a product in WooCommerce by searching for the product with the specified title.
     * If the product is found, it updates the product tags with the specified tag ID.
     *
     * @param string $title The title to search for.
     * @param int $tagId The tag ID to update the product with.
     *
     * @return array|null Returns an associative array containing the product data if the product is found, or null if the product is not found.
     */
    public function find(string $title, int $tagId): ?array
    {
        $products = (array) $this->client->get("products", [
            'search' => $title,
        ]);

        if (count($products) === 1) {
            $product = $products[0];
            $this->client->put("products/{$product->id}", [
                'tags' => [['id' => $tagId]],
            ]);

            return (array) $product;
        }

        return null;
    }

    /**
     * Updates the product formats in WooCommerce.
     *
     * This method updates the product formats in WooCommerce by adding the specified format to the product attributes.
     * If the format is already in the product attributes, it returns the product data.
     *
     * @param array $product The product data to update.
     *
     * @return array|null Returns an associative array containing the updated product data if the product formats are updated, or null if the product formats are not updated.
     */
    public function updateFormatsAttr(array $product): ?array
    {
        $formats = [
            'id'        => $this->settings['alfaomega_ebooks_format_attr_id'],
            'name'      => 'Formato',
            'slug'      => 'pa_book-format',
            'position'  => 0,
            'visible'   => false,
            'variation' => true,
            'options'   => [
                'Impreso',
                'Digital',
                'Impreso + Digital',
            ],
        ];

        return Service::make()
            ->wooCommerce()
            ->format()
            ->setValue($product, $formats);
    }

    /**
     * Updates the ebook product attribute in WooCommerce.
     * @param array $product
     *
     * @return array|null
     */
    public function updateEbookAttr(array $product): ?array
    {
        $ebook = [
            'id'        => $this->settings['alfaomega_ebooks_ebook_attr_id'],
            'name'      => 'eBook',
            'slug'      => 'pa_ebook',
            'position'  => 1,
            'visible'   => false,
            'variation' => false,
            'options'   => ['Si'],
        ];

        return Service::make()
            ->wooCommerce()
            ->ebook()
            ->setValue($product, $ebook);
    }

    /**
     * Updates the product type in WooCommerce.
     *
     * This method updates the product type in WooCommerce by changing the product type to the specified type.
     * If the product type is already the specified type, it returns the product data.
     *
     * @param array $product The product data to update.
     * @param string $type The product type to update to. Default is 'variable'.
     *
     * @return array|null Returns an associative array containing the updated product data if the product type is updated, or null if the product type is not updated.
     */
    public function updateType(array $product, string $type = 'variable'): ?array
    {
        if ($product['type'] !== $type) {
            $regularPrice = $product['regular_price'];
            $salePrice = $product['sale_price'];
            if ($type === 'variable') {
                $product = (array) $this->client->put("products/{$product['id']}", [
                    'type' => $type,
                ]);
            } else {
                $product = (array) $this->client->put("products/{$product['id']}", [
                    'type'          => $type,
                    'regular_price' => $regularPrice,
                    'sale_price'    => $salePrice,
                ]);
            }

            if (empty($product)) {
                return null;
            }

            $product['regular_price'] = $regularPrice;
            $product['sale_price'] = $salePrice;

            return $product;
        }

        return $product;
    }

    /**
     * Get the variant entity.
     *
     * @return Variant
     */
    public function variant(): Variant
    {
        return $this->variant;
    }
}
