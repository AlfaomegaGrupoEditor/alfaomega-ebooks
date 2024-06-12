<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use Automattic\WooCommerce\Client;

class AbstractEntity
{
    /**
     * AbstractEntity constructor.
     * @param Client $client
     */
    public function __construct(
        protected Client $client
    ){}
}
