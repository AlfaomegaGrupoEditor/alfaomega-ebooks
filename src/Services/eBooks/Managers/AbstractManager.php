<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use AlfaomegaEbooks\Services\Alfaomega\Api;

class AbstractManager
{
    /**
     * AbstractManager constructor.
     *
     * @param Api $api
     * @param array $settings
     */
    public function __construct(
        protected Api $api,
        protected array $settings
    ) {}

    /**
     * Update the settings.
     *
     * This method is used to update the settings of the AbstractManager.
     * It accepts an array of settings, assigns it to the $settings property, and returns the instance of the class.
     *
     * @param array $settings The new settings.
     * @return self The instance of the AbstractManager class.
     */
    public function updateSettings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }
}
