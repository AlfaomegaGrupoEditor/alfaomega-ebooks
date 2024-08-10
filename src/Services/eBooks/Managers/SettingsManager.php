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
            $this->getOption('alfaomega_ebooks_general_options'),
            $this->getOption('alfaomega_ebooks_platform_options'),
            $this->getOption('alfaomega_ebooks_api_options'),
            $this->getOption('alfaomega_ebooks_product_options')
        );

        return $this->values;
    }

    /**
     * Check the existence of the eBook attribute.
     * @param array $values
     *
     * @return array
     * @throws \Exception
     */
    public function checkEbookAttr(array $values = []): array
    {
        if (isset($values['alfaomega_ebooks_ebook_attr_id']) &&
            empty($values['alfaomega_ebooks_ebook_attr_id']) ) {
            $ebookAttribute = Service::make()
                ->wooCommerce()
                ->ebook()
                ->updateOrCreate('pa_ebook', [
                    'name' => 'eBook',
                    'slug' => 'pa_ebook'
                ], [
                    ['name' => 'Si', 'description' => 'eBook disponible'],
                    ['name' => 'No', 'description' => 'eBook no disponible'],
                ]);

            if (empty($ebookAttribute)) {
                throw new \Exception('The ebook attribute could not be created.');
            }

            $values['alfaomega_ebooks_ebook_attr_id'] = $ebookAttribute->id;
        }

        return $values;
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
        if (isset($values['alfaomega_ebooks_format_attr_id']) &&
            empty($values['alfaomega_ebooks_format_attr_id']) ) {
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

    /**
     * Get the option values from the database or from the environment variables.
     * @param string $option
     *
     * @return array
     */
    public function getOption(string $option): array
    {
        $value = get_option($option);

        return match ($option) {
            'alfaomega_ebooks_general_options' => $value
                ?: [
                    'alfaomega_ebooks_active'             => $_ENV['AO_EBOOK_ACTIVE'] === 'true',
                    'alfaomega_ebooks_username'           => $_ENV['AO_EBOOK_USERNAME'] ?? 'username',
                    'alfaomega_ebooks_password'           => $_ENV['AO_EBOOK_PASSWORD'] ?? 'password',
                    'alfaomega_ebooks_notify_to'          => $_ENV['AO_EBOOK_NOTIFY_TO'] ?? 'your_email@domain.com',
                    'alfaomega_ebooks_import_limit'       => $_ENV['AO_EBOOK_IMPORT_LIMIT'] ?? 1000,
                    'alfaomega_ebooks_import_from_latest' => $_ENV['AO_EBOOK_IMPORT_FROM_LATEST'] === 'true',
                ],
            'alfaomega_ebooks_platform_options' => $value
                ?: [
                    'alfaomega_ebooks_reader' => $_ENV['AO_EBOOK_READER'] ?? 'https://reader.alfaomega.com.mx',
                    'alfaomega_ebooks_panel'  => $_ENV['AO_EBOOK_PANEL'] ?? 'https://panel.alfaomega.com.mx',
                    'alfaomega_ebooks_client' => $_ENV['AO_EBOOK_CLIENT'] ?? 'client',
                ],
            'alfaomega_ebooks_api_options' => $value
                ?: [
                    'alfaomega_ebooks_token'         => $_ENV['AO_EBOOK_API_TOKEN'] ?? 'https://api.alfaomega.com.mx/oauth/token',
                    'alfaomega_ebooks_api'           => $_ENV['AO_EBOOK_API'] ?? 'https://api.alfaomega.com.mx',
                    'alfaomega_ebooks_client_id'     => $_ENV['AO_EBOOK_CLIENT_ID'] ?? 'client_id',
                    'alfaomega_ebooks_client_secret' => $_ENV['AO_EBOOK_CLIENT_SECRET'] ?? 'client_secret',
                ],
            'alfaomega_ebooks_product_options' => $value
                ?: [
                    'alfaomega_ebooks_format_attr_id'        => $_ENV['AO_EBOOK_FORMAT_ATTR_ID'] ?? '0',
                    'alfaomega_ebooks_ebook_attr_id'         => $_ENV['AO_EBOOK_EBOOK_ATTR_ID'] ?? '0',
                    'alfaomega_ebooks_price'                 => $_ENV['AO_EBOOK_PRICE'] ?? 80,
                    'alfaomega_ebooks_printed_digital_price' => $_ENV['AO_EBOOK_COMBO_PRICE'] ?? 130,
                ],
            default => [],
        };
    }
}
