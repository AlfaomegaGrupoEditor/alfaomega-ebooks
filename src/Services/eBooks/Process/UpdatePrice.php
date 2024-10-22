<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega\EbookPostEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

/**
 * The update product price process.
 */
class UpdatePrice extends LinkProduct implements ProcessContract
{
    /**
     * The calculation factor
     * @var string
     */
    protected string $factor;

    /**
     * The factor value
     * @var float
     */
    protected float $value;

    /**
     * To calculate the digital price based on the printed price.
     * @var int
     */
    protected int $digitalPrice = 80;

    /**
     * To calculate the digital+printed price based on the printed price.
     * @var int
     */
    protected int $comboPrice = 120;


    /**
     * The number of decimals to round the price.
     * @var int
     */
    protected int $decimals = 2;

    /**
     * Set the new product price.
     *
     * @param array $eBook: eBook attributes
     * @param bool $throwError: Whether to throw an error or not.
     * @param int|null $postId: eBook post ID.
     *
     * @return void
     * @throws \Exception
     */
    public function single(array $eBook, bool $throwError=false, int $postId=null): int
    {
        try {
            if (!empty($eBook['error'])) {
                throw new \Exception($eBook['error']);
            }

            $post = wc_get_product($postId);
            if (empty($post)) {
                throw new \Exception('Error getting the product.');
            }

            $eBook['id'] = $postId;
            if ($this->updateProduct) {
                $productId = Service::make()
                    ->wooCommerce()
                    ->product()
                    ->updatePrice($eBook);

                if (empty($productId)) {
                    throw new \Exception('Error linking the eBook with the product.');
                }
            }

            return $postId;
        } catch (\Exception $e) {
            if ($throwError) {
                throw $e;
            }
            return 0;
        }
    }

    /**
     * Sets the price calculator factor and its value.
     *
     * This method sets the calculation factor and its corresponding value for the update price process.
     *
     * @param string $factor The calculation factor.
     * @param mixed $value The value associated with the factor.
     * @return self Returns the instance of the UpdatePrice class.
     */
    public function setFactor(string $factor, float $value): self
    {
        $this->factor = $factor;
        $this->value = $value;
        $this->digitalPrice = intval($this->settings['alfaomega_ebooks_price'] ?? 80);
        $this->comboPrice = intval($this->settings['alfaomega_ebooks_printed_digital_price'] ?? 120);
        $this->decimals = defined('AO_STORE_CENTS') && AO_STORE_CENTS ? 2 : 0;
        return $this;
    }

    /**
     * Processes a batch of products synchronously or asynchronously.
     *
     * @param array $data An optional array of post IDs. If provided, the method will process only these eBooks.
     * @throws \Exception If the enqueuing of the refresh action fails.
     * @return array An array with the total number of products prices configured.
     */
    public function batch(array $data = [], bool $async = false): array
    {
        if (empty($data)) {
            return $this->chunk();
        }

        return $async
            ? $this->queueProcess($data)
            : $this->doProcess($data);
    }

    /**
     * Link the products to the ebooks synchronously.
     *
     * @param array $entities
     *
     * @return array|null
     * @throws \Exception
     */
    protected function doProcess(array $entities): ?array
    {
        $processed = [];
        foreach ($entities as $priceSetup) {
            $result = $this->single($priceSetup, postId: $priceSetup['id']);
            $processed[] = $result;
        }

        return $processed;
    }

    /**
     * Queue the process to update ebooks.
     *
     * @param array $entities
     *
     * @return array|null
     * @throws \Exception
     */
    protected function queueProcess(array $entities): ?array
    {
        $onQueue = [];
        foreach ($entities as $productId) {
            $priceSetup = $this->getPayload($productId);

            $result = as_enqueue_async_action(
                'alfaomega_ebooks_queue_setup_price',
                [$priceSetup, true, $productId]
            );
            if ($result !== 0) {
                $onQueue[] = $result;
            }
        }
        return $onQueue;
    }

