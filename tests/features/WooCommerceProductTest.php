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
     * @param bool $delete
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

    /**
     * Test updating prices
     *
     * @param string $factor
     * @param string $value
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('setupPriceProvider')]
    public function testSetupPrices(string $factor, string $value): void
    {
        $result = Service::make()
            ->wooCommerce()
            ->updatePrice()
            ->setFactor($factor, $value)
            ->batch();

        $this->assertNotNull($result);
    }

    /**
     * Data provider for testSetupPrices
     * @return array[]
     */
    public static function setupPriceProvider(): array
    {
        return [
            'price_update' => [
                'factor' => 'price_update',
                'value' => '1',
            ],
            'fixed_number' => [
                'factor' => 'fixed',
                'value' => '50',
            ],
            'percent' => [
                'factor' => 'percent',
                'value' => '3',
            ],
            'page_count' => [
                'factor' => 'page_count',
                'value' => '10',
            ],
        ];
    }

    /**
     * Test updating product price
     *
     * @param array $data
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('productPriceProvider')]
    public function testUpdateProductPrice(array $data): void
    {
        $productId = Service::make()
            ->wooCommerce()
            ->product()
            ->updatePrice($data);

        $this->assertNotNull($productId);
    }

    /**
     * Data provider for testUpdateProductPrice
     * @return array[]
     */
    public static function productPriceProvider(): array
    {
        return [
            'price_update' => [
                'data' => [
                    'id'                        => 27976,
                    'printed_isbn'              => '9786076221129',
                    'ebook_isbn'                => '9786076225738',
                    'title'                     => 'CUANDO LAS PERSONAS SON EL CENTRO - Cómo abordar la gestión de RR.HH. sin medios',
                    'page_count'                => 276,
                    'factor'                    => 'price_update',
                    'value'                     => 1,
                    'current_price'             => 79,
                    'new_regular_price'         => 100,
                    'new_regular_digital_price' => 80,
                    'new_regular_combo_price'   => 120,
                    'new_sales_price'           => '',
                    'new_sales_digital_price'   => '',
                    'new_sales_combo_price'     => '',
                ],
            ],
        ];
    }
}
