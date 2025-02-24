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
    public function quickUpdateMeta(int $postId, string $redirectUrl): string
    {
        try {
            $result = Service::make()
                ->ebooks()
                ->refreshEbook()
                ->batch([$postId]);

            $eBooks = count($result);
            if ($eBooks === 0) {
                throw new Exception("Can't update specified ebook, please add the eBook ISBN to the product.");
            }

            $redirectUrl = add_query_arg('updated-meta', $eBooks, $redirectUrl);
        } catch (Exception $exception) {
            Service::make()->log($exception->getMessage());
            $redirectUrl = add_query_arg('updated-meta', 'fail', $redirectUrl);
        }

        return $redirectUrl;
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
    public function quickFindProduct(int $postId, string $redirectUrl): string
    {
        try {
            $product = Service::make()
                ->wooCommerce()
                ->linkEbook()
                ->product($postId);

            if (empty($product)) {
                throw new Exception("Can't find the related ebook, please add the eBook ISBN to the product.");
            }

            $parsed_url = parse_url($redirectUrl);
            $redirectUrl = $parsed_url['scheme'] . '://' . $parsed_url['host'] . '/wp-admin/post.php';
            $redirectUrl = add_query_arg('post', $product->get_id(), $redirectUrl);
            $redirectUrl = add_query_arg('action', 'edit', $redirectUrl);
        } catch (Exception $exception) {
            Service::make()->log($exception->getMessage());
            $redirectUrl = add_query_arg('find-product', 'fail', $redirectUrl);
        }

        return $redirectUrl;
    }

    /**
     * Link an ebook to a product.
     * @param int $postId
     * @param string $redirectUrl
     *
     * @return string
     */
    public function quickLinkEbook(int $postId, string $redirectUrl): string
    {
        try {
            $result = Service::make()
                ->wooCommerce()
                ->linkEbook()
                ->batch([$postId]);

            $links = count($result);
            if ($links === 0) {
                throw new Exception("Can't find the related ebook, please add the eBook ISBN to the product.");
            }

            $redirectUrl = add_query_arg('link-ebook', $links, $redirectUrl);
        } catch (Exception $exception) {
            Service::make()->log($exception->getMessage());
            $redirectUrl = add_query_arg('link-ebook', 'fail', $redirectUrl);
        }
        return $redirectUrl;
    }

    /**
     * Link an ebook to a product.
     * @param int $postId
     * @param string $redirectUrl
     *
     * @return string
     */
    public function quickUnlinkEbook(int $postId, string $redirectUrl): string
    {
        try {
            $unlinked = Service::make()
                ->wooCommerce()
                ->linkEbook()
                ->remove($postId);

            if ($unlinked === 0) {
                throw new Exception("Can't remove the link to the ebook.");
            }

            $redirectUrl = add_query_arg('unlink-ebook', $unlinked, $redirectUrl);
        } catch (Exception $exception) {
            Service::make()->log($exception->getMessage());
            $redirectUrl = add_query_arg('unlink-ebook', 'fail', $redirectUrl);
        }
        return $redirectUrl;
    }
}
