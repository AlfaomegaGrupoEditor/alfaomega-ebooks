<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\AbstractEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Exception;
use WP_Query;

class SamplePost extends AlfaomegaPostAbstract implements AlfaomegaPostInterface
{
    protected S3Client $client;

    /**
     * The bucket name.
     *
     * @var string
     */
    protected string $bucked = 'alfaomega-codes';

    /**
     * Make a new instance of the class.
     *
     * @return self The new instance.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * The SamplePost constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = new S3Client([
            'version'                 => 'latest',
            'region'                  => 'nyc3',
            'endpoint'                => 'https://nyc3.digitaloceanspaces.com',
            'credentials'             => [
                'key'    => AWS_S3_ACCESS_KEY,
                'secret' => AWS_S3_SECRECT_KEY,
            ],
        ]);
    }

    /**
     * Get the metadata of a specific post or the current metadata.
     * This method is used to get the metadata of a specific post or the current metadata if no post ID is provided.
     * If a post ID is provided, it fetches the post and its metadata, assigns them to the $meta property, and returns
     * the $meta. If no post ID is provided, it simply returns the current $meta. If the post does not exist, it throws
     * an exception.
     *
     * @param int|null $postId The ID of the post. Default is null.
     *
     * @return array The metadata of the post or the current metadata.
     * @throws Exception If the post does not exist.
     */
    public function get(int $postId = null): array
    {
        if (empty($postId)) {
            return $this->meta;
        }

        $post = get_post($postId);
        if (empty($post)) {
            throw new Exception("Post $postId not found");
        }

        $description = get_post_meta($postId, 'alfaomega_sample_description', true);
        $description = !empty($description) ? $description : __('Sample code', 'alfaomega-ebooks');

        $destination = get_post_meta($postId, 'alfaomega_sample_destination', true);
        $destination = !empty($destination) ? $destination : '';

        $promoter = get_post_meta($postId, 'alfaomega_sample_promoter', true);
        $promoter = !empty($promoter) ? $promoter : '';

        $status = get_post_meta($postId, 'alfaomega_sample_status', true);
        $status = !empty($status) ? $status : 'created';

        $type = get_post_meta($postId, 'alfaomega_sample_type', true);
        $type = !empty($type) ? $type : 'sample';

        $jsonFile = get_post_meta($postId, 'alfaomega_sample_json_file', true);
        $jsonFile = !empty($jsonFile) ? $jsonFile : '';

        $payloadJson = get_post_meta($postId, 'alfaomega_sample_payload', true);
        $payload = json_decode($payloadJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $payload = [];
        } else {
            $service = Service::make()->ebooks()->eBookPost();
            foreach ($payload as &$item) {
                $eBookPost = $service->search($item['isbn']);
                $item['title'] = !empty($eBookPost) ? $eBookPost['title'] : '';
                $item['cover'] = !empty($eBookPost) ? $eBookPost['cover'] : '';
                $item['author'] = !empty($eBookPost) ? $eBookPost['author'] : '';
                $item['category'] = !empty($eBookPost) ? $eBookPost['isbn'] : '';
                $item['year'] = !empty($eBookPost) ? $eBookPost['year'] : '';
                $item['access_time_desc'] = match ($item['access_time']) {
                    '3'   => sprintf(__('%s days', 'alfaomega-ebooks'), 3),
                    '7'   => sprintf(__('%s week', 'alfaomega-ebooks'), 1),
                    '15'  => sprintf(__('%s weeks', 'alfaomega-ebooks'), 2),
                    '30'  => sprintf(__('%s month', 'alfaomega-ebooks'), 1),
                    '60'  => sprintf(__('%s months', 'alfaomega-ebooks'), 2),
                    '180' => sprintf(__('%s months', 'alfaomega-ebooks'), 6),
                    '365' => sprintf(__('%s year', 'alfaomega-ebooks'), 1),
                    '0'   => __('Unlimited', 'alfaomega-ebooks'),
                    default => sprintf(__('%s days', 'alfaomega-ebooks'), $item['access_time']),
                };
                $item['details'] = !empty($eBookPost) ? $eBookPost['details'] : '';
            }
        }

        $dueDate = get_post_meta($postId, 'alfaomega_sample_due_date', true);
        $dueDate = !empty($dueDate) ? Carbon::parse($dueDate) : '';

        $activatedAt = get_post_meta($postId, 'alfaomega_sample_activated_at', true);
        $activatedAt = !empty($activatedAt) ? Carbon::parse($activatedAt) : '';

        $this->meta = [
            'id'           => $postId,
            'code'         => $post->post_title,
            'description'  => $description,
            'author'       => $post->post_author,
            'destination'  => $destination,
            'promoter'     => $promoter,
            'status'       => $status,
            'type'         => $type,
            'json_file'    => $jsonFile,
            'payload'      => $payload,
            'due_date'     => $dueDate,
            'activated_at' => $activatedAt,
        ];

        return $this->meta;
    }

