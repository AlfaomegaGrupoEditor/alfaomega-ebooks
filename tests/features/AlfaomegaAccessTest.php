<?php

namespace tests\features;

use AlfaomegaEbooks\Services\eBooks\Service;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\WordpressTest;

class AlfaomegaAccessTest extends WordpressTest
{
    /**
     * Create access post.
     *
     * @param int $postId
     * @param array $payload
     * @return void
     * @throws \Exception
     */
    #[DataProvider('ebookProvider')]
    public function testCreateAccess(int $postId, array $payload): void {

        $accessPost = Service::make()
            ->ebooks()
            ->accessPost()
            ->updateOrCreate($postId, $payload);

        $this->assertNotNull($accessPost);
    }

    /**
     * Data provider for test_product_attributes
     * @return array[]
     */
    public static function ebookProvider(): array
    {
        return [
            'purchase' => [
                'post_id' => null,
                'payload' => [
                    'ebook_id' => 34968, // post_parent, post_title, post_content, post_category, thumbnail, isbn
                    'user_id'  => 10001, // post_author
                    'access'   => [
                        'type'     => 'purchase', // sample, import, purchase
                        'order_id' => 34881,      // purchase order id
                        // 'sample_id'   => null,       // sample id
                        // 'status'      => 'active',   // created, active, pas-due, inactive
                        // 'read'        => 1,          // 0, 1
                        // 'download'    => 1,          // 0, 1
                        // 'due_date'    => null,       // Y-m-d H:i:s
                        // 'download_at' => null,       // Y-m-d H:i:s
                        // 'read_at'     => null,       // Y-m-d H:i:s
                    ],
                ],
            ],
        ];
    }
}
