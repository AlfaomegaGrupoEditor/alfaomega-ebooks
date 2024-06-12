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
     * Do the process on a single object.
     *
     * @param array $eBook
     * @param bool $throwError
     *
     * @return void
     * @throws \Exception
     */
    public function single(array $eBook, bool $throwError=false): void
    {
    }

    /**
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array
     */
    public function batch(array $data = []): array
    {
        return [];
    }
}
