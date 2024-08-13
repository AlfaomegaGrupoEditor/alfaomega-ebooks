<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

/**
 * Interface ProcessContract
 *
 * This interface defines the contract for eBook processing classes. It ensures that all classes that implement this
 * interface have the required methods for processing eBooks. The interface defines two methods: 'single' and 'batch'.
 *
 * @package AlfaomegaEbooks\Services\eBooks\Process
 */
interface ProcessContract
{
    /**
     * Processes a single eBook.
     *
     * This method takes an eBook array, a boolean indicating whether to throw an error, and an optional post ID as input.
     * The specific implementation of this method depends on the class that implements this interface.
     *
     * @param array $eBook The eBook data.
     * @param bool $throwError Indicates whether to throw an error.
     * @param int|null $postId The post ID of the eBook. If provided, the method will process only this eBook.
     *
     * @throws \Exception If there is an error during the processing of the eBook.
     * @return int
     */
    public function single(array $eBook, bool $throwError=false, int $postId = null): int;

    /**
     * Processes a batch of eBooks.
     *
     * This method takes an optional array of eBook data as input. If no array is provided, the specific implementation
     * of this method should determine how to retrieve the eBooks to be processed.
     *
     * @param array $data An optional array of eBook data. If provided, the method will process only these eBooks.
     *                bool $async
     *
     * @return array|null An array with the results of the batch processing. The specific structure of this array depends on
     *               the class that implements this interface.
     */
    public function batch(array $data = [], bool $async = false): ?array;
}
