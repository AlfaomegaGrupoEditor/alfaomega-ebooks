<?php

namespace AlfaomegaEbooks\Http\Controllers;

use AlfaomegaEbooks\Services\eBooks\Service;

class WebhooksController
{
    /**
     * Check API
     *
     * @param array $data
     *
     * @return array
     */
    public function generateCode(array $data = []): array
    {
        $code = 'generate';

        return [
            'status'  => 'success',
            'data'    => [
                'code' => $code,
            ],
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }
}
