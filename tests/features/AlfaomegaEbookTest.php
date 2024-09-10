<?php

namespace tests\features;

use AlfaomegaEbooks\Http\RouteManager;
use AlfaomegaEbooks\Services\eBooks\Service;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\WordpressTest;

class AlfaomegaEbookTest extends WordpressTest
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
    public function testUpdateMetaAction(int $postId, int $userId)
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
     * Test update ebook metadata
     *
     * @param int $postId
     * @param int $userId
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('ebookProvider')]
    public function testLinkAction(int $postId, int $userId)
    {
        // call the method to test
        wp_set_current_user($userId);
        $result = Service::make()
            ->ebooks()
            ->importEbook()
            ->batch([$postId]);

        $this->assertNotNull($result);
    }

    /**
     * Test import ebooks
     *
     * @return void
     * @throws \Exception
     */
    public function testImportEbooks()
    {
        $result = Service::make()
            ->ebooks()
            ->importEbook()
            ->batch();

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
                'postId' => 34900,
                'userId' => 1,
            ],
            /*'Análisis y minería de textos con Python' => [
                'postId' => 32168,
                'userId' => 1,
            ],*/
        ];
    }

    /**
     * Test search ebooks
     *
     * @param string $query
     * @param array $expected
     *
     * @return void
     */
    #[DataProvider('searchProvider')]
    public function testSearchEbooks(string $query = 'python', array $expected = []): void
    {
        $apiUrl =  esc_url(rest_url(RouteManager::ROUTE_NAMESPACE));
        $response = wp_remote_get("{$apiUrl}/search-ebooks", [
            'headers' => [
                'X-WP-Nonce' => wp_create_nonce('wp_rest'),
            ],
            'sslverify' => false,
            'body' => [
                'query' => $query,
                'limit' => 50,
                'page'  => 1,
            ],
        ]);
        
        
        $this->assertNotNull($response);
    }
    
    /**
     * Data provider for test_product_attributes
     * @return array[]
     */
    public static function searchProvider(): array
    {
        return [
            [ 'query' => 'python', 'expected' => [ 'count' => 1 ] ],
        ];
    }
    
}
