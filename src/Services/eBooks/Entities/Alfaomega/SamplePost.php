<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\AbstractEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Carbon\Carbon;
use Exception;
use WP_Query;

class SamplePost extends AlfaomegaPostAbstract implements AlfaomegaPostInterface
{
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
                    default => __('Unknown', 'alfaomega-ebooks'),
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
            'payload'      => $payload,
            'due_date'     => $dueDate,
            'activated_at' => $activatedAt,
        ];

        return $this->meta;
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
        $current_user = wp_get_current_user();

        foreach (range(1, $data['count']) as $i) {
            if (empty($data['code'])) {
                $data['code'] = $this->generateCode();
            }

            $newPost = [
                'post_title'  => $data['code'],
                'post_status' => 'publish',
                'post_author' => $current_user ? $current_user->ID : 1,
                'post_type'   => 'alfaomega-sample',
            ];

            if (!empty($postId)) {
                $newPost['ID'] = $postId;
            }

            $postId = wp_insert_post($newPost);
            if (empty($postId)) {
                throw new Exception(esc_html__('Unable to create post.', 'alfaomega-ebook'));
            }

            $result[] = $this->save($postId, $data);

            // send email to destination and promoter after creating the post
            if (!empty($data['destination']) || !empty($data['promoter'])) {
                $data['status'] = $this->email($postId) ? 'sent' : 'failed';
                $this->save($postId, $data);
            }
        }

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

        $result = strtoupper(join('-', $code));
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
            's'           => $code
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
     *
     * @return bool
     * @throws \Exception
     */
    public function email(int $postId): bool
    {
        $mailer = WC()->mailer();
        $mails = $mailer->get_emails();
        $email = $mails['Alfaomega_Ebooks_Sample_Email'];
        return $email->trigger($this->get($postId));
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
}
