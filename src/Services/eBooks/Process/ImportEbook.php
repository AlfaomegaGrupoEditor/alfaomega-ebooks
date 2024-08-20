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
    protected const QUEUE_MAX_SIZE = 25;

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
        if (empty($data)) {
            return $this->chunk();
        }

        return $async
            ? $this->queueProcess($data)
            : $this->doProcess($data);
    }

    /**
     * Pull new eBooks to the system.
     * Retrieve a chunk of data to process.
     * This method should be implemented by child classes to retrieve a chunk of data to process.
     * The method should return an array of data to process, or null if there is no more data to process.
     *
     * @return array|null An array of data to process, or null if there is no more data to process.
     * @throws \Exception
     */
    protected function chunk(): ?array
    {
        $isbn = '';
        $onQueue = [];
        if ($this->settings['alfaomega_ebooks_import_from_latest']) {
            $latest = $this->getEbookEntity()->latest();
            $isbn = $latest['isbn'] ?? '';
        }
        $limit = intval($this->settings['alfaomega_ebooks_import_limit']) ?? 1000;
        $countPerPage = self::QUEUE_MAX_SIZE;
        do {
            $countPerPage = min($limit, $countPerPage);
            $ebooks = $this->getEbookEntity()
                ->retrieve($isbn, $countPerPage);

            $onQueue = array_merge($onQueue, $this->batch($ebooks, true));
            $isbn = end($ebooks)['isbn'] ?? null;
        } while (count($ebooks) > 0 && count($onQueue) < $limit);

        return $onQueue;
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
        foreach ($entities as $ebook) {
            if (empty($ebook['printed_isbn'])) {
                continue;
            }

            $productId = wc_get_product_id_by_sku($ebook['printed_isbn']);
            if (empty($productId)) {
                continue;
            }

            $ebook['product_id'] = $productId;
            $result = $this->single($ebook);
            if ($result !== 0) {
                $processed[] = $productId;
            }
        }

        return $processed;
    }

    /**
     * Queue the process to link the products to the ebooks asynchronously.
     *
     * @param array $entities
     *
     * @return array|null
     */
    protected function queueProcess(array $entities): ?array
    {
        $onQueue = [];
        foreach ($entities as $ebook) {
            if (empty($ebook['printed_isbn'])) {
                continue;
            }

            $productId = wc_get_product_id_by_sku($ebook['printed_isbn']);
            if (empty($productId)) {
                continue;
            }

            $ebook['product_id'] = $productId;
            $result = as_enqueue_async_action('alfaomega_ebooks_queue_import', [$ebook]);
            if ($result !== 0) {
                $onQueue[] = $productId;
            }
        }
        return $onQueue;
    }
}
