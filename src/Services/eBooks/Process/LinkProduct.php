<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce\ProductEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

/**
 * Link ebooks process.
 */
class LinkProduct extends AbstractProcess
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
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array
     * @throws \Exception
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
