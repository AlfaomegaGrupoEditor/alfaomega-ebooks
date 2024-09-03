<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\Alfaomega;

use AlfaomegaEbooks\Services\eBooks\Entities\AbstractEntity;

abstract class AlfaomegaPostAbstract extends AbstractEntity
{
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
}
