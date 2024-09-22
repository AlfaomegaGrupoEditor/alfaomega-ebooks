<?php

namespace AlfaomegaEbooks\Http\Controllers;

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
}
