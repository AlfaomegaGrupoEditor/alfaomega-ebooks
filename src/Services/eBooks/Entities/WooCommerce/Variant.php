<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

/**
 * Class Variant
 *
 * The Variant class extends the WooAbstractEntity class and represents a variant entity in WooCommerce.
 * It provides methods to update a product's variants and to get data for a specific variant format.
 */
class Variant extends WooAbstractEntity
{
    /**
     * Update the product's variants.
     *
     * This method is used to update the variants of a WooCommerce product.
     * It first gets the existing variants of the product.
     * Then it iterates over the variants and updates the prices for the 'impreso' format.
     * It also stores the IDs of the variants by format.
     * Then it iterates over the possible formats and updates or creates the variant for each format.
     * Finally, it updates the product's default attributes and returns the updated product.
     *
     * @param array $product The product to update.
     * @param array $prices The prices to set for the variants.
     * @param array $ebook The ebook related to the product.
     * @return array|null The updated product, or null if the product update failed.
     * @throws Exception If the variant creation failed.
     */
    public function update(array $product, array $prices, array $ebook): ?array
    {
        $variations = (array) $this->client
            ->get("products/{$product['id']}/variations");

        $variationIds = [];
        if (!empty($variations)) {
            foreach ($variations as $variation) {
                $format = '';
                foreach ($variation->attributes as $attribute) {
                    if ($attribute->name === 'Formato') {
                        $format = match ($attribute->option) {
                            'Impreso' => 'impreso',
                            'Digital' => 'digital',
                            'Impreso + Digital' => 'impreso-digital',
                            default => null,
                        };
                        if ($format === 'impreso') {
                            $prices = [
                                'regular_price' => $variation->regular_price,
                                'sale_price' => $variation->sale_price,
                            ];
                        }
                    }
                }
                if (empty($format)) {
                    return $product;
                }

                $variationIds[$format] = $variation->id;
            }

            if (empty($prices['regular_price'])) {
                return $product;
            }
        }

        $formatOptions = ['impreso', 'digital', 'impreso-digital'];
        foreach ($formatOptions as $format) {
            $data = $this->getData($product, $format, $prices, $ebook['id'], $ebook['isbn']);
            try {
                $variation = empty($variationIds[$format])
                    ? $this->client->post("products/{$product['id']}/variations", $data)
                    : $this->client->put("products/{$product['id']}/variations/{$variationIds[$format]}", $data);
            } catch (Exception $e) {
                $variation = null;
                Service::make()->log("creating variation for format $format failed");
                Service::make()->log($e->getMessage(), $e->getTraceAsString());
            }

            if (empty($variation)) {
                throw new Exception("Variation creation failed");
            }
        }

        $product = (array) $this->client
            ->put("products/{$product['id']}", [
                'default_attributes' => [
                    [
                        'id'     => $this->settings['alfaomega_ebooks_format_attr_id'],
                        'name'   => 'Formato',
                        'option' => 'Impreso + Digital',
                    ],
                ],
            ]);

        if (empty($product)) {
            return null;
        }

        return $product;
    }

    /**
     * Get the stock quantity for a product.
     *
     * This method is used to get the stock quantity for a product.
     * It takes the stock quantity of the product as a parameter.
     * It returns an array with the stock quantity for the 'impreso' and 'impreso_digital' formats.
     *
     * @param int $stock The stock quantity of the product.
     * @return array The stock quantity for the 'impreso' and 'impreso_digital' formats.
     */
    public function getStockQuantity(int $stock): array
    {
        $infiniteStock = defined('AO_STORE_INFINITE_STOCK') && AO_STORE_INFINITE_STOCK;
        if ($infiniteStock) {
            return [
                'impreso' => $stock,
                'impreso_digital' => $stock,
            ];
        }

        $stockQuantity = [
            'impreso' => 0,
            'impreso_digital' => 0,
        ];

        if ($stock > 0) {
            $splitStock = $stock / 2;
            if ($stock % 2 === 0) {
                $stockQuantity['impreso'] = $splitStock;
                $stockQuantity['impreso_digital'] = $splitStock;
            } else {
                $stockQuantity['impreso'] = ceil($splitStock);
                $stockQuantity['impreso_digital'] = floor($splitStock);
            }
        }

        return $stockQuantity;
    }


