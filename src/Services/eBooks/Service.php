<?php
namespace AlfaomegaEbooks\Services\eBooks;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Managers\EbookManager;
use AlfaomegaEbooks\Services\eBooks\Managers\QueueManager;
use AlfaomegaEbooks\Services\eBooks\Managers\SettingsManager;
use AlfaomegaEbooks\Services\eBooks\Managers\WooCommerceManager;
use Dotenv\Dotenv;

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
     * Helper library.
     * @var Helper
     */
    protected Helper $helper;

    /**
     * Action Scheduler High Volume Setup.
     * @var ActionSchedulerSetup
     */
    protected ActionSchedulerSetup $actionSchedulerSetup;

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
        $this->loadEnv();
        $settings = SettingsManager::make()->get();
        $this->api = new Api($settings);

        $this->ebooksManager = new EbookManager($this->api, $settings);
        $this->queueManager = new QueueManager($this->api, $settings);
        $this->wooCommerceManager = new WooCommerceManager($this->api, $settings);
        $this->helper = new Helper();
        $this->actionSchedulerSetup = new ActionSchedulerSetup();
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

    protected function loadEnv(): void
    {
        if (!file_exists(ABSPATH . "/wp-content/plugins/alfaomega-ebooks/.env")) {
            // WP_DEBUG && error_log('No .env file found in the plugin directory, please duplicate the .env.example file and rename it to .env and setup the environment variables.');
            return;
        }

        $dotenv = Dotenv::createImmutable(ABSPATH . "/wp-content/plugins/alfaomega-ebooks");
        $dotenv->load();
    }

    /**
     * Get the environment variable.
     *
     * @param string $key The key of the environment variable.
     * @param mixed $default The default value of the environment variable.
     *
     * @return mixed The value of the environment variable.
     */
    public function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Get the helper library.
     *
     * @return Helper
     */
    public function helper(): Helper
    {
        return $this->helper;
    }

    /**
     * Get the Action Scheduler High Volume Setup.
     *
     * @return ActionSchedulerSetup
     */
    public function actionSchedulerSetup(): ActionSchedulerSetup
    {
        return $this->actionSchedulerSetup;
    }
}
