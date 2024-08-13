<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

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

    /**
     * AbstractProcess constructor.
     *
     * Initializes the process with the provided settings.
     *
     * @param array $settings The settings for the process. These settings can include various configuration options.
     */
    public function __construct(
        protected array $settings
    ) {}

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
}
