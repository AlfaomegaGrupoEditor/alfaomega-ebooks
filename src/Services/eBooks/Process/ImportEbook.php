<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega\EbookPostEntity;
use AlfaomegaEbooks\Services\eBooks\Service;
use Exception;

/**
 * Class ImportEbook
 *
 * This class is responsible for the import process of eBooks. It extends the AbstractProcess class and implements
 * the ProcessContract interface. The class provides methods to process a single eBook or a batch of eBooks.
 *
 * @package AlfaomegaEbooks\Services\eBooks\Process
 */
class ImportEbook extends AbstractProcess implements ProcessContract
{
    /**
     * ImportEbook constructor.
     *
     * Initializes the import process with the provided settings and eBook entity.
     *
     * @param array $settings The settings for the import process. These settings can include various configuration options.
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
     *
     * This method takes an eBook array, a boolean indicating whether to throw an error, and an optional post ID as input.
     * It updates the eBook entity with the provided eBook data.
     * If the 'updateProduct' property is true, it also links the eBook to a WooCommerce product.
     *
     * @param array $eBook The eBook data.
     * @param bool $throwError Indicates whether to throw an error.
     * @param int|null $postId The post ID of the eBook. If provided, the method will process only this eBook.
     * @throws \Exception
     * @return void
     */
    public function single(array $eBook, bool $throwError=false, int $postId = null): int
    {
        $eBook = $this->entity->update(null, $eBook);

        if ($this->updateProduct) {
            Service::make()->wooCommerce()
                ->linkProduct()
                ->single($eBook, false);
        }
    }

    /**
     * Processes a batch of eBooks.
     *
     * This method takes an optional array of eBook data as input. If no array is provided, it retrieves a list of eBooks
     * from the database and enqueues an asynchronous action to import each eBook.
     *
     * The method uses the 'alfaomega_ebooks_queue_import' action to import each eBook. This action takes
     * an array of eBook data as arguments.
     *
     * If the enqueuing of the action fails, the method throws an Exception with the message 'Import queue failed'.
     *
     * The method returns an array with the total number of eBooks imported.
     *
     * @param array $data An optional array of eBook data. If provided, the method will process only these eBooks.
     * @throws \Exception If the enqueuing of the import action fails.
     * @return array An array with the total number of eBooks imported.
     */
    public function batch(array $data = [], bool $async = false): array
    {
        $isbn = '';
        if ($this->settings['alfaomega_ebooks_import_from_latest']) {
            $latestBook = $this->entity->latest();
            $isbn = empty($latestBook) ? '' : $latestBook['isbn'];
        }
        $countPerPage = intval($this->settings['alfaomega_ebooks_import_limit']);
        $imported = 0;
        do {
            $eBooks = $this->entity->retrieve($isbn, $countPerPage);
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
     * Retrieve a chunk of data to process.
     * This method should be implemented by child classes to retrieve a chunk of data to process.
     * The method should return an array of data to process, or null if there is no more data to process.
     *
     * @return array|null An array of data to process, or null if there is no more data to process.
     */protected function chunk(): ?array
    {
        // TODO: Implement chunk() method.
        return null;
    }
}