    /**
     * Get by chunks the products id linked successfully to ebooks
     * and pass them to the batch method to process asynchronously
     * the update of the prices.
     *
     * @return array|null An array of data to process, or null if there is no more data to process.
     * @throws \Exception
     */
    protected function chunk(): ?array
    {
        global $wpdb;

        $onQueue = [];
        $limit = 10000;
        $countPerPage = $this->chunkSize;

        $page = 0;
        do {
            $countPerPage = min($limit, $countPerPage);

            // retrieves a list of published products of type variable
            $dataQuery = $wpdb->prepare("SELECT p.ID
                FROM {$wpdb->prefix}posts AS p
                INNER JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
                INNER JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                INNER JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
                WHERE p.post_type = 'product'
                AND p.post_status = 'publish'
                AND tt.taxonomy = 'product_type'
                AND t.name = 'variable'
                LIMIT %d, %d;", $page * $countPerPage, $countPerPage);

            $results = $wpdb->get_results($dataQuery, 'ARRAY_A');
            if (empty($results)) {
                break;
            }

            $products = array_column($results, 'ID');
            $onQueue = array_merge($onQueue, $this->batch($products, true));
            $page++;
        } while (count($results) === $this->chunkSize && count($onQueue) < $limit);


        return $onQueue;
    }

    /**
     * Calculate the new price based on the factor and value.
     *
     * @param float $price   The current price of the product.
     * @param int $pageCount Book's number of pages.
     *
     * @return float The new price of the product.
     * @throws \Exception
     */
    protected function calculatePrice(float $price, int $pageCount = 1): float
    {
        switch ($this->factor) {
            case 'page_count':
                if ($pageCount === 0) {
                    throw new \Exception('page_count can\'t 0');
                }
                return $pageCount + $this->value;

            case 'percent':
                $percentage = $price * abs($this->value) / 100;
                if ($this->value < 0 && $price < $percentage) {
                    throw new \Exception('The new price can\'t be negative');
                }
                return round($this->value > 0
                    ? $price + $percentage
                    : $price - $percentage, 2);

            case 'fixed':
                if ($this->value < 0 && $price < $this->value) {
                    throw new \Exception('The new price can\'t be negative');
                }
                return $price + $this->value;

            case 'price_update':
            default:
                return $price;
        }
    }

    /**
     * Get the payload for the given entity ID.
     *
     * This method takes an entity ID as input and returns the payload for that entity. The specific implementation of
     * this method depends on the class that implements this interface.
     *
     * @param int $entityId The entity ID.
     *
     * @return array|null The payload for the entity.
     */
    public function getPayload(int $entityId): ?array
    {
        try {
            $priceSetup = [
                'id'     => $entityId,
                'factor' => $this->factor,
                'value'  => $this->value,
            ];

            $product = wc_get_product($entityId);
            if (empty($product)) {
                throw new \Exception(esc_html__('The product is not available in the system.'));
            }
            $priceSetup['printed_isbn'] = $product->get_sku();
            $priceSetup['title'] = $product->get_name();

            $ebookIsbn = $product->get_meta('alfaomega_ebooks_ebook_isbn');
            if (empty($ebookIsbn)) {
                throw new \Exception(
                    esc_html__('The eBook ISBN is not available in book with SKU: ', 'alfaomega-ebooks') . $product->get_sku()
                );
            }
            $priceSetup['ebook_isbn'] = $ebookIsbn;

            $ebookPost = Service::make()->ebooks()->ebookPost()->search($ebookIsbn);
            if (empty($ebookPost)) {
                throw new \Exception(esc_html__('The eBook with ISBN: ', 'alfaomega-ebooks') . $ebookIsbn . esc_html__(' is not available in the system.'));
            }
            $priceSetup['page_count'] = $ebookPost['page_count'] ?? 0;

            if (empty($priceSetup['page_count']) && $this->factor === 'page_count') {
                throw new \Exception(throw new \Exception(
                    esc_html__('The page count is not available in book with ISBN: ', 'alfaomega-ebooks') . $product->get_sku()
                ));
            }
            $regularPrice = $product->get_regular_price();
            $salesPrice = $product->get_sale_price();
            if (empty($regularPrice)) {
                $backupPrices = $product->get_meta('_ao_price_backup');
                if (empty($backupPrices)) {
                    throw new \Exception(esc_html__('The regular price is not available in book with SKU: ', 'alfaomega-ebooks') . $product->get_sku());
                } else {
                    $backupPrices = json_decode($backupPrices, true);
                    $regularPrice = floatval($backupPrices['regular_price']);
                    $salesPrice = floatval($backupPrices['sale_price']);
                }
            }
            $priceSetup['current_regular_price'] = $regularPrice;
            $priceSetup['current_sales_price'] = $salesPrice;

            $newRegularPrice = $this->calculatePrice($regularPrice, intval($priceSetup['page_count']));
            if (empty($newRegularPrice) || $newRegularPrice < 0) {
                throw new \Exception(esc_html__('The new regular price can\'t be 0 or negative.', 'alfaomega-ebooks'));
            }
            $priceSetup['new_regular_price'] = $newRegularPrice;

            $newRegularDigitalPrice = round($newRegularPrice * $this->digitalPrice / 100, $this->decimals);
            if (empty($newRegularDigitalPrice) || $newRegularDigitalPrice < 0) {
                throw new \Exception(esc_html__('The new regular digital price can\'t be 0 or negative.', 'alfaomega-ebooks'));
            }
            $priceSetup['new_regular_digital_price'] = $newRegularDigitalPrice;

            $newRegularComboPrice = round($newRegularPrice * $this->comboPrice / 100, $this->decimals);
            if (empty($newRegularComboPrice) || $newRegularComboPrice < 0) {
                throw new \Exception(esc_html__('The new regular combo price can\'t be 0 or negative.', 'alfaomega-ebooks'));
            }
            $priceSetup['new_regular_combo_price'] = $newRegularComboPrice;

            if (!empty($salesPrice)) {
                $newSalePrice = $this->calculatePrice($salesPrice, intval($priceSetup['page_count']));
                if (empty($newSalePrice) || $newSalePrice < 0) {
                    throw new \Exception(esc_html__('The new sale price can\'t be 0 or negative.', 'alfaomega-ebooks'));
                }
                $priceSetup['new_sales_price'] = $newSalePrice;

                $newSaleDigitalPrice = round($newSalePrice * $this->digitalPrice / 100, $this->decimals);
                if (empty($newSaleDigitalPrice) || $newSaleDigitalPrice < 0) {
                    throw new \Exception(esc_html__('The new sale digital price can\'t be 0 or negative.', 'alfaomega-ebooks'));
                }
                $priceSetup['new_sales_digital_price'] = $newSaleDigitalPrice;

                $newSaleComboPrice = round($newSalePrice * $this->comboPrice / 100, $this->decimals);
                if (empty($newSaleComboPrice) || $newSaleComboPrice < 0) {
                    throw new \Exception(esc_html__('The new sale combo price can\'t be 0 or negative.', 'alfaomega-ebooks'));
                }
                $priceSetup['new_sales_combo_price'] = $newSaleComboPrice;
            }
        } catch (\Exception $e) {
            $priceSetup['error'] = $e->getMessage();
            Service::make()->helper()->log($e->getMessage());
        }

        return $priceSetup;
    }
}
