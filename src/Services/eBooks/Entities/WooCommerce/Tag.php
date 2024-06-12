<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use Exception;

/**
 * Class Tag
 *
 * The Tag class extends the WooAbstractEntity class and represents a tag entity in WooCommerce.
 * It provides a method to find a tag by its name.
 */
class Tag extends WooAbstractEntity
{
    /**
     * Find a tag by its name.
     *
     * This method is used to find a tag by its name in WooCommerce.
     * It first tries to get the tags that match the provided name.
     * If there are matching tags, it returns the first one.
     * If there are no matching tags, it tries to create a new tag with the provided name.
     * If the creation is successful, it returns the created tag.
     * If the creation fails, it throws an exception.
     *
     * @param string $name The name of the tag to find.
     * @return object|null The found or created tag, or null if the tag creation failed.
     * @throws Exception If the tag creation failed.
     */
    public function find(string $name): ?object
    {
        $tags = (array) $this->client->get("products/tags", [
            'search' => $name,
        ]);
        if (count($tags) > 0) {
            return $tags[0];
        }

        $tag = $this->client->post("products/tags", [
            'name' => $name,
        ]);
        if (empty($tag)) {
            throw new Exception("Tag creation failed");
        }
        return $tag;
    }
}
