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
        wp_enqueue_style(
            $this->plugin_name,
            ALFAOMEGA_EBOOKS_URL . 'public/css/bundle.css',
            [],
            $this->version,
            'all'
        );

        // Enqueue loader CSS
        wp_enqueue_style("{$this->plugin_name}-loader-style", ALFAOMEGA_EBOOKS_URL . 'public/css/loader.css');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts()
    {
        // Enqueue custom JavaScript
        wp_enqueue_script("{$this->plugin_name}-loader-script", ALFAOMEGA_EBOOKS_URL  . 'public/js/loader.js', array('jquery'), null, true);

        add_action('wp_footer', function() {
            $plugin_name = $this->plugin_name;
            if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
                // development
                $plugin_name .= "-dev";
                wp_enqueue_script(
                    'vite-client',
                    'http://localhost:3000/@vite/client',
                    [],
                    null,
                    true
                );
                wp_enqueue_script(
                    $plugin_name,
                    'http://localhost:3000/Resources/main.ts',
                    [],
                    null,
                    true
                );
            } else {
                // production
                wp_enqueue_script(
                    $plugin_name,
                    ALFAOMEGA_EBOOKS_URL . 'public/js/bundle.js',
                    [],
                    $this->version,
                    true
                );
            }

            wp_localize_script($plugin_name, 'wpApiSettings', [
                'root'   => esc_url_raw(site_url()), // Root URL for the API
                //'root'   => esc_url_raw(rest_url()), // Root URL for the API
                'nonce'  => wp_create_nonce('wp_rest'), // Create a nonce for secure API calls
                'covers' => ALFAOMEGA_COVER_PATH,
                'migration' => defined('AO_SHOW_MIGRATION_ALERT') && AO_SHOW_MIGRATION_ALERT && AO_OLD_STORE,
                'oldStore' => defined('AO_OLD_STORE') ? AO_OLD_STORE : null
            ]);
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
    public function alfaomega_add_type_attribute($tag, $handle, $src) {
        if (in_array($handle, [ "{$this->plugin_name}-dev", "vite-client", $this->plugin_name ])) {
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
            ALFAOMEGA_EBOOKS_PATH . 'website/alfaomega-ebooks-routes.php',
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
    public function download_product_filepath($file_path, $email_address, $order, $product, $download): string|bool {
        $downloadId = $download->data['download_id'];
        $filePathArray = explode('/', trim($file_path, '/'));
        $eBookId = intval(end($filePathArray));
        if (empty($downloadId) || empty($eBookId)) {
            return wp_safe_redirect('/my-ao-ebooks');
        }

        try {
            return Service::make()->ebooks()
                ->download($eBookId, $downloadId);

        } catch (Exception $exception) {
            return wp_safe_redirect('/my-ao-ebooks');
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
            //$service = new Alfaomega_Ebooks_Service(); // @fixme deprecated
            //$readerUrl = $service->readEbookUrl($eBookId, $downloadId);
            $readerUrl = site_url("alfaomega-ebooks/read/{$eBookId}?key={$downloadId}");
            echo '<a href="' . $readerUrl . '" class="woocommerce-MyAccount-downloads-file button alt">' . esc_html__( 'Read', 'alfaomega-ebooks' ) . '</a>';
        } catch (Exception $exception) {
            esc_html_e( 'Not available', 'alfaomega-ebooks' );
        }
    }

    public function modify_download_file_column(array $download): void {
        try {
            $filePathArray = explode('/', trim($download['file']['file'], '/'));
            $eBookId = intval(end($filePathArray));
            $accessPost = Service::make()
                ->ebooks()
                ->accessPost();
            $userAccess = $accessPost->find($eBookId, get_current_user_id(), true);
            $downloadUrl = site_url("alfaomega-ebooks/download/{$eBookId}?access={$userAccess['id']}");
            echo '<a href="' . $downloadUrl . '" class="woocommerce-MyAccount-downloads-file button alt">eBook</a>';
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
        global $product;

        $printed = '<select id="pa_book-format" style="color: black;" name="attribute_pa_book-format" data-attribute_name="attribute_pa_book-format" data-show_option_none="yes"><option value="">Elige una opción</option><option value="impreso" >Impreso</option></select>';
        $printedSelected = '<select id="pa_book-format" style="color: black;" name="attribute_pa_book-format" data-attribute_name="attribute_pa_book-format" data-show_option_none="yes"><option value="">Elige una opción</option><option value="impreso" selected="selected">Impreso</option></select>';
        if ($html === $printed) {
            return $printedSelected;
        }

        /*$outOfStock = !wc_get_product($product->get_id())->is_in_stock();
        if ($outOfStock) {*/
            $html = str_replace("selected='selected'", '', $html);
            $html = str_replace(
                'value="impreso"',
                'selected="selected" value="impreso"',
                $html
            );

        /*}*/

        return str_replace('id="pa_book-format"', 'id="pa_book-format" style="color: black;"', $html);
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
     * Handle the permission for downloadable files.
     *
     * @param \WC_Customer_Download $download
     * @param \WC_Product $product
     * @param \WC_Order $order
     * @param int $qty
     * @param \WC_Order_Item $item
     *
     * @return \WC_Customer_Download
     * @throws \Exception
     */
    public function on_downloadable_file_permission(WC_Customer_Download $download,
                                                    WC_Product $product,
                                                    WC_Order $order,
                                                    int $qty,
                                                    WC_Order_Item  $item
    ): WC_Customer_Download {
        if (! empty($order->get_id())) {
            $this->on_order_complete($order->get_id());
        } else {
            Service::make()->log('eBook activation failed: ' . json_encode([
                    'download' => $download,
                    '$product' => $product,
                    'order'    => $order,
                    'qty'      => $qty,
                    'item'     => $item
                ], JSON_PRETTY_PRINT));
        }

        return $download;
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

    /**
     * Add the content table tab to the product page
     * @param $tabs
     *
     * @return array
     * @throws \Exception
     */
    function alfaomega_product_tabs($tabs): array
    {
        global $product;
        $sku = $product->get_sku();

        $ebookPost = Service::make()->ebooks()
            ->ebookPost()
            ->search($sku, 'alfaomega_ebook_product_sku');
        if (!empty($ebookPost) && !empty($ebookPost['content_table'])) {
            $tabs['content_table'] = array(
                'title'    => __( 'Content table', 'alfaomega-ebooks' ),
                'priority' => 50,
                'callback' => function() use ($ebookPost){
                    $contentTable = 'https://alfaomega-content-tables.nyc3.cdn.digitaloceanspaces.com/' . $ebookPost['content_table'];
                    $this->content_table_tab_content('content_table', $contentTable);
                }
            );
        }

        return $tabs;
    }

    /**
     * Display the content table tab content
     *
     * @param string $key
     * @param string $content
     */
    function content_table_tab_content($key, $content): void
    {
        if ($key === 'content_table') {
            echo '<h3 class="fusion-woocommerce-tab-title">' . esc_html__('Content table', 'alfaomega-ebooks') . '</h3>';
            echo '<embed src="' . esc_url($content) . '" type="application/pdf" width="100%" height="800px" />';
        }
    }

    /**
     * Deactivate the variation if it is out of stock
     *
     * @param bool $active
     * @param WC_Product_Variation $variation
     *
     * @return bool
     */
    function deactivate_variation_if_out_of_stock($active, $variation): bool
    {
        // Check if the variation is out of stock
        if (!$variation->is_in_stock() && $variation->get_attribute('pa_book-format') !== 'Digital') {
            return false;
        }
        return $active;
    }

    /**
     * Format the price of the product variation
     *
     * @param $price
     * @param $product
     *
     * @return string
     */
    function alfaomega_product_variation_price_format($price, $product): string
    {
        if ( $product->is_type( 'variable' ) ) {
            $variations = $product->get_available_variations();
            foreach ($variations as $variation) {
                if ($variation['attributes']['attribute_pa_book-format'] === 'impreso') {
                    if ($variation['display_price'] !== $variation['display_regular_price']) {
                        return '<p class="price">
                                    <del aria-hidden="true">' . wc_price($variation['display_regular_price']) . '</del> 
                                    <span class="screen-reader-text"> El precio original era: $'. $variation['display_regular_price'] . '</span>
                                    <ins aria-hidden="true">' . wc_price($variation['display_price']) . '</ins>
                                    <span class="screen-reader-text">El precio actual es:'. $variation['display_price'] . '</span>
                                </p>';
                    }
                    return '<span class="woocommerce-Price-amount amount">' . wc_price($variation['display_price']) . '</span>';
                }
            }
        }

        return $price;
    }
}
