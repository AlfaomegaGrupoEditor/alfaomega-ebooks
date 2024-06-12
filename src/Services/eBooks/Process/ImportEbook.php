<?php

namespace AlfaomegaEbooks\Services\Process;

use AlfaomegaEbooks\Services\Entities\EbookPostEntity;
use AlfaomegaEbooks\Services\Service;
use Exception;

/**
 * Import ebooks process.
 */
class ImportEbook extends AbstractProcess implements ProcessContract
{
    /**
     * Initialize the process.
     *
     * @param array $settings The settings.
     * @param EbookPostEntity $entity The entity.
     */
    public function __construct(
        array $settings,
        protected EbookPostEntity $entity)
    {
        parent::__construct($settings);
    }

    /**
     * Do the process on a single object.
     *
     * @param array $eBook
     *
     * @return array
     * @throws \Exception
     */
    public function single(array $eBook): void
    {
        $eBook = $this->entity->update(null, $eBook);
        Service::make()->wooCommerce()
            ->linkProduct()
            ->single($eBook, false);
    }

    /**
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array
     * @throws \Exception
     */
    public function batch(array $data = []): array
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
}
