<?php
namespace AlfaomegaEbooks\Services\eBooks;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Managers\EbookManager;
use AlfaomegaEbooks\Services\eBooks\Managers\QueueManager;
use AlfaomegaEbooks\Services\eBooks\Managers\SettingsManager;
use AlfaomegaEbooks\Services\eBooks\Managers\WooCommerceManager;

/**
 * eBooks service.
 */
class Service
{
    /**
     * @var EbookManager
     */
    protected EbookManager $ebooksManager;
    /**
     * @var QueueManager
     */
    protected QueueManager $queueManager;
    /**
     * @var WooCommerceManager
     */
    protected WooCommerceManager $wooCommerceManager;

    /**
     * @var Api
     */
    protected Api $api;

    /**
     * Make the eBooks service.
     *
     * @return self
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * The eBooks service constructor.
     *
     * @throws \Exception
     */
    public function __construct() {
        $settings = SettingsManager::make()->get();
        $this->api = new Api($settings);

        $this->ebooksManager = new EbookManager($this->api, $settings);
        $this->queueManager = new QueueManager($this->api, $settings);
        $this->wooCommerceManager = new WooCommerceManager($this->api, $settings);
    }

    /**
     * Get the eBooks manager.
     * This method is used to get the instance of the EbookManager.
     * It first checks the settings using the SettingsManager. If the settings are not valid,
     * it updates the settings of the EbookManager with the current settings.
     *
     * @return EbookManager The instance of the EbookManager.
     * @throws \Exception
     */
    public function ebooks(): EbookManager
    {
        return $this->ebooksManager;
    }

    /**
     * Get the queue manager.
     *
     * @return QueueManager
     */
    public function queue(): QueueManager
    {
        return $this->queueManager;
    }

    /**
     * Get the WooCommerce manager.
     *
     * @return WooCommerceManager
     */
    public function wooCommerce(): WooCommerceManager
    {
        return $this->wooCommerceManager;
    }
}
