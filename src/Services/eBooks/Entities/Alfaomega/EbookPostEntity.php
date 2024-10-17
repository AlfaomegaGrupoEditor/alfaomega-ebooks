<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use Exception;

interface EbookPostEntity extends AlfaomegaPostInterface
{
    /**
     * Get the latest post.
     * This method is used to get the latest post of the 'alfaomega-ebook' post type.
     * It creates a query to fetch the latest post, executes the query, and if there are posts, it gets the metadata of
     * the latest post. If there are no posts, it returns null.
     *
     * @return array|null The metadata of the latest post or null if there are no posts.
     * @throws \Exception
     */
    public function latest(): ?array;

    /**
     * Retrieves eBooks from Alfaomega.
     *
     * This method sends a GET request to the Alfaomega API to retrieve eBooks.
     * The eBooks are identified by their ISBNs, which are passed as an array.
     * The method throws an exception if the API response code is not 200 or if the status of the content is not 'success'.
     *
     * @param string $isbn The ISBN of the eBook to start retrieving from. Default is an empty string.
     * @param int $count The number of eBooks to retrieve. Default is 100.
     *
     * @return array Returns an associative array containing the eBooks information.
     * @throws Exception Throws an exception if the API response code is not 200 or if the status of the content is not 'success'.
     */
    public function retrieve(string $isbn = '', int $count=100): array;

    /**
     * Searches for a post of type 'alfaomega-ebook' by ISBN.
     * This method searches for a post of type 'alfaomega-ebook' in the WordPress database by ISBN.
     * It retrieves the post metadata if a post is found.
     *
     * @param string $value field value.
     * @param string $field field to search by.
     *
     * @return array|null Returns an associative array containing the post metadata if a post is found, or null if no post is found.
     * @throws \Exception
     */
    public function search(string $value, string $field = 'alfaomega_ebook_isbn'): ?array;

    /**
     * Retrieves eBooks information from Alfaomega.
     * This method sends a POST request to the Alfaomega API to retrieve information about eBooks.
     * The eBooks are identified by their ISBNs, which are passed as an array.
     * The method throws an exception if the API response code is not 200 or if the status of the content is not
     * 'success'.
     *
     * @param array $isbns An array of ISBNs of the eBooks to retrieve information for.
     *
     * @return array|null Returns an associative array containing the eBooks information.
     * @throws \Exception
     */
    public function index(array $isbns): ?array;

    /**
     * Update the imported registry in the portal for the current store
     *
     * @param array|null $isbns
     * @param string $status
     *
     * @return array
     */
    public function updateImported(array $isbns=null, string $status = 'on-queue'): array;

    /**
     * Retrieve information of the new ebooks
     *
     * @param int $count
     *
     * @return array
     * @throws \Exception
     */
    public function getNewEbooks(int $count = 100): array;
}
