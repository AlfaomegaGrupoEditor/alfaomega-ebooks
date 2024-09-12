<?php

use AlfaomegaEbooks\Http\RouteManager;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/admin
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
class Alfaomega_Ebooks_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/alfaomega-ebooks-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/alfaomega-ebooks-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Load dependencies for additional WooCommerce settings
     *
     * @since    1.0.0
     * @access   private
     */
    public function alfaomega_ebooks_add_settings( $settings ) {
        $settings[] = include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-alfaomega-ebooks-wc-settings.php';

        return $settings;
    }

    /**
     * Localize script variables for use in JavaScript
     *
     * @since    1.0.0
     * @access   public
     */
    public function localize_script_variables(): void
    {
        wp_localize_script($this->plugin_name, 'php_vars', [
            'admin_post_url' => esc_url(admin_url('admin-post.php')),
            'root'           => esc_url_raw(rest_url()),
            'api_url'        => esc_url(rest_url(RouteManager::ROUTE_NAMESPACE)),
            'nonce'          => wp_create_nonce('wp_rest'),
        ]);
    }

    /**
     * Add custom bulk actions for Alfaomega eBooks
     *
     * @since    1.0.0
     * @access   public
     * @param    array    $bulk_actions    The existing bulk actions.
     * @return   array    The modified bulk actions.
     */
    public function bulk_actions_alfaomega_ebooks($bulk_actions): array
    {
        $bulk_actions['update-meta'] = __('Update meta', 'alfaomega-ebooks');
        //$bulk_actions['link-product'] = __('Link product', 'alfaomega-ebooks');
        return $bulk_actions;
    }

    /**
     * Add custom bulk actions for WooCommerce products
     *
     * @since    1.0.0
     * @access   public
     * @param    array    $bulk_actions    The existing bulk actions.
     * @return   array    The modified bulk actions.
     */
    public function bulk_actions_wc_product($bulk_actions): array
    {
        $bulk_actions['link-ebook'] = __('Link eBook', 'alfaomega-ebooks');
        return $bulk_actions;
    }

    /**
     * Show notifications based on request parameters
     *
     * @since    1.0.0
     * @access   public
     */
    public function show_notification(): void
    {
        if (!empty($_REQUEST['updated-meta'])) {
            if ($_REQUEST['updated-meta'] === 'fail') {
                $class = 'notice notice-error is-dismissible';
                $message = esc_html__('Update eBooks meta failed!', 'alfaomega-ebooks');
            } else {
                $class = 'notice notice-success is-dismissible';
                $updatedEbooks = intval($_REQUEST['updated-meta']);
                $message = sprintf(esc_html__('%u eBooks meta updated successfully', 'alfaomega-ebooks'), intval($updatedEbooks));
            }

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        } elseif (!empty($_REQUEST['find-product'])) {
            if ($_REQUEST['find-product'] === 'fail') {
                $class = 'notice notice-error is-dismissible';
                $message = esc_html__('Product not found', 'alfaomega-ebooks');
            } else {
                $class = 'notice notice-success is-dismissible';
                $message = esc_html__('Showing the related eBook product', 'alfaomega-ebooks');
            }

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        } elseif (!empty($_REQUEST['link-ebook'])) {
            if ($_REQUEST['link-ebook'] === 'fail') {
                $class = 'notice notice-error is-dismissible';
                $message = esc_html__('Link eBook failed! Please check the product ebook ISBN and simple product price.', 'alfaomega-ebooks');
            } else {
                $linkEbook = intval($_REQUEST['link-ebook']);
                $class = 'notice notice-success is-dismissible';
                $message = sprintf(esc_html__('%u ebooks linked successfully', 'alfaomega-ebooks'), $linkEbook);
            }

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        } elseif (!empty($_REQUEST['unlink-ebook'])) {
            if ($_REQUEST['unlink-ebook'] === 'fail') {
                $class = 'notice notice-error is-dismissible';
                $message = esc_html__('Unlink eBook failed!', 'alfaomega-ebooks');
            } else {
                $linkEbook = intval($_REQUEST['unlink-ebook']);
                $class = 'notice notice-success is-dismissible';
                $message = sprintf(esc_html__('Ebook unlinked successfully', 'alfaomega-ebooks'));
            }

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        }
    }

    /**
     * Adds custom quick actions to the WordPress admin dashboard for 'product' and 'alfaomega-ebook' post types.
     *
     * This method takes an array of existing actions and a post object as parameters. It checks the post type of the
     * given post, and based on the post type, it removes the default quick actions and adds custom quick actions.
     *
     * For 'product' post type, it adds a 'Link' quick action which links the product to an eBook.
     * For 'alfaomega-ebook' post type, it adds 'Update' and 'Link' quick actions which update the eBook metadata and
     * link the eBook to a product respectively.
     *
     * @param array $actions An array of existing quick actions.
     * @param WP_Post $post The post object for which to add the quick actions.
     * @return array The modified array of quick actions.
     */
    public function add_custom_quick_actions($actions, $post): array
    {
        switch ($post->post_type) {
            case 'product':
                $product = wc_get_product($post->ID);
                if ($product->get_type() === 'simple') {
                    $actions['link-ebook'] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(['ebook_action' => 'link-ebook', 'post' => $post->ID], 'admin-post.php')),
                        esc_html__('Link eBook', 'alfaomega-ebooks')
                    );
                } else {
                    $actions['unlink-ebook'] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(['ebook_action' => 'unlink-ebook', 'post' => $post->ID], 'admin-post.php')),
                        esc_html__('Unlink eBook', 'alfaomega-ebooks')
                    );
                }
                break;

            case 'alfaomega-ebook':
                // Remove the default quick actions
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);

                $actions = array_merge([
                    'update-meta'  => sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(['ebook_action' => 'update-meta', 'post' => $post->ID], 'admin-post.php')),
                        esc_html__('Update', 'alfaomega-ebooks')),
                    'find-product' => sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(['ebook_action' => 'find-product', 'post' => $post->ID], 'admin-post.php')),
                        esc_html__('Product', 'alfaomega-ebooks')
                    ),
                ], $actions);
        }

        return $actions;
    }

    /**
     * Adds custom fields to the WooCommerce product editor.
     *
     * This method adds a custom text input field to the WooCommerce product editor. The field is used to store the
     * ISBN of an eBook.
     */
    public function woocommerce_product_custom_fields(): void
    {
        global $woocommerce, $post;
        echo '<div class=" product_custom_field ">';
        woocommerce_wp_text_input(
            array(
                'id' => 'alfaomega_ebooks_ebook_isbn',
                'placeholder' => 'NUMERO ISBN',
                'label' => __('eBook ISBN', 'woocommerce'),
                'desc_tip' => 'true',
            )
        );
        echo '</div>';
    }

    /**
     * Saves the custom fields for the WooCommerce product editor.
     *
     * This method saves the custom field data for the WooCommerce product editor. It saves the ISBN of an eBook.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function woocommerce_product_custom_fields_save($post_id): void
    {
        $woocommerce_alfaomega_ebooks_ebook_isbn = $_POST['alfaomega_ebooks_ebook_isbn'];
        if (!empty($woocommerce_alfaomega_ebooks_ebook_isbn)) {
            update_post_meta($post_id, 'alfaomega_ebooks_ebook_isbn', esc_attr($woocommerce_alfaomega_ebooks_ebook_isbn));
        }
    }

    /**
     * Adds a custom column to the WooCommerce product list table.
     *
     * This method adds a custom column to the WooCommerce product list table. The custom column displays the ISBN of
     * an eBook.
     *
     * @param array $columns An array of existing columns in the product list table.
     * @return array The modified array of columns.
     */
    public function woocommerce_product_post_set_columns($columns): array
    {
        $columns['ebook_isbn'] = __('eBook', 'cs-text');

        return $columns;
    }

    /**
     * Displays the content of the custom column in the WooCommerce product list table.
     *
     * This method displays the content of the custom column in the WooCommerce product list table. The custom column
     * displays the ISBN of an eBook.
     *
     * @param string $column The name of the custom column.
     * @param int $post_id The ID of the post being displayed.
     */
    public function woocommerce_product_post_custom_column($column, $post_id ): void
    {
        switch ( $column ) {
            case 'ebook_isbn' : // This has to match to the defined column in function above
                $get_ebook_isbn = get_field('alfaomega_ebooks_ebook_isbn', $post_id);
                echo !empty($get_ebook_isbn) ? "<span>$get_ebook_isbn</span>" : '';
                break;
        }
    }

    /**
     * Makes the custom column in the WooCommerce product list table sortable.
     * @param $columns
     *
     * @return array
     */
    public function woocommerce_product_column_sortable($columns): array
    {
        $columns['ebook_isbn'] = __('eBook', 'cs-text');
        return $columns;
    }

    /**
     * Adds the eBook column to the WooCommerce product list table.
     * @param $columns
     *
     * @return array
     */
    public function woocommerce_product_column_ebook($columns): array
    {
        $columns['ebook_isbn'] = __('eBook', 'cs-text');
        return array_slice( $columns, 0, 4, true ) + array( 'ebook_isbn' => __('eBook', 'cs-text') ) + array_slice( $columns, 4, count( $columns ) - 3, true );
    }

    /**
     * Filter products by eBook synchronization status
     * @param $post_type
     *
     * @return void
     */
    public function ebooks_product_filters($post_type)
    {
        $value1 = '';
        $value2 = '';
        $value3 = '';
        $value4 = '';
        $value5 = '';
        if (isset($_GET['ebooks_filter'])) {
            switch ($_GET['ebooks_filter']) {
                case 'all':
                    $value1 = ' selected';
                    break;
                case 'sync':
                    $value2 = ' selected';
                    break;
                case 'un_sync':
                    $value3 = ' selected';
                    break;
                case 'none':
                    $value4 = ' selected';
                    break;
                case 'disabled':
                    $value5 = ' selected';
                    break;
            }
        }

        // Check this is the products screen
        if ($post_type == 'product') {
            /*echo '<select name="ebooks_filter">';
            echo '  <option value>' . esc_html__('Filter by eBook', 'alfaomega_ebooks') . '</option>';
            echo '  <option value="all"' . $value1 . '>' . esc_html__('All eBooks', 'alfaomega_ebooks') . '</option>';
            echo '  <option value="sync"' . $value2 . '>' . esc_html__('eBook Linked', 'alfaomega_ebooks') . '</option>';
            echo '  <option value="un_sync"' . $value3 . '>' . esc_html__('eBook Unlinked', 'alfaomega_ebooks') . '</option>';
            echo '</select>';*/

            echo '<select name="ebooks_filter">';
            echo '  <option value>' . esc_html__('Filtrar por eBook', 'alfaomega_ebooks') . '</option>';
            echo '  <option value="none"' . $value4 . '>' . esc_html__('Producto simple', 'alfaomega_ebooks') . '</option>';
            echo '  <option value="all"' . $value1 . '>' . esc_html__('Todos los eBooks', 'alfaomega_ebooks') . '</option>';
            echo '  <option value="sync"' . $value2 . '>' . esc_html__('Vinculado correctamente', 'alfaomega_ebooks') . '</option>';
            echo '  <option value="un_sync"' . $value3 . '>' . esc_html__('Desvinculado', 'alfaomega_ebooks') . '</option>';
            echo '  <option value="disabled"' . $value5 . '>' . esc_html__('Desactivado', 'alfaomega_ebooks') . '</option>';
            echo '</select>';
        }
    }

    /**
     * Applies the selected eBook filter
     * @param $query
     *
     * @return void
     */
    function apply_ebooks_product_filters( $query ) {
        global $pagenow;
        // Ensure it is an edit.php admin page, the filter exists and has a value, and that it's the products page
        if ( $query->is_admin &&
             $pagenow == 'edit.php' &&
             isset( $_GET['ebooks_filter'] )
             && $_GET['ebooks_filter'] != ''
             && $_GET['post_type'] == 'product' ) {

            switch (esc_attr($_GET['ebooks_filter'])) {
                // all products without ebook isbn and type is simple
                case 'none':
                    $query->set('meta_query', [
                        'relation' => 'OR',
                        [
                            'key'     => 'alfaomega_ebooks_ebook_isbn',
                            'compare' => 'NOT EXISTS',
                        ],
                        [
                            'key'     => 'alfaomega_ebooks_ebook_isbn',
                            'value'   => '',
                            'compare' => '=',
                        ],
                    ]);
                    $query->set('tax_query', [
                        [
                            'taxonomy' => 'product_type',
                            'field'    => 'slug',
                            'terms'    => 'simple',
                        ],
                    ]);
                    break;
                // all products with ebook isbn no matter the type
                case 'all':
                    $query->set('meta_query', [
                        [
                            'key'     => 'alfaomega_ebooks_ebook_isbn',
                            'value'   => '',
                            'compare' => '!=',
                        ],
                    ]);
                    break;
                // all products with ebook isbn and type is variable and attribute ebook is `Si`
                case 'sync':
                    $query->set('meta_query', [
                        [
                            'key'     => 'alfaomega_ebooks_ebook_isbn',
                            'value'   => '',
                            'compare' => '!=',
                        ],
                    ]);
                    $query->set('tax_query', [
                        [
                            'taxonomy' => 'product_type',
                            'field'    => 'slug',
                            'terms'    => 'variable',
                        ],
                        [
                            'taxonomy' => 'pa_ebook',
                            'field'    => 'slug',
                            'terms'    => 'si',
                        ],
                    ]);
                    break;
                // all products with ebook isbn and type is simple
                case 'un_sync':
                    $query->set('meta_query', [
                        [
                            'key'     => 'alfaomega_ebooks_ebook_isbn',
                            'value'   => '',
                            'compare' => '!=',
                        ],
                    ]);
                    $query->set('tax_query', [
                        [
                            'taxonomy' => 'product_type',
                            'field'    => 'slug',
                            'terms'    => 'simple',
                        ],
                    ]);
                    break;
                // all products with ebook isbn and type is variable and attribute ebook is not `Si`
                case 'disabled':
                    $query->set('meta_query', [
                        [
                            'key'     => 'alfaomega_ebooks_ebook_isbn',
                            'value'   => '',
                            'compare' => '!=',
                        ],
                    ]);
                    $query->set('tax_query', [
                        [
                            'taxonomy' => 'product_type',
                            'field'    => 'slug',
                            'terms'    => 'variable',
                        ],
                        [
                            'taxonomy' => 'pa_ebook',
                            'field'    => 'slug',
                            'terms'    => ['no', 'desactivado'],
                            'compare' => 'IN',
                        ],
                    ]);
                    break;
            }
        }

    }

    /**
     * Save product meta data
     * @param $product
     *
     * @return void
     */
    function action_save_product_meta( $product ) {
        $product->update_meta_data( 'alfaomega_ebooks_ebook_isbn', $_POST['alfaomega_ebooks_ebook_isbn'] ?? '' );
    }

    /**
     * Boot the Carbon Fields framework.
     * This method initializes the Carbon Fields framework, which is used for creating custom fields
     * and meta boxes in the WordPress admin area.
     *
     * @since 1.0.0
     */
    public function boot_carbon_fields_framework(): void
    {
        \Carbon_Fields\Carbon_Fields::boot();
    }

    /**
     * To avoid the jQuery Migrate message in the console
     * @param $scripts
     *
     * @return void
     */
    function remove_jquery_Migrate($scripts)
    {
        if ( /*! is_admin() &&*/ isset( $scripts->registered['jquery'] ) ) {
            $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
        }
    }

    /**
     * Remove autosave script from the post editor
     * @return void
     */
    function remove_autosave_on_custom_post_types(): void
    {
        if ( get_post_type() == ALFAOMEGA_EBOOKS_SAMPLE_POST_TYPE ) {
            wp_dequeue_script( 'autosave' );
        }
    }
}
