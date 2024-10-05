<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class WordpressTest extends TestCase
{
    protected function setUp(): void
    {
        // @see https://macarthur.me/posts/simpler-unit-testing-for-wordpress
    }

    protected function tearDown(): void
    {

    }

    /**
     * Test if WordPress is loaded
     *
     * @return void
     */
    public function test_wp_loaded(): void
    {
        $loaded = defined('ABSPATH');
        $this->assertTrue($loaded,'WordPress is not loaded');
    }

    /**
     * Send a json request to the given URL
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return \WP_REST_Response
     */
    public function jsonRequest(string $method, string $url, array $data = [], array $headers = []): \WP_REST_Response
    {
        if ($method === 'GET' && !empty($data)) {
            $url = add_query_arg($data, $url);
        }

        $request = new \WP_REST_Request($method, $url);

        if ($method !== 'GET') {
            $request->set_body(json_encode($data));
        }

        $request->set_header('Content-Type', 'application/json');
        foreach ($headers as $key => $value) {
            $request->set_header($key, $value);
        }

        $server = rest_get_server();
        return $server->dispatch($request);
    }
}
