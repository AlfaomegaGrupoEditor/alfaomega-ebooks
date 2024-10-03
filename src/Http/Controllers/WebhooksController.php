<?php

namespace AlfaomegaEbooks\Http\Controllers;

use AlfaomegaEbooks\Services\eBooks\Service;

class WebhooksController
{
    /**
     * Import access and generate the sample codes
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function generateCode(array $data = []): array
    {
        $service = Service::make()->ebooks()->samplePost();

        return [
            'status'  => 'success',
            'data'    => $data['type'] === 'single'
                ? $service->import($data)
                : $service->importBatch($data),
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }
}
