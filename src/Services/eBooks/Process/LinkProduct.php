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
    public function single(array $eBook, bool $throwError=false, int $postId = null): int
    {
        try {
            $product = $this->entity->get($eBook['product_id']);
            if (empty($product)) {
                throw new Exception("Products with digital ISBN {$eBook['isbn']} not found");
            }
            $originalProduct = $product;

            $prices = [
                'regular_price' => $product['regular_price'],
                'sale_price'    => $product['sale_price'],
            ];
            if (empty($prices['regular_price'])) {
                throw new Exception("Product price not specified");
            }

            $product = $this->entity->updateType($product);
            if (empty($product)) {
                throw new Exception("Product type not supported");
            }

            $product = $this->entity->updateFormatsAttr($product);
            if (empty($product)) {
                throw new Exception("Product formats attribute update failed");
            }

            $product = $this->entity->updateEbookAttr($product);
            if (empty($product)) {
                throw new Exception("Product eBook attribute update failed");
            }

            $product = $this->entity->variant()->update($product, $prices, $eBook);
            if (empty($product)) {
                throw new Exception("Product variants failed");
            }

            update_post_meta(
                $eBook['product_id'],
                'alfaomega_ebooks_ebook_isbn',
                $eBook['isbn']
            );

            return $product['id'];
        } catch (\Exception $e) {
            if (!empty($originalProduct)) {
                $this->entity->updateType($originalProduct, 'simple');
            }

            if ($throwError) {
                throw $e;
            }
            return 0;
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
    public function batch(array $data = [], bool $async = false): array
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

    /**
     * Link unlinked yet eBooks to products.
     * Retrieve a chunk of data to process.
     * This method should be implemented by child classes to retrieve a chunk of data to process.
     * The method should return an array of data to process, or null if there is no more data to process.
     *
     * @return array|null An array of data to process, or null if there is no more data to process.
     */
    protected function chunk(): ?array
    {
        // Gather all no linked eBooks by chunks of 100
        // call $this->batch($data, true) with the chunk
        return null;
    }
}
