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
    protected ImportEbook $importEbook;

    /**
     * The RefreshEbook instance.
     *
     * @var RefreshEbook
     */
    protected RefreshEbook $refreshEbook;

    /**
     * The EbookPost instance.
     *
     * @var EbookPost
     */
    protected EbookPost $ebookPost;

    /**
     * The EbookManager constructor.
     *
     * @param Api $api The API.
     * @param array $settings The settings.
     */
    public function __construct(Api $api, array $settings) {
        parent::__construct($api, $settings);

        $this->ebookPost = EbookPost::make($api);
        $this->importEbook = new ImportEbook($settings, $this->ebookPost);
        $this->refreshEbook = new RefreshEbook($settings, $this->ebookPost);
    }

    /**
     * Import the ebooks.
     *
     * @return ImportEbook
     */
    public function importEbook(): ImportEbook
    {
        return $this->importEbook;
    }

    /**
     * Refresh the ebooks.
     *
     * @return RefreshEbook
     */
    public function refreshEbook(): RefreshEbook
    {
        return $this->refreshEbook;
    }

    /**
     * Get the EbookPost instance.
     *
     * @return EbookPost
     */
    public function ebookPost(): EbookPost
    {
        return $this->ebookPost;
    }
}
