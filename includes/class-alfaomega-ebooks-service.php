<?php

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

        public function __construct(bool $initApi=true)
        {
            if ($initApi) {
                $this->getSettings();
                $this->api = new Alfaomega_Ebooks_Api($this->settings);
            }
        }

        public function getSettings(): void
        {
            $this->settings = array_merge(
                (array) get_option('alfaomega_ebooks_general_options'),
                (array) get_option('alfaomega_ebooks_platform_options'),
                (array) get_option('alfaomega_ebooks_api_options')
            );
        }

        public function importEbooks(): array
        {
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
                    as_enqueue_async_action(
                        'alfaomega_ebooks_queue_import',
                        [ $eBook ]
                    );
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
            $this->linkProduct($eBook);
        }

        public function refreshEbooks($postIds = null): array
        {
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
                        as_enqueue_async_action(
                            'alfaomega_ebooks_queue_refresh_list',
                            [ $isbns ]
                        );
                        throw new Exception('Queue action failed');
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
                    [ $isbns[$eBook['isbn']], $eBook ],
                    '',
                    true
                );
                if ($result === 0) {
                    throw new Exception('Queue action failed');
                }
            }
        }

        public function refreshEbook($postId, array $eBook): void
        {
            $eBook = $this->updateEbookPost($postId, $eBook);
            $this->linkProduct($eBook);
        }

        public function linkProducts(): array
        {
            // TODO: pull the eBook information of each linked product
            // update or create the eBook information
            // refresh de link of each product

            return [
                'linked' => rand(0, 2)
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

        protected function linkProduct($ebook): void
        {
            // link WooCommerce Product to eBook
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
    }
}
