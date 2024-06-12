<?php

namespace AlfaomegaEbooks\Services\Process;

use AlfaomegaEbooks\Services\Entities\EbookPostEntity;

/**
 * The refresh ebooks process.
 */
class RefreshEbook extends AbstractProcess implements ProcessContract
{
    /**
     * Initialize the process.
     *
     * @param array $settings The settings.
     * @param EbookPostEntity $entity The entity.
     */
    public function __construct(
        array $settings,
        protected EbookPostEntity $entity)
    {
        parent::__construct($settings);
    }

    /**
     * @inheritDoc
     */
    public function single(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function batch(array $data = []): array
    {
        return [];
    }
}
