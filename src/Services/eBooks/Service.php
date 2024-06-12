<?php
namespace AlfaomegaEbooks\Services;

use AlfaomegaEbooks\Alfaomega\Api;
use AlfaomegaEbooks\Services\Managers\EbookManager;
use AlfaomegaEbooks\Services\Managers\QueueManager;
use AlfaomegaEbooks\Services\Managers\SettingsManager;
use AlfaomegaEbooks\Services\Managers\WooCommerceManager;

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
        $this->wooCommerceManager = (new WooCommerceManager($this->api, $settings))->init();
        $this->checkSettings();
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

    /**
     * Check and update settings.
     * This method is used to check the settings using the SettingsManager.
     * If the settings are not valid, it updates the settings of the EbookManager, QueueManager, and WooCommerceManager
     * with the current settings.
     *
     * @return void
     * @throws \Exception
     */
    protected function checkSettings(): void
    {
        $settings = SettingsManager::make();
        if (!$settings->check()) {
            $this->ebooksManager->updateSettings($settings->get());
            $this->queueManager->updateSettings($settings->get());
            $this->wooCommerceManager->updateSettings($settings->get());
        };
    }
}
