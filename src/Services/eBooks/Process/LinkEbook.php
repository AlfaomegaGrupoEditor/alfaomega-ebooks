<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega\EbookPostEntity;
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
    public function __construct(
        array                      $settings,
        protected ProductEntity    $entity,
        protected ?EbookPostEntity $ebookEntity = null)
    {
        parent::__construct($settings);
    }

    /**
     * Link a product to an eBook.
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
            $post = $this->getEbookEntity()
                ->updateOrCreate($postId, $eBook);
            if (empty($post)) {
                throw new \Exception('Error updating or creating the eBook post.');
            }

            $eBook['id'] = $post['id'];
            if ($this->updateProduct) {
                $productId = Service::make()
                    ->wooCommerce()
                    ->linkProduct()
                    ->single($eBook);

                if (empty($productId)) {
                    throw new \Exception('Error linking the eBook with the product.');
                }
            }

            return $post['id'];
        } catch (\Exception $e) {
            if ($throwError) {
                throw $e;
            }
            return 0;
        }
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
     * Search the eBook by ISBN or product SKU.
     * @param string|null $isbn
     * @param string|null $sku
     *
     * @return array|null
     * @throws \Exception
     */
    protected function searchEbook(?string $isbn, ?string $sku): ?array
    {
        if (!empty($sku)) {
            return $this->getEbookEntity()
                ->search($sku, 'alfaomega_ebook_product_sku');
        }

        if (!empty($isbn)) {
            return $this->getEbookEntity()
                ->search($isbn);
        }

        return null;
    }

    /**
     * Get the eBook entity.
     *
     * @return EbookPostEntity
     * @throws \Exception
     */
    protected function getEbookEntity(): EbookPostEntity
    {
        if (empty($this->ebookEntity)) {
            $this->ebookEntity = Service::make()
                ->ebooks()
                ->ebookPost();
        }

        return $this->ebookEntity;
    }

    /**
     * Link the products to the ebooks synchronously.
     * @param array $products
     *
     * @return array|null
     * @throws \Exception
     */
    protected function doProcess(array $products): ?array
    {
        $processed = [];
        foreach ($products as $productId => $ebook) {
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
     * @param array $products
     *
     * @return array|null
     */
    protected function queueProcess(array $products): ?array
    {
        $queued = [];
        foreach ($products as $productId => $ebook) {
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
}
