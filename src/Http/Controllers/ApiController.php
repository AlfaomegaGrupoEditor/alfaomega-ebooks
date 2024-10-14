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
        try {
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
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }

    /**
     * Get current client Catalog
     *
     * @return array
     */
    public function getCatalog(): array
    {
        try {
            /*$result = Service::make()
                ->ebooks()
                ->accessPost()
                ->catalog();*/

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
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }

    /**
     * Redeem a code to add associated books to the user's personal library.
     *
     * @param array $data The data containing the code to redeem.
     *
     * @return array The result of the redemption process.
     */
    public function redeemCode(array $data = []): array
    {
        try {
            Service::make()
                ->ebooks()
                ->samplePost()
                ->redeem($data['code']);

            return [
                'status'  => 'success',
                'data'    => null,
                'message' => esc_html__('Code redeemed successfully, the associated books were added to your personal library. Enjoy the reading!', 'alfaomega-ebooks'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get eBooks Info
     *
     * @return array
     */
    public function getEbooksInfo(): array
    {
        try {
            $result = Service::make()
                ->ebooks()
                ->ebookPost()
                ->getInfo();

            return [
                'status'  => 'success',
                'data' => $result,
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }

    /**
     * Get products Info
     *
     * @return array
     */
    public function getProductsInfo(): array
    {
        try {
            $result = Service::make()
                ->wooCommerce()
                ->product()
                ->getInfo();

            return [
                'status'  => 'success',
                'data' => $result,
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }

    /**
     * Get access Info
     *
     * @return array
     */
    public function getAccessInfo(): array
    {
        try {
            $result = Service::make()
                ->ebooks()
                ->accessPost()
                ->getInfo();

            return [
                'status'  => 'success',
                'data' => $result,
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }

    /**
     * Get codes Info
     *
     * @return array
     */
    public function getCodesInfo(): array
    {
        try {
            $result = Service::make()
                ->ebooks()
                ->samplePost()
                ->getInfo();

            return [
                'status'  => 'success',
                'data' => $result,
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }

    /**
     * Get codes Info
     *
     * @param array $data
     *
     * @return array
     */
    public function getProcessInfo(array $data): array
    {
        try {
            if (empty($data['process'])) {
                throw new \Exception('The process is required.');
            }

            if (!in_array($data['process'], ['import-new-ebooks', 'update-ebooks', 'link-products', 'setup-prices'])) {
                throw new \Exception('The process is invalid.');
            }

            $queue = match($data['process']) {
                'import-new-ebooks' => 'alfaomega_ebooks_queue_import',
                'update-ebooks' => 'alfaomega_ebooks_queue_refresh',
                'link-products' => 'alfaomega_ebooks_queue_link',
                'setup-prices' => 'alfaomega_ebooks_queue_prices',
                default => null,
            };
            if (empty($queue)) {
                throw new \Exception('The process is invalid.');
            }

            $result = Service::make()
                ->queue()
                ->status($queue, true);

            return [
                'status'  => 'success',
                'data'    => $result,
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }

    /**
     * Clear the queue
     *
     * @param array $data
     *
     * @return array
     */
    public function clearQueue(array $data): array
    {
        try {
            if (empty($data['process'])) {
                throw new \Exception('The process is required.');
            }

            if (!in_array($data['process'], ['import-new-ebooks', 'update-ebooks', 'link-products', 'setup-prices'])) {
                throw new \Exception('The process is invalid.');
            }

            $queue = match($data['process']) {
                'import-new-ebooks' => 'alfaomega_ebooks_queue_import',
                'update-ebooks' => 'alfaomega_ebooks_queue_refresh',
                'link-products' => 'alfaomega_ebooks_queue_link',
                'setup-prices' => 'alfaomega_ebooks_queue_prices',
                default => null,
            };
            if (empty($queue)) {
                throw new \Exception('The process is invalid.');
            }

            $result = Service::make()
                ->queue()
                ->clear($queue);

            return [
                'status'  => 'success',
                'data'    => $result,
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }

    /**
     * Get process actions
     *
     * @param array $data
     *
     * @return array
     */
    public function getProcessActions(array $data): array
    {
        try {
            if (empty($data['process'])) {
                throw new \Exception('The process is required.');
            }

            if (!in_array($data['process'], ['import', 'update', 'link', 'setup'])) {
                throw new \Exception('The process is invalid.');
            }

            $queue = match($data['process']) {
                'import' => 'alfaomega_ebooks_queue_import',
                'update' => 'alfaomega_ebooks_queue_refresh',
                'link' => 'alfaomega_ebooks_queue_link',
                'setup' => 'alfaomega_ebooks_queue_prices',
                default => null,
            };
            if (empty($queue)) {
                throw new \Exception('The process is invalid.');
            }

            if (empty($data['status'])) {
                throw new \Exception('The status is required.');
            }

            if (!in_array($data['status'], ['processing', 'completed', 'failed'])) {
                throw new \Exception('The status is invalid.');
            }

            $result = Service::make()
                ->queue()
                ->actions(
                    $queue,
                    $data['status'] === 'processing' ? [ 'in-process', 'pending' ] :  [ $data['status'] ],
                    $data['page'] ?? 1,
                    $data['perPage'] ?? 10
                );

            return array_merge([
                'status'  => 'success',
                'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
            ], $result);
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks'),
            ];
        }
    }
}
