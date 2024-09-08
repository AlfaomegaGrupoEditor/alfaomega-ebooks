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
     * @param int $post_id
     * @param array $payload
     * @return void
     * @throws \Exception
     */
    #[DataProvider('ebookProvider')]
    public function testCreateAccess(?int $post_id, array $payload): void {

        $accessPost = Service::make()
            ->ebooks()
            ->accessPost()
            ->updateOrCreate($post_id, $payload);

        $this->assertNotNull($accessPost);
    }

    /**
     * Get access post.
     *
     * @param int $post_id
     * @return void
     * @throws \Exception
     */
    #[DataProvider('accessProvider')]
    public function testGetAccessPost(int $post_id): void {

        $accessPost = Service::make()
            ->ebooks()
            ->accessPost()
            ->get($post_id);

        $this->assertNotNull($accessPost);
    }

    /**
     * Order complete access.
     *
     * @param int $order_id
     * @param int $user_id
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('orderProvider')]
    public function testOrderCompleteAccess(int $order_id, int $user_id): void {

        $user = wp_set_current_user($user_id);
        $this->assertNotNull($user);

        $orders = Service::make()
            ->wooCommerce()
            ->order()
            ->onComplete($order_id);

        $this->assertNotNull($orders);
    }

    /**
     * Data provider
     * @return array[]
     */
    public static function ebookProvider(): array
    {
        return [
            'purchase' => [
                'post_id' => 35031, // null,
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

    /**
     * Data provider
     * @return array[]
     */
    public static function accessProvider(): array
    {
        return [
            'purchase' => [ 'post_id' => 35031 ],
        ];
    }

    /**
     * Order provider
     * @return array[]
     */
    public static function orderProvider(): array
    {
        return [
            'purchase' => [
                'order_id' => 35141,
                'user_id'  => 10001, //1630
            ],
        ];
    }
}