<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use Exception;

interface EbookPostEntity
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
     * Get the metadata of a specific post or the current metadata.
     * This method is used to get the metadata of a specific post or the current metadata if no post ID is provided.
     * If a post ID is provided, it fetches the post and its metadata, assigns them to the $meta property, and returns
     * the $meta. If no post ID is provided, it simply returns the current $meta. If the post does not exist, it throws
     * an exception.
     *
     * @param int|null $postId The ID of the post. Default is null.
     *
     * @return array The metadata of the post or the current metadata.
     * @throws Exception If the post does not exist.
     */
    public function get(int $postId = null): array;

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
     * Updates or creates a new eBook post.
     *
     * This method updates an existing eBook post or creates a new one if it doesn't exist.
     * It uses the provided eBook data to set the post title, content, status, author, and type.
     * It also saves the post metadata.
     *
     * @param int|null $postId The ID of the post to update. If null, a new post is created.
     * @param array $data An associative array containing the eBook data.
     *
     * @return array Returns an associative array containing the updated post metadata.
     * @throws Exception Throws an exception if unable to create post.
     */
    public function updateOrCreate(?int $postId, array $data): array;

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
     * Saves the post metadata.
     * This method saves the metadata for a post of type 'alfaomega-ebook'.
     * It sanitizes the new values before saving them and uses default values if the new values are empty.
     * It also publishes the post and retrieves the updated post metadata.
     *
     * @param int $postId The ID of the post to save metadata for.
     * @param array $data An associative array containing the new metadata values.
     *
     * @return array Returns an associative array containing the updated post metadata.
     * @throws \Exception
     */
    public function save(int $postId, array $data): array;

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
}
