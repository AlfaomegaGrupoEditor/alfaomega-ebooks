<?php

namespace AlfaomegaEbooks\Http\Controllers;

use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

class EbooksQuickActionsController
{
    /**
     * Performs a mass update of metadata for a set of eBooks.
     *
     * This method takes an array of post IDs and a redirect URL as parameters. It attempts to refresh the eBook metadata
     * for each post ID in the array by calling the `refreshEbook` method on the eBooks service. The number of eBooks
     * successfully updated is then added to the redirect URL as a query parameter.
     *
     * If an exception occurs during the update process, the exception message is logged using the `error_log` function,
     * and 'fail' is added to the redirect URL as a query parameter.
     *
     * @param array $postIds An array of post IDs for which to update the eBook metadata.
     * @param string $redirectUrl The URL to which the user should be redirected after the update process is complete.
     * @return string The redirect URL with the result of the update process added as a query parameter.
     * @throws Exception If an error occurs during the update process.
     */
    public function quickUpdateMeta(int $postId): void
    {
        try {
            Service::make()->ebooks()
                ->refreshEbook()
                ->batch([$postId]);
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
    }

    /**
     * Performs a mass linking of products for a set of eBooks.
     *
     * This method takes an array of post IDs and a redirect URL as parameters. It attempts to link the products
     * for each post ID in the array by calling the `linkProduct` method on the WooCommerce service. The number of products
     * successfully linked is then added to the redirect URL as a query parameter.
     *
     * If an exception occurs during the linking process, the exception message is logged using the `error_log` function,
     * and 'fail' is added to the redirect URL as a query parameter.
     *
     * @param array $postIds An array of post IDs for which to link the products.
     * @param string $redirectUrl The URL to which the user should be redirected after the linking process is complete.
     * @return string The redirect URL with the result of the linking process added as a query parameter.
     * @throws Exception If an error occurs during the linking process.
     */
    public function quickLinkProduct(int $postId): void
    {
        try {
            Service::make()->wooCommerce()
                ->linkProduct()
                ->batch([$postId]);
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
    }

    /**
     * Performs a mass linking of eBooks for a set of products.
     *
     * This method takes an array of post IDs and a redirect URL as parameters. It attempts to link the eBooks
     * for each post ID in the array by calling the `linkEbook` method on the WooCommerce service. The number of eBooks
     * successfully linked is then added to the redirect URL as a query parameter.
     *
     * If an exception occurs during the linking process, the exception message is logged using the `error_log` function,
     * and 'fail' is added to the redirect URL as a query parameter.
     *
     * @param array $postIds An array of post IDs for which to link the eBooks.
     * @param string $redirectUrl The URL to which the user should be redirected after the linking process is complete.
     * @return string The redirect URL with the result of the linking process added as a query parameter.
     * @throws Exception If an error occurs during the linking process.
     */
    public function quickLinkEbook(int $postId): void
    {
        try {
            Service::make()->wooCommerce()
                ->linkEbook()
                ->batch([$postId]);
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
    }
}