<?php

/**
 * This class defines the general plugin settings.
 *
 * @since      1.0.0
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/includes
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
if( ! class_exists( 'Alfaomega_Ebooks_Settings' )){
    class Alfaomega_Ebooks_Settings{

        public static $options;

        /**
         * Constructor
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
            self::$options = get_option('alfaomega_ebooks_options');
            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }

        public function admin_init(): void
        {
            register_setting('alfaomega_ebooks_group', 'alfaomega_ebooks_options');

            // General tab
            /*add_settings_section(
                'alfaomega_ebooks_main_section',
                esc_html__( 'How does it work?', 'alfaomega-ebooks' ),
                null,
                'alfaomega_ebooks_page1'
            );*/

            /*add_settings_field(
                'alfaomega_ebooks_shortcode',
                esc_html__('Product->eBook', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_product_link_callback'],
                'alfaomega_ebooks_page1',
                'alfaomega_ebooks_main_section'
            );*/

            // TODO: General settings to setup the service
            add_settings_section(
                'alfaomega_ebooks_second_section',
                esc_html__( 'General Configuration', 'alfaomega-ebooks' ),
                null,
                'alfaomega_ebooks_page2'
            );

            add_settings_field(
                'alfaomega_ebooks_active',
                esc_html__('Active', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_active_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section'
            );

            add_settings_field(
                'alfaomega_ebooks_username',
                esc_html__('Username', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_username_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section'
            );

            add_settings_field(
                'alfaomega_ebooks_password',
                esc_html__('Password', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_password_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section'
            );

            add_settings_field(
                'alfaomega_ebooks_notify_to',
                esc_html__('Notify to', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_notify_to_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section'
            );

            // Platform tab
            // TODO Alfaomega external services to use by the Client Digital Library
            add_settings_section(
                'alfaomega_ebooks_third_section',
                esc_html__( 'eBooks Platform', 'alfaomega-ebooks' ),
                null,
                'alfaomega_ebooks_page3'
            );

            add_settings_field(
                'alfaomega_ebooks_reader',
                esc_html__('Reader App', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_reader_callback'],
                'alfaomega_ebooks_page3',
                'alfaomega_ebooks_third_section'
            );

            add_settings_field(
                'alfaomega_ebooks_panel',
                esc_html__('Panel Server', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_panel_callback'],
                'alfaomega_ebooks_page3',
                'alfaomega_ebooks_third_section'
            );

            // API tab
            // TODO Alfaomega API configuration.
            add_settings_section(
                'alfaomega_ebooks_fourth_section',
                esc_html__( 'API Settings', 'alfaomega-ebooks' ),
                null,
                'alfaomega_ebooks_page4'
            );


        }

        /**
         * Render the product_link field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_product_link_callback(): void
        {
            ?>
                <span>
                    <?php esc_html_e( 'Products and eBooks are linked by Digital ISBN, so make sure the Product digital ISBN is set properly.', 'alfaomega-ebooks' ); ?>
                </span>
            <?php
        }

        /**
         * Render the username field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_username_callback(): void
        {
            ?>
                <input
                    type="text"
                    name="alfaomega_ebooks_options[alfaomega_ebooks_username]"
                    id="alfaomega_ebooks_username"
                    size="50"
                    value="<?php echo isset(self::$options['alfaomega_ebooks_username']) ? esc_attr(self::$options['alfaomega_ebooks_username']) : ''; ?>"
                >
                <p class="description"> <? esc_html_e("User's email authorized to access Alfaomega Ebooks Platform.", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the active field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_active_callback(): void
        {
            ?>
            <input
                type="checkbox"
                name="alfaomega_ebooks_options[alfaomega_ebooks_active]"
                id="alfaomega_ebooks_active"
                value="1"
                <?php
                    if ( isset( self::$options['alfaomega_ebooks_active'])) {
                        checked( "1", self::$options['alfaomega_ebooks_active'], true);
                    }
                ?>
            >
            <label for="alfaomega_ebooks_active">
                <? echo esc_html__('Whether to update eBooks link to relative product with the same Digital ISBN.', 'alfaomega-ebooks'); ?>
            </label>
            <?php
        }

        /**
         * Render the password field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_password_callback(): void
        {
            ?>
            <input
                type="password"
                name="alfaomega_ebooks_options[alfaomega_ebooks_password]"
                id="alfaomega_ebooks_password"
                size="50"
                value="<?php echo isset(self::$options['alfaomega_ebooks_password']) ? esc_attr(self::$options['alfaomega_ebooks_password']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("User's password authorized to access Alfaomega Ebooks Platform.", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the notify_to field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_notify_to_callback(): void
        {
            ?>
            <input
                type="email"
                name="alfaomega_ebooks_options[alfaomega_ebooks_notify_to]"
                id="alfaomega_ebooks_notify_to"
                size="50"
                value="<?php echo isset(self::$options['alfaomega_ebooks_notify_to']) ? esc_attr(self::$options['alfaomega_ebooks_notify_to']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Email address to send a copy of every download code set to clients.", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the reader field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_reader_callback(): void
        {
            ?>
            <input
                type="url"
                name="alfaomega_ebooks_options[alfaomega_ebooks_readero]"
                id="alfaomega_ebooks_reader"
                size="50"
                value="<?php echo isset(self::$options['alfaomega_ebooks_reader']) ? esc_attr(self::$options['alfaomega_ebooks_reader']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Reader app URL.", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the panel field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_panel_callback(): void
        {
            ?>
            <input
                type="email"
                name="alfaomega_ebooks_options[alfaomega_ebooks_panel]"
                id="alfaomega_ebooks_panel"
                size="50"
                value="<?php echo isset(self::$options['alfaomega_ebooks_panel']) ? esc_attr(self::$options['alfaomega_ebooks_panel']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Publisher Panel server URL.", 'alfaomega-ebooks') ?> </p>
            <?php
        }
    }
}
