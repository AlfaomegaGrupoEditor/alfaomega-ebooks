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
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('productProvider')]
    public function test_link_ebook(int $postId)
    {
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
            'Análisis y minería de textos con Python' => [ 32168 ],
        ];
    }
}
