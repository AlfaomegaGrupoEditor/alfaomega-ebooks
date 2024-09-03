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
     * The EbookPost constructor.
     *
     * @param Api $api The API.
     * @param array $meta The metadata.
     */
    public function __construct(
        protected array $meta = []
    ) {}

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
            'author'      => $post->post_author,
            'isbn'        => get_post_meta($postId, 'alfaomega_ebook_isbn', true),
            'pdf_id'      => get_post_meta($postId, 'alfaomega_ebook_id', true),
            'ebook_url'   => get_post_meta($postId, 'alfaomega_ebook_url', true),
            'date'        => $post->post_date,
            'product_sku' => intval(get_post_meta($postId, 'alfaomega_ebook_product_sku', true)),
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
        // search by isbn to get the post id
        if (empty($postId)) {
            $post = $this->search($data['isbn']);
            if (!empty($post)) {
                $postId = $post['id'];
            }
        }

        $user = wp_get_current_user();
        if (empty($data['printed_isbn']) && ! empty($data['product_sku'])) {
            $data['printed_isbn'] = $data['product_sku'];
        }

        if (empty($data['printed_isbn'])) {
            throw new Exception(esc_html__('Printed ISBN is required.', 'alfaomega-ebook'));
        }

        $newPost = [
            'post_title'       => $data['title'],
            'post_content'     => $data['description'],
            'post_status'      => 'publish',
            'post_author'      => $user->ID,
            'post_type'        => 'alfaomega-ebook',
            'post_product_sku' => $data['printed_isbn'] ?? 'UNKNOWN',
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
            'alfaomega_ebook_id'     => [
                'old'     => get_post_meta($postId, 'alfaomega_ebook_id', true),
                'new'     => ! empty($data['adobe']) ? $data['adobe'] : ($data['pdf_id'] ?? ''),
                'default' => '',
            ],
            'alfaomega_ebook_url'    => [
                'old'     => get_post_meta($postId, 'alfaomega_ebook_url', true),
                'new'     => ! empty($data['html_ebook']) ? $data['html_ebook'] : ($data['ebook_url'] ?? ''),
                'default' => '',
            ],
            'alfaomega_ebook_product_sku' => [
                'old'     => get_post_meta($postId, 'alfaomega_ebook_product_sku', true),
                'new'     => ! empty($data['printed_isbn']) ?  $data['printed_isbn'] : 'UNKNOWN',
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
