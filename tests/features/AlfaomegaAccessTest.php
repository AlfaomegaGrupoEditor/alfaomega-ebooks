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

    /**
     * Load catalog.
     *
     * @param int $userId
     * @param array $results
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('catalogProvider')]
    public function testLoadCatalog(int $userId = 1, array $results = null): void
    {
        // TODO: refactor load catalog to return the structure ready to pass to tree view component
        wp_set_current_user($userId);
        $result = Service::make()
            ->ebooks()
            ->accessPost()
            ->catalog($results);

        $this->assertNotNull($result);
    }

    public static function catalogProvider(): array
    {
        return [
            'catalog' => [
                'userId'   => 1,
                'results' => json_decode('[
                  {
                    "term_id": 124,
                    "slug": "administracion-y-gestion",
                    "name": "Administración y gestión",
                    "parent": 188,
                    "book_count": 1
                  },
                  {
                    "term_id": 211,
                    "slug": "bases-y-estructuras-de-datos",
                    "name": "Bases y estructuras de datos",
                    "parent": 115,
                    "book_count": 1
                  },
                  {
                    "term_id": 188,
                    "slug": "ciencias-economicas",
                    "name": "Ciencias Económicas",
                    "parent": 111,
                    "book_count": 1
                  },
                  {
                    "term_id": 208,
                    "slug": "contabilidad-y-finanzas",
                    "name": "Contabilidad y finanzas",
                    "parent": 188,
                    "book_count": 2
                  },
                  {
                    "term_id": 212,
                    "slug": "diseno-por-computadora",
                    "name": "Diseño por computadora",
                    "parent": 115,
                    "book_count": 1
                  },
                  {
                    "term_id": 140,
                    "slug": "electronica",
                    "name": "Eléctrica / Electrónica",
                    "parent": 113,
                    "book_count": 1
                  },
                  {
                    "term_id": 143,
                    "slug": "ingenieria-industrial",
                    "name": "Industrial",
                    "parent": 113,
                    "book_count": 1
                  },
                  {
                    "term_id": 213,
                    "slug": "ingenieria-de-software",
                    "name": "Ingeniería de Software",
                    "parent": 115,
                    "book_count": 1
                  },
                  {
                    "term_id": 179,
                    "slug": "marketing",
                    "name": "Marketing",
                    "parent": 188,
                    "book_count": 1
                  },
                  {
                    "term_id": 223,
                    "slug": "otros-educacion-y-psicologia",
                    "name": "Otros",
                    "parent": 189,
                    "book_count": 1
                  },
                  {
                    "term_id": 215,
                    "slug": "paqueteria",
                    "name": "Paquetería",
                    "parent": 115,
                    "book_count": 1
                  },
                  {
                    "term_id": 111,
                    "slug": "profesional",
                    "name": "Profesional",
                    "parent": 0,
                    "book_count": 1
                  }
                ]'),
            ],
        ];
    }

    /**
     * Consolidate user catalog.
     *
     * @param int $userId
     * @param array $expected
     *
     * @return void
     * @throws \Exception
     */
    public function testConsolidateCatalog (int $userId = 1, array $expected = []): void
    {
        wp_set_current_user($userId);
        $result = Service::make()->ebooks()
            ->accessPost()
            ->consolidateSamples();

        $this->assertTrue($result);
    }

    /**
     * Generate code webhook.
     *
     * @param array $data
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('webhookProvider')]
    public function testGenerateCodeWebhook(array $data = []): void
    {
        $endpoint = get_site_url(). '/alfaomega-ebooks/webhook/generate-code';

        $response = $this->jsonRequest('POST', $endpoint, $data, [
            'AO-TOKEN' => WEBHOOK_TOKEN
        ]);

        $this->assertEquals(200, $response->get_status());
        $data = $response->get_data();
        $this->assertEquals('success', $data['status']);
    }

    /**
     * Webhook provider
     * @return array[]
     */
    public static function webhookProvider(): array
    {
        return [
            'livan2r' => [
                'data' => [
                    "json_file" => "15_livan2r_gmail_com.json",
                    "folder"    => "\/testing\/",
                    "books"     => [
                        [
                            "isbn"     => "9786075381862",
                            "read"     => true,
                            "download" => false,
                            "due_date" => null
                        ],
                        [
                            "isbn"     => "9786075386577",
                            "read"     => true,
                            "download" => false,
                            "due_date" => "2024-12-31"
                        ],
                        [
                            "isbn"     => "PENISBN1600",
                            "read"     => true,
                            "download" => true,
                            "due_date" => null
                        ],
                    ],
                ],
            ]
        ];
    }
}
