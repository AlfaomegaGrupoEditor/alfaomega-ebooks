<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\ProductEntity;
use AlfaomegaEbooks\Services\eBooks\Service;

/**
 * Link ebooks process.
 */
class LinkEbook extends AbstractProcess implements ProcessContract
{
    /**
     * Initialize the process.
     *
     * @param array $settings       The settings.
     * @param ProductEntity $entity The entity.
     *
     * @throws \Exception
     */
    public function __construct(array $settings,
                                protected ProductEntity $entity,
                                protected bool $forceEbook = true)
    {
        parent::__construct($settings);
    }

    /**
     * Gather the related eBook information for each specified products. Also,
     * call the async or sync methods to link the product to the eBook.
     *
     * @param array $data Array of products id.
     * @param bool $async
     *
     * @return array|null
     * @throws \Exception
     */
    public function batch(array $data = [], bool $async = false): ?array
    {
        if (empty($data)) {
            $this->forceEbook = false;
            return $this->chunk();
        }

        $products = []; // products to be updated
        $isbns = [];    // eBooks to be imported
        foreach ($data as $productId) {
            $product = wc_get_product($productId);
            if (empty($product)) {
                continue;
            }

            // do not link products if eBook attr is `No`, if the batch is global ($data=[])
            if (!$this->forceEbook &&
                !empty($configEbook = $product->get_attribute('pa_ebook')) &&
                $configEbook === 'Desactivado') {
                continue;
            }

            $isbn = $product->get_meta('alfaomega_ebooks_ebook_isbn') ?? null;
            $sku = $product->get_sku();
            $products[$productId] = [
                'isbn'         => $isbn,
                'product_sku'  => $sku,
                'printed_isbn' => $sku,
            ];
            $ebookPost = $this->searchEbook($isbn, $sku);
            if (empty($ebookPost)) {
                if ($this->forceEbook) {
                    $isbns[empty($isbn) ? $sku : $isbn] = $productId;
                }
            } else {
                $products[$productId] = array_merge($products[$productId], $ebookPost);
            }
        }

        if (!empty($isbns)) {
            $ebooks = $this->getEbookEntity()->index(array_keys($isbns));
            if (!empty($ebooks)) {
                foreach ($ebooks as $ebook) {
                    if (!empty($isbns[$ebook['isbn']])) {
                        $productId = $isbns[$ebook['isbn']];
                    } else {
                        $productId = $isbns[$ebook['printed_isbn']] ?? null;
                    }
                    if (!empty($productId)) {
                        $ebook['printed_isbn'] = $products[$productId]['product_sku'];
                        $products[$productId] = array_merge($products[$productId], $ebook);
                    }
                }
            }
        }

        return $async
            ? $this->queueProcess($products)
            : $this->doProcess($products);
    }

    /**
     * Remove the product link to the ebook and restores the simple product type.
     *
     * @param int $postId
     *
     * @return int
     * @throws \Exception
     */
    public function remove(int $postId): int
    {
        // get the product
        $product = wc_get_product($postId);
        if ($product->get_type() !== 'variable') {
            throw new \Exception("Product type not supported");
        }

        // remove the variations
        $variations = $this->entity
            ->variant()
            ->list($product->get_id());

        $productPrice = null;
        $helper = Service::make()->helper();

        foreach ($variations as $variation) {
            $format = $helper->getQueryParam($variation->permalink, 'attribute_pa_book-format');
            if ($format === "impreso") {
                $productPrice = [
                    'regular_price' => $variation->regular_price,
                    'sale_price'    => $variation->sale_price,
                ];
            }
            $result = $this->entity
                ->variant()
                ->delete($product->get_id(), $variation->id);
            if (empty($result)) {
                throw new \Exception("Variation deletion failed");
            }
        }
        if (empty($productPrice)) {
            $prices = $product->get_meta('_ao_price_backup') ?? null;
            if (empty($prices)) {
                throw new \Exception("Product price not available");
            }
            $productPrice = json_decode($prices, true);
        }

        // restore the product type
        $result = $this->entity->updateType(
            array_merge(['id' => $product->get_id()], $productPrice),
            'simple'
        );
        if (empty($result)) {
            throw new \Exception("Restoring product type failed");
        }

        // remove the attributes
        $product = $this->entity->get($product->get_id());
        $product = $this->entity->updateFormatsAttr($product, ['Impreso']);
        if (empty($product)) {
            throw new \Exception("Product formats attribute update failed");
        }

        $product = $this->entity->updateEbookAttr($product, ['No']);
        if (empty($product)) {
            throw new \Exception("Product eBook attribute update failed");
        }

        // clear the eBook field value
        update_post_meta($product['id'], 'alfaomega_ebooks_ebook_isbn', '');

        return 1;
    }

    /**
     * Get the linked product
     *
     * @param array $ebook
     * @param int|null $postId
     *
     * @return int
     * @throws \Exception
     */
    public function product(int $postId): \WC_Product|null
    {
        $productSku = get_post_meta($postId, 'alfaomega_ebook_product_sku', true);
        if (empty($productSku)) {
            return null;
        }
        $productId = wc_get_product_id_by_sku($productSku);
        return wc_get_product($productId);
    }

    /**
     * Link the products to the ebooks synchronously.
     * @param array $entities
     *
     * @return array|null
     * @throws \Exception
     */
    protected function doProcess(array $entities): ?array
    {
        $processed = [];
        foreach ($entities as $productId => $ebook) {
            if (empty($ebook['printed_isbn']) ||
                empty($ebook['isbn']) ||
                empty($ebook['title'])) {
                continue;
            }
            $ebook['product_id'] = $productId;
            $result = $this->single($ebook, postId: $ebook['id'] ?? null);
            if ($result > 0) {
                $processed[] = $result;
            }
        }

        return $processed;
    }

    /**
     * Queue the process to link the products to the ebooks asynchronously.
     * @param array $entities
     *
     * @return array|null
     */
    protected function queueProcess(array $entities): ?array
    {
        $onQueue = [];
        foreach ($entities as $productId => $ebook) {
            if (empty($ebook['printed_isbn']) ||
                empty($ebook['isbn']) ||
                empty($ebook['title'])) {
                continue;
            }
            $ebook['product_id'] = $productId;
            $result = as_enqueue_async_action(
                'alfaomega_ebooks_queue_link',
                [$ebook, true, $ebook['id']]
            );
            if ($result !== 0) {
                $onQueue[] = $result;
            }
        }
        return $onQueue;
    }

    /**
     * Link products not yet linked to an eBook.
     * Retrieve a chunk of data to process.
     * This method should be implemented by child classes to retrieve a chunk of data to process.
     * The method should return an array of data to process, or null if there is no more data to process.
     *
     * @return array|null An array of data to process, or null if there is no more data to process.
     * @throws \Exception
     */
    protected function chunk(): ?array
    {
        $onQueue = [];
        $limit = 10000; // TODO: setup a limit if required
        $countPerPage = $this->chunkSize;

        $page = 1;
        do {
            $countPerPage = min($limit, $countPerPage);
            $args = [
                'limit' => $countPerPage,
                'page'  => $page,
                'type'  => 'simple',
            ];
            $posts = wc_get_products($args);
            if (empty($posts)) {
                break;
            }

            $products = array_column($posts, 'id');
            $onQueue = array_merge($onQueue, $this->batch($products, true));
            $page++;
        } while (count($posts) === $this->chunkSize && count($onQueue) < $limit);

        return $onQueue;
    }
}
