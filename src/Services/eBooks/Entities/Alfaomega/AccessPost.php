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

        $this->meta = [
            'id'          => $postId,
            'title'       => $post->post_title,
            'description' => $post->post_content,
            'ebook_id'    => $post->post_parent,
            'user_id'     => $post->post_author,
            'category_id' => $post->post_category,
            'isbn'        => get_post_meta($postId, 'alfaomega_access_isbn', true),
            'cover'       => get_post_meta($postId, 'alfaomega_access_cover', true),
            'type'        => get_post_meta($postId, 'alfaomega_access_type', true),
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

        $newPost = [
            'post_title'       => $data['title'],
            'post_content'     => $data['description'],
            'post_status'      => 'publish',
            'post_author'      => $data['user_id'],
            'post_type'        => 'alfaomega-access',
            'post_category'    => [$data['category_id']],
            'post_parent'      => $data['ebook_id'],
        ];

        if (!empty($postId)) {
            $newPost['ID'] = $postId;
        }

        $postId = wp_insert_post($newPost);
        if (empty($postId)) {
            throw new Exception(esc_html__('Unable to create post.', 'alfaomega-ebook'));
        }

        return $this->save($postId, $data);
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
            'alfaomega_ebook_isbn'   => [
                'old'     => get_post_meta($postId, 'alfaomega_ebook_isbn', true),
                'new'     => $data['isbn'],
                'default' => '',
            ],

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

        foreach ($fields as $field => $value) {
            if (empty($value['new'])) {
                throw new Exception("Field value '$field' is required.");
            }
        }

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
}
