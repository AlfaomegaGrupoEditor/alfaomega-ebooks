<?php

namespace tests\features;

use AlfaomegaEbooks\Services\eBooks\Service;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\WordpressTest;

class AlfaomegaAccessTest extends WordpressTest
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
    public function testCreateAccess(int $postId, int $userId)
    {
        // modify the post
        $postId = wp_insert_post([
            'ID'           => $postId,
            'post_title'   => 'Test Product',
            'post_content' => 'Test Product Content',
            'post_status'  => 'publish',
            'post_author'  => $userId,
            'post_type'    => 'alfaomega-ebook',
        ]);
        Service::make()
            ->ebooks()
            ->ebookPost()
            ->save($postId, [
                'printed_isbn' => ''
            ]);

        // call the method to test
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
            'purchase' => [
                'post_id'  => null, // post_id,'
                'ebook_id' => 34900, // post_parent, post_title, post_content, post_category, thumbnail, isbn
                'user_id'  => 1,     // post_author
                'access'   => [
                    'type'        => 'purchase', // sample, import, purchase
                    'order_id'    => 1,          // purchase order id
                    'sample_id'   => null,       // sample id
                    'status'      => 'active',   // created, active, pas-due, inactive
                    'read'        => 1,          // 0, 1
                    'download'    => 1,          // 0, 1
                    'due_date'    => null,       // Y-m-d H:i:s
                    'download_at' => null,       // Y-m-d H:i:s
                    'read_at'     => null,       // Y-m-d H:i:s
                ],
            ],
        ];
    }
}
