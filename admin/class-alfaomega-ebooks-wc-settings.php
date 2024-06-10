<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if the class AlfaomegaEbooks_WC_Settings exists
if ( ! class_exists( 'AlfaomegaEbooks_WC_Settings' ) ) {

    /**
     * The AlfaomegaEbooks_WC_Settings class extends the WC_Settings_Page class.
     * It provides the settings for the Alfaomega Ebooks plugin in the WooCommerce settings.
     *
     * @since 1.0.0
     */
    class AlfaomegaEbooks_WC_Settings extends WC_Settings_Page {

        /**
         * Constructor for the AlfaomegaEbooks_WC_Settings class.
         * It initializes the parent class and sets the id and label properties.
         * It also adds the necessary hooks for the settings page.
         *
         * @since  1.0
         */
        public function __construct() {
            parent::__construct();
            $this->id    = 'alfaomega-ebooks';
            $this->label = esc_html__( 'Alfaomega Ebooks', 'alfaomega-ebooks' );

            // Define all hooks instead of inheriting from parent
            add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
            add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
            add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

        }

        /**
         * Get the sections for the settings page.
         *
         * @return array The sections for the settings page.
         */
        public function get_sections() {
            $sections = array(
                '' => esc_html__( 'Settings', 'alfaomega-ebooks' ),
                'log' => esc_html__( 'Log', 'alfaomega-ebooks' ),
                'results' => esc_html__( 'Sync Results', 'alfaomega-ebooks' )
            );

            return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
        }

        /**
         * Get the settings for the current section.
         *
         * @return array The settings for the current section.
         */
        public function get_settings() {

            global $current_section;
            $prefix = 'alfaomega_ebooks_';
            $settings = array();

            switch ($current_section) {
                case 'log':
                    $settings = array(
                            array()
                    );
                    break;
                default:
                    include 'partials/alfaomega-ebooks-settings-main.php';
            }

            return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
        }

        /**
         * Output the settings for the current section.
         */
        public function output() {
            global $current_section;

            switch ($current_section) {
                case 'results':
                    include 'partials/alfaomega-ebooks-settings-results.php';
                    break;
                default:
                    $settings = $this->get_settings();
                    WC_Admin_Settings::output_fields( $settings );
            }

        }

        /**
         * Save the settings for the current section.
         *
         * @since 1.0
         */
        public function save() {
            $settings = $this->get_settings();

            WC_Admin_Settings::save_fields( $settings );
        }

    }

}

// Return a new instance of the AlfaomegaEbooks_WC_Settings class
return new AlfaomegaEbooks_WC_Settings();
