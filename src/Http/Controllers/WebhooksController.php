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
        if (!is_user_logged_in()) {
            wp_set_current_user(1);
        }

        return [
            'status'  => 'success',
            'data'    => $service->import($data),
            'message' => esc_html__('God Job!', 'alfaomega-ebooks'),
        ];
    }
}
