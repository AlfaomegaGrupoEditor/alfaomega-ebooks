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
            $result = Service::make()
                ->ebooks()
                ->accessPost()
                ->search(
                    category: $data['category'],
                    search: $data['filter']['searchKey'],
                    type: $data['filter']['accessType'],
                    status: $data['filter']['accessStatus'],
                    page: $data['page'],
                    perPage: $data['filter']['perPage'],
                    orderBy: $data['filter']['order']['field'],
                    orderDirection: $data['filter']['order']['direction'],
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

        /*return [
            'status'  => 'success',
            'data' => [
                [
                    'id'         => 1,
                    'title'      => 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
                    'cover'      => 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-3.png',
                    'download'   => true,
                    'read'       => true,
                    'accessType' => 'purchase',
                    'status'     => 'active',
                    'addedAt'    => '2024-07-01',
                    'validUntil' => null,
                    'url'        => 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/',
                ],
                [
                    'id'         => 2,
                    'title'      => 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
                    'cover'      => 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-4.png',
                    'download'   => true,
                    'read'       => true,
                    'accessType' => 'purchase',
                    'status'     => 'active',
                    'addedAt'    => '2024-07-01',
                    'validUntil' => null,
                    'url'        => 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/',
                ],
                [
                    'id'         => 3,
                    'title'      => 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
                    'cover'      => 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-2.png',
                    'download'   => true,
                    'read'       => true,
                    'accessType' => 'purchase',
                    'status'     => 'active',
                    'addedAt'    => '2024-07-01',
                    'validUntil' => null,
                    'url'        => 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/',
                ],
                [
                    'id'         => 4,
                    'title'      => 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
                    'cover'      => 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-1.png',
                    'download'   => true,
                    'read'       => true,
                    'accessType' => 'purchase',
                    'status'     => 'active',
                    'addedAt'    => '2024-07-01',
                    'validUntil' => null,
                    'url'        => 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/',
                ],
                [
                    'id'         => 5,
                    'title'      => 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
                    'cover'      => 'https://alfaomegaportal.test/wp-content/uploads/2024/07/2-1.png',
                    'download'   => true,
                    'read'       => true,
                    'accessType' => 'purchase',
                    'status'     => 'active',
                    'addedAt'    => '2024-07-01',
                    'validUntil' => null,
                    'url'        => 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/',
                ],
                [
                    'id'         => 6,
                    'title'      => 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
                    'cover'      => 'https://alfaomegaportal.test/wp-content/uploads/2024/07/3-1.png',
                    'download'   => true,
                    'read'       => true,
                    'accessType' => 'purchase',
                    'status'     => 'active',
                    'addedAt'    => '2024-07-01',
                    'validUntil' => null,
                    'url'        => 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/',
                ],
            ],
            'meta' => [
                'total'   => 6,
                'page'    => 1,
                'perPage' => 8,
            ],
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];*/
    }
}
