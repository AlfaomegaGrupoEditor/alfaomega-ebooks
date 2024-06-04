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

        /**
         * @see https://stackoverflow.com/questions/65204134/woocommerce-api-giving-json-syntax-error-on-every-request
         * @return $this
         *
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
            // FIXME too slow
            // $this->linkProduct($eBook, false);
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
            $this->linkProduct($eBook, false);
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

        public function linkEbooks($productsId): array
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
         * @param bool $notFoundError
         *
         * @return void
         * @throws \Exception
         * @see https://woocommerce.github.io/woocommerce-rest-api-docs/#introduction
         */
        protected function linkProduct($ebook, $notFoundError=true): void
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

            $product = $this->updateProductVariants($product, $prices);
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

        public function updateProductVariants(array $product, array $prices): ?array
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
                                    'sale_price' => $variation->sale_price
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
                $data = $this->getVariationData($product, $format, $prices);
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
                    'attributes' => $attributes
                ]);

            return !empty($product) ? $product : null;
        }

        public function checkFormatAttribute(): void
        {
            if (empty($this->settings['alfaomega_ebooks_format_attr_id'])) {
                $productOptions = (array) get_option('alfaomega_ebooks_product_options');
                $productOptions['alfaomega_ebooks_format_attr_id'] = $this->getOrCreateFormatAttribute();
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

        protected function getVariationData(array $product, string $format, array $prices): array
        {
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
                        'option' => $format
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
                    //'downloads'       => 'something_goes_here',
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
                    //'downloads'       => 'something_goes_here',
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
                        'option' => $format
                    ]],
                ],
            };
        }
    }
}
