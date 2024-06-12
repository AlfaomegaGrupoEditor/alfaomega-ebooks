<?php

namespace AlfaomegaEbooks\Services\Process;

use AlfaomegaEbooks\Services\Entities\WooCommerce\ProductEntity;

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

    public function single(): array
    {
        // TODO: Implement single() method.
    }

    public function batch(array $data = []): array
    {
        // TODO: Implement batch() method.
    }
}
