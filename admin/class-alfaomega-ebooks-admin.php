<?php

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
        wp_localize_script( $this->plugin_name, 'php_vars', [
            'my_variable' => "Hello, World!",
            'admin_post_url' => esc_url(admin_url('admin-post.php'))
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
        $bulk_actions['link-product'] = __('Link product', 'alfaomega-ebooks');
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
        } elseif (!empty($_REQUEST['link-product'])) {
            if ($_REQUEST['link-product'] === 'fail') {
                $class = 'notice notice-error is-dismissible';
                $message = esc_html__('Link product failed!', 'alfaomega-ebooks');
            } else {
                $linkProduct = intval($_REQUEST['link-product']);
                $class = 'notice notice-success is-dismissible';
                $message = sprintf(esc_html__('%u products linked successfully', 'alfaomega-ebooks'), $linkProduct);
            }

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        } elseif (!empty($_REQUEST['link-ebook'])) {
            if ($_REQUEST['link-ebook'] === 'fail') {
                $class = 'notice notice-error is-dismissible';
                $message = esc_html__('Link eBook failed! Please check the product ISBN tag.', 'alfaomega-ebooks');
            } else {
                $linkEbook = intval($_REQUEST['link-ebook']);
                $class = 'notice notice-success is-dismissible';
                $message = sprintf(esc_html__('%u ebooks linked successfully', 'alfaomega-ebooks'), $linkEbook);
            }

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        }
    }
}
