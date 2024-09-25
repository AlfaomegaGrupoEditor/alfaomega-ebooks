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
            $key = join('-', [
                'user-books-search',
                'user_id'        => wp_get_current_user()->ID,
                'category'       => $data['category'],
                'search'         => $data['filter']['search'],
                'accessType'     => $data['filter']['accessType'],
                'accessStatus'   => $data['filter']['accessStatus'],
                'page'           => $data['page'],
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
                            category: $data['category'],
                            search: $data['filter']['search'],
                            type: $data['filter']['accessType'],
                            status: $data['filter']['accessStatus'],
                            page: $data['page'],
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
            $key = 'ebooks-catalog-' . wp_get_current_user()->ID;
            Service::make()->helper()->cacheForget($key);
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
}
