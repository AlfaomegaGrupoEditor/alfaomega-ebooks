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
     * @param array $value|null Value to set.
     *
     * @return array|null Returns an associative array containing the updated product data if the product formats are updated, or null if the product formats are not updated.
     */
    public function updateFormatsAttr(array $product, ?array $value=null): ?array
    {
        $formats = [
            'id'        => $this->settings['alfaomega_ebooks_format_attr_id'],
            'name'      => 'Formato',
            'slug'      => 'pa_book-format',
            'position'  => 0,
            'visible'   => false,
            'variation' => true,
            'options'   => is_null($value)
                ? [
                    'Impreso',
                    'Digital',
                    'Impreso + Digital',
                ]
                : $value,
        ];

        return Service::make()
            ->wooCommerce()
            ->format()
            ->setValue($product, $formats);
    }

    /**
     * Updates the ebook product attribute in WooCommerce.
     *
     * @param array $product
     * @param array $value|null Value to set.
     *
     * @return array|null
     */
    public function updateEbookAttr(array $product, ?array $value=null): ?array
    {
        $ebook = [
            'id'        => $this->settings['alfaomega_ebooks_ebook_attr_id'],
            'name'      => 'eBook',
            'slug'      => 'pa_ebook',
            'position'  => 1,
            'visible'   => false,
            'variation' => false,
            'options'   => is_null($value) ? ['Si'] : $value,
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
            if ($type === 'variable') {
                $product = (array) $this->client->put("products/{$product['id']}", [
                    'type' => $type,
                ]);
            } else {
                if (!empty($product['regular_price'])) {
                    $product = (array) $this->client->put("products/{$product['id']}", [
                        'type'          => $type,
                        'regular_price' => $product['regular_price'],
                        'sale_price'    => $product['sale_price'] ?? '',
                    ]);
                }
            }

            if (empty($product)) {
                return null;
            }

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

    /**
     * Get the product info.
     *
     * @return array
     */
    public function getInfo(): array
    {
        global $wpdb;

        $dataQuery = "SELECT t.name AS product_type, COUNT(p.ID) AS total_products
            FROM {$wpdb->prefix}posts AS p
            INNER JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND tt.taxonomy = 'product_type'
            GROUP BY t.name;";

        $results = $wpdb->get_results($dataQuery, 'ARRAY_A');
        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[$row['product_type']] = intval($row['total_products']);
        }

        return [
            'catalog'  => ($formattedResults['simple'] ?? 0) + ($formattedResults['variable'] ?? 0),
            'unlinked' => $formattedResults['simple'] ?? 0,
            'linked'   => $formattedResults['variable'] ?? 0,
        ];
    }

    /**
     * Update the product price.
     *
     * @param array $data
     *
     * @return int
     */
    public function updatePrice(array $data): int
    {
        $product = wc_get_product($data['id']);
        if (empty($product)) {
            return 0;
        }

        if ($product->is_type('variable')) {
            update_post_meta($data['id'], '_price', $data['new_regular_price']);
            update_post_meta($data['id'], '_regular_price', $data['new_regular_price']);
            if ($data['new_sales_price']) {
                update_post_meta($data['id'], '_sale_price', $data['new_sales_price']);
            }

            // Update variations
            $variations = $product->get_children();
            foreach ($variations as $variation_id) {
                $newPrice = $this->getVariationPrice($variation_id, $data);

                update_post_meta($variation_id, '_price', $newPrice['regular']);
                update_post_meta($variation_id, '_regular_price', $newPrice['regular']);
                if ($newPrice['sales']) {
                    update_post_meta($variation_id, '_sale_price', $newPrice['sales']);
                }
            }

            wc_delete_product_transients($data['id']);
        }

        return $data['id'];
    }

    /**
     * Get the variation new price.
     *
     * @param int $variationId
     * @param array $data
     *
     * @return array
     */
    protected function getVariationPrice(int $variationId, array $data): array
    {
        $variation = wc_get_product($variationId);
        switch ($variation->get_attribute('pa_book-format')) {
            case 'digital':
                $newPrice = [
                    'regular' => $data['new_regular_digital_price'],
                    'sales'    => $data['new_sales_digital_price'],
                ];
                break;
            case 'impreso-digital':
                $newPrice = [
                    'regular' => $data['new_regular_combo_price'],
                    'sales'    => $data['new_sales_combo_price'],
                ];
                break;
            case 'impreso':
            default:
                $newPrice = [
                    'regular' => $data['new_regular_price'],
                    'sales'    => $data['new_sales_price'],
                ];
                break;
        }

        return $newPrice;
    }
}
