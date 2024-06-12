<?php
namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use AlfaomegaEbooks\Services\Entities\WooCommerce\ProductEntity;
use Automattic\WooCommerce\Client;

class Product extends WooAbstractEntity implements ProductEntity
{
    /**
     * Factory method for the Product class.
     *
     * This static method is used to create a new instance of the Product class.
     * It takes a Client object as a parameter, which is used to interact with the WooCommerce API.
     * The method returns a new instance of the Product class, with the Client object passed to the constructor.
     *
     * @param Client $client The WooCommerce API client.
     * @return ProductEntity A new instance of the Product class.
     */
    public static function make(Client $client): ProductEntity
    {
        return new self($client);
    }
}
