<?php
use \Automattic\WooCommerce\Client;

/**
 * This class defines the general plugin settings.
 *
 * @since      1.0.0
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/includes
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
if( ! class_exists( 'Alfaomega_Ebooks_Service' )){
    class Alfaomega_Ebooks_Service{
        /**
         * @var Alfaomega_Ebooks_Api $api
         * This protected property holds an instance of the Alfaomega_Ebooks_Api class.
         * It is used to interact with the Alfaomega Ebooks API.
         */
        protected Alfaomega_Ebooks_Api $api;

        /**
         * @var array $settings
         * This protected property holds the settings for the Alfaomega Ebooks service.
         * It is an associative array where the keys are the setting names and the values are the setting values.
         */
        protected array $settings = [];

        /**
         * @var Client|null $woocommerce
         * This protected property holds an instance of the WooCommerce Client class.
         * It is used to interact with the WooCommerce API.
         * It is nullable, meaning it can also hold a null value.
         */
        protected ?Client $woocommerce = null;

        /**
         * This is the class constructor.
         *  It initializes the WooCommerce client and retrieves the settings.
         *
         * @param bool $initApi
         */
        public function __construct(bool $initApi=true)
        {
            if ($initApi) {
                $this->initWooCommerceClient();
                $this->getSettings();
                $this->api = new Alfaomega_Ebooks_Api($this->settings);
            }
        }

        /**
         * Initializes the WooCommerce client.
         *
         * This method creates a new instance of the WooCommerce Client class and assigns it to the $woocommerce property.
         * The client is configured with the site URL, API key, and API secret, along with other necessary settings.
         *
         * FIXME: make sure the permanent_links are updated in the site settings
         * @see https://stackoverflow.com/questions/65204134/woocommerce-api-giving-json-syntax-error-on-every-request
         * @return self Returns the current instance of the class to allow for method chaining.
         */
        public function initWooCommerceClient(): self
        {
            $this->woocommerce = new Client(
                get_site_url(),
                WOOCOMMERCE_API_KEY,
                WOOCOMMERCE_API_SECRET,
                [
                    'wp_api'           => true,
                    'version'          => 'wc/v3',
                    'verify_ssl'       => false,
                    'timeout'          => 180,
                ]
            );

            return $this;
        }

        /**
         * Retrieves the settings for the Alfaomega Ebooks service.
         *
         * This method retrieves the settings from the WordPress options table and assigns them to the $settings property.
         * @return void
         */
        public function getSettings(): void
        {
            $this->settings = array_merge(
                (array) get_option('alfaomega_ebooks_general_options'),
                (array) get_option('alfaomega_ebooks_platform_options'),
                (array) get_option('alfaomega_ebooks_api_options'),
                (array) get_option('alfaomega_ebooks_product_options')
            );
        }

        /**
         * Imports eBooks from Alfaomega.
         *
         * This method imports eBooks from the Alfaomega API and creates posts for them in WordPress.
         * It retrieves the eBooks from the API, creates a post for each eBook, and links the post to a product in WooCommerce.
         *
         * @return array Returns an array with the number of eBooks imported.
         * @throws \Exception Throws an exception if the import queue fails.
         */
        public function importEbooks(): array
        {
            $this->checkFormatAttribute();
            $isbn = '';
            if ($this->settings['alfaomega_ebooks_import_from_latest']) {
                $latestBook = $this->latestPost();
                $isbn = empty($latestBook) ? '' : $latestBook['isbn'];
            }
            $countPerPage = intval($this->settings['alfaomega_ebooks_import_limit']);
            $imported = 0;
            do {
                $eBooks = $this->retrieveEbooks($isbn, $countPerPage);
                foreach ($eBooks as $eBook) {
                    $result = as_enqueue_async_action(
                        'alfaomega_ebooks_queue_import',
                        [ $eBook ]
                    );
                    if ($result === 0) {
                        throw new Exception("Import queue failed");
                    }
                    $imported++;
                }
                $last = end($eBooks);
                $isbn = $last['isbn'];
            } while (count($eBooks) === $countPerPage);

            return [
                'imported' => $imported,
            ];
        }

        /**
         * Imports a single eBook from Alfaomega.
         * This method imports a single eBook from the Alfaomega API and creates a post for it in WordPress.
         * It retrieves the eBook from the API, creates a post for it, and links the post to a product in WooCommerce.
         *
         * @param array $eBook The eBook data to import.
         *
         * @return void
         * @throws \Exception
         */
        public function importEbook(array $eBook): void
        {
            $eBook = $this->updateEbookPost(null, $eBook);
            $this->linkProduct($eBook, false);
        }

        /**
         * Refreshes eBooks in WordPress.
         *
         * This method refreshes the eBooks in WordPress by updating the post content and metadata for each eBook.
         * It retrieves the eBooks from the API, updates the post content and metadata for each eBook, and links the post to a product in WooCommerce.
         *
         * @param array|null $postIds An array of post IDs to refresh. If null, all eBooks are refreshed.
         *
         * @return array Returns an array with the number of eBooks refreshed.
         * @throws \Exception Throws an exception if the refresh list queue fails.
         */
        public function refreshEbooks(array $postIds = null): array
        {
            $this->checkFormatAttribute();
            $postsPerPage = 5;
            $page = 0;
            $args = [
                'posts_per_page' => $postsPerPage,
                'post_type'      => 'alfaomega-ebook',
                'orderby'        => 'ID',
                'order'          => 'ASC',
            ];
            $total = 0;

            if (empty($postIds)) {
                do {
                    $args['offset'] = $postsPerPage * $page;
                    $posts = get_posts($args);
                    $isbns = [];
                    foreach ($posts as $post) {
                        $isbn = get_post_meta($post->ID, 'alfaomega_ebook_isbn', true);
                        $isbns[$isbn] = $post->ID;
                    }

                    $result = as_enqueue_async_action(
                        'alfaomega_ebooks_queue_refresh_list',
                        [ $isbns ]
                    );
                    if ($result === 0) {
                        throw new Exception('Refresh list queue failed');
                    }
                    $page++;
                    $total += count($isbns);
                } while (count($posts) === $postsPerPage);
            } else {
                foreach ($postIds as $postId) {
                    $isbn = get_post_meta($postId, 'alfaomega_ebook_isbn', true);
                    $isbns[$isbn] = $postId;
                }
                $eBooks = $this->getEbooksInfo(array_keys($isbns));
                foreach ($eBooks as $eBook) {
                    $this->refreshEbook($isbns[$eBook['isbn']], $eBook);
                    $total++;
                }
            }

            return [
                'refreshed' => $total,
            ];
        }

        /**
         * Refreshes a single eBook in WordPress.
         * This method refreshes a single eBook in WordPress by updating the post content and metadata.
         * It retrieves the eBook from the API, updates the post content and metadata, and links the post to a product in WooCommerce.
         *
         * @param int $postId  The post ID of the eBook to refresh.
         * @param array $eBook The eBook data to refresh.
         *
         * @return void
         * @throws \Exception
         */
        public function refreshEbookList(array $isbns): void
        {
            $eBooks = $this->getEbooksInfo(array_keys($isbns));
            foreach ($eBooks as $eBook) {
                $result = as_enqueue_async_action(
                    'alfaomega_ebooks_queue_refresh',
                    [ $isbns[$eBook['isbn']], $eBook ]
                );
                if ($result === 0) {
                    throw new Exception('Refresh queue failed');
                }
            }
        }

        /**
         * Refreshes a single eBook in WordPress.
         * This method refreshes a single eBook in WordPress by updating the post content and metadata.
         * It retrieves the eBook from the API, updates the post content and metadata, and links the post to a product in WooCommerce.
         *
         * @param int $postId  The post ID of the eBook to refresh.
         * @param array $eBook The eBook data to refresh.
         *
         * @return void
         * @throws \Exception
         */
        public function refreshEbook(int $postId, array $eBook): void
        {
            $eBook = $this->updateEbookPost($postId, $eBook);
            $this->linkProduct($eBook, false);
        }

        /**
         * Links eBooks to products in WooCommerce.
         * This method links eBooks to products in WooCommerce by creating a product for each eBook.
         * It retrieves the eBooks from the API, creates a product for each eBook, and links the product to the eBook post.
         *
         * @param array $postIds An array of post IDs to link.
         *
         * @return array Returns an array with the number of eBooks linked.
         * @throws \Exception
         */
        public function linkProducts(array $postIds): array
        {
            $this->checkFormatAttribute();
            $linked = 0;
            foreach ($postIds as $postId) {
                $this->linkProduct($this->getPostMeta($postId));
                $linked++;
            }

            return [
                'linked' => $linked,
            ];
        }

        /**
         * Links eBooks to products in WooCommerce.
         * This method links eBooks to products in WooCommerce by creating a product for each eBook.
         * It retrieves the eBooks from the API, creates a product for each eBook, and links the product to the eBook post.
         *
         * @param array $productsId An array of product IDs to link.
         *
         * @return array Returns an array with the number of eBooks linked.
         * @throws \Exception
         */
        public function linkEbooks(array $productsId): array
        {
            $linked = 0;
            $this->checkFormatAttribute();

            foreach ($productsId as $productId) {
                $tags = wp_get_post_terms( $productId, 'product_tag' );
                foreach ($tags as $tag) {
                    $isbns[] = $tag->name;
                }
            }

            $eBooks = $this->getEbooksInfo($isbns);
            foreach ($eBooks as $eBook) {
                $this->importEbook($eBook);
                $linked++;
            }

            return [
                'linked' => $linked,
            ];
        }

        /**
         * Retrieves a set number of posts of type 'alfaomega-ebook'.
         * This method retrieves a set number of posts of type 'alfaomega-ebook' from the WordPress database.
         * The number of posts to retrieve and any additional query parameters can be specified.
         *
         * @param int $count   The number of posts to retrieve.
         * @param array $query Optional. An array of additional query parameters.
         *
         * @return WP_Query Returns a new instance of the WP_Query class with the specified query parameters.
         */
        protected function getPosts(int $count, array $query = []): WP_Query
        {
            $args = array_merge($query, [
                'posts_per_page' => $count,
                'paged'          => 1,
                'post_type'      => 'alfaomega-ebook',
            ]);

            return new WP_Query($args);
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
        protected function savePostMeta(int $postId, array $data): array
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
                'alfaomega_ebook_tag_id' => [
                    'old'     => get_post_meta($postId, 'alfaomega_ebook_tag_id', true),
                    'new'     => ! empty($data['isbn']) ? $this->getTagId($data['isbn']) : '',
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

            return $this->getPostMeta($postId);
        }

        /**
         * Retrieves the metadata for a post of type 'alfaomega-ebook'.
         * This method retrieves the metadata for a post of type 'alfaomega-ebook' from the WordPress database.
         * It retrieves the post title, author, ISBN, Adobe ID, eBook URL, publication date, and tag ID.
         *
         * @param int $postId The ID of the post to retrieve metadata for.
         *
         * @return array Returns an associative array containing the post metadata.
         * @throws \Exception
         */
        protected function getPostMeta(int $postId): array
        {
            $post = get_post($postId);
            if (empty($post)) {
                throw new Exception("Post $postId not found");
            }
            return [
                'id'        => $postId,
                'title'     => $post->post_title,
                'author'    => $post->post_author,
                'isbn'      => get_post_meta($postId, 'alfaomega_ebook_isbn', true),
                'pdf_id'    => get_post_meta($postId, 'alfaomega_ebook_id', true),
                'ebook_url' => get_post_meta($postId, 'alfaomega_ebook_url', true),
                'date'      => $post->post_date,
                'tag_id'    => intval(get_post_meta($postId, 'alfaomega_ebook_tag_id', true)),
            ];
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
        protected function searchPost($isbn): ?array
        {
            $query = [
                'numberposts' => 1,
                'post_type'    => 'alfaomega-ebook',
                'meta_key'     => 'alfaomega_ebook_isbn',
                'meta_value'   => $isbn,
                'meta_compare' => '=',
            ];

            $posts = get_posts($query);
            if (empty($posts)) {
                return null;
            }

            return $this->getPostMeta($posts[0]->ID);
        }

        /**
         * Retrieves the latest post of type 'alfaomega-ebook'.
         * This method retrieves the latest post of type 'alfaomega-ebook' from the WordPress database.
         * It retrieves the post metadata if a post is found.
         *
         * @return array|null Returns an associative array containing the post metadata if a post is found, or null if no post is found.
         * @throws \Exception
         */
        protected function latestPost(): ?array
        {
            $query = [
                'numberposts' => 1,
                'post_type'    => 'alfaomega-ebook',
            ];

            $posts = get_posts($query);
            if (empty($posts)) {
                return null;
            }

            return $this->getPostMeta($posts[0]->ID);
        }

        /**
         * Retrieves eBooks information from Alfaomega.
         * This method sends a POST request to the Alfaomega API to retrieve information about eBooks.
         * The eBooks are identified by their ISBNs, which are passed as an array.
         * The method throws an exception if the API response code is not 200 or if the status of the content is not 'success'.
         *
         * @param array $isbns An array of ISBNs of the eBooks to retrieve information for.
         *
         * @return array Returns an associative array containing the eBooks information.
         * @throws Exception Throws an exception if the API response code is not 200 or if the status of the content is not 'success'.
         */
        protected function getEbooksInfo(array $isbns): array
        {
            // get eBooks info from Alfaomega
            $response = $this->api->post("/book/index", ['isbns' => $isbns]);
            if ($response['response']['code'] !== 200) {
                throw new Exception($response['response']['message']);
            }

            $content = json_decode($response['body'], true);
            if ($content['status'] !== 'success') {
                throw new Exception($content['message']);
            }

            return json_decode($response['body'], true)['data'];
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
        protected function retrieveEbooks(string $isbn = '', int $count=100): array
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
        protected function updateEbookPost(?int $postId, array $data): array
        {
            if (empty($postId)) {
                $post = $this->searchPost($data['isbn']);
                if (!empty($post)) {
                    $postId = $post['id'];
                }
            }

            $user = wp_get_current_user();

            $newPost = [
                'post_title'   => $data['title'],
                'post_content' => $data['description'],
                'post_status'  => 'publish',
                'post_author'  => $user->ID,
                'post_type'    => 'alfaomega-ebook',
            ];

            if (!empty($postId)) {
                $newPost['ID'] = $postId;
            }

            $postId = wp_insert_post($newPost);
            if (empty($postId)) {
                throw new Exception(esc_html__('Unable to create post.', 'alfaomega-ebook'));
            }

            return $this->savePostMeta($postId, $data);
        }

        /**
         * Links an eBook to a product in WooCommerce.
         *
         * This method links an eBook to a product in WooCommerce by creating a product for the eBook.
         * It retrieves the product by its tag ID and title, updates the product type, formats, and variants, and links the product to the eBook post.
         *
         * @param array $ebook An associative array containing the eBook data.
         * @param bool $notFoundError If true, throws an exception if the product is not found. Default is true.
         *
         * @return void
         * @throws Exception Throws an exception if the product is not found, the product type is not supported, product formats failed, or product variants failed.
         */
        protected function linkProduct(array $ebook, bool $notFoundError=true): void
        {
            $product = $this->getProduct($ebook['tag_id'], $ebook['title']);
            if (empty($product)) {
                if ($notFoundError) {
                    throw new Exception("Products with digital ISBN {$ebook['isbn']} not found");
                }
                return;
            }

            $product = $this->updateProductType($product);
            $prices = [
                'regular_price' => $product['regular_price'],
                'sale_price'    => $product['sale_price'],
            ];
            if (empty($product)) {
                throw new Exception("Product type not supported");
            }

            $product = $this->updateProductFormats($product);
            if (empty($product)) {
                throw new Exception("Product formats failed");
            }

            $product = $this->updateProductVariants($product, $prices, $ebook);
            if (empty($product)) {
                throw new Exception("Product variants failed");
            }
        }

        /**
         * Retrieves the status of a queue.
         *
         * This method retrieves the status of a queue by querying the WordPress actionscheduler_actions table.
         * It retrieves the number of actions with the specified queue name and status.
         *
         * @param string $queue The queue name to query.
         *
         * @return array Returns an associative array containing the queue name and the number of actions with the specified status.
         */
        public function queueStatus(string $queue): array
        {
            global $wpdb;
            $results = $wpdb->get_results( "
                SELECT status, count(*) as 'count'
                FROM wp_actionscheduler_actions a
                WHERE 1 = 1
                  AND (a.hook like '$queue%' OR
                       (a.extended_args IS NULL AND a.args like '$queue%') OR
                       a.extended_args like '$queue%')
                GROUP BY status
            " );

            $data = [
                'queue'    => $queue,
                'complete' => 0,
                'failed'   => 0,
                'pending'  => 0,
            ];
            foreach ($results as $result) {
                $data[$result->status] = intval($result->count);
            }

            return $data;
        }

        /**
         * Clears a queue.
         *
         * This method clears a queue by deleting all actions with the specified queue name and status.
         *
         * @return array Returns an empty array.
         */
        public function clearQueue(): array
        {
            global $wpdb;
            $wpdb->get_results( "
                DELETE
                FROM wp_actionscheduler_actions
                WHERE hook like '%alfaomega_ebooks_queue%'
                    AND status not in ('pending', 'in-process');
            " );

            return [];
        }

        /**
         * Retrieves the tag ID for a product in WooCommerce.
         *
         * This method retrieves the tag ID for a product in WooCommerce by searching for the product tag with the specified ISBN.
         * If the tag is not found, it creates a new tag with the specified ISBN.
         *
         * @param string $isbn The ISBN to search for.
         *
         * @return int Returns the tag ID for the product.
         * @throws Exception Throws an exception if the tag creation failed.
         */
        public function getTagId(string $isbn): int
        {
            $tags = (array) $this->woocommerce->get("products/tags", [
                'search' => $isbn,
            ]);
            if (count($tags) > 0) {
                return $tags[0]->id;
            }

            $tag = $this->woocommerce->post("products/tags", [
                'name' => $isbn,
            ]);
            if (empty($tag->id)) {
                throw new Exception("Tag creation failed");
            }
            return $tag->id;
        }

        /**
         * Retrieves a product from WooCommerce.
         *
         * This method retrieves a product from WooCommerce by searching for the product with the specified tag ID and title.
         * If the product is not found, it searches for the product with the specified title.
         * If the product is still not found, it searches for the product with the specified tag ID.
         * If the product is still not found, it searches for the product with the specified title and creates a new product if the title is not empty.
         *
         * @param int $tagId The tag ID to search for.
         * @param string $title The title to search for. Default is an empty string.
         *
         * @return array|null Returns an associative array containing the product data if the product is found, or null if the product is not found.
         */
        public function getProduct(int $tagId, string $title = null): ?array
        {
            $products = (array) $this->woocommerce
                ->get("products", [
                    'tag'=> $tagId,
                ]);

            if (count($products) === 1) {
                return (array) $products[0];
            }

            if (count($products) === 0 || !empty($title)) {
                $product = $this->findProduct($title, $tagId);
                if (!empty($product)) {
                    return $product;
                }
            }

            return null;
        }

        /**
         * Finds a product in WooCommerce.
         *
         * This method finds a product in WooCommerce by searching for the product with the specified title.
         * If the product is found, it updates the product tags with the specified tag ID.
         *
         * @param string $title The title to search for.
         * @param int $tagId The tag ID to update the product with.
         *
         * @return array|null Returns an associative array containing the product data if the product is found, or null if the product is not found.
         */
        public function findProduct(string $title, int $tagId): ?array
        {
            $products = (array) $this->woocommerce
                ->get("products", [
                    'search' => $title,
                ]);

            if (count($products) === 1) {
                $product = $products[0];
                $this->woocommerce
                    ->put("products/{$product->id}", [
                        'tags' => [[ 'id' => $tagId ]],
                    ]);

                return (array) $product;
            }

            return null;
        }

        /**
         * Updates the product type in WooCommerce.
         *
         * This method updates the product type in WooCommerce by changing the product type to the specified type.
         * If the product type is already the specified type, it returns the product data.
         *
         * @param array $product The product data to update.
         * @param string $type The product type to update to. Default is 'variable'.
         *
         * @return array|null Returns an associative array containing the updated product data if the product type is updated, or null if the product type is not updated.
         */
        public function updateProductType(array $product, string $type = 'variable'): ?array
        {
            if ($product['type'] !== $type) {
                $regularPrice = $product['regular_price'];
                $salePrice = $product['sale_price'];
                $product = (array) $this->woocommerce
                    ->put("products/{$product['id']}", [
                        'type' => $type,
                    ]);

                if (empty($product)) {
                    return null;
                }

                $product['regular_price'] = $regularPrice;
                $product['sale_price'] = $salePrice;

                return $product;
            }

            return $product;
        }

        /**
         * Retrieves the variation data for a product in WooCommerce.
         * This method retrieves the variation data for a product in WooCommerce by creating an associative array with the specified data.
         * The data includes the product ID, regular price, sale price, and attributes.
         *
         * @param array $product The product data to retrieve the variation data for.
         * @param string $format The format of the product. Default is 'impreso'.
         * @param array $prices  The prices of the product. Default is an empty array.
         * @param int $ebookId   The eBook ID to retrieve the variation data for. Default is 0.
         *
         * @return array Returns an associative array containing the variation data for the product.
         * @throws \Exception
         */
        public function updateProductVariants(array $product, array $prices, array $ebook): ?array
        {
            $variations = (array) $this->woocommerce
                ->get("products/{$product['id']}/variations");

            $variationIds = [];
            if (!empty($variations)) {
                foreach ($variations as $variation) {
                    $format = '';
                    foreach ($variation->attributes as $attribute) {
                        if ($attribute->name === 'Formato') {
                            $format = match ($attribute->option) {
                                'Impreso' => 'impreso',
                                'Digital' => 'digital',
                                'Impreso + Digital' => 'impreso-digital',
                                default => null,
                            };
                            if ($format === 'impreso') {
                                $prices = [
                                    'regular_price' => $variation->regular_price,
                                    'sale_price' => $variation->sale_price,
                                ];
                            }
                        }
                    }
                    if (empty($format)) {
                        return $product;
                    }

                    $variationIds[$format] = $variation->id;
                }

                if (empty($prices['regular_price'])) {
                    return $product;
                }
            }

            $formatOptions = ['impreso', 'digital', 'impreso-digital'];
            foreach ($formatOptions as $format) {
                $data = $this->getVariationData($product, $format, $prices, $ebook['id']);
                $variation = empty($variationIds[$format])
                    ? $this->woocommerce->post("products/{$product['id']}/variations", $data)
                    : $this->woocommerce->put("products/{$product['id']}/variations/{$variationIds[$format]}", $data);

                if (empty($variation)) {
                    throw new Exception("Variation creation failed");
                }
            }

            $product = (array) $this->woocommerce
                ->put("products/{$product['id']}", [
                    'default_attributes' => [
                        [
                            'id'     => $this->settings['alfaomega_ebooks_format_attr_id'],
                            'name'   => 'Formato',
                            'option' => 'Impreso + Digital',
                        ],
                    ],
                ]);

            if (empty($product)) {
                return null;
            }

            return $product;
        }

        /**
         * Updates the product formats in WooCommerce.
         *
         * This method updates the product formats in WooCommerce by adding the specified format to the product attributes.
         * If the format is already in the product attributes, it returns the product data.
         *
         * @param array $product The product data to update.
         *
         * @return array|null Returns an associative array containing the updated product data if the product formats are updated, or null if the product formats are not updated.
         */
        public function updateProductFormats(array $product): ?array
        {
            $formats = [
                'id'        => $this->settings['alfaomega_ebooks_format_attr_id'],
                'name'      => 'Formato',
                'slug'      => 'pa_book-format',
                'position'  => 0,
                'visible'   => false,
                'variation' => true,
                'options'   => [
                    'Impreso',
                    'Digital',
                    'Impreso + Digital',
                ],
            ];
            $found = false;
            $attributes = [];
            foreach ($product['attributes'] as $attribute) {
                if ($attribute->slug === 'pa_book-format') {
                    $attributes[] = array_merge((array) $attributes, $formats);
                    $found = true;
                } else {
                    $attributes[] = (array) $attributes;
                }
            }
            if (!$found) {
                $attributes[] = $formats;
            }

            $product = (array) $this->woocommerce
                ->put("products/{$product['id']}", [
                    'attributes' => $attributes,
                ]);

            return !empty($product) ? $product : null;
        }

        /**
         * Checks the format attribute in WooCommerce.
         * This method checks the format attribute in WooCommerce by searching for the attribute with the specified name.
         * If the attribute is not found, it creates a new attribute with the specified name.
         *
         * @return void
         * @throws \Exception
         */
        public function checkFormatAttribute(): void
        {
            if (empty($this->settings['alfaomega_ebooks_format_attr_id'])) {
                $productOptions = (array) get_option('alfaomega_ebooks_product_options');
                $productOptions['alfaomega_ebooks_format_attr_id'] = $this->getOrCreateFormatAttribute();
                update_option('alfaomega_ebooks_product_options', $productOptions);
                $this->settings['alfaomega_ebooks_format_attr_id'] = $productOptions['alfaomega_ebooks_format_attr_id'];
            }
        }

        /**
         * Gets or creates the format attribute in WooCommerce.
         * This method gets or creates the format attribute in WooCommerce by searching for the attribute with the specified name.
         * If the attribute is not found, it creates a new attribute with the specified name.
         *
         * @param string $name The name of the attribute to search for. Default is 'pa_book-format'.
         *
         * @return int Returns the ID of the format attribute.
         * @throws \Exception
         */
        public function getOrCreateFormatAttribute(string $name = 'pa_book-format'): int
        {
            // search the attribute
            $attributes = (array) $this->woocommerce->get('products/attributes');
            foreach ($attributes as $attribute) {
                if ($attribute->slug === $name) {
                    return $attribute->id;
                }
            }

            // if it doesn't exist create it
            $data = [
                'name'         => 'Formato',
                'slug'         => $name,
                'type'         => 'select',
            ];
            $formatAttribute = $this->woocommerce->post("products/attributes", $data);
            if (empty($formatAttribute)) {
                throw new Exception("Format attribute creation failed");
            }

            // create the attribute terms
            $options = [
                ['name' => 'Impreso', 'description' => 'Libro impreso'],
                ['name' => 'Digital', 'description' => 'Lectura en línea y descaga del PDF'],
                ['name' => 'Impreso + Digital', 'description' => 'Libro impreso, digital en línea y descarga del PDF'],
            ];
            foreach ($options as $option) {
                $newOption = $this->woocommerce->post("products/attributes/{$formatAttribute->id}/terms", $option);
                if (empty($newOption)) {
                    throw new Exception("Format attribute option creation failed");
                }
            }

            return $formatAttribute->id;
        }

        /**
         * Gets the variation data for a product in WooCommerce.
         * This method gets the variation data for a product in WooCommerce by creating an associative array with the specified data.
         * The data includes the description, SKU, regular price, status, virtual, downloadable, manage stock, stock quantity, stock status, weight, dimensions, shipping class, and attributes.
         *
         * @param array $product The product data to get the variation data for.
         * @param string $format The format of the product.
         * @param array $prices The prices of the product.
         * @param int $ebookId The eBook ID to get the variation data for.
         *
         * @return array Returns an associative array containing the variation data for the product.
         */
        protected function getVariationData(array $product, string $format, array $prices, int $ebookId): array
        {
            $uploads = wp_get_upload_dir();
            $ebooksDir = $uploads['baseurl'] . '/woocommerce_uploads/alfaomega_ebooks/';

            return match ($format) {
                'impreso' => [
                    'description'     => 'Libro impreso',
                    'sku'             => $product['id'] . '_printed',
                    'regular_price'   => $prices['regular_price'],
                    'status'          => 'publish',
                    'virtual'         => false,
                    'downloadable'    => false,
                    'manage_stock'    => true,
                    'stock_quantity'  => $product['stock_quantity'],
                    'stock_status'    => $product['stock_status'],
                    'weight'          => $product['weight'],
                    'dimensions'      => $product['dimensions'],
                    'shipping_class'  => $product['shipping_class'],
                    'attributes'      => [[
                        'id' => $this->settings['alfaomega_ebooks_format_attr_id'],
                        'option' => $format,
                    ]],
                ],
                'digital' => [
                    'description'     => 'Libro digital para lectura en línea y descarga del PDF con DRM',
                    'sku'             => $product['id'] . '_digital',
                    'regular_price'   => number_format($prices['regular_price']
                                                       * ($this->settings['alfaomega_ebooks_price'] / 100), 0),
                    'status'          => 'publish',
                    'virtual'         => true,
                    'downloadable'    => true,
                    'downloads'       => [
                        [ 'name' => 'PDF', 'file' => $ebooksDir . $ebookId ]
                    ],
                    'download_limit'  => -1,
                    'download_expiry' => 30,
                    'manage_stock'    => false,
                    'attributes'      => [
                        [
                            'id'     => $this->settings['alfaomega_ebooks_format_attr_id'],
                            'option' => $format,
                        ],
                    ],
                ],
                'impreso-digital' => [
                    'description'     => 'Libro impreso y libro digital para lectura en línea y descarga del PDF con DRM',
                    'sku'             => $product['id'] . '_printed_digital',
                    'regular_price'   => number_format($prices['regular_price'] * ($this->settings['alfaomega_ebooks_printed_digital_price'] / 100), 0),
                    'status'          => 'publish',
                    'virtual'         => true,
                    'downloadable'    => true,
                    'downloads'       => [
                        [ 'name' => 'PDF', 'file' => $ebooksDir . $ebookId ]
                    ],
                    'download_limit'  => -1,
                    'download_expiry' => 30,
                    'manage_stock'    => true,
                    'stock_quantity'  => $product['stock_quantity'],
                    'stock_status'    => $product['stock_status'],
                    'weight'          => $product['weight'],
                    'dimensions'      => $product['dimensions'],
                    'shipping_class'  => $product['shipping_class'],
                    'attributes'      => [[
                        'id' => $this->settings['alfaomega_ebooks_format_attr_id'],
                        'option' => $format,
                    ]],
                ],
            };
        }

        /**
         * Downloads an eBook.
         * This method downloads an eBook by its ID and download ID. It first retrieves the eBook metadata.
         * If the eBook is found, it constructs the file path for the eBook download.
         * If the file already exists, it returns the file path.
         * If the file does not exist, it retrieves the download file content and writes it to the file path.
         * If the file write is successful, it returns the file path.
         * If the eBook is not found, the download file content is empty, or the file write is unsuccessful, it returns an empty string.
         *
         * @param int $ebookId       The ID of the eBook to download.
         * @param string $downloadId The download ID of the eBook.
         *
         * @return string Returns the file path of the downloaded eBook if the download is successful, or an empty string if the download is unsuccessful.
         * @throws \Exception
         */
        public function downloadEbook(int $ebookId, string $downloadId): string
        {
            $eBook = $this->getPostMeta($ebookId);
            if (empty($eBook)) {
                return '';
            }

            $filePath = ALFAOMEGA_EBOOKS_PATH . "downloads/{$eBook['isbn']}_{$downloadId}.acsm";
            if (file_exists($filePath)) {
                return $filePath;
            }

            $content = $this->getDownloadFileContent($eBook['isbn'], $downloadId);
            if (empty($content)) {
                return '';
            }

            $success = file_put_contents($filePath, $content);
            if (! $success) {
                return '';
            }

            return $filePath;
        }

        /**
         * Reads an eBook.
         * This method reads an eBook by its ID and download ID. It first retrieves the eBook metadata.
         * If the eBook is found, it constructs the file path for the eBook download.
         * If the file already exists, it returns the file path.
         * If the file does not exist, it retrieves the download file content and writes it to the file path.
         * If the file write is successful, it returns the file path.
         * If the eBook is not found, the download file content is empty, or the file write is unsuccessful, it returns an empty string.
         *
         * @param int $ebookId       The ID of the eBook to read.
         * @param string $downloadId The download ID of the eBook.
         *
         * @return void
         * @throws \Exception
         */
        public function readEbook(int $ebookId, string $key): void
        {
            $this->validateAccess($ebookId, $key);

            $eBook = $this->getPostMeta($ebookId);
            if (empty($eBook)) {
                throw new Exception(esc_html__('Online eBook not available, please check order status', 'alfaomega-ebooks'));
            }

            require( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_reader_page.php' );
        }

        /**
         * Validates access to an eBook.
         * This method validates access to an eBook by its ID and download ID. It first retrieves the current user.
         * If the user is not found, it throws an exception.
         * It then retrieves the customer downloads for the user.
         * If the customer downloads are not found, it throws an exception.
         * If the requested download is not found, it throws an exception.
         * If the requested download is found, it returns true.
         *
         * @param int $ebookId The ID of the eBook to validate access for.
         * @param string $key   The download ID of the eBook.
         *
         * @return bool Returns true if access to the eBook is validated, or false if access to the eBook is not validated.
         * @throws \Exception
         */
        public function validateAccess(int $ebookId, string $key): bool
        {
            $customer = wp_get_current_user();
            if (empty($customer)) {
                throw new Exception(esc_html__('User not logged in yet', 'alfaomega-ebooks'));
            }

            $customerDownloads = (array) $this->woocommerce
                ->get("customers/{$customer->ID}/downloads", [
                    'download_id' => $key,
                ]);
            if (empty($customerDownloads)) {
                throw new Exception(esc_html__('eBook download not available, please check order status', 'alfaomega-ebooks'));
            }

            $requestedDownload = null;
            foreach ($customerDownloads as $download) {
                if (/*$download->download_id === $key &&*/
                    str_ends_with($download->file->file, "/$ebookId")) {
                    $requestedDownload = $download;
                    break;
                }
            }

            if (empty($requestedDownload)) {
                throw new Exception(esc_html__('eBook download not available, please check order status', 'alfaomega-ebooks'));
            }

            return true;
        }

        /**
         * Generates a URL for reading an eBook.
         * This method generates a URL for reading an eBook by its ID and download ID.
         * The URL is constructed using the site URL, the eBook ID, and the download ID.
         *
         * @param int $ebookId       The ID of the eBook to generate a URL for.
         * @param string $downloadId The download ID of the eBook.
         *
         * @return string Returns the URL for reading the eBook.
         */
        public function readEbookUrl(int $ebookId, string $downloadId): string
        {
            return site_url("alfaomega-ebooks/read/{$ebookId}?key={$downloadId}");
        }

        /**
         * Retrieves the download file content for an eBook from Alfaomega.
         * This method sends a GET or POST request to the Alfaomega API to retrieve the download file content for an eBook.
         * The eBook is identified by its ISBN and transaction ID, which are passed as parameters.
         * If rights are provided, a POST request is sent, otherwise a GET request is sent.
         * The method returns null if the API response code is not 200, the status of the content is not 'success', or the download file content is empty.
         *
         * @param string $isbn        The ISBN of the eBook to retrieve the download file content for.
         * @param string $transaction The transaction ID of the eBook.
         * @param string|null $rights Optional. The rights for the eBook. Default is null.
         *
         * @return string|null Returns the download file content for the eBook if the retrieval is successful, or null if the retrieval is unsuccessful.
         * @throws \Exception
         */
        public function getDownloadFileContent($isbn, $transaction, $rights = null): ?string
        {
            $result = $rights
                ? $this->api->post("/book/store/fulfilment/$isbn/$transaction", ["rights" => $rights])
                : $this->api->get("/book/store/fulfilment/$isbn/$transaction");

            if ($result['response']['code'] !== 200) {
                return null;
            }

            $link = json_decode($result['body'], true);
            if ($link['status'] == "success") {
                return $link['content'];
            }

            return null;
        }

        /**
         * Retrieves the post metadata for an eBook.
         * This method retrieves the post metadata for an eBook by its ID.
         * It retrieves the post metadata for the eBook post type, including the title, author, ISBN, PDF ID, eBook URL, date, and tag ID.
         *
         * @param int $postId The ID of the eBook to retrieve the post metadata for.
         *
         * @return array|null Returns an associative array containing the post metadata for the eBook if the post is found, or null if the post is not found.
         * @throws \Exception
         */
        public function getReaderData(int $ebookId, string $key): ?array
        {
            if (!$this->validateAccess($ebookId, $key)) {
                return null;
            };

            $eBook = $this->getPostMeta($ebookId);
            if (empty($eBook)) {
                return null;
            }

            $token = $this->getUserToken($eBook['isbn']);
            if (empty($token)) {
                return null;
            }

            return [
                'title'          => $eBook['title'],
                'isbn'           => $eBook['isbn'],
                'favicon'        => get_site_icon_url(),
                'readerUrl'      => $this->settings['alfaomega_ebooks_reader'],
                'libraryBaseUrl' => $this->settings['alfaomega_ebooks_panel'],
                'token'          => $token,
            ];
        }

        /**
         * Retrieves the user token for an eBook.
         * This method retrieves the user token for an eBook by its ISBN.
         * It retrieves the user token for the current user, or null if the current user is not found.
         *
         * @param string $isbn The ISBN of the eBook to retrieve the user token for.
         *
         * @return string|null Returns the user token for the eBook if the user is found, or null if the user is not found.
         * @throws \Exception
         */
        public function getUserToken(string $isbn): ?string
        {
            $customer = wp_get_current_user();
            if (empty($customer)) {
                return null;
            }

            $result = $this->api->post( '/user/access-token', [
                'email'          => $customer->data->user_email,
                'username'       => $customer->data->user_nicename,
                'password'       => "4lf40m3g4",
                'bookIsbn'       => $isbn,
                'partial_access' => false,
                'subdomain'      => 'ecommerce'
            ]);
            if ($result['response']['code'] !== 200) {
                return null;
            }

            $response = json_decode($result['body'], true);
            if ($response['status'] != "success") {
                return null;
            }

            return $response['token'];
        }
    }
}
