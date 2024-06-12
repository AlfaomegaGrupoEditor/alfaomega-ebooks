<?php

namespace AlfaomegaEbooks\Services\Entities;

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
}
