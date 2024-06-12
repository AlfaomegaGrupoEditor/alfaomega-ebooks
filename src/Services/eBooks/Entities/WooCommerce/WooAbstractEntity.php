<?php

namespace AlfaomegaEbooks\Services\eBooks\Entities\WooCommerce;

use AlfaomegaEbooks\Services\Entities\AbstractEntity;
use Automattic\WooCommerce\Client;

class WooAbstractEntity extends AbstractEntity
{
    /**
     * AbstractEntity constructor.
     *
     * @param Client $client
     * @param array $settings
     */
    public function __construct(
        protected Client $client,
        protected array $settings
    ){}
}
