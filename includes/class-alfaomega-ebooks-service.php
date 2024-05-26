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

        public function __construct()
        {
            $this->getSettings();
            $this->api = new Alfaomega_Ebooks_Api($this->settings);
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
            $latestBook = $this->latestPost();
            $isbn = empty($latestBook) ? '' : $latestBook['isbn'];
            $countPerPage = 100;
            $page = 0;
            $imported = 0;
            do {
                $eBooks = $this->retrieveEbooks($isbn, $countPerPage, $page);
                foreach ($eBooks as $eBook) {
                    $eBook = $this->updateEbookPost(null, $eBook);
                    $this->linkProduct($eBook);
                }
                $imported += count($eBooks);
            } while (count($eBooks) === $countPerPage);

            return [
                'imported' => $imported
            ];
        }

        public function refreshEbooks(): array
        {
            $postsPerPage = 50;
            $page = 0;
            $args = [
                'posts_per_page' => $postsPerPage,
                'post_type'      => 'alfaomega-ebook',
            ];
            $isbns = [];

            do {
                $args['offset'] = $postsPerPage * $page;
                $posts = get_posts($args);
                foreach ($posts as $post) {
                    $isbn = get_post_meta($post->ID, 'alfaomega_ebook_isbn', true);
                    $isbns[$isbn] = $post->ID;
                }

                $eBooks = $this->getEbooksInfo(array_keys($isbns));
                foreach ($eBooks as $eBook) {
                    $eBook = $this->updateEbookPost($isbns[$eBook['isbn']], $eBook);
                    $this->linkProduct($eBook);
                }
                $page++;
            } while (count($posts) === $postsPerPage);

            return [
                'refreshed' => count($isbns)
            ];
        }

        public function linkProducts(): array
        {
            // pull the eBook information of each linked product
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
                    'new'     => $data['pdf_id'],
                    'default' => '',
                ],
                'alfaomega_ebook_url'  => [
                    'old'     => get_post_meta($postId, 'alfaomega_ebook_url', true),
                    'new'     => $data['ebook_url'],
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

        protected function getPostMeta($post): array
        {
            return [
                'id'        => $post->ID,
                'title'     => $post->post_title,
                'author'    => $post->post_author,
                'isbn'      => get_post_meta($post->ID, 'alfaomega_ebook_isbn', true),
                'pdf_id'    => get_post_meta($post->ID, 'alfaomega_ebook_id', true),
                'ebook_url' => get_post_meta($post->ID, 'alfaomega_ebook_url', true),
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

            return $this->getPostMeta($posts[0]);
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

            return $this->getPostMeta($posts[0]);
        }

        protected function getEbooksInfo(array $isbns): array
        {
            // get eBooks info from Alfaomega
            $response = $this->api->post("/book/index", ['isbns' => $isbns]);
            if ($response['response']['code'] !== 200) {
                throw new Exception($response['response']['message']);
            }
            return json_decode($response['body'], true)['data'];
        }

        protected function retrieveEbooks($isbn = '', $count=100, $page=0): array
        {
            // pull from Panel all eBooks updated after the specified book
            $response = $this->api->get("/book/index/$isbn?page={$page}&items={$count}");
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
            }

            return [];
        }

        protected function linkProduct($ebook): void
        {
            // link WooCommerce Product to eBook
        }
    }
}
