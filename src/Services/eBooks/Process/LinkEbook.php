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
     * Do the process on synchronously on a single object.
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

            if ($this->updateProduct) {
                Service::make()
                    ->wooCommerce()
                    ->linkProduct()
                    ->single($eBook);
            }

            return !empty($post) ? $post['id'] : 0;
        } catch (\Exception $e) {
            if ($throwError) {
                throw $e;
            }
        }
    }

    /**
     * Gather the information required to perform the process on each object.
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

        // Recommended for quick and bulk actions
        $processed = [];
        if (!$async) {
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
        }

        // Recommended for big amount of records
        // TODO: queue the process

        return $processed;
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
}
