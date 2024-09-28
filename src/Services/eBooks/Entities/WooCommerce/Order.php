<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;
use WC_Order;

/**
 * Class Order
 *
 * The Order class extends the WooAbstractEntity class and represents an order entity in WooCommerce.
 * It provides a method to complete an order.
 */
class Order extends WooAbstractEntity
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
    public function get(int $orderId): ?array
    {
        $order = (array) $this->client->get("orders/$orderId");

        return !empty($order) ? $order : null;
    }

    /**
     * Completes an order.
     * This method completes an order in WooCommerce by updating the order status to 'completed'.
     * It also updates the order status in the database and returns the updated order data.
     *
     * @param int $orderId The ID of the order to complete.
     *
     * @return array The updated order data.
     * @throws \Exception
     */
    public function onComplete(int $orderId): array
    {
        $order = wc_get_order( $orderId );
        $customer_id = $order->get_customer_id();
        // FIXME: This is a temporary fix to avoid the error when the customer is not logged in.
        if (empty($customer_id)) {
            $customer_id = get_current_user_id();
        }

        if (empty($customer_id) || $order->get_status() !== 'completed') {
            return [];
        }

        $result = [];
        $items = $order->get_items();
        foreach ($items as $item) {
            $product = $item->get_product();
            if ($product->get_type() === 'variation') {
                $variation = wc_get_product($item->get_variation_id());
                if (empty($variation)) {
                    continue;
                }
                $attributes = $variation->get_attributes();
                $format = $attributes['pa_book-format'] ?? '';
                if (in_array($format, ['digital', 'impreso-digital'])) {
                    foreach ($variation->get_downloads() as $download) {
                        $filePathArray = explode('/', trim($download->get_file(), '/'));
                        $ebookId = end($filePathArray);
                        if (empty($ebookId) || empty(get_post($ebookId))) {
                            continue;
                        }

                        $result[] = Service::make()
                            ->ebooks()
                            ->accessPost()
                            ->updateOrCreate(null, [
                                'ebook_id' => $ebookId,
                                'user_id'  => $customer_id,
                                'access'   => [
                                    'type'     => 'purchase',
                                    'order_id' => $orderId,
                                ],
                            ]);
                    }
                }
            }
        }

        Service::make()->ebooks()
            ->accessPost()
            ->clearCustomerCache();
        return $result;
    }
}