    /**
     * Find a post by its code.
     * This method finds a post by its code and returns the metadata of the post.
     * If the post is not found, it returns an empty array.
     *
     * @param string $code The code of the post to find.
     *
     * @return array The metadata of the post.
     * @throws \Exception
     */
    public function find(string $code): ?array
    {
        $query = new WP_Query([
            'post_type'   => 'alfaomega-sample',
            'title'       => $code,
            'numberposts' => 1,
        ]);

        if (!$query->have_posts()) {
            return null;
        }

        $query->the_post();
        return $this->get(get_the_ID());
    }

    /**
     * Updates or creates a new eBook post.
     *
     * This method updates an existing eBook post or creates a new one if it doesn't exist.
     * It uses the provided eBook data to set the post title, content, status, author, and type.
     * It also saves the post metadata.
     *
     * @param int|null $postId The ID of the post to update. If null, a new post is created.
     * @param array $data An associative array containing the eBook data.
     *
     * @return array Returns an associative array containing the updated post metadata.
     * @throws Exception Throws an exception if unable to create post.
     */
    public function updateOrCreate(?int $postId, array $data): array
    {
        if (!empty($postId)) {
            $post = get_post($postId);
            if (empty($post)) {
                throw new Exception(esc_html__('Post not found.', 'alfaomega-ebook'));
            }
        }

        if (empty($data['count'])) {
            $data['count'] = 1;
        }

        $result = [];
        $accessCodes = [];
        $description = $data['description'];
        $current_user = wp_get_current_user();

        foreach (range(1, $data['count']) as $i) {
            $data['code'] = $this->generateCode();

            $newPost = [
                'post_title'  => $data['code'],
                'post_status' => 'publish',
                'post_author' => $current_user ? $current_user->ID : 1,
                'post_type'   => 'alfaomega-sample',
            ];

            $postId = wp_insert_post($newPost);
            if (empty($postId)) {
                throw new Exception(esc_html__('Unable to create post.', 'alfaomega-ebook'));
            }

            if ($data['count'] > 1) $data['description'] = $description . " " . $i . "/" . $data['count'];
            $result[] = $this->save($postId, $data);

            // send email to destination and promoter after creating the post
            if (!empty($data['destination']) || !empty($data['promoter'])) {
                $data['status'] = 'sent';
                $accessCodes[] = $data['code'];
                $this->save($postId, $data);
            }
        }

        $this->email($postId, $accessCodes);

        return count($result) === 1 ? $result[0] : $result;
    }

