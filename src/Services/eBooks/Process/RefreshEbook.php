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
    public function __construct(
        array $settings,
        protected EbookPostEntity $entity)
    {
        parent::__construct($settings);
    }

    /**
     * Processes a single eBook.
     * This method takes an eBook array, a boolean indicating whether to throw an error, and an optional post ID as input.
     * It updates the eBook entity with the provided post ID and eBook data.
     * If the 'updateProduct' property is true, it also links the eBook to a WooCommerce product.
     *
     * @param array $eBook     The eBook data.
     * @param bool $throwError Indicates whether to throw an error.
     * @param int|null $postId The post ID of the eBook. If provided, the method will process only this eBook.
     *
     * @return void
     * @throws \Exception
     */
    public function single(array $eBook, bool $throwError=false, int $postId = null): void
    {
        $eBook = $this->entity->update($postId, $eBook);

        if ($this->updateProduct) {
            Service::make()->wooCommerce()
                ->linkProduct()
                ->single($eBook, false);
        }
    }

    /**
     * Processes a batch of eBooks.
     *
     * This method takes an optional array of post IDs as input. If no array is provided, it retrieves a list of eBooks
     * from the database and enqueues an asynchronous action to refresh each eBook.
     *
     * The method uses the 'alfaomega_ebooks_queue_refresh_list' action to refresh each eBook. This action takes
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
    public function batch(array $data = []): array
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

        if (empty($data)) {
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
            foreach ($data as $postId) {
                $isbn = get_post_meta($postId, 'alfaomega_ebook_isbn', true);
                $isbns[$isbn] = $postId;
            }
            $eBooks = Service::make()->ebooks()
                ->ebookPost()
                ->index(array_keys($isbns));
            foreach ($eBooks as $eBook) {
                $this->single($eBook, false, $isbns[$eBook['isbn']]);
                $total++;
            }
        }

        return [
            'refreshed' => $total,
        ];
    }

    /**
     * Processes a batch of eBooks by their ISBNs.
     *
     * This method takes an array of ISBNs as input. It retrieves the eBook data associated with these ISBNs
     * and enqueues an asynchronous action to refresh each eBook.
     *
     * The method uses the 'alfaomega_ebooks_queue_refresh' action to refresh each eBook. This action takes
     * the post ID of the eBook and the eBook data as arguments.
     *
     * If the enqueuing of the action fails, the method throws an Exception with the message 'Refresh queue failed'.
     *
     * @param array $isbns An array of ISBNs of the eBooks to be processed. The keys are the ISBNs and the values are the post IDs.
     * @throws \Exception If the enqueuing of the refresh action fails.
     * @return void
     */
    public function batchByIsbn(array $isbns): void
    {
        $eBooks = Service::make()->ebooks()
            ->ebookPost()
            ->index(array_keys($isbns));
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
}
