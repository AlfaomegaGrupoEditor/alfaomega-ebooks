<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\ProductEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

/**
 * Class LinkProduct
 *
 * This class is responsible for linking eBooks to WooCommerce products. It extends the AbstractProcess class and implements
 * the ProcessContract interface. The class provides methods to process a single eBook or a batch of eBooks.
 *
 * @package AlfaomegaEbooks\Services\eBooks\Process
 */
class LinkProduct extends AbstractProcess implements ProcessContract
{
    /**
     * LinkProduct constructor.
     *
     * Initializes the link process with the provided settings and product entity.
     *
     * @param array $settings The settings for the link process. These settings can include various configuration options.
     * @param ProductEntity $entity The product entity to be processed. This entity represents a WooCommerce product in the system.
     */
    public function __construct(
        array $settings,
        protected ProductEntity $entity)
    {
        parent::__construct($settings);
    }

    /**
     * Processes a single eBook.
     *
     * This method takes an eBook array, a boolean indicating whether to throw an error, and an optional post ID as input.
     * It retrieves the WooCommerce product associated with the eBook and updates its type, formats, and variants.
     *
     * @param array $eBook The eBook data.
     * @param bool $throwError Indicates whether to throw an error.
     * @param int|null $postId The post ID of the eBook. If provided, the method will process only this eBook.
     * @throws \Exception If there is an error during the processing of the eBook.
     * @return void
     */
    public function single(array $eBook, bool $throwError=false, int $postId = null): void
    {
        $product = $this->entity->get($eBook['tag_id'], $eBook['title']);
        if (empty($product)) {
            if ($throwError) {
                throw new Exception("Products with digital ISBN {$eBook['isbn']} not found");
            }
            return;
        }

        $product = $this->entity
            ->updateType($product);
        $prices = [
            'regular_price' => $product['regular_price'],
            'sale_price'    => $product['sale_price'],
        ];
        if (empty($product)) {
            throw new Exception("Product type not supported");
        }

        $product = $this->entity
            ->updateFormats($product);
        if (empty($product)) {
            throw new Exception("Product formats failed");
        }

        $product = $this->entity->variant()
            ->update($product, $prices, $eBook);
        if (empty($product)) {
            throw new Exception("Product variants failed");
        }
    }

    /**
     * Processes a batch of eBooks.
     *
     * This method takes an optional array of eBook data as input. If no array is provided, it retrieves a list of eBooks
     * from the database and processes each eBook.
     *
     * The method returns an array with the total number of eBooks linked.
     *
     * @param array $data An optional array of eBook data. If provided, the method will process only these eBooks.
     * @throws \Exception If there is an error during the processing of the eBooks.
     * @return array An array with the total number of eBooks linked.
     */
    public function batch(array $data = []): array
    {
        $linked = 0;
        $ebookPost = Service::make()->ebooks()->ebookPost();

        foreach ($data as $postId) {
            $this->single($ebookPost->get($postId));
            $linked++;
        }

        return [
            'linked' => $linked,
        ];
    }
}
