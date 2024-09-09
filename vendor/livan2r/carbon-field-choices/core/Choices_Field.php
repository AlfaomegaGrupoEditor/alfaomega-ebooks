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
        'searchResultLimit'
    ];

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
}
