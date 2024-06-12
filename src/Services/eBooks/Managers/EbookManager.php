<?php

namespace AlfaomegaEbooks\Services\Managers;

use AlfaomegaEbooks\Alfaomega\Api;
use AlfaomegaEbooks\Services\Entities\EbookPost;
use AlfaomegaEbooks\Services\Process\ImportEbook;
use AlfaomegaEbooks\Services\Process\RefreshEbook;

/**
 * The ebook manager.
 */
class EbookManager extends AbstractManager
{
    /**
     * The ImportEbook instance.
     *
     * @var ImportEbook
     */
    protected ImportEbook $importEbooks;
    /**
     * The RefreshEbook instance.
     *
     * @var RefreshEbook
     */
    protected RefreshEbook $refreshEbooks;

    /**
     * The EbookManager constructor.
     *
     * @param Api $api The API.
     * @param array $settings The settings.
     */
    public function __construct(Api $api, array $settings) {
        parent::__construct($api, $settings);

        $this->importEbooks = new ImportEbook($settings, EbookPost::make($api));
        $this->refreshEbooks = new RefreshEbook($settings, EbookPost::make($api));
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
