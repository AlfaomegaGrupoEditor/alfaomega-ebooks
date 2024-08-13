<?php

namespace tests\features;

use AlfaomegaEbooks\Services\eBooks\Service;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\WordpressTest;

class WooCommerceEbookTest extends WordpressTest
{
    /**
     * Test update ebook metadata
     *
     * @param int $postId
     * @param int $userId
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('ebookProvider')]
    public function testMassUpdateMeta(int $postId, int $userId)
    {
        wp_set_current_user($userId);
        $result = Service::make()
            ->ebooks()
            ->refreshEbook()
            ->batch([$postId]);

        $this->assertNotNull($result);
    }

    /**
     * Data provider for test_product_attributes
     * @return array[]
     */
    public static function ebookProvider(): array
    {
        return [
            'LEAN MANUFACTURING STEP BY STEP'           => [
                'postId' => 34896,
                'userId' => 1,
            ],
            /*'Análisis y minería de textos con Python' => [
                'postId' => 32168,
                'userId' => 1,
            ],*/
        ];
    }
}
