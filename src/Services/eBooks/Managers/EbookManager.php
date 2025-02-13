<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega\AccessPost;
use AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega\EbookPost;
use AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega\SamplePost;
use AlfaomegaEbooks\Services\eBooks\Process\ImportEbook;
use AlfaomegaEbooks\Services\eBooks\Process\RefreshEbook;
use AlfaomegaEbooks\Services\eBooks\Service;
use Carbon\Carbon;
use Exception;
use WC_Product_Query;
use WP_Query;

/**
 * The ebook manager.
 */
class EbookManager extends AbstractManager
{
    /**
     * The ImportEbook instance.
     *
     * @var ImportEbook
     */
    protected ImportEbook $importEbook;

    /**
     * The RefreshEbook instance.
     *
     * @var RefreshEbook
     */
    protected RefreshEbook $refreshEbook;

    /**
     * The EbookPost instance.
     *
     * @var EbookPost
     */
    protected EbookPost $ebookPost;

    /**
     * The AccessPost instance.
     *
     * @var EbookPost
     */
    protected AccessPost $accessPost;

    /**
     * The SamplePost instance.
     *
     * @var SamplePost
     */
    protected SamplePost $samplePost;

    /**
     * The EbookManager constructor.
     *
     * @param Api $api The API.
     * @param array $settings The settings.
     */
    public function __construct(Api $api, array $settings) {
        parent::__construct($api, $settings);

        $this->ebookPost = EbookPost::make($api);
        $this->accessPost = AccessPost::make();
        $this->samplePost = SamplePost::make();
        $this->importEbook = new ImportEbook($settings, $this->ebookPost);
        $this->refreshEbook = new RefreshEbook($settings, $this->ebookPost);
    }

    /**
     * Import the ebooks.
     *
     * @return ImportEbook
     */
    public function importEbook(): ImportEbook
    {
        return $this->importEbook;
    }

    /**
     * Refresh the ebooks.
     *
     * @return RefreshEbook
     */
    public function refreshEbook(): RefreshEbook
    {
        return $this->refreshEbook;
    }

    /**
     * Get the EbookPost instance.
     *
     * @return EbookPost
     */
    public function ebookPost(): EbookPost
    {
        return $this->ebookPost;
    }

    /**
     * Get the AccessPost instance.
     *
     * @return EbookPost
     */
    public function accessPost(): AccessPost
    {
        return $this->accessPost;
    }

