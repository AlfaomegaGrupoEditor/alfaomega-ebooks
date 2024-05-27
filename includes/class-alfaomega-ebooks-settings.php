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

        public static $generalOptions;
        public static $platformOptions;
        public static $apiOptions;

        /**
         * Constructor
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
            self::$generalOptions = get_option('alfaomega_ebooks_general_options');
            self::$platformOptions = get_option('alfaomega_ebooks_platform_options');
            self::$apiOptions = get_option('alfaomega_ebooks_api_options');

            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }

        public function admin_init(): void
        {
            register_setting(
                'alfaomega_ebooks_general_group',
                'alfaomega_ebooks_general_options',
                [ $this, 'alfaomega_books_validate']
            );

            register_setting(
                'alfaomega_ebooks_platform_group',
                'alfaomega_ebooks_platform_options',
                [ $this, 'alfaomega_books_validate']
            );

            register_setting(
                'alfaomega_ebooks_api_group',
                'alfaomega_ebooks_api_options',
                [ $this, 'alfaomega_books_validate']
            );

            // General tab
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
                'alfaomega_ebooks_second_section',
                ['label_for' => 'alfaomega_ebooks_active']
            );

            add_settings_field(
                'alfaomega_ebooks_username',
                esc_html__('Username', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_username_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section',
                ['label_for' => 'alfaomega_ebooks_username']
            );

            add_settings_field(
                'alfaomega_ebooks_password',
                esc_html__('Password', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_password_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section',
                ['label_for' => 'alfaomega_ebooks_password']
            );

            add_settings_field(
                'alfaomega_ebooks_notify_to',
                esc_html__('Notify to', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_notify_to_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section',
                ['label_for' => 'alfaomega_ebooks_notify_to']
            );

            add_settings_field(
                'alfaomega_ebooks_import_limit',
                esc_html__('Import Limit', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_import_limit_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section',
                ['label_for' => 'alfaomega_ebooks_import_limit']
            );

            add_settings_field(
                'alfaomega_ebooks_import_from_latest',
                esc_html__('Import from Latest', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_import_from_latest_callback'],
                'alfaomega_ebooks_page2',
                'alfaomega_ebooks_second_section',
                ['label_for' => 'alfaomega_ebooks_import_from_latest']
            );

            // Platform tab
            // TODO Alfaomega external services to use by the Client Digital Library
            add_settings_section(
                'alfaomega_ebooks_third_section',
                esc_html__( 'eBooks Platform', 'alfaomega-ebooks' ),
                null,
                'alfaomega_ebooks_page3',
                ['label_for' => 'alfaomega_ebooks_username']
            );

            add_settings_field(
                'alfaomega_ebooks_reader',
                esc_html__('Reader App', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_reader_callback'],
                'alfaomega_ebooks_page3',
                'alfaomega_ebooks_third_section',
                ['label_for' => 'alfaomega_ebooks_reader']
            );

            add_settings_field(
                'alfaomega_ebooks_panel',
                esc_html__('Panel Server', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_panel_callback'],
                'alfaomega_ebooks_page3',
                'alfaomega_ebooks_third_section',
                ['label_for' => 'alfaomega_ebooks_panel']
            );

            add_settings_field(
                'alfaomega_ebooks_client',
                esc_html__('Panel Client', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_client_callback'],
                'alfaomega_ebooks_page3',
                'alfaomega_ebooks_third_section',
                ['label_for' => 'alfaomega_ebooks_client']
            );

            add_settings_field(
                'alfaomega_ebooks_price',
                esc_html__('Digital Price', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_price_callback'],
                'alfaomega_ebooks_page3',
                'alfaomega_ebooks_third_section',
                ['label_for' => 'alfaomega_ebooks_price']
            );

            add_settings_field(
                'alfaomega_ebooks_printed_price',
                esc_html__('Printed + Digital Price', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_printed_price_callback'],
                'alfaomega_ebooks_page3',
                'alfaomega_ebooks_third_section',
                ['label_for' => 'alfaomega_ebooks_printed_price']
            );

            // API tab
            // TODO Alfaomega API configuration.
            add_settings_section(
                'alfaomega_ebooks_fourth_section',
                esc_html__( 'API Settings', 'alfaomega-ebooks' ),
                null,
                'alfaomega_ebooks_page4'
            );

            add_settings_field(
                'alfaomega_ebooks_token',
                esc_html__('Token Url', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_token_callback'],
                'alfaomega_ebooks_page4',
                'alfaomega_ebooks_fourth_section',
                ['label_for' => 'alfaomega_ebooks_token']
            );

            add_settings_field(
                'alfaomega_ebooks_api',
                esc_html__('API Server', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_api_callback'],
                'alfaomega_ebooks_page4',
                'alfaomega_ebooks_fourth_section',
                ['label_for' => 'alfaomega_ebooks_api']
            );

            add_settings_field(
                'alfaomega_ebooks_client_id',
                esc_html__('Client Id', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_client_id_callback'],
                'alfaomega_ebooks_page4',
                'alfaomega_ebooks_fourth_section',
                ['label_for' => 'alfaomega_ebooks_client_id']
            );

            add_settings_field(
                'alfaomega_ebooks_client_secret',
                esc_html__('Client Secret', 'alfaomega-ebooks'),
                [$this, 'alfaomega_ebooks_client_secret_callback'],
                'alfaomega_ebooks_page4',
                'alfaomega_ebooks_fourth_section',
                ['label_for' => 'alfaomega_ebooks_client_secret']
            );
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
                    name="alfaomega_ebooks_general_options[alfaomega_ebooks_username]"
                    id="alfaomega_ebooks_username"
                    size="50"
                    value="<?php echo isset(self::$generalOptions['alfaomega_ebooks_username']) ? esc_attr(self::$generalOptions['alfaomega_ebooks_username']) : ''; ?>"
                >
                <p class="description"> <? esc_html_e("User's email authorized to access Alfaomega Ebooks Platform", 'alfaomega-ebooks') ?> </p>
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
                name="alfaomega_ebooks_general_options[alfaomega_ebooks_active]"
                id="alfaomega_ebooks_active"
                value="1"
                <?php
                    if ( isset( self::$generalOptions['alfaomega_ebooks_active'])) {
                        checked( "1", self::$generalOptions['alfaomega_ebooks_active'], true);
                    }
                ?>
            >
            <label for="alfaomega_ebooks_active">
                <? esc_html_e('Whether to update eBooks link to relative product with the same Digital ISBN', 'alfaomega-ebooks'); ?>
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
                name="alfaomega_ebooks_general_options[alfaomega_ebooks_password]"
                id="alfaomega_ebooks_password"
                size="50"
                value="<?php echo isset(self::$generalOptions['alfaomega_ebooks_password']) ? esc_attr(self::$generalOptions['alfaomega_ebooks_password']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("User's password authorized to access Alfaomega Ebooks Platform", 'alfaomega-ebooks') ?> </p>
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
                name="alfaomega_ebooks_general_options[alfaomega_ebooks_notify_to]"
                id="alfaomega_ebooks_notify_to"
                size="50"
                value="<?php echo isset(self::$generalOptions['alfaomega_ebooks_notify_to']) ? esc_attr(self::$generalOptions['alfaomega_ebooks_notify_to']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Email address to send a copy of every download code set to clients", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the notify_to field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_import_limit_callback(): void
        {
            ?>
            <input
                type="number"
                name="alfaomega_ebooks_general_options[alfaomega_ebooks_import_limit]"
                id="alfaomega_ebooks_notify_to"
                min="1"
                max="5000"
                style="width: 7em"
                value="<?php echo isset(self::$generalOptions['alfaomega_ebooks_import_limit']) ? esc_attr(self::$generalOptions['alfaomega_ebooks_import_limit']) : '1000'; ?>"
            >
            <p class="description"> <? esc_html_e("Max number of new eBooks to import at once", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the active field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_import_from_latest_callback(): void
        {
            ?>
            <input
                type="checkbox"
                name="alfaomega_ebooks_general_options[alfaomega_ebooks_import_from_latest]"
                id="alfaomega_ebooks_import_from_latest"
                value="1"
                <?php
                if ( isset( self::$generalOptions['alfaomega_ebooks_import_from_latest'])) {
                    checked( "1", self::$generalOptions['alfaomega_ebooks_import_from_latest'], true);
                }
                ?>
            >
            <label for="alfaomega_ebooks_active">
                <? esc_html_e('Start importing from the latest imported eBook.', 'alfaomega-ebooks'); ?>
            </label>
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
                name="alfaomega_ebooks_platform_options[alfaomega_ebooks_reader]"
                id="alfaomega_ebooks_reader"
                size="50"
                value="<?php echo isset(self::$platformOptions['alfaomega_ebooks_reader']) ? esc_attr(self::$platformOptions['alfaomega_ebooks_reader']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Reader app URL", 'alfaomega-ebooks') ?> </p>
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
                type="url"
                name="alfaomega_ebooks_platform_options[alfaomega_ebooks_panel]"
                id="alfaomega_ebooks_panel"
                size="50"
                value="<?php echo isset(self::$platformOptions['alfaomega_ebooks_panel']) ? esc_attr(self::$platformOptions['alfaomega_ebooks_panel']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Publisher Panel server URL", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the client field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_client_callback(): void
        {
            ?>
            <input
                type="text"
                name="alfaomega_ebooks_platform_options[alfaomega_ebooks_client]"
                id="alfaomega_ebooks_client"
                size="50"
                value="<?php echo isset(self::$platformOptions['alfaomega_ebooks_client']) ? esc_attr(self::$platformOptions['alfaomega_ebooks_client']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Publisher Client", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the client field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_price_callback(): void
        {
            ?>
            <input
                type="number"
                name="alfaomega_ebooks_platform_options[alfaomega_ebooks_price]"
                id="alfaomega_ebooks_price"
                size="50"
                value="<?php echo isset(self::$platformOptions['alfaomega_ebooks_price']) ? esc_attr(self::$platformOptions['alfaomega_ebooks_price']) : 80; ?>"
            >
            <p class="description"> <? esc_html_e("Percent of printed price", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the client field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_printed_price_callback(): void
        {
            ?>
            <input
                type="number"
                name="alfaomega_ebooks_platform_options[alfaomega_ebooks_printed_price]"
                id="alfaomega_ebooks_printed_price"
                size="50"
                value="<?php echo isset(self::$platformOptions['alfaomega_ebooks_printed_price']) ? esc_attr(self::$platformOptions['alfaomega_ebooks_printed_price']) : '130'; ?>"
            >
            <p class="description"> <? esc_html_e("Percent of printed price", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the token field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_token_callback(): void
        {
            ?>
            <input
                type="url"
                name="alfaomega_ebooks_api_options[alfaomega_ebooks_token]"
                id="alfaomega_ebooks_token"
                size="50"
                value="<?php echo isset(self::$apiOptions['alfaomega_ebooks_token']) ? esc_attr(self::$apiOptions['alfaomega_ebooks_token']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Endpoint to renovate the access token", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the api field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_api_callback(): void
        {
            ?>
            <input
                type="url"
                name="alfaomega_ebooks_api_options[alfaomega_ebooks_api]"
                id="alfaomega_ebooks_api"
                size="50"
                value="<?php echo isset(self::$apiOptions['alfaomega_ebooks_api']) ? esc_attr(self::$apiOptions['alfaomega_ebooks_api']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("API Server URL", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the client_id field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_client_id_callback(): void
        {
            ?>
            <input
                type="text"
                name="alfaomega_ebooks_api_options[alfaomega_ebooks_client_id]"
                id="alfaomega_ebooks_client_id"
                size="50"
                value="<?php echo isset(self::$apiOptions['alfaomega_ebooks_client_id']) ? esc_attr(self::$apiOptions['alfaomega_ebooks_client_id']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Client Id of the eCommerce account in the Publisher Panel", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        /**
         * Render the client_secret field
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function alfaomega_ebooks_client_secret_callback(): void
        {
            ?>
            <input
                type="password"
                name="alfaomega_ebooks_api_options[alfaomega_ebooks_client_secret]"
                id="alfaomega_ebooks_client_secret"
                size="50"
                value="<?php echo isset(self::$apiOptions['alfaomega_ebooks_client_secret']) ? esc_attr(self::$apiOptions['alfaomega_ebooks_client_secret']) : ''; ?>"
            >
            <p class="description"> <? esc_html_e("Client Secret of the eCommerce account in the Publisher Panel", 'alfaomega-ebooks') ?> </p>
            <?php
        }

        public function alfaomega_books_validate(array $input): array
        {
            $new_input = [];
            foreach ($input as $key => $value) {
                $new_input[$key] = match ($key) {
                    'alfaomega_ebooks_username',
                    'alfaomega_ebooks_notify_to' => sanitize_email($value),
                    'alfaomega_ebooks_reader',
                    'alfaomega_ebooks_panel',
                    'alfaomega_ebooks_token',
                    'alfaomega_ebooks_api' => esc_url_raw($value),
                    default => sanitize_text_field($value),
                };
                if (empty($value) && !in_array($key, ['alfaomega_ebooks_active'])) {
                    add_settings_error(
                        'alfaomega_ebook_options',
                        'alfaomega_ebook_message',
                        printf( esc_html__( 'The field %d can not be left empty', 'alfaomega-ebooks' ), esc_html__($key, 'alfaomega-ebooks') ),
                        'error'
                    );
                    return [];
                }
            }

            return $new_input;
        }
    }
}
