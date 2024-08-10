<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\ProductEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

/**
 * Link ebooks process.
 */
class LinkEbook extends AbstractProcess implements ProcessContract
{
    /**
     * Initialize the process.
     *
     * @param array $settings The settings.
     * @param ProductEntity $entity The entity.
     *
     */
    public function __construct(
        array $settings,
        protected ProductEntity $entity)
    {
        parent::__construct($settings);
    }

    /**
     * Do the process on a single object.
     *
     * @param array $eBook
     * @param bool $throwError
     * @param int|null $postId
     *
     * @return void
     * @throws \Exception
     */
    public function single(array $eBook, bool $throwError=false, int $postId = null): void
    {
        // todo implement this method
    }

    /**
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array|null
     * @throws \Exception
     */
    public function batch(array $data = []): ?array
    {
        $products = [];
        $ebookService = Service::make()->ebooks()->ebookPost();
        $isbns = [];

        // get products isbn and ebook_isbn
        foreach ($data as $productId) {
            $product = wc_get_product($productId);
            if ($product) {
                $isbn = $product->get_sku();
                $ebookIsbn = $product->get_meta('alfaomega_ebooks_ebook_isbn') ?? null;
                $ebookPost = $ebookService->search($ebookIsbn);
                if (empty($ebookPost)) {
                    $isbns[$ebookIsbn ?? $product->get_sku()] = $productId;
                    $ebookPost = null;
                }
                $products[$productId] = [
                    'isbn'      => $isbn,
                    'ebookIsbn' => $ebookIsbn,
                    'ebookPost' => array_merge($ebookPost, ['product_id' => $productId]),
                ];
            }
        }
        if (empty($products)) {
            return null;
        }

        // get ebook information from API
        if (!empty($isbns)) {
            $ebooks = $ebookService->index(array_keys($isbns));
            if ($ebooks) {
                foreach ($ebooks as $ebook) {
                    if (!empty($isbns[$ebook['isbn']])) {
                        $productId = $isbns[$ebook['isbn']];
                    } else {
                        $productId = $isbns[$ebook['printed_isbn']] ?? null;
                    }
                    $ebookPost = $ebookService->updateOrCreate(null, $ebook);
                    $products[$productId]['ebookPost'] = array_merge($ebookPost, ['product_id' => $productId]);
                }
            }
        }

        // link the ebook posts to each product
        $productService = Service::make()->wooCommerce()->linkProduct();
        foreach ($products as $product) {
            $productService->single($product['ebookPost'], false);
        }

        return [];
    }
}
