<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use AlfaomegaEbooks\Services\eBooks\Service;/**
 * Class SettingsManager
 * This class is responsible for managing the settings of the Alfaomega eBooks.
 * It provides methods to create an instance of the class, get the settings, and check the settings.
 */
class SettingsManager
{
    /**
     * @var array $values The array to store the settings values.
     */
    protected array $values = [];

    /**
     * Create a new instance of the SettingsManager class.
     *
     * @return self A new instance of the SettingsManager class.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * The SettingsManager constructor.
     *
     * It initializes the $values property with the settings values.
     */
    public function __construct()
    {
        $this->values = $this->get();
    }

    /**
     * Get the settings values.
     *
     * If the $values property is not empty, it returns the $values.
     * Otherwise, it fetches the settings from the options and assigns them to the $values property.
     *
     * @return array The settings values.
     */
    public function get(): array
    {
        if (!empty($this->values)) {
            return $this->values;
        }

        $this->values = array_merge(
            (array) get_option('alfaomega_ebooks_general_options'),
            (array) get_option('alfaomega_ebooks_platform_options'),
            (array) get_option('alfaomega_ebooks_api_options'),
            (array) get_option('alfaomega_ebooks_product_options')
        );

        return $this->values;
    }

    /**
     * Check the existence of the eBook attribute.
     * @return void
     * @throws \Exception
     */
    public function checkEbookAttr(): void
    {
        $ebookAttribute = Service::make()
            ->wooCommerce()
            ->ebook()
            ->updateOrCreate('pa_ebook', [
                'name' => 'eBook',
                'slug' => 'pa_ebook'
            ]);

        if (empty($ebookAttribute)) {
            throw new \Exception('The ebook attribute could not be created.');
        }
    }

    /**
     * Check the settings.
     *
     * This method checks the settings values and updates them if necessary.
     *
     * @param array $values The settings values to check.
     * @return bool True if the settings are valid, false otherwise.
     * @throws \Exception If the format attribute could not be created.
     */
    public function checkFormatAttr(array $values = []): array
    {
        // check if the format attribute was configured already
        if (isset($this->values['alfaomega_ebooks_format_attr_id']) &&
            empty($this->values['alfaomega_ebooks_format_attr_id']) ) {
            $formatAttribute = Service::make()
                ->wooCommerce()
                ->format()
                ->updateOrCreate('pa_book-format', [
                    'name' => 'Formato',
                    'slug' => 'pa_book-format',
                    'type' => 'select',
                ], [
                    ['name' => 'Impreso', 'description' => 'Libro impreso'],
                    ['name' => 'Digital', 'description' => 'Lectura en línea y descaga del PDF'],
                    ['name' => 'Impreso + Digital', 'description' => 'Libro impreso, digital en línea y descarga del PDF'],
                ]);
            if (empty($formatAttribute)) {
                throw new \Exception('The format attribute could not be created.');
            }

            $values['alfaomega_ebooks_format_attr_id'] = $formatAttribute->id;
        }

        return $values;
    }
}
