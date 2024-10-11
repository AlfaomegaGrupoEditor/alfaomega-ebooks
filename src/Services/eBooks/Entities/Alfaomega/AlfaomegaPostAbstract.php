<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Entities\AbstractEntity;

abstract class AlfaomegaPostAbstract extends AbstractEntity
{
    /**
     * The EbookPost constructor.
     *
     * @param Api $api The API.
     * @param array $meta The metadata.
     */
    public function __construct(
        protected array $meta = []
    ) {}

    /**
     * Delete a post.
     * This method is used to delete a post from the WordPress database.
     * It takes the post ID as an argument and uses the wp_delete_post() function to delete the post.
     *
     * @param int $postId The ID of the post to delete.
     *
     * @return bool True if the post is deleted, false otherwise.
     */
    public function delete(int $postId): bool
    {
        $result = wp_delete_post($postId, true);
        return !empty($result);
    }

    /**
     * Get the information of the entity.
     *
     * @return array The information of the entity.
     */
    abstract public function getInfo(): array;
}
