<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use AlfaomegaEbooks\Services\Entities\AbstractEntity;
use Automattic\WooCommerce\Client;

class WooAbstractEntity extends AbstractEntity
{
    /**
     * AbstractEntity constructor.
     * @param Client $client
     */
    public function __construct(
        protected Client $client
    ){}
}