    /**
     * Saves the post metadata.
     * This method saves the metadata for a post of type 'alfaomega-ebook'.
     * It sanitizes the new values before saving them and uses default values if the new values are empty.
     * It also publishes the post and retrieves the updated post metadata.
     *
     * @param int $postId The ID of the post to save metadata for.
     * @param array $data An associative array containing the new metadata values.
     *
     * @return array Returns an associative array containing the updated post metadata.
     * @throws \Exception
     */
    public function save(int $postId, array $data): array
    {
        $fields = [
            'alfaomega_sample_description' => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_description', true),
                'new'     => $data['description'],
                'default' => __('Sample code', 'alfaomega-ebooks'),
            ],
            'alfaomega_sample_destination' => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_destination', true),
                'new'     => $data['destination'],
                'default' => '',
            ],
            'alfaomega_sample_promoter' => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_promoter', true),
                'new'     => $data['promoter'],
                'default' => '',
            ],
            'alfaomega_sample_status'   => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_status', true),
                'new'     => $data['status'],
                'default' => 'created', // created, redeemed, expired, failed
            ],
            'alfaomega_sample_type'   => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_type', true),
                'new'     => $data['type'],
                'default' => 'sample', // sample, import
            ],
            'alfaomega_sample_json_file'   => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_json_file', true),
                'new'     => $data['json_file'],
                'default' => '',
            ],
            'alfaomega_sample_payload'   => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_payload', true),
                'new'     => is_string($data['payload']) ? $data['payload'] : json_encode($data['payload']),
                'default' => '',
            ],
            'alfaomega_sample_due_date'   => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_due_date', true),
                'new'     => is_object($data['due_date']) ? $data['due_date']->toDateTimeString() : $data['due_date'],
                'default' => '',
            ],
            'alfaomega_sample_activated_at'  => [
                'old'     => get_post_meta($postId, 'alfaomega_sample_activated_at', true),
                'new'     => is_object($data['activated_at']) ? $data['activated_at']->toDateTimeString() : $data['activated_at'],
                'default' => '',
            ],
        ];

        wp_publish_post($postId);
        foreach ($fields as $field => $data) {
            $new_value = sanitize_text_field($data['new']);
            $old_value = $data['old'];

            if (empty($new_value)) {
                $new_value = $data['default'];
            }

            update_post_meta($postId, $field, $new_value, $old_value);
        }

        return $this->get($postId);
    }

    /**
     * Generate a code.
     *
     * @return string The generated code.
     */
    public function generateCode(int $groups = 3, int $size = 4): string
    {
        $code = [];
        foreach (range(1, $groups) as $item) {
            $code[] = wp_generate_password($size, false);
        }

        $result = join('-', $code);
        return $this->codeExists($result)
            ? $this->generateCode($groups, $size)
            : $result;
    }

    /**
     * Redeem a code and create access posts.
     *
     * This method redeems a code by checking its validity and updating the status of the associated post.
     * It throws exceptions if the code is not found, already redeemed, or expired.
     * If the code is valid, it creates access posts for the eBooks in the payload and updates the status to 'redeemed'.
     *
     * @param string $code The code to redeem.
     *
     * @return array The IDs of the redeemed eBooks.
     * @throws Exception If the code is not found, already redeemed, or expired.
     */
    public function redeem(string $code): array
    {
        $query = new WP_Query([
            'post_type'   => 'alfaomega-sample',
            'title'       => $code,
            'numberposts' => 1,
        ]);

        if (!$query->have_posts()) {
            throw new Exception(esc_html__('Code not found.', 'alfaomega-ebooks'));
        }

        // check if the code is valid and not redeemed
        $query->the_post();
        $postId = get_the_ID();
        $samplePost = $this->get($postId);
        if (!in_array($samplePost['status'], ['created', 'sent'])) {
            match ($samplePost['status']) {
                'failed' => throw new Exception(esc_html__('This code is not working anymore.', 'alfaomega-ebooks')),
                'redeemed' => throw new Exception(esc_html__('Code already redeemed.', 'alfaomega-ebooks')),
                'expired' => throw new Exception(esc_html__('Code expired.', 'alfaomega-ebooks')),
            };
        } elseif (!empty($samplePost['due_date']) && $samplePost['due_date'] < Carbon::now()) {
            $this->expire($postId);
            throw new Exception(esc_html__('Code expired.', 'alfaomega-ebooks'));
        }

        $redeemed = [];
        $user = wp_get_current_user();
        foreach ($samplePost['payload'] as $payload) {
            $eBookPost = Service::make()->ebooks()
                ->eBookPost()
                ->search($payload['isbn']);
            if (empty($eBookPost)) {
                continue;
            }

            $accessPost = Service::make()
                ->ebooks()
                ->accessPost()
                ->updateOrCreate(null, [
                    'ebook_id' => $eBookPost['id'],
                    'user_id'  => $user->ID,
                    'access'   => [
                        'type'      => 'sample',
                        'sample_id' => $samplePost['id'],
                        'status'    => 'created',
                        'read'      => $payload['read'] ?? 0,
                        'download'  => $payload['download'] ?? 0,
                        'due_date'  => $payload['access_time'] === 0 ? 0
                            : Carbon::now()->addDays($payload['access_time']),
                    ],
                ]);
            if (!empty($accessPost)) {
                $redeemed[] = $eBookPost['id'];
            }
        }

        // update the code status in S3 too
        if ($samplePost['type'] === 'import' && !empty($jsonFile = $samplePost['json_file'])) {
            try {
                if ($this->client->doesObjectExist($this->bucked, $jsonFile)) {
                    $response = $this->client->getObject([
                        'Bucket' => $this->bucked,
                        'Key'    => $jsonFile,
                    ]);
                    $jsonContent = json_decode($response['Body'], true);
                    $status = count($redeemed) === 0 ? 'failed' : 'redeemed';
                    if ($jsonContent['code'] === $code) {
                        $jsonContent['status'] = $status;
                        if ($status === 'redeemed') {
                            $jsonContent['redeemed'] = [
                                'date'    => Carbon::now()->toDateTimeString(),
                                'user'    => $user->user_email,
                                'website' => get_site_url(),
                            ];
                        }
                        $this->client->putObject([
                            'Bucket'       => $this->bucked,
                            'Key'          => $jsonFile,
                            'Body'         => json_encode($jsonContent),
                            'CacheControl' => 'no-cache',
                            'ACL'          => 'private',
                        ]);
                    }
                }
            } catch (Exception $e) {
                // do nothing
            }
        }

        if (count($redeemed) === 0) {
            $this->failed($postId);
            throw new Exception(esc_html__('Code redeem failed.', 'alfaomega-ebooks'));
        } else {
            $this->redeemed($postId);
        }

        Service::make()->ebooks()
            ->accessPost()
            ->consolidateSamples();

        Service::make()->ebooks()
            ->accessPost()
            ->clearCustomerCache();

        return $redeemed;
    }

    /**
     * Check if the code exists.
     *
     * @param string $code The code to check.
     *
     * @return bool Returns true if the code exists, false otherwise.
     */
    public function codeExists(string $code): bool
    {
        $query = new WP_Query([
            'post_type'   => 'alfaomega-sample',
            's'           => $code,
        ]);

        return $query->have_posts();
    }

    /**
     * Generate the sample code payload.
     * @param array $payload
     *
     * @return array
     */
    public function generate(array $payload): array
    {
        $result = [];
        foreach ($payload as $item) {
            $result[] = [
                'isbn'        => $item['isbn'],
                'read'        => $item['read'] ?? 0,
                'download'    => $item['download'] ?? 0,
                'access_time' => $item['access_time'] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Send to code to recipients.
     *
     * @param int $postId The ID of the post.
     * @param array $accessCodes
     *
     * @return bool
     * @throws \Exception
     */
    public function email(int $postId, array $accessCodes = []): bool
    {
        $mailer = WC()->mailer();
        $mails = $mailer->get_emails();
        $email = $mails['Alfaomega_Ebooks_Sample_Email'];
        return $email->trigger($this->get($postId), $accessCodes);
    }

    /**
     * Expire a sample code.
     *
     * @param int $postId The ID of the post.
     *
     */
    public function expire(int $postId): void
    {
        update_post_meta(
            $postId,
            'alfaomega_sample_status',
            'expired',
            get_post_meta($postId, 'alfaomega_sample_status', true)
        );
    }

    /**
     * Mark as failed the sample code.
     *
     * @param int $postId The ID of the post.
     *
     */
    public function failed(int $postId): void
    {
        update_post_meta(
            $postId,
            'alfaomega_sample_status',
            'failed',
            get_post_meta($postId, 'alfaomega_sample_status', true)
        );
    }

    /**
     * Mark as redeemed the sample code.
     *
     * @param int $postId The ID of the post.
     *
     */
    public function redeemed(int $postId): void
    {
        update_post_meta(
            $postId,
            'alfaomega_sample_status',
            'redeemed',
            get_post_meta($postId, 'alfaomega_sample_status', true)
        );
        update_post_meta(
            $postId,
            'alfaomega_sample_activated_at',
            Carbon::now()->toDateTimeString(),
            get_post_meta($postId, 'alfaomega_sample_activated_at', true)
        );
    }

    /**
     * Import sample codes.
     *
     * @param array $dataCollection The sample codes to import.
     *
     * @return array The imported sample codes.
     * @throws \Exception
     */
    public function import(array $dataCollection): array
    {
        $result = [];
        foreach ($dataCollection as $data) {
            if (empty($data['json_file'])) {
                throw new Exception(esc_html__('Invalid JSON file.', 'alfaomega-ebooks'));
            }

            if (empty($data['folder'])
                || !in_array($data['folder'], ['testing', 'mexico', 'argentina', 'ferias', 'spain'])) {
                throw new Exception(esc_html__('Invalid folder.', 'alfaomega-ebooks'));
            }

            if (empty($data['customer'])) {
                throw new Exception(esc_html__('Customer is required.', 'alfaomega-ebooks'));
            }

            if (empty($data['email'])) {
                throw new Exception(esc_html__('Email is required.', 'alfaomega-ebooks'));
            }

            if (empty($data['store'])) {
                throw new Exception(esc_html__('Store is required.', 'alfaomega-ebooks'));
            }

            if (empty($data['books'])) {
                throw new Exception(esc_html__('No books found.', 'alfaomega-ebooks'));
            }

            $date = Carbon::now()->toDateTimeString();

            // get the json file from S3, if exists
            $filename = "{$data['folder']}/{$data['json_file']}";
            if ($this->client->doesObjectExist($this->bucked, $filename)) {
                $response = $this->client->getObject([
                    'Bucket' => $this->bucked,
                    'Key'    => $filename,
                ]);
                $jsonContent = json_decode($response['Body'], true);
            } else {
                $jsonContent = [];
            }

            $post = null;
            if (!empty($jsonContent['code'])) {
                $result[$data['json_file']] = [
                    'status'   => $jsonContent['status'] ?? 'created',
                    'code'     => $jsonContent['code'],
                    'redeemed' => $jsonContent['redeemed'] ?? null,
                ];

                // update current code
                if (!empty($data['update'])) {
                    $post = $this->find($jsonContent['code']);
                    if (!empty($post)) {
                        $data['code'] = $jsonContent['code'];
                    }
                } else {
                    continue;
                }
            }

            $payload = array_merge([
                'status'   => 'created',
                'code'     => '',
                'redeemed' => [
                    'date'    => '',
                    'user'    => '',
                    'website' => '',
                ],
            ], $jsonContent, $data);

            $codeData = [
                'destination' => '',
                'promoter'    => '',
                'description' => sprintf(
                    esc_html__('Imported from %s account at %s store', 'alfaomega-ebooks'),
                    $data['email'],
                    $data['folder']
                ),
                'payload'     => [],
                'due_date'    => null,
                'count'       => 1,
                'type'        => 'import',
                'json_file'   => $filename,
                'code'        => $data['code'] ?? '',
            ];

            foreach ($data['books'] as $book) {
                if (empty($book['isbn'])) {
                    throw new Exception(esc_html__('Isbn is required.', 'alfaomega-ebooks'));
                }

                $codeData['payload'][] = [
                    'isbn'        => $book['isbn'],
                    'read'        => $book['read'] ?? false,
                    'download'    => $book['download'] ?? false,
                    'access_time' => empty('due_date') ? 0
                        : Carbon::parse($book['due_date'])->diffInDays(Carbon::now()),
                ];
            }

            $postId = $post ? $post['id'] : null;
            $accessPost = $this->updateOrCreate($postId, $codeData);
            $payload['code'] = $accessPost['code'];
            $payload['status'] = $accessPost['status'];

            $response = $this->client->putObject([
                'Bucket'       => $this->bucked,
                'Key'          => $filename,
                'Body'         => json_encode($payload),
                'CacheControl' => 'no-cache',
                'ACL'          => 'private',
            ]);
            if ($response['@metadata']['statusCode'] !== 200) {
                throw new Exception(esc_html__('Unable to save the JSON file.', 'alfaomega-ebooks'));
            }

            $result[$data['json_file']] = [
                'status'   => $accessPost['status'],
                'code'     => $accessPost['code'],
                'redeemed' => $payload['redeemed'],
            ];
        }

        return $result;
    }

    /**
     * Get the information of the samples.
     *
     * @return array The information of the samples.
     */
    public function getInfo(): array
    {
        global $wpdb;

        $fields = ['alfaomega_sample_type', 'alfaomega_sample_status'];
        $formattedResults = [];
        $total = 0;
        foreach ($fields as $field) {
            // Prepare the SQL query
            $dataQuery = "SELECT pm.meta_value AS field_type, COUNT(p.ID) AS total_posts
                FROM {$wpdb->prefix}posts AS p
                INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
                WHERE p.post_type = 'alfaomega-sample'
                AND p.post_status = 'publish'
                AND pm.meta_key = '{$field}'
                GROUP BY pm.meta_value;";

            // Execute the query
            $results = $wpdb->get_results($dataQuery, 'ARRAY_A');
            foreach ($results as $row) {
                $formattedResults[$row['field_type']] = intval($row['total_posts']);
                if (!in_array($row['field_type'], ['import', 'sample'])) {
                    $total += intval($row['total_posts']);
                }
            }
        }

        return [
            'samples'   => $total - ($formattedResults['import'] ?? 0),
            'import'    => $formattedResults['import'] ?? 0,
            'created'   => $formattedResults['created'] ?? 0,
            'sent'      => $formattedResults['sent'] ?? 0,
            'redeemed'  => $formattedResults['redeemed'] ?? 0,
            'expired'   => $formattedResults['expired'] ?? 0,
            'failed'    => $formattedResults['failed'] ?? 0,
            'cancelled' => $formattedResults['cancelled'] ?? 0,
            'total'     => $total,
        ];
    }
}
