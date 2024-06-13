<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

/**
 * The abstract process.
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
     * Initialize the process.
     *
     * @param array $entity The entity.
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
