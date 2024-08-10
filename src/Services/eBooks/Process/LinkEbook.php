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
        $service = Service::make()->ebooks()->ebookPost();
        $isbns = [];

        // get products isbn and ebook_isbn
        foreach ($data as $productId) {
            $product = wc_get_product($productId);
            if ($product) {
                $isbn = $product->get_sku();
                $ebookIsbn = $product->get_meta('alfaomega_ebooks_ebook_isbn') ?? null;
                $ebookPost = $service->search($ebookIsbn);
                if (!empty($ebookPost)) {
                    $ebookPost = $service->search($ebookIsbn);
                } else {
                    $isbns[] = $ebookIsbn ?? $product->get_sku();
                    $ebookPost = null;
                }
                $products[$productId] = [
                    'isbn'      => $isbn,
                    'ebookIsbn' => $ebookIsbn,
                    'ebookPost' => $ebookPost,
                ];
            }
        }
        if (empty($products)) {
            return null;
        }

        // get ebook information from API
        if (!empty($isbns)) {
            $ebooks = $service->index($isbns);
            if ($ebooks) {
                foreach ($ebooks as $ebook) {
                    // TODO: create the ebook post
                }
            }
        }

        // link the ebook post to the product


        return [];
    }
}
