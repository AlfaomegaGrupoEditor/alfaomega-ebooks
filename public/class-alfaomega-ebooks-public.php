<?php

use AlfaomegaEbooks\Services\eBooks\Service;

/**
 * Class Alfaomega_Ebooks_Public
 *
 * This class handles the public-facing functionality of the Alfaomega Ebooks plugin.
 * It includes methods for enqueuing styles and scripts, loading routes, showing notices,
 * and handling ebook downloads.
 */
class Alfaomega_Ebooks_Public {

    /**
     * The ID of this plugin.
     *
     * @var string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @var string $version The current version of this plugin.
     */
    private $version;

    /**
     * Alfaomega_Ebooks_Public constructor.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        if ( !defined('WP_DEBUG') || !WP_DEBUG ) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'dist/css/bundle.css', [], $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        add_action('wp_footer', function() {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                wp_enqueue_script('vite-client', 'http://localhost:3000/@vite/client', [], null, true);
                wp_enqueue_script($this->plugin_name, 'http://localhost:3000/assets/src/js/main.ts', [], null, true);
            } else {
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'dist/js/bundle.js', [], $this->version, true);
            }
        });
    }

    /**
     * Add the type attribute to the script tag.
     *
     * @param string $tag The original script tag.
     * @param string $handle The script handle.
     * @param string $src The script source.
     * @return string The modified script tag.
     */
    public function myplugin_add_type_attribute($tag, $handle, $src) {
        if ('myplugin-dev' === $handle) {
            $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        }
        return $tag;
    }

    /**
     * Load custom routes for the plugin.
     */
    public function load_routes(): void
    {
        new Alfaomega_Ebooks_Custom_Route(
            'alfaomega-ebooks/(.+?)/(.+?)/?$',
            ['param_1', 'param_2'],
            ALFAOMEGA_EBOOKS_PATH . 'public/alfaomega-ebooks-routes.php',
            true
        );
    }

    /**
     * Show notices to the user.
     */
    public function show_notice(): void {
        $msg = session_id() ? $_SESSION['alfaomega_ebooks_msg'] : null;

        if (!empty($msg)) {
            wc_add_notice($msg['message'], $msg['type']);
            $_SESSION['alfaomega_ebooks_msg'] =  null;
        }
    }

    /**
     * Get the file path for the product download.
     *
     * @param string $file_path The original file path.
     * @param string $email_address The email address of the user.
     * @param WC_Order $order The order object.
     * @param WC_Product $product The product object.
     * @param WC_Customer_Download $download The download object.
     * @return string The file path for the download.
     */
    public function download_product_filepath($file_path, $email_address, $order, $product, $download): string {
        $downloadId = $download->data['download_id'];
        $filePathArray = explode('/', trim($file_path, '/'));
        $eBookId = intval(end($filePathArray));
        if (empty($downloadId) || empty($eBookId)) {
            return $file_path;
        }

        try {
            return Service::make()->ebooks()
                ->download($eBookId, $downloadId);

        } catch (Exception $exception) {
            return $file_path;
        }
    }

    /**
     * Modify the columns for the product download.
     *
     * @param array $columns The original columns.
     * @return array The modified columns.
     */
    public function download_product_columns(array $columns): array {
        unset($columns['download-remaining']);
        unset($columns['download-expires']);
        $columns['read-online'] = __('Read&nbsp;Online', 'alfaomega-ebooks');
        return $columns;
    }

    /**
     * Add a "Read Online" column to the product download.
     *
     * @param array $download The download data.
     */
    public function add_read_online_column(array $download): void {
        try {
            $filePathArray = explode('/', trim($download['file']['file'], '/'));
            $downloadId = $download['download_id'];
            $eBookId = intval(end($filePathArray));
            $service = new Alfaomega_Ebooks_Service();
            $readerUrl = $service->readEbookUrl($eBookId, $downloadId);
            echo '<a href="' . $readerUrl . '" class="woocommerce-MyAccount-downloads-file button alt">' . esc_html__( 'Read', 'alfaomega-ebooks' ) . '</a>';
        } catch (Exception $exception) {
            esc_html_e( 'Not available', 'alfaomega-ebooks' );
        }
    }

    /**
     * Modify the product attributes when the eBook is not enabled.
     *
     * @param array $args The original arguments.
     * @return array The modified arguments.
     */
    public function product_get_attributes(array $args): array {
        if ($args['attribute'] !== 'pa_book-format' ||
            $args['product']->get_type() !== 'variable' ||
            $args['product']->get_attribute('pa_ebook') === 'Si') {
            return $args;
        }

        $args['options'] = array_filter($args['options'], function($option) {
            return $option === 'impreso';
        });

        return $args;
    }

    /**
     * Modify the product attributes dropdown when the eBook is not enabled.
     *
     * @param array $args The original arguments.
     * @return array The modified arguments.
     */
    public function dropdown_variation_attribute_options_html(string $html): string {
        $printed = '<select id="pa_book-format" class="" name="attribute_pa_book-format" data-attribute_name="attribute_pa_book-format" data-show_option_none="yes"><option value="">Elige una opción</option><option value="impreso" >Impreso</option></select>';
        $printedSelected = '<select id="pa_book-format" class="" name="attribute_pa_book-format" data-attribute_name="attribute_pa_book-format" data-show_option_none="yes"><option value="">Elige una opción</option><option value="impreso" selected="selected">Impreso</option></select>';

        return $html === $printed ? $printedSelected : $html;
    }

    /**
     * Update the access post when the order is complete.
     *
     * @param int $order_id The ID of the order.
     *
     * @throws \Exception
     */
    public function on_order_complete($order_id): void {
        Service::make()
            ->wooCommerce()
            ->order()
            ->onComplete($order_id);
    }

    /**
     * Register the shortcode for displaying the customer's purchased eBooks
     * @return void
     */
    function my_ao_ebook_shortcode(): false|string
    {
        ob_start();

        require ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_my_ebooks.php';

        return ob_get_clean();
    }
}
