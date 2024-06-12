<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\EbookPost;
use AlfaomegaEbooks\Services\eBooks\Process\ImportEbook;
use AlfaomegaEbooks\Services\eBooks\Process\RefreshEbook;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

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
     * The EbookManager constructor.
     *
     * @param Api $api The API.
     * @param array $settings The settings.
     */
    public function __construct(Api $api, array $settings) {
        parent::__construct($api, $settings);

        $this->ebookPost = EbookPost::make($api);
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
    public function download(int $ebookId, string $downloadId): string
    {
        $eBook = $this->ebookPost->get($ebookId);
        if (empty($eBook)) {
            return '';
        }

        $filePath = ALFAOMEGA_EBOOKS_PATH . "downloads/{$eBook['isbn']}_{$downloadId}.acsm";
        if (file_exists($filePath)) {
            return $filePath;
        }

        $content = $this->getContent($eBook['isbn'], $downloadId);
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
    public function read(int $ebookId, string $key): void
    {
        $this->validate($ebookId, $key);

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
        if (!$this->validate($ebookId, $key)) {
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
}
