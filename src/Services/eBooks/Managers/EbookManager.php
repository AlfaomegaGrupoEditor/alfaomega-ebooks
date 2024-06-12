<?php

namespace AlfaomegaEbooks\Services\Managers;

use AlfaomegaEbooks\Alfaomega\Api;
use AlfaomegaEbooks\Services\Process\ImportEbook;
use AlfaomegaEbooks\Services\Process\RefreshEbook;

/**
 * The ebook manager.
 */
class EbookManager extends AbstractManager
{
    protected ImportEbook $importEbooks;
    protected RefreshEbook $refreshEbooks;

    public function __construct(Api $api, array $settings) {
        parent::__construct($api, $settings);

        $this->importEbooks = new ImportEbook($api, $settings);
        $this->refreshEbooks = new RefreshEbook($api, $settings);
    }

    /**
     * Import the ebooks.
     *
     * @return ImportEbook
     */
    public function import(): ImportEbook
    {
        return $this->importEbooks;
    }

    /**
     * Refresh the ebooks.
     *
     * @return RefreshEbook
     */
    public function refresh(): RefreshEbook
    {
        return $this->refreshEbooks;
    }
}
