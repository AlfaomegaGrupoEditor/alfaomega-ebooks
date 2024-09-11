<?php

namespace Carbon_Field_Choices;

use Carbon_Fields\Field\Select_Field;

class Choices_Field extends Select_Field {

    /**
     * {@inheritDoc}
     */
    protected $allowed_attributes = [
        'placeholder',
        'shouldSort',
        'searchEnabled',
        'searchResultLimit',
        'loadingText',
        'removeItemButton',
        'allowHTML',
        'searchPlaceholderValue',
        'searchFloor',
        'searchResultLimit',
    ];

    /**
     * Endpoint url to fetch choices
     *
     * @var null|string
     */
    protected ?string $fetch_url = null;

    /**
     * The setup_search property is used to define the fields that will be used to search the choices.
     *
     * @var array
     */
    protected ?array $setup_search = null;

    /**
     * The amount of choices to be rendered within the dropdown list ("-1" indicates no limit).
     *
     * @var int
     */
    protected int $render_choice_limit = -1;

    /**
     * Enqueue scripts and styles in admin
     * Called once per field type
     */
    public static function admin_enqueue_scripts() {
        $root_uri = \Carbon_Fields\Carbon_Fields::directory_to_url( \Carbon_Field_Choices\DIR );

        // Enqueue field styles.
        wp_enqueue_style(
            'carbon-field-choice',
            $root_uri . '/build/bundle' . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' ) . '.css'
        );

        // Enqueue field scripts.
        wp_enqueue_script(
            'carbon-field-choice',
            $root_uri . '/build/bundle' . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' ) . '.js',
            array( 'carbon-fields-core' )
        );
    }

    /**
     * Returns an array that holds the field data, suitable for JSON representation.
     *
     * @param bool $load  Should the value be loaded from the database or use the value from the current instance.
     * @return array
     */
    public function to_json( $load ): array
    {
        $field_data = parent::to_json( $load );

        $default_setup_search = $searchSetup = [
            'method'  => 'GET',
            'headers' => [
                'Content-Type' => 'application/json',
                'X-WP-Nonce'   => wp_create_nonce('wp_rest'),
            ],
            'params' => [
                'query' => '',
                'limit' => -1, // -1 indicates no limit
                'page'  => 1,
            ],
            'options' => [
                'value' => 'value',
                'label' => 'label',
            ],
        ];

        return array_merge($field_data, [
            'fetch_url'           => $this->fetch_url,
            'setup_search'        => empty($this->setup_search)
                ? $default_setup_search
                : array_merge($default_setup_search, $this->setup_search),
            'render_choice_limit' => $this->render_choice_limit,
        ]);
    }

    /**
     * Set endpoint url to fetch choices
     *
     * @param string $url
     * @param array $setup_search
     *
     * @return self       $this
     */
    function set_fetch_url( string $url, array $setup_search = [] ): self
    {
        $this->fetch_url = $url;
        $this->setup_search = $setup_search;
        return $this;
    }

    /**
     * Set the amount of choices to be rendered within the dropdown list
     *
     * @param  int|string $number
     * @return self $this
     */
    function set_render_choice_limit( int|string $number ): self
    {
        $this->render_choice_limit = is_numeric($number) ? $number : -1;
        return $this;
    }
}
