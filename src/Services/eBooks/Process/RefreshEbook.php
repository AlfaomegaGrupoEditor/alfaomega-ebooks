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
     * @param bool $throwError
     * @param int|null $postId
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
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array
     * @throws \Exception
     */
    public function batch(array $data = []): array
    {
        $eBooks = $this->entity->index(array_keys($data));
        foreach ($eBooks as $eBook) {
            $result = as_enqueue_async_action(
                'alfaomega_ebooks_queue_refresh',
                [ $data[$eBook['isbn']], $eBook ]
            );
            if ($result === 0) {
                throw new Exception('Refresh queue failed');
            }
        }

        return [
            'refreshed' => count($eBooks),
        ];
    }
}
