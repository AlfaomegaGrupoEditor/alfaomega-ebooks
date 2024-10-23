<?php

namespace AlfaomegaEbooks\Http\Controllers;

use AlfaomegaEbooks\Services\eBooks\Service;

class ApiController
{
    /**
     * Check API
     *
     * @param array $data
     *
     * @return array
     */
    public function checkApi(array $data = []): array
    {
        return [
            'status'  => 'success',
            'data'    => [
                'user_id' => wp_get_current_user()->ID,
            ],
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Get Books
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function getBooks(array $data = []): array
    {
        $data['filter']['category'] = $data['filter']['category'] === 'all_ebooks'
            ? null
            : $data['filter']['category'];
        $key = join('-', [
            'user-books-search',
            'user_id'        => wp_get_current_user()->ID,
            'category'       => $data['filter']['category'],
            'search'         => $data['filter']['searchKey'],
            'accessType'     => $data['filter']['accessType'],
            'accessStatus'   => $data['filter']['accessStatus'],
            'page'           => $data['filter']['currentPage'],
            'perPage'        => $data['filter']['perPage'],
            'orderBy'        => $data['filter']['order']['field'],
            'orderDirection' => $data['filter']['order']['direction'],
        ]);
        Service::make()->helper()->cacheForget($key);
        $result = Service::make()->helper()
            ->cacheRemember($key, 1 * HOUR_IN_SECONDS, function () use ($data) {
                return Service::make()
                    ->ebooks()
                    ->accessPost()
                    ->search(
                        category: $data['filter']['category'],
                        search: $data['filter']['searchKey'],
                        type: $data['filter']['accessType'],
                        status: $data['filter']['accessStatus'],
                        page: $data['filter']['currentPage'],
                        perPage: $data['filter']['perPage'],
                        orderBy: $data['filter']['order']['field'],
                        orderDirection: $data['filter']['order']['direction'],
                    );
            });
        return array_merge([
                'status'  => 'success',
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ], $result);

    }

    /**
     * Get current client Catalog
     *
     * @return array
     * @throws \Exception
     */
    public function getCatalog(): array
    {
        try {
            $key = 'ebooks-catalog-' . wp_get_current_user()->ID;
            //Service::make()->helper()->cacheForget($key);
            $result = Service::make()->helper()
                ->cacheRemember($key, 24 * HOUR_IN_SECONDS, function () {
                    return Service::make()
                        ->ebooks()
                        ->accessPost()
                        ->catalog();
                });

            return [
                'status'  => 'success',
                'data' => $result,
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ];
        } catch (\Exception $e) {
            Service::make()->helper()
                ->cacheForget($key);

            throw $e;
        }
    }

    /**
     * Redeem a code to add associated books to the user's personal library.
     *
     * @param array $data The data containing the code to redeem.
     *
     * @return array The result of the redemption process.
     * @throws \Exception
     */
    public function redeemCode(array $data = []): array
    {
        Service::make()
            ->ebooks()
            ->samplePost()
            ->redeem($data['code']);

        return [
            'status'  => 'success',
            'data'    => null,
            'message' => esc_html__('Code redeemed successfully, the associated books were added to your personal library. Enjoy the reading!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Get eBooks Info
     *
     * @return array
     * @throws \Exception
     */
    public function getEbooksInfo(): array
    {
        $result = Service::make()
            ->ebooks()
            ->ebookPost()
            ->getInfo();

        return [
            'status'  => 'success',
            'data' => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Get products Info
     *
     * @return array
     */
    public function getProductsInfo(): array
    {
        $result = Service::make()
            ->wooCommerce()
            ->product()
            ->getInfo();

        return [
            'status'  => 'success',
            'data' => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Get access Info
     *
     * @return array
     * @throws \Exception
     */
    public function getAccessInfo(): array
    {
        $result = Service::make()
            ->ebooks()
            ->accessPost()
            ->getInfo();

        return [
            'status'  => 'success',
            'data' => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Get codes Info
     *
     * @return array
     * @throws \Exception
     */
    public function getCodesInfo(): array
    {
        $result = Service::make()
            ->ebooks()
            ->samplePost()
            ->getInfo();

        return [
            'status'  => 'success',
            'data' => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Get codes Info
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function getProcessInfo(array $data): array
    {
        if (empty($data['process'])) {
            throw new \Exception(esc_html__('The process is required.', 'alfaomega-ebooks'), 400);
        }

        if (!in_array($data['process'], ['import-new-ebooks', 'update-ebooks', 'link-products', 'setup-prices'])) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        $queue = match($data['process']) {
            'import-new-ebooks' => 'alfaomega_ebooks_queue_import',
            'update-ebooks' => 'alfaomega_ebooks_queue_refresh',
            'link-products' => 'alfaomega_ebooks_queue_link',
            'setup-prices' => 'alfaomega_ebooks_queue_setup_price',
            default => null,
        };
        if (empty($queue)) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        $result = Service::make()
            ->queue()
            ->status($queue, true);

        return [
            'status'  => 'success',
            'data'    => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Clear the queue
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function clearQueue(array $data): array
    {
        if (empty($data['process'])) {
            throw new \Exception(esc_html__('The process is required.', 'alfaomega-ebooks'), 400);
        }

        if (!in_array($data['process'], ['import-new-ebooks', 'update-ebooks', 'link-products', 'setup-prices'])) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        $queue = match($data['process']) {
            'import-new-ebooks' => 'alfaomega_ebooks_queue_import',
            'update-ebooks' => 'alfaomega_ebooks_queue_refresh',
            'link-products' => 'alfaomega_ebooks_queue_link',
            'setup-prices' => 'alfaomega_ebooks_queue_setup_price',
            default => null,
        };
        if (empty($queue)) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        $result = Service::make()
            ->queue()
            ->clear($queue);

        return [
            'status'  => 'success',
            'data'    => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Get process actions
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function getProcessActions(array $data): array
    {
        if (empty($data['process'])) {
            throw new \Exception(esc_html__('The process is required.', 'alfaomega-ebooks'), 400);
        }

        if (!in_array($data['process'], ['import', 'update', 'link', 'setup'])) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        $queue = match($data['process']) {
            'import' => 'alfaomega_ebooks_queue_import',
            'update' => 'alfaomega_ebooks_queue_refresh',
            'link' => 'alfaomega_ebooks_queue_link',
            'setup' => 'alfaomega_ebooks_queue_setup_price',
            default => null,
        };
        if (empty($queue)) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        if (empty($data['status'])) {
            throw new \Exception(esc_html__('The status is required.', 'alfaomega-ebooks'), 400);
        }

        if (!in_array($data['status'], ['processing', 'completed', 'failed', 'excluded'])) {
            throw new \Exception(esc_html__('The status is invalid.', 'alfaomega-ebooks'), 400);
        }

        $result = Service::make()
            ->queue()
            ->actions($queue,
                match($data['status']) {
                    'processing' => [ 'in-process', 'pending' ],
                    'completed' => [ 'complete' ],
                    default => [ $data['status'] ],
                },
                $data['page'] ?? 1,
                $data['perPage'] ?? 10
            );

        return array_merge([
            'status'  => 'success',
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ], $result);
    }

    /**
     * Delete action
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function deleteAction(array $data): array
    {
        if (empty($data['process'])) {
            throw new \Exception(esc_html__('The process is required.', 'alfaomega-ebooks'), 400);
        }

        if (!in_array($data['process'], ['import-new-ebooks', 'update-ebooks', 'link-products', 'setup-prices'])) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        $queue = match($data['process']) {
            'import-new-ebooks' => 'alfaomega_ebooks_queue_import',
            'update-ebooks' => 'alfaomega_ebooks_queue_refresh',
            'link-products' => 'alfaomega_ebooks_queue_link',
            'setup-prices' => 'alfaomega_ebooks_queue_setup_price',
            default => null,
        };
        if (empty($queue)) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        if (empty($data['ids'])) {
            throw new \Exception(esc_html__('The ids are required.', 'alfaomega-ebooks'), 400);
        }

        $result = Service::make()
            ->queue()
            ->delete($queue, $data['ids'], $data['type'] ?? 'action');

        return [
            'status'  => 'success',
            'data'    => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Retry action
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function retryAction(array $data): array
    {
        if (empty($data['process'])) {
            throw new \Exception(esc_html__('The process is required.', 'alfaomega-ebooks'), 400);
        }

        if (!in_array($data['process'], ['import-new-ebooks', 'update-ebooks', 'link-products', 'setup-prices'])) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        $queue = match($data['process']) {
            'import-new-ebooks' => 'alfaomega_ebooks_queue_import',
            'update-ebooks' => 'alfaomega_ebooks_queue_refresh',
            'link-products' => 'alfaomega_ebooks_queue_link',
            'setup-prices' => 'alfaomega_ebooks_queue_setup_price',
            default => null,
        };
        if (empty($queue)) {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        if (empty($data['ids'])) {
            throw new \Exception(esc_html__('The ids are required.', 'alfaomega-ebooks'), 400);
        }

        $result = Service::make()
            ->queue()
            ->retry($queue, $data['ids'], $data['type'] ?? 'action');

        return [
            'status'  => 'success',
            'data'    => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Retry action
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function excludeAction(array $data): array
    {
        if (empty($data['process'])) {
            throw new \Exception(esc_html__('The process is required.', 'alfaomega-ebooks'), 400);
        }

        if ($data['process'] !== 'import-new-ebooks') {
            throw new \Exception(esc_html__('The process is invalid.', 'alfaomega-ebooks'), 400);
        }

        $queue = 'alfaomega_ebooks_queue_import';

        if (empty($data['ids'])) {
            throw new \Exception(esc_html__('The ids are required.', 'alfaomega-ebooks'), 400);
        }

        $result = Service::make()
            ->queue()
            ->exclude($queue, $data['ids']);

        return [
            'status'  => 'success',
            'data'    => $result,
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }

    /**
     * Import new ebooks
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function importNewEbooks(): array
    {
        // to make sure all imported ebooks are registered in the Panel
        $service = Service::make()->ebooks();
        if (defined('AO_STORE_UPDATE') && AO_STORE_UPDATE) {
            $service->updateCatalogImport();
        }

        // start the import process
        $response = $service->importEbook()->batch();
        Service::make()->queue()->run();

        return [
            'status'  => 'success',
            'data'    => $response,
            'message' => count($response) > 0
                ? str_replace('%s', count($response), esc_html__("Scheduled to import %s new ebooks successfully!", 'alfaomega-ebooks'))
                : esc_html__('No new eBooks to import', 'alfaomega-ebooks')
        ];
    }

    /**
     * Update ebooks
     * @return array
     * @throws \Exception
     */
    public function updateEbooks(): array
    {
        $response = Service::make()
            ->ebooks()
            ->refreshEbook()
            ->batch();
        Service::make()->queue()->run();

        return [
            'status'  => 'success',
            'data'    => $response,
            'message' => count($response) > 0
                ? str_replace('%s', count($response), esc_html__("Scheduled to refresh %s ebooks successfully!", 'alfaomega-ebooks'))
                : esc_html__('No eBooks found to refresh', 'alfaomega-ebooks')
        ];
    }

    /**
     * Link products
     * @return array
     * @throws \Exception
     */
    public function linkProducts(): array
    {
        // TODO: check implementation and test it
        $response = Service::make()
            ->wooCommerce()
            ->linkEbook()
            ->batch();
        Service::make()->queue()->run();

        return [
            'status'  => 'success',
            'data'    => $response,
            'message' => count($response) > 0
                ? str_replace('%s', count($response), esc_html__("Scheduled to link %s products successfully!", 'alfaomega-ebooks'))
                : esc_html__('No products found to link', 'alfaomega-ebooks')
        ];
    }

    /**
     * Setup prices based on a given factor and value.
     *
     * This method sets up the prices for products based on the provided factor and value.
     * It validates the input data and then processes the price update.
     *
     * @param array $data The data containing the factor and value for price setup.
     *
     * @return array The result of the price setup process.
     * @throws \Exception If the factor or value is invalid.
     */
    public function setupPrices(array $data): array
    {
        if (empty($data['factor'])
            || !in_array($data['factor'], ['page_count', 'fixed_number', 'percent', 'price_update'])) {
            throw new \Exception(esc_html__('The factor is not valid.', 'alfaomega-ebooks'), 400);
        }

        if (empty($data['value'])) {
            throw new \Exception(esc_html__('The factor value is invalid.', 'alfaomega-ebooks'), 400);
        }

        $response = Service::make()
            ->wooCommerce()
            ->updatePrice()
            ->setFactor($data['factor'], floatval($data['value']))
            ->batch();
        Service::make()->queue()->run();

        return [
            'status'  => 'success',
            'data'    => $response,
            'message' => count($response) > 0
                ? str_replace('%s', count($response), esc_html__("Scheduled to import %s new ebooks successfully!", 'alfaomega-ebooks'))
                : esc_html__('No new eBooks to import', 'alfaomega-ebooks')
        ];
    }
}
