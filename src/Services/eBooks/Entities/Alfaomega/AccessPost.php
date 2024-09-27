<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\AbstractEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Carbon\Carbon;
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
     * Expire the access to an eBook.
     * @param int $postId
     *
     * @return void
     * @throws \Exception
     */
    public function expire(int $postId): void
    {
        update_post_meta(
            $postId,
            'alfaomega_access_status',
            'expired',
            get_post_meta($postId, 'alfaomega_access_status', true)
        );
    }

    /**
     * Search the current user eBooks access based on various criteria.
     * This method allows searching for eBooks by category, search term, type, status, and other parameters.
     * It returns an array of search results.
     *
     * @param string|null $category  The category to filter by. Default is null.
     * @param string|null $search    The search term to filter by. Default is null.
     * @param string|null $type      The type of eBook to filter by. Default is null.
     * @param string|null $status    The status of the eBook to filter by. Default is null.
     * @param int $page              The page number for pagination. Default is 1.
     * @param int $perPage           The number of results per page. Default is 8.
     * @param string $orderBy        The field to order the results by. Default is 'title'.
     * @param string $orderDirection The direction to order the results ('asc' or 'desc'). Default is 'asc'.
     *
     * @return array An array of search results.
     * @throws \Exception
     */
    public function search(
        string $category = null,
        string $search = null,
        string $type = null,
        string $status = null,
        int $page = 1,
        int $perPage = 8,
        string $orderBy = 'title',
        string $orderDirection = 'asc'
    ): array {
        global $wpdb;

        $currentUserId = get_current_user_id();
        $offset = ($page - 1) * $perPage;

        // Map orderBy to the corresponding column or meta field
        $orderByFields = [
            'title'       => 'p.post_title',
            'created_at'  => 'p.post_date',
            'status'      => 'status.meta_value',
            'valid_until' => 'valid_until.meta_value',
            'access_at'   => 'access_at.meta_value',
        ];

        // Start building the base SQL query for data fetching
        $baseSql = "FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm_cover ON (p.ID = pm_cover.post_id AND pm_cover.meta_key = 'alfaomega_access_cover')
                LEFT JOIN {$wpdb->postmeta} pm_download ON (p.ID = pm_download.post_id AND pm_download.meta_key = 'alfaomega_access_download')
                LEFT JOIN {$wpdb->postmeta} pm_read ON (p.ID = pm_read.post_id AND pm_read.meta_key = 'alfaomega_access_read')
                LEFT JOIN {$wpdb->postmeta} status ON (p.ID = status.post_id AND status.meta_key = 'alfaomega_access_status')
                LEFT JOIN {$wpdb->postmeta} pm_type ON (p.ID = pm_type.post_id AND pm_type.meta_key = 'alfaomega_access_type')
                LEFT JOIN {$wpdb->postmeta} valid_until ON (p.ID = valid_until.post_id AND valid_until.meta_key = 'alfaomega_access_due_date')
                LEFT JOIN {$wpdb->postmeta} access_at ON (p.ID = access_at.post_id AND access_at.meta_key = 'alfaomega_access_at')
                WHERE p.post_type = 'alfaomega-access'
                  AND p.post_status = 'publish'
                  AND p.post_author = %d";

        $queryParams = [$currentUserId]; // Always add the current user ID

        // Add filters to the base query
        if ($category) {
            $baseSql .= " AND EXISTS (
                        SELECT 1
                        FROM {$wpdb->term_relationships} tr
                        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
                        WHERE t.term_id IN ({$category}) AND tr.object_id = p.ID AND tt.taxonomy = 'product_cat'
                      )";
        }

        if ($search) {
            $baseSql .= " AND (p.post_title LIKE %s OR p.post_content LIKE %s)";
            $searchTerm = '%' . $wpdb->esc_like($search) . '%';
            $queryParams[] = $searchTerm;
            $queryParams[] = $searchTerm;
        }

        if ($type) {
            $baseSql .= " AND pm_type.meta_value = %s";
            $queryParams[] = $type;
        }

        if ($status) {
            $baseSql .= " AND status.meta_value = %s";
            $queryParams[] = $status;
        }

        // Add the ORDER BY clause
        if (isset($orderByFields[$orderBy])) {
            $baseSql .= " ORDER BY {$orderByFields[$orderBy]} " . esc_sql($orderDirection);
        }

        // Add LIMIT and OFFSET for pagination
        $dataSql = "SELECT p.ID, 
                        p.post_title as title, 
                        p.post_date as addedAt,
                        p.post_parent as ebookId,
                        pm_cover.meta_value as cover,
                        pm_download.meta_value as download,
                        '' as download_url,
                        pm_read.meta_value as `read`,
                        '' as read_url,
                        status.meta_value as status,
                        pm_type.meta_value as accessType,
                        valid_until.meta_value as validUntil
                $baseSql
                LIMIT %d OFFSET %d";

        $queryParams[] = $perPage;
        $queryParams[] = $offset;

        // Prepare and execute the query for fetching data
        $dataQuery = $wpdb->prepare($dataSql, ...$queryParams);
        $results = $wpdb->get_results($dataQuery);

        // Fetch the categories for each post
        foreach ($results as $key => &$result) {
            // Build the category path
            $categories = get_the_terms($result->ID, 'product_cat');
            if ($categories && !is_wp_error($categories)) {
                $categoryPath = array_map(function ($cat) {
                    return $cat->name;
                }, $categories);
                $result->categories = implode(' > ', $categoryPath);
            } else {
                $result->categories = '';
            }

            $result->title = strtoupper($result->title);

            // Add the post URL
            $ebookPost = Service::make()->ebooks()
                ->ebookPost()
                ->get($result->ebookId);
            if (!empty($ebookPost)) {
                $product = wc_get_product($ebookPost['product_id']);
                $result->url = $product ? $product->get_permalink() : '';
            }

            // check the expiration dade
            if (in_array($result->status, ['created', 'active'])) {
                if (!empty($result->validUntil) && Carbon::parse($result->validUntil)->isPast()) {
                    $result->status = 'expired';
                    $this->expire($result->ID);
                    $result->read = false;
                    $result->download = false;
                    continue;
                }
            } else {
                $result->read = false;
                $result->download = false;
                continue;
            }

            // Add the read URL
            if ($result->read) {
                $result->read_url = site_url("alfaomega-ebooks/read/{$result->ebookId}?access={$result->ID}");
            }

            // Add the download URL
            if ($result->download) {
                $result->download_url = site_url("alfaomega-ebooks/download/{$result->ebookId}?access={$result->ID}");
            }

            // format dates
            $result->addedAt = Carbon::parse($result->addedAt)->format('d/m/Y');
            $result->validUntil = empty($result->validUntil)
                ? '-'
                : Carbon::parse($result->validUntil)->format('d/m/Y');
        }

        // Count query to get total results (without LIMIT and OFFSET)
        $countSql = "SELECT COUNT(1) $baseSql";

        // Prepare and execute the count query
        $countQuery = $wpdb->prepare($countSql, ...$queryParams);
        $total = $wpdb->get_var($countQuery);

        return [
            'data' => $results,
            'meta' => [
                'total'        => $total,
                'pages'        => ceil($total / $perPage),
                'current_page' => $page,
            ],
        ];
    }

    /**
     * Get the catalog of eBooks for the current user.
     *
     * This method fetches the catalog of eBooks for the current user.
     * It groups the eBooks by category and returns the categories as a tree structure.
     *
     * @return array The catalog of eBooks for the current user.
     */
    public function catalog(): array
    {
        global $wpdb;
        $currentUserId = get_current_user_id();

        // Query to get all alfaomega-access posts for the current user with associated categories
        $query = $wpdb->prepare("
            SELECT t.term_id, t.slug, t.name, tt.parent, COUNT(p.ID) as book_count
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE p.post_type = 'alfaomega-access'
              AND p.post_status = 'publish'
              AND p.post_author = %d
              AND tt.taxonomy = 'product_cat'
            GROUP BY t.term_id
            ORDER BY t.name ASC
        ", $currentUserId);

        // Execute the query and get the results
        $results = $wpdb->get_results($query);

        // Build a tree structure based on the parent-child relationships
        $categoryTree = [];

        // Create an associative array for easy parent-child lookup
        $categories = array_column($results, null, 'term_id');

        // The categories with parent = 0
        $rootCategories = [];

        foreach ($categories as $key => $result) {
            if (! isset($categoryTree[$key])) {
                $categoryTree[$key] = [
                    'id'       => $key,
                    'title'    => $result->name,
                    'children' => [],
                ];
            }

            if ($result->parent === 0) {
                if (!in_array($key, $rootCategories)) {
                    $rootCategories[] = $key;
                }
            } else {
                if (isset($categoryTree[$result->parent])) {
                    // exists in $categoryTree
                    $categoryTree[$result->parent]['children'][] = $key;
                } elseif (isset($categories[$result->parent])) {
                    // exists in $categories but is not moved to $categoryTree yet
                    $categoryTree[$result->parent] = [
                        'id'       => $result->parent,
                        'title'    => $categories[$result->parent]->name,
                        'children' => [$key],
                    ];
                } else {
                    // load all parents until root
                    $parent = $result->parent;
                    $error = false;
                    $newKey = $key;
                    do {
                        $term = get_term($parent, 'product_cat');
                        if ($term) {
                            $categoryTree[$parent] = [
                                'id'       => $parent,
                                'title'    => $term->name,
                                'children' => [$newKey],
                            ];
                            $newKey = $parent;
                            $parent = $term->parent;
                        } else {
                            $error = true;
                        }
                    } while ($parent !== 0 || $error);
                    if (!in_array($key, $rootCategories)) {
                        $rootCategories[] = $newKey;
                    }
                }
            }
        }

        return [
            'root' => $rootCategories,
            'tree' => $categoryTree,
        ];
    }
}
