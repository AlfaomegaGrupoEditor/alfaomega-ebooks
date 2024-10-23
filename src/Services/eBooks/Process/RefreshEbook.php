<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega\EbookPostEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

/**
 * The refresh ebooks process.
 */
class RefreshEbook extends AbstractProcess implements ProcessContract
{
    /**
     * Constructs a new instance of the RefreshEbook class.
     *
     * This constructor method initializes the RefreshEbook process with the provided settings and eBook entity.
     * It calls the parent constructor with the provided settings.
     *
     * @param array $settings The settings for the RefreshEbook process. These settings can include various configuration options.
     * @param EbookPostEntity $entity The eBook entity to be processed. This entity represents an eBook in the system.
     */
    public function __construct(array $settings,
                                protected EbookPostEntity $entity
    ){
        parent::__construct($settings);
    }

    /**
     * Processes a batch of eBooks.
     *
     * This method takes an optional array of post IDs as input. If no array is provided, it retrieves a list of eBooks
     * from the database and enqueues an asynchronous action to refresh each eBook.
     *
     * The method uses the 'alfaomega_ebooks_queue_refresh' action to refresh each eBook. This action takes
     * an array of ISBNs and their associated post IDs as arguments.
     *
     * If the enqueuing of the action fails, the method throws an Exception with the message 'Refresh list queue failed'.
     *
     * If an array of post IDs is provided, the method retrieves the eBook data associated with these post IDs
     * and calls the 'single' method for each eBook.
     *
     * The method returns an array with the total number of eBooks refreshed.
     *
     * @param array $data An optional array of post IDs. If provided, the method will process only these eBooks.
     * @throws \Exception If the enqueuing of the refresh action fails.
     * @return array An array with the total number of eBooks refreshed.
     */
    public function batch(array $data = [], bool $async = false): array
    {
        if (empty($data)) {
            return $this->chunk();
        }

        $isbns = [];
        $toDelete = [];
        foreach ($data as $postId) {
            $post = get_post($postId);
            $meta = get_post_meta($postId, single: true);
            if (!empty($post) && !empty($meta)) {
                $sku = $meta['alfaomega_ebook_product_sku'][0] ?? '';
                $ebook = [
                    'id'           => $postId,
                    'isbn'         => $meta['alfaomega_ebook_isbn'][0] ?? '',
                    'title'        => $post->post_title,
                    'description'  => $post->post_content,
                    'adobe'        => $meta['alfaomega_ebook_id'][0] ?? '',
                    'html_ebook'   => $meta['alfaomega_ebook_url'][0] ?? '',
                    'printed_isbn' => $sku,
                    'product_sku'  => $sku,
                    'product_id'   => !empty($sku) ? wc_get_product_id_by_sku($sku) : 0,
                    'cover'        => $meta['alfaomega_ebook_cover'][0] ?? '',
                    'page_count'   => $meta['alfaomega_ebook_page_count'][0] ?? 0,
                ];
                $isbn = empty($ebook['isbn']) ? $ebook['printed_isbn'] : $ebook['isbn'];
                if (empty($isbn)) {
                    $toDelete[] = $postId;
                } else {
                    $isbns[$isbn] = $ebook;
                }
            }
        }

        // delete ebooks not valid
        if (!empty($toDelete)) {
            foreach ($toDelete as $postId) {
                $this->getEbookEntity()->delete($postId);
            }
        }

        $modified = [];
        if (!empty($isbns)) {
            $notFound = array_keys($isbns);
            $ebooks = $this->getEbookEntity()->index(array_keys($isbns));
            if (!empty($ebooks)) {
                foreach ($ebooks as $ebook) {
                    $key = array_search($ebook['isbn'], $notFound);
                    if ($key !== false) {
                        unset($notFound[$key]);
                    }

                    // check eBook post exists
                    $ebookPost = $isbns[$ebook['isbn']] ?? null;
                    if (empty($ebookPost)) {
                        continue; // eBook post not found update not needed
                    }

                    // check product linked
                    $productId = $isbns[$ebook['isbn']]['product_id'];
                    if (!empty($productId)) {
                        $product = wc_get_product($productId);
                        if ($product->get_type() === 'simple' &&
                            $product->get_attribute('pa_ebook') !== 'Desactivado') {
                            $modified[] = array_merge($ebookPost, $ebook);
                            continue; // product not linked, update not needed
                        }
                    }

                    // check if eBook changed
                    if ($ebook['title'] === $ebookPost['title'] &&
                        $ebook['description'] === $ebookPost['description'] &&
                        $ebook['adobe'] === $ebookPost['adobe'] &&
                        $ebook['html_ebook'] === $ebookPost['html_ebook'] &&
                        $ebook['cover'] === $ebookPost['cover'] &&
                        $ebook['printed_isbn'] === $ebookPost['printed_isbn'] &&
                        $ebook['page_count'] === $ebookPost['page_count'] ) {
                        continue; // didn't change anything, no update needed
                    }

                    $modified[] = array_merge($ebookPost, $ebook);
                }
            }
        }

        // delete ebooks not valid
        if (!empty($notFound)) {
            foreach ($notFound as $ebook) {
                $this->getEbookEntity()->delete($ebook['id']);
            }
        }

        if (empty($modified)) {
            return array_keys($isbns);
        }

        return $async
            ? $this->queueProcess($modified)
            : $this->doProcess($modified);
    }

