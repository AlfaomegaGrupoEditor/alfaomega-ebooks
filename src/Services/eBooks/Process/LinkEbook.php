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
     * @return array
     * @throws \Exception
     */
    public function batch(array $data = []): array
    {
        foreach ($data as $productId) {
            // get the ebook reference for each product
            $product = wc_get_product($productId);
            if ($product) {
                $isbn = $product->get_sku();
                $ebookIsbn = $product->get_meta('alfaomega_ebooks_ebook_isbn') ?? null;

                // check if the ebook already exists to get the ebook_isbn
                $service = Service::make()->ebooks()->ebookPost();
                $ebookPost = $service->search($ebookIsbn);
                if (empty($ebookPost)) {
                    $response = $service->index([$ebookIsbn]);
                    if ($response) {
                        // create the ebook post

                    }
                }

                // link the ebook post to the product
            }


        }


        return [];
    }
}
