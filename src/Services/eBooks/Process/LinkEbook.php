<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\ProductEntity;

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
                                protected ProductEntity $entity
    ){
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
            return $this->chunk();
        }
        $products = []; // products to be updated
        $isbns = [];    // eBooks to be imported
        foreach ($data as $productId) {
            $product = wc_get_product($productId);
            if (empty($product)) {
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
                $isbns[empty($isbn) ? $sku : $isbn] = $productId;
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
        foreach ($variations as $variation) {
            if ($variation['description'] === 'Libro impreso') {
                $productPrice = [
                    'regular_price' => $variation['regular_price'],
                    'sale_price'    => $variation['sale_price'],
                ];
            }
            $result = $this->entity
                ->variant()
                ->delete($product->get_id(), $variation['id']);
            if (empty($result)) {
                throw new \Exception("Variation deletion failed");
            }
        }
        if (empty($productPrice)) {
            throw new \Exception("Product price not available");
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
        $product = $this->entity->updateFormatsAttr($product, []);
        if (empty($product)) {
            throw new \Exception("Product formats attribute update failed");
        }

        $product = $this->entity->updateEbookAttr($product, ['No']);
        if (empty($product)) {
            throw new \Exception("Product eBook attribute update failed");
        }

        // clear the eBook field value
        //update_post_meta($product['id'], 'alfaomega_ebooks_ebook_isbn', '');

        return 1;
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
            if (empty($ebook['printed_isbn'])) {
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
        $queued = [];
        foreach ($entities as $productId => $ebook) {
            if (empty($ebook['printed_isbn'])) {
                continue;
            }
            $ebook['product_id'] = $productId;
            //TODO: queue the process
            //$result = $this->single($ebook, postId: $ebook['id'] ?? null);

            $queued[] = $productId;
        }
        return $queued;
    }

    /**
     * Link products not yet linked to an eBook.
     * Retrieve a chunk of data to process.
     * This method should be implemented by child classes to retrieve a chunk of data to process.
     * The method should return an array of data to process, or null if there is no more data to process.
     *
     * @return array|null An array of data to process, or null if there is no more data to process.
     */
    protected function chunk(): ?array
    {
        // get all products of type 'simple' by chunks of 100
        // call $this->batch($data, true) with the chunk
        return null;
    }
}
