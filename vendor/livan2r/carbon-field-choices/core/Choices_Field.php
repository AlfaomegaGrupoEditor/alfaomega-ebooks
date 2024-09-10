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
     * @var null|float
     */
    protected $fetch_url = null;

    /**
     * The amount of choices to be rendered within the dropdown list ("-1" indicates no limit).
     *
     * @var int
     */
    protected $render_choice_limit = -1;

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
    public function to_json( $load ) {
        $field_data = parent::to_json( $load );

        return array_merge($field_data, [
            'fetch_url'           => $this->fetch_url,
            'render_choice_limit' => $this->render_choice_limit,
        ]);
    }

    /**
     * Set endpoint url to fetch choices
     *
     * @param  null|float $min
     * @return self       $this
     */
    function set_fetch_url( string $url ) {
        $this->fetch_url = $url;
        return $this;
    }

    /**
     * Set the amount of choices to be rendered within the dropdown list
     *
     * @param  int $number
     * @return self $this
     */
    function set_render_choice_limit( $number ) {
        $this->render_choice_limit = is_numeric($number) ? $number : -1;
        return $this;
    }
}
