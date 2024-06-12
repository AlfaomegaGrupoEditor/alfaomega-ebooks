<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

/**
 * The abstract process.
 */
abstract class AbstractProcess implements ProcessContract
{
    /**
     * Initialize the process.
     *
     * @param array $entity The entity.
     */
    public function __construct(
        protected array $settings
    ) {}
}
