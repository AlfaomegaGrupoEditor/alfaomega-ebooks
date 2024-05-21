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

        public function admin_init()
        {
            register_setting('alfaomega_ebooks_group', 'alfaomega_ebooks_options');

            add_settings_section(
                'alfaomega_ebooks_main_section',
                esc_html__( 'How does it work?', 'alfaomega-ebooks' ),
                null,
                'alfaomega_ebooks_page1'
            );

            add_settings_section(
                'alfaomega_ebooks_second_section',
                esc_html__( 'General Configuration', 'alfaomega-ebooks' ),
                null,
                'alfaomega_ebooks_page2'
            );

            add_settings_field(
                'alfaomega_ebooks_shortcode',
                esc_html__('Product->eBook', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_product_link_callback'],
                'alfaomega_ebooks_page1',
                'alfaomega_ebooks_main_section'
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
                    <?php esc_html_e( 'A product and an eBook are linked by Digital ISBN, so make sure the Product digital ISBN is set properly.', 'alfaomega-ebooks' ); ?>
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
            <?php
        }
    }
}
