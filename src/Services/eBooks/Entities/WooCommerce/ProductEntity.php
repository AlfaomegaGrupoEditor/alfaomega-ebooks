<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

interface ProductEntity
{
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
    public function get(int $postId): ?array;

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
    public function find(string $title, int $tagId): ?array;

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
    public function updateFormats(array $product): ?array;

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
    public function updateType(array $product, string $type = 'variable'): ?array;
}
