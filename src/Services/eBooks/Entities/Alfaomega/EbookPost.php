<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\AbstractEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

class EbookPost extends AlfaomegaPostAbstract implements EbookPostEntity
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
        array $meta = []
    ) {
        parent::__construct($meta);
    }

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

        $thumbnail_url = '';
        $details = null;
        // try to get from get_post_meta($postId, 'alfaomega_ebook_cover', true) first
        $product_sku = get_post_meta($postId, 'alfaomega_ebook_product_sku', true);
        if (!empty($product_sku)) {
            $product_id = wc_get_product_id_by_sku($product_sku);
            if (!empty($product_id)) {
                //$thumbnail_url = get_the_post_thumbnail_url($product_id, 'full');
                $categories = get_the_terms($product_id, 'product_cat');
                if (is_wp_error($categories) || empty($categories)) {
                    $categories = null;
                } else {
                    $categories = wp_list_pluck($categories, 'term_id');
                }
                $post = get_post($product_id);
                $details = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
            }
        }
        $this->meta = [
            'id'          => $postId,
            'title'       => $post->post_title,
            'author'      => $post->post_author,
            'description' => $post->post_content,
            'isbn'        => get_post_meta($postId, 'alfaomega_ebook_isbn', true),
            'pdf_id'      => get_post_meta($postId, 'alfaomega_ebook_id', true),
            'ebook_url'   => get_post_meta($postId, 'alfaomega_ebook_url', true),
            'date'        => $post->post_date,
            'product_sku' => $product_sku,
            'product_id'  => $product_id,
            'cover'       => get_post_meta($postId, 'alfaomega_ebook_cover', true), //$thumbnail_url,
            'details'     => $details,
            'categories'  => $categories ?? [],
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
        Service::make()->helper()->log($response['body']);

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

        $ebook = $this->save($postId, $data);
        $this->updateAccess($postId, $ebook);

        $this->updateImported([$data['isbn']], 'completed');
        return $ebook;
    }

    /**
     * Searches for a post of type 'alfaomega-ebook' by ISBN.
     * This method searches for a post of type 'alfaomega-ebook' in the WordPress database by ISBN.
     * It retrieves the post metadata if a post is found.
     *
     * @param string $value field value.
     * @param string $field field to search by.
     *
     * @return array|null Returns an associative array containing the post metadata if a post is found, or null if no post is found.
     * @throws \Exception
     */
    public function search(string $value, string $field = 'alfaomega_ebook_isbn'): ?array
    {
        $query = [
            'numberposts'  => 1,
            'post_type'    => 'alfaomega-ebook',
            'meta_key'     => $field,
            'meta_value'   => $value,
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
            'alfaomega_ebook_cover' => [
                'old'     => get_post_meta($postId, 'alfaomega_ebook_cover', true),
                'new'     => ! empty($data['cover']) ?  $data['cover'] : '',
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

    /**
     * Return the count of ebooks in the catalog.
     * @return array
     * @throws \Exception
     */
    public function catalogStats(): array
    {
        $response = $this->api->get('/book/catalog/stats');
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
     * Update all the access to this ebook
     *
     * @param int $postId
     * @param array $ebook
     *
     * @return void
     * @throws \Exception
     */
    public function updateAccess(int $postId, array $ebook): void
    {
        // get all the access to this ebook
        $accesses = get_posts([
            'post_type'   => 'alfaomega-access',
            'post_parent' => $postId,
            'numberposts' => -1,
        ]);
        $service = Service::make()->ebooks()->accessPost();
        foreach ($accesses as $access) {
            $data = $service->get($access->ID);
            $data = array_merge($data, [
                'isbn'        => $ebook['isbn'],
                'cover'       => $ebook['cover'],
                'title'       => $ebook['title'],
                'description' => $ebook['description'],
                'categories'  => $ebook['categories'],
            ]);
            $service->save($access->ID, $data);
        }
    }

    /**
     * Get the information of the eBooks.
     *
     * @return array The information of the eBooks.
     * @throws \Exception
     */
    public function getInfo(): array
    {
        global $wpdb;

        // Prepare the SQL query
        $dataQuery = "SELECT COUNT(p.ID) AS total_posts
            FROM {$wpdb->prefix}posts AS p
            WHERE p.post_type = 'alfaomega-ebook'
            AND p.post_status = 'publish'";

        // Execute the query
        $results = $wpdb->get_results($dataQuery, 'ARRAY_A');
        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults['total_posts'] = intval($row['total_posts']);
        }

        try {
            $stats = $this->catalogStats();
        } catch (Exception $e) {
            $stats = null;
        }

        return [
            'catalog' => empty($stats) ? 0 : $stats['size'],
            'imported' => $formattedResults['total_posts'] ?? 0,
        ];
    }

    /**
     * Update the imported registry in the portal for the current store
     *
     * @param array|null $isbns
     * @param string $status
     *
     * @return array
     * @throws \Exception
     */
    public function updateImported(array $isbns=null, string $status = 'on-queue'): array
    {
        if (! defined('AO_STORE_UUID')){
            throw new Exception(esc_html__('AO_STORE_UUID is not defined!', 'alfaomega-ebooks'));
        }

        $storeUuid = AO_STORE_UUID;
        $response = $this->api->post("/book/imported/$storeUuid", [
            'isbns'  => $isbns,
            'status' => $status,
        ]);
        $content = json_decode($response['body'], true);
        if ($content['status'] !== 'success') {
            throw new Exception($content['message']);
        }

        return $content['data'];
    }

    /**
     * Retrieve information of the new ebooks
     *
     * @param int $count
     *
     * @return array
     * @throws \Exception
     */
    public function getNewEbooks(int $count = 100): array
    {
        if (! defined('AO_STORE_UUID')){
            throw new Exception(esc_html__('AO_STORE_UUID is not defined!', 'alfaomega-ebooks'));
        }
        $storeUuid = AO_STORE_UUID;

        $response = $this->api->get("/book/import-new/$storeUuid?items={$count}");
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
     * Updates the catalog import by processing chunks of 'alfaomega-ebook' posts.
     * This method handles the update of imported eBooks in store identified by AO_STORE_UUID.
     * Processes the posts in chunks and sends them to the API for updating the catalog status as completed.
     *
     * @return void
     * @throws \Exception If AO_STORE_UUID is not defined or API response indicates a failure.
     */
    protected function updateCatalogImport(): void
    {
        global $wpdb;

        if (! defined('AO_STORE_UUID')){
            throw new Exception(esc_html__('AO_STORE_UUID is not defined!', 'alfaomega-ebooks'));
        }
        $storeUuid = AO_STORE_UUID;
        $chunkSize = 100;
        $page = 0;

        do {
            // Calculate the offset
            $offset = $chunkSize * $page;

            // Query to get a chunk of posts
            $posts = $wpdb->get_results($wpdb->prepare("
                SELECT p.ID, pm.meta_value AS isbn
                FROM {$wpdb->prefix}posts p
                INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
                WHERE p.post_type = 'alfaomega-ebook'
                AND p.post_status = 'publish'
                AND pm.meta_key = 'alfaomega_ebook_isbn'
                LIMIT %d OFFSET %d
            ", $chunkSize, $offset), OBJECT);

            if (!empty($posts)) {
                $isbns = [];
                $response = $this->api->post("/book/imported/$storeUuid", [
                    'isbns'  => $isbns,
                    'status' => 'completed',
                ]);
                $content = json_decode($response['body'], true);
                if ($content['status'] !== 'success') {
                    throw new Exception($content['message']);
                }

                $page++;
            }
        } while (! empty($posts));
    }
}
