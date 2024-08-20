<?php

namespace tests\features;

use AlfaomegaEbooks\Services\eBooks\Service;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\WordpressTest;

class AlfaomegaQueueTest extends WordpressTest
{
    /**
     * Test import queue status
     *
     * @param string $queue
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('queueProvider')]
    public function testImportQueueStatus(string $queue)
    {
        $response = Service::make()
            ->queue()
            ->status($queue);

        $this->assertNotNull($response);
    }

    /**
     * @return array[]
     */
    public static function queueProvider(): array
    {
        return [
            'import'  => ['queue' => 'alfaomega_ebooks_queue_import'],
            'refresh' => ['queue' => 'alfaomega_ebooks_queue_refresh'],
        ];
    }
}
