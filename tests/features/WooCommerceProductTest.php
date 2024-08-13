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
    public function testMassLinkEbook(int $postId,
                                      int $userId,
                                      bool $delete
    ): void {
        // delete the eBook
        if ($delete) {
            $product = wc_get_product($postId);
            $this->assertNotNull($product);
            $service = Service::make()->ebooks()->ebookPost();
            $eBook = $service->search(
                $product->get_sku(),
                'alfaomega_ebook_product_sku'
            );
            if ($eBook) {
                $success = $service->delete($eBook['id']);
                $this->assertTrue($success);
            }
        }

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
            'update' => [
                'postId' => 34842,
                'userId' => 1,
                'delete' => false,
            ],
            'create' => [
                'postId' => 34778,
                'userId' => 1,
                'delete' => true,
            ],
        ];
    }
}
