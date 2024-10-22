<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega\EbookPostEntity;
use AlfaomegaEbooks\Services\eBooks\Service;

/**
 * AbstractProcess Class
 * This abstract class provides a blueprint for creating specific eBook processing classes.
 * It implements the ProcessContract interface, which ensures that all child classes have the required methods for processing eBooks.
 * The class also provides a property and a method for controlling whether a product should be updated during the processing of an eBook.
 *
 * @package AlfaomegaEbooks\Services\eBooks\Process
 */
abstract class AbstractProcess implements ProcessContract
{
    /**
     * @var bool $updateProduct
     *
     * A boolean property that determines whether the product should be updated or not.
     * By default, this property is set to true, meaning the product will be updated.
     */
    protected bool $updateProduct = true;
    protected ?EbookPostEntity $ebookEntity = null;
    protected int $chunkSize = 25;
    protected string $entityId = 'id';

    /**
     * AbstractProcess constructor.
     *
     * Initializes the process with the provided settings.
     *
     * @param array $settings The settings for the process. These settings can include various configuration options.
     */
    public function __construct(protected array $settings)
    {}

    /**
     * Link a product to an eBook.
     *
     * @param array $eBook: eBook attributes
     * @param bool $throwError: Whether to throw an error or not.
     * @param int|null $postId: eBook post ID.
     *
     * @return void
     * @throws \Exception
     */
    public function single(array $eBook, bool $throwError=false, int $postId=null): int
    {
        try {
            if (!empty($eBook['error'])) {
                throw new \Exception($eBook['error']);
            }

            $post = $this->getEbookEntity()
                ->updateOrCreate($postId, $eBook);
            if (empty($post)) {
                $this->getEbookEntity()->updateImported($eBook['isbn'], 'failed', errorCode: 'ebook_post_update_failed');
                throw new \Exception('Error updating or creating the eBook post.');
            }

            $eBook['id'] = $post['id'];
            if ($this->updateProduct) {
                $productId = Service::make()
                    ->wooCommerce()
                    ->linkProduct()
                    ->single($eBook, $throwError);

                if (empty($productId)) {
                    throw new \Exception('Error linking the eBook with the product.');
                }
            }

            return $post['id'];
        } catch (\Exception $e) {
            if ($throwError) {
                throw $e;
            }
            return 0;
        }
    }

    /**
     * Gather the related eBook information for each specified products. Also,
     * call the async or sync methods to link the product to the eBook.
     *
     * @param array $data Array of products id.
     * @param bool $async
     *
     * @return array|null
     * @throws \Exception
     */
    abstract public function batch(array $data = [], bool $async = false): ?array;

    /**
     * Set the value of the $updateProduct property.
     *
     * This method allows you to control whether the product should be updated or not.
     * By default, the product will be updated.
     *
     * @param bool $updateProduct Optional. Whether to update the product. Default is true.
     * @return self Returns the current instance of the class, to allow for method chaining.
     */
    public function setUpdateProduct(bool $updateProduct = true): self
    {
        $this->updateProduct = $updateProduct;
        return $this;
    }

    /**
     * Search the eBook by ISBN or product SKU.
     * @param string|null $isbn
     * @param string|null $sku
     *
     * @return array|null
     * @throws \Exception
     */
    protected function searchEbook(?string $isbn, ?string $sku): ?array
    {
        if (!empty($sku)) {
            return $this->getEbookEntity()
                ->search($sku, 'alfaomega_ebook_product_sku');
        }

        if (!empty($isbn)) {
            return $this->getEbookEntity()
                ->search($isbn);
        }

        return null;
    }

    /**
     * Get the eBook entity.
     *
     * @return EbookPostEntity
     * @throws \Exception
     */
    protected function getEbookEntity(): EbookPostEntity
    {
        if (empty($this->ebookEntity)) {
            $this->ebookEntity = Service::make()
                ->ebooks()
                ->ebookPost();
        }

        return $this->ebookEntity;
    }

    /**
     * Retrieve a chunk of data to process.
     *
     * This method should be implemented by child classes to retrieve a chunk of data to process.
     * The method should return an array of data to process, or null if there is no more data to process.
     *
     * @return array|null An array of data to process, or null if there is no more data to process.
     */
    abstract protected function chunk(): ?array;

    /**
     * Link the products to the ebooks synchronously.
     * @param array $entities
     *
     * @return array|null
     * @throws \Exception
     */
    abstract protected function doProcess(array $entities): ?array;

    /**
     * Queue the process to link the products to the ebooks asynchronously.
     * @param array $entities
     *
     * @return array|null
     */
    abstract protected function queueProcess(array $entities): ?array;

    /**
     * Get the payload for the given entity ID.
     *
     * This method takes an entity ID as input and returns the payload for that entity. The specific implementation of
     * this method depends on the class that implements this interface.
     *
     * @param int|string $entityId The entity ID.
     * @param array|null $data The initial payload data
     *
     * @return array|null The payload for the entity.
     */
    public function getPayload(int|string $entityId, array $data = null): ?array
    {
        return null;
    }

    /**
     * Get the entity ID.
     *
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }
}
