<?php

namespace AlfaomegaEbooks\Services\Process;

use AlfaomegaEbooks\Services\Service;

/**
 * Import ebooks process.
 */
class ImportEbook extends AbstractProcess implements ProcessContract
{
    /**
     * @inheritDoc
     */
    public function single(): array
    {
        $eBook = $this->updateEbookPost(null, $eBook);
        $this->linkProduct($eBook, false);
    }

    /**
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array
     */
    public function batch(array $data = []): array
    {
        Service::make()->wooCommerce()->check();
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
