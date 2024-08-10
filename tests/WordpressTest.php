<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class WordpressTest extends TestCase
{
    protected function setUp(): void
    {

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
}
