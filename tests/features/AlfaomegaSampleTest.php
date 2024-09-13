<?php

namespace tests\features;

use AlfaomegaEbooks\Services\eBooks\Service;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\WordpressTest;

class AlfaomegaSampleTest extends WordpressTest
{
    /**
     * Create sample post.
     *
     * @param int $post_id
     * @param array $payload
     * @return void
     * @throws \Exception
     */
    #[DataProvider('sampleProvider')]
    public function testCreateAccess(?int $post_id, array $payload): void
    {
        wp_set_current_user(1);
        $accessPost = Service::make()
            ->ebooks()
            ->samplePost()
            ->updateOrCreate($post_id, $payload);

        $this->assertNotNull($accessPost);
    }

    /**
     * Test code redemption.
     *
     * @param string $code
     * @param int $user_id
     * @param $expected
     *
     * @return void
     */
    #[DataProvider('redeemProvider')]
    public function testRedeemCode(string $code, int $user_id, $expected): void
    {
        try {
            wp_set_current_user($user_id);
            $access = Service::make()
                ->ebooks()
                ->samplePost()
                ->redeem($code);

            $this->assertNotNull($access);
        } catch (\Exception $e) {
            $this->assertEquals(0, $expected);
        }
    }
    /**
     * Data provider
     * @return array[]
     */
    public static function sampleProvider(): array
    {
        return [
            'single' => [
                'post_id' => null,
                'payload' => [
                    'destination' => 'livan2r+SMP001@gmail.com',   // Email address to send the samples
                    'promoter'    => 'livan2r+SMP002@gmail.com',   // Email address to sean a copy of the samples
                    'description' => '3 dias de acceso a 9786076220306',
                    'payload'     => [
                        [
                            'isbn'        => '9786076220306', // eBook ISBN
                            // 3, 7, 15, 30, 60, 180, 365, 0 (unlimited)
                            'access_time' => 3,    // Days available to access the eBook after activation
                            'read'        => true, // Allow to read the eBook
                            'download'    => true, // Allow to download the eBook
                        ],
                    ],
                    'due_date'    => '2024-11-04',    // Valid until this date
                    'count'       => 1,               // Number of samples to generate
                ],
            ],
            'multiple' => [
                'post_id' => null,
                'payload' => [
                    'destination' => 'livan2r+SMP001@gmail.com',   // Email address to send the samples
                    'promoter'    => 'livan2r+SMP002@gmail.com',   // Email address to sean a copy of the samples
                    'description' => 'Acceso a 9786076224663 y 9786077079507',
                    'payload'     => [
                        [
                            'isbn'        => '9786076224663', // eBook ISBN
                            // 3, 7, 15, 30, 60, 180, 365, 0 (unlimited)
                            'access_time' => 3, // Days available to access the eBook after activation
                            'read'        => true, // Allow to read the eBook
                            'download'    => true, // Allow to download the eBook
                        ],
                        [
                            'isbn'        => '9786077079507', // eBook ISBN
                            // 3, 7, 15, 30, 60, 180, 365, 0 (unlimited)
                            'access_time' => 0, // Days available to access the eBook after activation
                            'read'        => true, // Allow to read the eBook
                            'download'    => false, // Allow to download the eBook
                        ],
                    ],
                    'due_date'    => '2024-11-04',    // Valid until this date
                    'count'       => 1,               // Number of samples to generate
                ],
            ],
        ];
    }

    /**
     * Data provider
     * @return array[]
     */
    public static function redeemProvider(): array
    {
        return [
            'wrong'   => ['code' => '0000-0000-0000', 'user_id' => 1, 'expected' => 0],
            'valid'   => ['code' => '7RWI-WAUO-PW85', 'user_id' => 1, 'expected' => 1],
            'update'  => ['code' => '7RWI-WAUO-PW85', 'user_id' => 1, 'expected' => 1],
            'expired' => ['code' => '7RWI-WAUO-PW85', 'user_id' => 1, 'expected' => 1],
            'exists'  => ['code' => '7RWI-WAUO-PW85', 'user_id' => 1, 'expected' => 1],
        ];
    }

    /**
     * Test send sample by email.
     *
     * @param int $postId
     *
     * @return void
     * @throws \Exception
     */
    #[DataProvider('sampleEmailProvider')]
    public function testSendSampleByEmail(int $postId): void
    {
        Service::make()
            ->ebooks()
            ->samplePost()
            ->email($postId);

        $this->assertTrue(true);
    }

    /**
     * Data provider
     * @return array[]
     */
    public static function sampleEmailProvider(): array
    {
        return [
            ['postId' => 35676]
        ];
    }
}