    /**
     * Get data for a specific variant format.
     *
     * This method is used to get the data for a specific variant format.
     * It returns an array with the data for the variant, depending on the format.
     *
     * @param array $product The product to get the variant data for.
     * @param string $format The format to get the variant data for.
     * @param array $prices The prices to set for the variant.
     * @param int $ebookId The ID of the ebook related to the product.
     * @param string $ebookIsbn The ISBN of the ebook related to the product.
     * @return array The data for the variant.
     */
    public function getData(array $product, string $format, array $prices, int $ebookId, string $ebookIsbn): array
    {
        $uploads = wp_get_upload_dir();
        $ebooksDir = $uploads['baseurl'] . '/woocommerce_uploads/alfaomega_ebooks/';
        $stockQuantity = $this->getStockQuantity($product['stock_quantity'] ?? 0);

        return match ($format) {
            'impreso' => [
                'description'     => 'Libro impreso',
                'sku'             => $product['sku'] . ' (impreso)',
                'regular_price'   => $prices['regular_price'],
                'status'          => 'publish',
                'virtual'         => false,
                'downloadable'    => false,
                'manage_stock'    => true,
                'stock_quantity'  => $stockQuantity['impreso'],
                'stock_status'    => $stockQuantity['impreso'] === 0 ? 'outofstock' : 'instock',
                'weight'          => $product['weight'],
                'dimensions'      => $product['dimensions'],
                'shipping_class'  => $product['shipping_class'],
                'attributes'      => [[
                    'id' => $this->settings['alfaomega_ebooks_format_attr_id'],
                    'option' => $format,
                ]],
            ],
            'digital' => [
                'description'     => 'Libro digital para lectura en línea y descarga del PDF con DRM',
                'sku'             => $ebookIsbn . ' (digital)',
                'regular_price'   => number_format($prices['regular_price']
                                                   * ($this->settings['alfaomega_ebooks_price'] / 100), 0),
                'status'          => 'publish',
                'virtual'         => true,
                'downloadable'    => true,
                'downloads'       => [
                    [ 'name' => 'PDF', 'file' => $ebooksDir . $ebookId ]
                ],
                'download_limit'  => -1,
                'download_expiry' => 30,
                'manage_stock'    => false,
                'attributes'      => [
                    [
                        'id'     => $this->settings['alfaomega_ebooks_format_attr_id'],
                        'option' => $format,
                    ],
                ],
            ],
            'impreso-digital' => [
                'description'     => 'Libro impreso y libro digital para lectura en línea y descarga del PDF con DRM',
                'sku'             => $product['sku'] . ' (impreso), ' . $ebookIsbn . ' (digital)',
                'regular_price'   => number_format($prices['regular_price'] * ($this->settings['alfaomega_ebooks_printed_digital_price'] / 100), 0),
                'status'          => 'publish',
                'virtual'         => true,
                'downloadable'    => true,
                'downloads'       => [
                    [ 'name' => 'PDF', 'file' => $ebooksDir . $ebookId ]
                ],
                'download_limit'  => -1,
                'download_expiry' => 30,
                'manage_stock'    => true,
                'stock_quantity'  => $stockQuantity['impreso_digital'],
                'stock_status'    => $stockQuantity['impreso_digital'] === 0 ? 'outofstock' : 'instock',
                'weight'          => $product['weight'],
                'dimensions'      => $product['dimensions'],
                'shipping_class'  => $product['shipping_class'],
                'attributes'      => [[
                    'id' => $this->settings['alfaomega_ebooks_format_attr_id'],
                    'option' => $format,
                ]],
            ],
        };
    }

    /**
     * List the variants of a product.
     *
     * This method is used to list the variants of a product in WooCommerce.
     * It returns an array with the variants of the product.
     *
     * @param int $productId The ID of the product to list the variants for.
     * @return array The variants of the product.
     */
    public function list(int $productId): array
    {
        $variations = (array) $this->client
            ->get("products/{$productId}/variations");

        if (empty($variations)) {
            return [];
        }

        return (array) $variations;
    }

    /**
     * Delete a variant of a product.
     *
     * This method is used to delete a variant of a product in WooCommerce.
     * It takes the product ID and the variant ID as parameters.
     * It returns an array with the result of the deletion.
     *
     * @param int $productId The ID of the product to delete the variant from.
     * @param int $variationId The ID of the variant to delete.
     * @return array The result of the deletion.
     */
    public function delete(int $productId, int $variationId): array
    {
        return (array) $this->client
            ->delete("products/{$productId}/variations/{$variationId}", ['force' => true]);
    }
}
