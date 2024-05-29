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

        protected Alfaomega_Ebooks_Api $api;
        protected array $settings = [];
        protected ?Client $woocommerce = null;

        public function __construct(bool $initApi=true)
        {
            if ($initApi) {
                $this->initWooCommerceClient();
                $this->getSettings();
                $this->api = new Alfaomega_Ebooks_Api($this->settings);
            }
        }

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
                    'timeout'          => 180
                ]
            );

            return $this;
        }

        public function getSettings(): void
        {
            $this->settings = array_merge(
                (array) get_option('alfaomega_ebooks_general_options'),
                (array) get_option('alfaomega_ebooks_platform_options'),
                (array) get_option('alfaomega_ebooks_api_options'),
                (array) get_option('alfaomega_ebooks_product_options')
            );
        }

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
                'imported' => $imported
            ];
        }

        /**
         * Will be called from the job
         * @param array $eBook
         *
         * @return void
         * @throws \Exception
         */
        public function importEbook(array $eBook): void
        {
            $eBook = $this->updateEbookPost(null, $eBook);
            //$this->linkProduct($eBook);
        }

        public function refreshEbooks($postIds = null): array
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
                'refreshed' => $total
            ];
        }

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

        public function refreshEbook($postId, array $eBook): void
        {
            $eBook = $this->updateEbookPost($postId, $eBook);
            //$this->linkProduct($eBook);
        }

        public function linkProducts($postIds): array
        {
            $this->checkFormatAttribute();
            $linked = 0;
            foreach ($postIds as $postId) {
                $this->linkProduct($this->getPostMeta($postId));
                $linked++;
            }

            return [
                'linked' => $linked
            ];
        }

        protected function getPosts($count, $query = []): WP_Query
        {
            $args = array_merge($query, [
                'posts_per_page' => $count,
                'paged'          => 1,
                'post_type'      => 'alfaomega-ebook',
            ]);

            return new WP_Query( $args );
        }

        protected function savePostMeta($postId, $data): array
        {
            $fields = [
                'alfaomega_ebook_isbn' => [
                    'old'     => get_post_meta($postId, 'alfaomega_ebook_isbn', true),
                    'new'     => $data['isbn'],
                    'default' => '',
                ],
                'alfaomega_ebook_id'   => [
                    'old'     => get_post_meta($postId, 'alfaomega_ebook_id', true),
                    'new'     => !empty($data['adobe']) ? $data['adobe'] : '',
                    'default' => '',
                ],
                'alfaomega_ebook_url'  => [
                    'old'     => get_post_meta($postId, 'alfaomega_ebook_url', true),
                    'new'     => !empty($data['html_ebook']) ? $data['html_ebook'] : '',
                    'default' => '',
                ],
                'alfaomega_ebook_tag_id'  => [
                    'old'     => get_post_meta($postId, 'alfaomega_ebook_tag_id', true),
                    'new'     => !empty($data['isbn']) ? $this->getTagId($data['isbn']) : '',
                    'default' => '',
                ],
            ];

            wp_publish_post($postId);
            foreach ( $fields as $field => $data ) {
                $new_value = sanitize_text_field( $data['new'] );
                $old_value = $data['old'];

                if ( empty( $new_value ) ) {
                    $new_value = $data['default'];
                }

                update_post_meta( $postId, $field, $new_value, $old_value );
            }

            return $this->getPostMeta($postId);
        }

        protected function getPostMeta($postId): array
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

        protected function retrieveEbooks($isbn = '', $count=100): array
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

        protected function updateEbookPost($postId, $data): array
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
         * @param $ebook
         * @see https://woocommerce.github.io/woocommerce-rest-api-docs/#introduction
         *
         * @return void
         */
        protected function linkProduct($ebook): void
        {
            // TODO @see https://stackoverflow.com/questions/65204134/woocommerce-api-giving-json-syntax-error-on-every-request
            $product = $this->getProduct($ebook['tag_id'], $ebook['title']);
            if (empty($product)) {
                throw new Exception("Products with digital ISBN {$ebook['isbn']} not found");
            }

            $product = $this->updateProductType($product);
            if (empty($product)) {
                throw new Exception("Product type not supported");
            }

            $product = $this->updateProductFormats($product);
            if (empty($product)) {
                throw new Exception("Product formats failed");
            }

            $product = $this->updateProductVariants($product);
            if (empty($product)) {
                throw new Exception("Product variants failed");
            }
        }

        public function queueStatus($queue): array
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

        public function getProduct(int $tagId, string $title = null): ?array
        {
            $products = (array) $this->woocommerce
                ->get("products", [
                    'tag'=> $tagId
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

        public function findProduct(string $title, int $tagId): ?array
        {
            $products = (array) $this->woocommerce
                ->get("products", [
                    'search' => $title
                ]);

            if (count($products) === 1) {
                $product = $products[0];
                $this->woocommerce
                    ->put("products/{$product->id}", [
                        'tags' => [[ 'id' => $tagId ]]
                    ]);

                return (array) $product;
            }

            return null;
        }

        public function updateProductType(array $product, string $type = 'variable'): ?array
        {
            if ($product['type'] !== $type) {
                $regularPrice = $product['regular_price'];
                $salePrice = $product['sale_price'];
                $product = (array) $this->woocommerce
                    ->put("products/{$product['id']}", [
                        'type' => $type
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

        public function updateProductVariants(array $product): ?array
        {
            $variations = (array) $this->woocommerce
                ->get("products/{$product['id']}/variations");
            if (!empty($variations)) {
                $printedPrices = [
                    'regular_price' => 0,
                    'sale_price'    => 0,
                ];
                foreach ($variations as $variation) {
                    if ($variation->name === 'Impreso') {
                        $printedPrices['regular_price'] = $variation['regular_price'];
                        $printedPrices['sale_price'] = $variation['sale_price'];
                    }
                    break;
                }
                $newVariations = [];
                foreach ($variations as $variation) {
                    $newVariations[] = (array) $variation;
                    switch ($variation->name) {
                        case 'Impreso':
                            continue;
                        case 'Digital':

                            break;
                        case 'Digital y Impreso':
                            break;
                    }
                }
            }


            $product = (array) $this->woocommerce
                ->put("products/{$product['id']}", [
                    'variations' => $variations
                ]);

            return !empty($product) ? $product : null;
        }

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
                    'Impreso y digital',
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
                    'attributes' => $attributes
                ]);

            return !empty($product) ? $product : null;
        }

        public function checkFormatAttribute(): void
        {
            if (empty($this->settings['alfaomega_ebooks_format_attr_id'])) {
                $productOptions = (array) get_option('alfaomega_ebooks_product_options');
                $productOptions['alfaomega_ebooks_format_attr_id'] = $this->getOrCreateFormatAttribute('pa_book-format2');
                update_option('alfaomega_ebooks_product_options', $productOptions);
                $this->settings['alfaomega_ebooks_format_attr_id'] = $productOptions['alfaomega_ebooks_format_attr_id'];
            }
        }

        public function getOrCreateFormatAttribute($name = 'pa_book-format'): int
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
    }
}
