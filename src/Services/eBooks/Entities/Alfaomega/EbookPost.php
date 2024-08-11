<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\AbstractEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

class EbookPost extends AbstractEntity implements EbookPostEntity
{
    /**
     * Make a new instance of the class.
     *
     * @return self The new instance.
     */
    public static function make(Api $api): self
    {
        return new self($api);
    }

    /**
     * The EbookPost constructor.
     *
     * @param Api $api The API.
     * @param array $meta The metadata.
     */
    public function __construct(
        protected Api $api,
        protected array $meta = []
    ) {}

    /**
     * Get the latest post.
     * This method is used to get the latest post of the 'alfaomega-ebook' post type.
     * It creates a query to fetch the latest post, executes the query, and if there are posts, it gets the metadata of
     * the latest post. If there are no posts, it returns null.
     *
     * @return array|null The metadata of the latest post or null if there are no posts.
     * @throws \Exception
     */
    public function latest(): ?array
    {
        $query = ['numberposts' => 1, 'post_type' => 'alfaomega-ebook',];
        $posts = get_posts($query);
        if (empty($posts)) {
            return null;
        }

        return $this->get($posts[0]->ID);
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
            'id'        => $postId,
            'title'     => $post->post_title,
            'author'    => $post->post_author,
            'isbn'      => get_post_meta($postId, 'alfaomega_ebook_isbn', true),
            'pdf_id'    => get_post_meta($postId, 'alfaomega_ebook_id', true),
            'ebook_url' => get_post_meta($postId, 'alfaomega_ebook_url', true),
            'date'      => $post->post_date,
            'tag_id'    => intval(get_post_meta($postId, 'alfaomega_ebook_product_sku', true)),
        ];

        return $this->meta;
    }

    /**
     * Retrieves eBooks from Alfaomega.
     *
     * This method sends a GET request to the Alfaomega API to retrieve eBooks.
     * The eBooks are identified by their ISBNs, which are passed as an array.
     * The method throws an exception if the API response code is not 200 or if the status of the content is not 'success'.
     *
     * @param string $isbn The ISBN of the eBook to start retrieving from. Default is an empty string.
     * @param int $count The number of eBooks to retrieve. Default is 100.
     *
     * @return array Returns an associative array containing the eBooks information.
     * @throws Exception Throws an exception if the API response code is not 200 or if the status of the content is not 'success'.
     */
    public function retrieve(string $isbn = '', int $count=100): array
    {
        // pull from Panel all eBooks updated after the specified book
        $response = $this->api->get("/book/index/$isbn?items={$count}");
        if ($response['response']['code'] !== 200) {
            throw new Exception($response['response']['message']);
        }
        $content = json_decode($response['body'], true);
        if ($content['status'] !== 'success') {
            throw new Exception($content['message']);
        }

        return $content['data'];
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
        if (empty($postId)) {
            $post = $this->search($data['isbn']);
            if (!empty($post)) {
                $postId = $post['id'];
            }
        }

        $user = wp_get_current_user();

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
     * Searches for a post of type 'alfaomega-ebook' by ISBN.
     * This method searches for a post of type 'alfaomega-ebook' in the WordPress database by ISBN.
     * It retrieves the post metadata if a post is found.
     *
     * @param string $isbn The ISBN to search for.
     *
     * @return array|null Returns an associative array containing the post metadata if a post is found, or null if no post is found.
     * @throws \Exception
     */
    public function search($isbn): ?array
    {
        $query = [
            'numberposts'  => 1,
            'post_type'    => 'alfaomega-ebook',
            'meta_key'     => 'alfaomega_ebook_isbn',
            'meta_value'   => $isbn,
            'meta_compare' => '=',
        ];

        $posts = get_posts($query);
        if (empty($posts)) {
            return null;
        }

        return $this->get($posts[0]->ID);
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
                'new'     => ! empty($data['adobe']) ? $data['adobe'] : '',
                'default' => '',
            ],
            'alfaomega_ebook_url'    => [
                'old'     => get_post_meta($postId, 'alfaomega_ebook_url', true),
                'new'     => ! empty($data['html_ebook']) ? $data['html_ebook'] : '',
                'default' => '',
            ],
            'alfaomega_ebook_product_sku' => [
                'old'     => get_post_meta($postId, 'alfaomega_ebook_product_sku', true),
                'new'     => ! empty($data['printed_isbn']) ?  $data['printed_isbn'] : 'UNKNOWN',
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
     * Retrieves eBooks information from Alfaomega.
     * This method sends a POST request to the Alfaomega API to retrieve information about eBooks.
     * The eBooks are identified by their ISBNs, which are passed as an array.
     * The method throws an exception if the API response code is not 200 or if the status of the content is not
     * 'success'.
     *
     * @param array $isbns An array of ISBNs of the eBooks to retrieve information for.
     *
     * @return array|null Returns an associative array containing the eBooks information.
     * @throws \Exception
     */
    public function index(array $isbns): ?array
    {
        // get eBooks info from Alfaomega
        $response = $this->api->post("/book/index", ['isbns' => $isbns]);
        if ($response['response']['code'] !== 200) {
            throw new Exception($response['response']['message']);
        }

        $content = json_decode($response['body'], true);
        if ($content['status'] !== 'success') {
            return null;
        }

        return json_decode($response['body'], true)['data'];
    }
}