    /**
     * Get the SamplePost instance.
     *
     * @return SamplePost
     */
    public function samplePost(): SamplePost
    {
        return $this->samplePost;
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
     * @param bool $purchase     The request is from the purchase link.
     *
     * @return string Returns the file path of the downloaded eBook if the download is successful, or an empty string if the download is unsuccessful.
     * @throws \Exception
     */
    public function download(int $ebookId, string $downloadId, bool $purchase = true): string
    {
        if (!$purchase && !$this->validateAccess($ebookId, $downloadId, 'download')) {
            return '';
        }

        $eBook = $this->ebookPost->get($ebookId);
        if (empty($eBook)) {
            return '';
        }

        // If the download is not from a purchase, retrieve the downloadId
        // from the user downloads using the order_id and product_id
        $accessPost = $this->accessPost->get(intval($downloadId));
        if (!empty($accessPost['download_id'])) {
            $filePath = ALFAOMEGA_EBOOKS_PATH . "downloads/" . $accessPost['download_id'];
            if (file_exists($filePath)) {
                return $filePath;
            }
        }

        if (!$purchase && !empty($accessPost['orderId'])) {
            // TODO: Not tested yet
            $customerDownloads = Service::make()
                ->wooCommerce()
                ->getCustomerDownloads(get_current_user_id());
            foreach ($customerDownloads as $download) {
                if ($download->product_id == $eBook['product_id']) {
                    $downloadId = $download->download_id;
                    break;
                }
            }
        }

        $filename = md5("{$eBook['isbn']}_{$downloadId}") . '.acsm';
        $filePath = ALFAOMEGA_EBOOKS_PATH . "downloads/$filename";
        if (file_exists($filePath)) {
            $this->accessPost->updateDownloadId($accessPost['id'], $filename);
            return $filePath;
        }

        $content = $this->getContent($eBook['isbn'], $downloadId);
        if (empty($content)) {
            return '';
        }

        $success = file_put_contents($filePath, $content);
        if (! $success) {
            throw new Exception("Error writing file $filePath for eBook download");
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
     * @param bool $purchase     The request is from the purchase link.
     *
     * @return void
     * @throws \Exception
     */
    public function read(int $ebookId, string $key, bool $purchase = true): void
    {
        $valid = $purchase
            ? $this->validate($ebookId, $key)
            : $this->validateAccess($ebookId, $key, 'read');

        $eBook = $this->ebookPost->get($ebookId);
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
    public function validate(int $ebookId, string $key): bool
    {
        $customer = wp_get_current_user();
        if (empty($customer)) {
            throw new Exception(esc_html__('User not logged in yet', 'alfaomega-ebooks'));
        }

        $customerDownloads = Service::make()->wooCommerce()
            ->getCustomerDownloads($customer->ID);

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
     * Validates access to an eBook checking the access post.
     *
     * @param int $ebookId
     * @param int $accessId
     * @param bool $purchase
     *
     * @return bool
     * @throws \Exception
     */
    public function validateAccess(int $ebookId, string $accessId = '', bool $purchase = true): bool
    {
        // user should be logged in
        $userId = get_current_user_id();
        if (empty($userId)) {
            return false;
        }

        // ebook should exist
        $bookPost = $this->ebookPost->get($ebookId);
        if (empty($bookPost)) {
            return false;
        }

        // access should exist
        $accessPost = empty($accessId)
            ? $this->accessPost->find($ebookId, $userId)
            : $this->accessPost->get($accessId);
        if (empty($accessPost)) {
            return false;
        }

        if (!$accessPost['read']) {
            return false;
        }

        // Check valid until and expire|activate the access if necessary
        if (!empty($accessPost['validUntil'])
            && Carbon::parse($accessPost['validUntil'])->isPast()) {
            $this->accessPost->expire($accessPost['id']);

            return false;
        } elseif ($accessPost['status'] === 'created') {
            $this->accessPost->activate($accessPost['id']);
        }

        $this->accessPost
            ->touch($accessPost['id'], $purchase ? 'download' : 'read');

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
    public function readUrl(int $ebookId, string $downloadId): string
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
    public function getContent(string $isbn, string $transaction, array $rights = null): ?string
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
     * Retrieves the reader data for an eBook.
     * @param int $ebookId
     * @param string $key
     * @param bool $purchase
     *
     * @return array|null
     * @throws \Exception
     */
    public function getReaderData(int $ebookId, string $key, bool $purchase = true): ?array
    {
        $validate = $purchase
            ? $this->validate($ebookId, $key)
            : $this->validateAccess($ebookId, $key, 'read');
        if (!$validate) {
            return null;
        };

        $eBook = $this->ebookPost->get($ebookId);
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

    /**
     * Searches for eBooks.
     * This method searches for eBooks by a search query, limit, and page.
     *
     * @param string $searchQuery The search query to search for.
     * @param int $limit          The limit of items to retrieve. Default is 50.
     * @param int $page           The page of items to retrieve. Default is 1.
     *
     * @return array Returns an associative array containing the search results for the eBooks.
     */
    public function search(string $searchQuery = '', int $limit = -1, int $page = 1): array
    {
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => $limit,
            'paged'          => $page,
            'paginate'       => true,
            'orderby'        => 'post_title',
            'order'          => 'asc',
            'return'         => 'objects',
            'status'         => 'publish',
            'type'           => 'variable',
            'visibility'     => 'catalog',
            'tax_query'      => [
                [
                    'taxonomy' => 'pa_ebook',
                    'field'    => 'slug',
                    'terms'    => 'si',
                ],
            ],

        ];

        if (!empty($searchQuery)) {
            $args['s'] = $searchQuery;
            // FIXME: Doesn't work property with wc_get_products
            $args['meta_query'] = [
                [
                    'key'     => 'alfaomega_ebooks_ebook_isbn',
                    'value'   => $searchQuery,
                    'compare' => 'LIKE',
                ],
            ];
        } else {
            $args['meta_query'] = [
                [
                    'key'     => 'alfaomega_ebooks_ebook_isbn',
                    'value'   => '',
                    'compare' => '!=',
                ],
            ];
        }

        $data = [];
        $result = wc_get_products($args);
        // A hack to search by ISBN if no results are found
        if ($result->total === 0 && !empty($searchQuery)) {
            unset($args['s']);
            $query = new WP_Query($args);
            $result = (object) [
                'products'      => $query->get_posts(),
                'total'         => $query->found_posts,
                'max_num_pages' => $query->max_num_pages,
            ];
        }

        foreach ($result->products as $product) {
            if ($product instanceof \WP_Post) {
                $product = wc_get_product($product);
            }
            $image_id = $product->get_image_id();
            $image_url = wp_get_attachment_url($image_id);
            $isbn = $product->get_meta('alfaomega_ebooks_ebook_isbn');
            $data[] = [
                'id'    => $product->get_id(),
                'title' => $product->get_name() . " ($isbn)",
                'isbn'  => $isbn,
                'cover' => $image_url,
            ];
        }

        return [
            'items'   => $data,
            'total'   => $result->total ?? 0,
            'pages'   => $result->max_num_pages ?? 0,
            'current' => $page,
        ];
    }

    /**
     * Updates the catalog import by processing chunks of 'alfaomega-ebook' posts.
     * This method handles the update of imported eBooks in store identified by AO_STORE_UUID.
     * Processes the posts in chunks and sends them to the API for updating the catalog status as completed.
     *
     * @return void
     * @throws \Exception If AO_STORE_UUID is not defined or API response indicates a failure.
     */
    public function updateCatalogImport(): void
    {
        global $wpdb;

        $chunkSize = 100;
        $page = 0;
        $clear = true;
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
                $isbns = array_map(function ($post) { return $post->isbn; }, $posts);
                $this->ebookPost->updateImported($isbns, 'completed', $clear);
                $page++;
                $clear = false;
            }
        } while (! empty($posts));
    }
}