    /**
     * Link the products to the ebooks synchronously.
     *
     * @param array $entities
     *
     * @return array|null
     * @throws \Exception
     */
    protected function doProcess(array $entities): ?array
    {
        $processed = [];
        foreach ($entities as $eBook) {
            $result = $this->single($eBook, postId: $eBook['id']);
            $processed[] = $result;
        }

        return $processed;
    }

    /**
     * Queue the process to update ebooks.
     *
     * @param array $entities
     *
     * @return array|null
     */
    protected function queueProcess(array $entities): ?array
    {
        $onQueue = [];
        foreach ($entities as $ebook) {
            /*$result = as_enqueue_async_action(
                'alfaomega_ebooks_queue_refresh',
                [$ebook, true, $ebook['id']]
            );*/
            $result = as_schedule_single_action(
                strtotime('+10 second'),
                'alfaomega_ebooks_queue_refresh',
                [$ebook, true, $ebook['id']]
            );
            if ($result !== 0) {
                $onQueue[] = $result;
            }
        }
        return $onQueue;
    }

    /**
     * Get the eBook entity.
     *
     * @return EbookPostEntity
     * @throws \Exception
     */
    protected function getEbookEntity(): EbookPostEntity
    {
        return $this->entity;
    }

    /**
     * Check if the eBook information matches the configuration in Alfaomega
     * Retrieve a chunk of data to process.
     * This method should be implemented by child classes to retrieve a chunk of data to process.
     * The method should return an array of data to process, or null if there is no more data to process.
     *
     * @return array|null An array of data to process, or null if there is no more data to process.
     * @throws \Exception
     */
    protected function chunk(): ?array
    {
        $onQueue = [];
        $limit = intval($this->settings['alfaomega_ebooks_import_limit']) ?? 1000;
        $countPerPage = $this->chunkSize;

        $page = 0;
        do {
            $countPerPage = min($limit, $countPerPage);
            $args = [
                'posts_per_page' => $countPerPage,
                'post_type'      => 'alfaomega-ebook',
                'orderby'        => 'ID',
                'order'          => 'ASC',
                'offset'         => $countPerPage * $page,
            ];
            $posts = get_posts($args);
            if (empty($posts)) {
                break;
            }

            $ebooks = array_column($posts, 'ID');
            $onQueue = array_merge($onQueue, $this->batch($ebooks, true));
            $page++;
        } while (count($posts) === $this->chunkSize && count($onQueue) < $limit);


        return $onQueue;
    }
}
