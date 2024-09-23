<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\AbstractEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

class AccessPost extends AlfaomegaPostAbstract implements AlfaomegaPostInterface
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

        $categories = get_the_terms($postId, 'product_cat');
        $this->meta = [
            'id'          => $postId,
            'title'       => $post->post_title,
            'description' => $post->post_content,
            'ebook_id'    => $post->post_parent,
            'user_id'     => $post->post_author,
            'user_email'  => get_the_author_meta('user_email', $post->post_author),
            'categories'  => !empty($categories) ? wp_list_pluck($categories, 'term_id') : [],
            'isbn'        => get_post_meta($postId, 'alfaomega_access_isbn', true),
            'cover'       => get_post_meta($postId, 'alfaomega_access_cover', true),
            'type'        => get_post_meta($postId, 'alfaomega_access_type', true),
            'order_id'    => get_post_meta($postId, 'alfaomega_access_order_id', true),
            'sample_id'   => get_post_meta($postId, 'alfaomega_access_sample_id', true),
            'status'      => get_post_meta($postId, 'alfaomega_access_status', true),
            'read'        => get_post_meta($postId, 'alfaomega_access_read', true),
            'download'    => get_post_meta($postId, 'alfaomega_access_download', true),
            'due_date'    => get_post_meta($postId, 'alfaomega_access_due_date', true),
            'download_at' => get_post_meta($postId, 'alfaomega_access_download_at', true),
            'read_at'     => get_post_meta($postId, 'alfaomega_access_read_at', true),
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

        $ebookService = Service::make()->ebooks()->ebookPost();
        $ebook = $ebookService->get($data['ebook_id']);
        if (empty($ebook)) {
            throw new Exception(esc_html__('Ebook not found.', 'alfaomega-ebook'));
        }

        $access = [
            'user_id'     => $data['user_id'],
            'ebook_id'    => $data['ebook_id'],
            'isbn'        => $ebook['isbn'],
            'cover'       => $ebook['cover'],
            'title'       => $ebook['title'],
            'description' => $ebook['description'],
            'categories'  => $ebook['categories'],
            'type'        => $data['access']['type'] ?? 'purchase',
            'order_id'    => $data['access']['order_id'] ?? '',
            'sample_id'   => $data['access']['sample_id'] ?? '',
            'status'      => $data['access']['status'] ?? 'created',
            'read'        => $data['access']['read'] ?? 1,
            'download'    => $data['access']['download'] ?? 1,
            'due_date'    => $data['access']['due_date'] ?? '',
            'download_at' => $data['access']['download_at'] ?? '',
            'read_at'     => $data['access']['read_at'] ?? '',
        ];

        $newPost = [
            'post_title'       => $ebook['title'],
            'post_content'     => $ebook['description'],
            'post_status'      => 'publish',
            'post_author'      => $data['user_id'],
            'post_type'        => 'alfaomega-access',
            'post_parent'      => $data['ebook_id'],
        ];

        if (!empty($postId)) {
            $newPost['ID'] = $postId;
        }

        $postId = wp_insert_post($newPost);
        if (empty($postId)) {
            throw new Exception(esc_html__('Unable to create post.', 'alfaomega-ebook'));
        }

        // Set the post categories
        if (!is_array($ebook['categories'])) {
            $ebook['categories'] = [$ebook['categories']];
        }
        $result = wp_set_post_terms( $postId, $ebook['categories'], 'product_cat', false );
        if (is_wp_error($result)) {
            throw new Exception($result->get_error_message());
        }

        return $this->save($postId, $access);
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
            'alfaomega_access_cover' => [
                'old'     => get_post_meta($postId, 'alfaomega_access_cover', true),
                'new'     => $data['cover'],
                'default' => '',
            ],
            'alfaomega_access_isbn' => [
                'old'     => get_post_meta($postId, 'alfaomega_access_isbn', true),
                'new'     => $data['isbn'],
                'default' => 'ISBN',
            ],
            'alfaomega_access_type'   => [
                'old'     => get_post_meta($postId, 'alfaomega_access_type', true),
                'new'     => $data['type'],
                'default' => 'purchase',
            ],
            'alfaomega_access_order_id'   => [
                'old'     => get_post_meta($postId, 'alfaomega_access_order_id', true),
                'new'     => $data['order_id'],
                'default' => '',
            ],
            'alfaomega_access_sample_id'   => [
                'old'     => get_post_meta($postId, 'alfaomega_access_sample_id', true),
                'new'     => $data['sample_id'],
                'default' => '',
            ],
            'alfaomega_access_status'  => [
                'old'     => get_post_meta($postId, 'alfaomega_access_status', true),
                'new'     => $data['status'],
                'default' => 'created',
            ],
            'alfaomega_access_read'  => [
                'old'     => get_post_meta($postId, 'alfaomega_access_read', true),
                'new'     => $data['read'],
                'default' => 1,
            ],
            'alfaomega_access_download'  => [
                'old'     => get_post_meta($postId, 'alfaomega_access_download', true),
                'new'     => $data['download'],
                'default' => 1,
            ],
            'alfaomega_access_due_date'  => [
                'old'     => get_post_meta($postId, 'alfaomega_access_due_date', true),
                'new'     => $data['due_date'],
                'default' => '',
            ],
            'alfaomega_access_download_at'  => [
                'old'     => get_post_meta($postId, 'alfaomega_access_download_at', true),
                'new'     => $data['download_at'],
                'default' => '',
            ],
            'alfaomega_access_read_at'  => [
                'old'     => get_post_meta($postId, 'alfaomega_access_read_at', true),
                'new'     => $data['read_at'],
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
     * Search the current user eBooks access based on various criteria.
     *
     * This method allows searching for eBooks by category, search term, type, status, and other parameters.
     * It returns an array of search results.
     *
     * @param string|null $category The category to filter by. Default is null.
     * @param string|null $search The search term to filter by. Default is null.
     * @param string|null $type The type of eBook to filter by. Default is null.
     * @param string|null $status The status of the eBook to filter by. Default is null.
     * @param int $page The page number for pagination. Default is 1.
     * @param int $perPage The number of results per page. Default is 8.
     * @param string $orderBy The field to order the results by. Default is 'title'.
     * @param string $orderDirection The direction to order the results ('asc' or 'desc'). Default is 'asc'.
     *
     * @return array An array of search results.
     */
    public function search(string $category = null,
                           string $search = null,
                           string $type = null,
                           string $status = null,
                           int $page = 1,
                           int $perPage = 8,
                           string $orderBy = 'title',
                           string $orderDirection = 'asc'
    ): array {
        $currentUserId = get_current_user_id();

        // TODO: Order by the following fields
        $orderByFields = [
            'title'       => 'title',
            'created_at'  => 'addedAt',
            'status'      => 'alfaomega_access_status',
            'valid_until' => 'validUntil',
            'access_at'   => 'access_at',
        ];

        $args = [
            'post_type'      => 'alfaomega-access',
            'posts_per_page' => $perPage,
            'paged'          => $page,
            'orderby'        => $orderBy,
            'order'          => $orderDirection,
            'author'         => $currentUserId,
            'meta_query'     => [
                'relation' => 'AND',
            ],
        ];

        if ($category) {
            $args['category_name'] = $category;
        }

        if ($search) {
            $args['s'] = $search;
        }

        if ($type) {
            $args['meta_query'][] = [
                'key'     => 'alfaomega_access_type',
                'value'   => $type,
                'compare' => '=',
            ];
        }

        if ($status) {
            $args['meta_query'][] = [
                'key'     => 'alfaomega_access_status',
                'value'   => $status,
                'compare' => '=',
            ];
        }

        $query = new \WP_Query($args);
        $results = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = [
                    'id'         => get_the_ID(),
                    'title'      => get_the_title(),
                    'cover'      => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                    'download'   => get_post_meta(get_the_ID(), 'alfaomega_access_download', true),
                    'read'       => get_post_meta(get_the_ID(), 'alfaomega_access_read', true),
                    'accessType' => get_post_meta(get_the_ID(), 'alfaomega_access_type', true),
                    'status'     => get_post_meta(get_the_ID(), 'alfaomega_access_status', true),
                    'addedAt'    => get_the_date('Y-m-d'),
                    'validUntil' => get_post_meta(get_the_ID(), 'alfaomega_access_due_date', true),
                    'url'        => get_permalink(),
                ];
            }
            wp_reset_postdata();
        }

        return [
            'data' => $results,
            'meta' => [
                'total'        => $query->found_posts,
                'pages'        => $query->max_num_pages,
                'current_page' => $page,
            ],
        ];
    }
}
