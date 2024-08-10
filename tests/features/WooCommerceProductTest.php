<?php

namespace tests\features;

use AlfaomegaEbooks\Services\eBooks\Service;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\WordpressTest;

class WooCommerceProductTest extends WordpressTest
{
    /**
     * Test linking an ebook to a product
     *
     * @param int $postId
     * @param int $userId
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('productProvider')]
    public function testMassLinkEbook(int $postId, int $userId)
    {
        wp_set_current_user($userId);
        $result = Service::make()
            ->wooCommerce()
            ->linkEbook()
            ->batch([$postId]);

        $this->assertNotNull($result);
    }

    /**
     * Data provider for test_product_attributes
     * @return array[]
     */
    public static function productProvider(): array
    {
        return [
            'Informática para bachillerato'           => [
                'postId' => 34842,
                'userId' => 1,
            ],
            /*'Análisis y minería de textos con Python' => [
                'postId' => 32168,
                'userId' => 1,
            ],*/
        ];
    }
}
