<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use Exception;

class Attribute extends WooAbstractEntity
{
    /**
     * Get the attribute by name.
     *
     * @param string $attributeName
     *
     * @return object|null
     */
    public function get(string $attributeName): ?object
    {
        $attributes = (array) $this->client->get('products/attributes');
        foreach ($attributes as $attribute) {
            if ($attribute->slug === $attributeName) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * Update or create an attribute.
     *
     * @param string $attributeName
     * @param array $data
     * @param array|null $options
     *
     * @return object|null
     * @throws \Exception
     */
    public function updateOrCreate(string $attributeName, array $data, array $options = null): ?object
    {
        $attribute = $this->get($attributeName);
        if (!empty($attribute)) {
            return $attribute;
        }

        $attribute = $this->client->post('products/attributes', $data);
        if (empty($attribute)) {
            throw new Exception("Attribute creation failed");
        }

        if (!empty($options)) {
            foreach ($options as $option) {
                $newOption = $this->client->post("products/attributes/{$attribute->id}/terms", $option);
                if (empty($newOption)) {
                    throw new Exception("Attribute option creation failed");
                }
            }
        }

        return $attribute;
    }
}
