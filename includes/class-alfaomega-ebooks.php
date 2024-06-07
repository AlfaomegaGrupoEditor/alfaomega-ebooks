<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/livan2r
 * @since      1.0.0
 * @see https://wpshout.com/the-wordpress-plugin-boilerplate-101/, https://wppb.me/
 *
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/includes
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
class Alfaomega_Ebooks {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Alfaomega_Ebooks_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ALFAOMEGA_EBOOKS_VERSION' ) ) {
			$this->version = ALFAOMEGA_EBOOKS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = ALFAOMEGA_EBOOKS_NAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

        $Alfaomega_Ebooks_Post_Type = new Alfaomega_Ebooks_Post_Type();
        $Alfaomega_Ebooks_Settings = new Alfaomega_Ebooks_Settings();

        add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Alfaomega_Ebooks_Loader. Orchestrates the hooks of the plugin.
	 * - Alfaomega_Ebooks_i18n. Defines internationalization functionality.
	 * - Alfaomega_Ebooks_Admin. Defines all hooks for the admin area.
	 * - Alfaomega_Ebooks_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alfaomega-ebooks-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alfaomega-ebooks-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-alfaomega-ebooks-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-alfaomega-ebooks-public.php';

        /**
         * The class responsible for defining the CPT alfaomega-ebook.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'post_types/class-alfaomega-ebooks-post-type.php';

        /**
         * The class responsible for defining the settings page.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alfaomega-ebooks-settings.php';

        /**
         * Loading custom route class.
         */
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alfaomega-ebooks-custom-route.php' );

        /**
         * The class responsible for processing the request.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alfaomega-ebooks-controller.php';

        /**
         * The class responsible for processing the plugin logic.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alfaomega-ebooks-service.php';

        /**
         * The class responsible for processing API calls.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alfaomega-ebooks-api.php';

        /**
         * Loading composer dependencies.
         */
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php' );

		$this->loader = new Alfaomega_Ebooks_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Alfaomega_Ebooks_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Alfaomega_Ebooks_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
    {

		$plugin_admin = new Alfaomega_Ebooks_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'localize_script_variables' );

        $controller = new Alfaomega_Ebooks_Controller();
        $this->loader->add_action( 'admin_post_nopriv_alfaomega_ebooks_form', $controller, 'process' );
        $this->loader->add_action( 'admin_post_alfaomega_ebooks_form', $controller,'process' );

        $this->loader->add_filter('bulk_actions-edit-alfaomega-ebook', $plugin_admin, 'bulk_actions_alfaomega_ebooks');
        $this->loader->add_filter('handle_bulk_actions-edit-alfaomega-ebook', $controller, 'bulk_action_alfaomega_ebooks', 10, 3);
        $this->loader->add_filter('bulk_actions-edit-product', $plugin_admin, 'bulk_actions_wc_product');
        $this->loader->add_filter('handle_bulk_actions-edit-product', $controller, 'bulk_action_alfaomega_ebooks', 10, 3);

        $this->loader->add_action('admin_notices', $plugin_admin, 'show_notification');

        // queue actions
        $service = new Alfaomega_Ebooks_Service();
        $this->loader->add_action('alfaomega_ebooks_queue_import', $service, 'importEbook');
        $this->loader->add_action('alfaomega_ebooks_queue_refresh_list', $service, 'refreshEbookList');
        $this->loader->add_action('alfaomega_ebooks_queue_refresh', $service, 'refreshEbook', 20, 2);

        // Add plugin settings to WooCommerce
        // @deprecated Using the own plugin settings
        // $this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_admin, 'alfaomega_ebooks_add_settings' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Alfaomega_Ebooks_Public( $this->get_plugin_name(), $this->get_version() );
        $plugin_public->load_routes();
        if (!session_id()) {
            session_start();
        }

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'show_notice' );

        $this->loader->add_filter('woocommerce_download_product_filepath', $plugin_public, 'download_product_filepath', 10, 5);
        $this->loader->add_filter('woocommerce_account_downloads_columns', $plugin_public, 'download_product_columns');
        $this->loader->add_action( 'woocommerce_account_downloads_column_read-online', $plugin_public, 'add_read_online_column' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Alfaomega_Ebooks_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    public function add_menu(): void
    {
        add_menu_page(
            'Alfaomega eBooks Admin ' . ALFAOMEGA_EBOOKS_VERSION,
            esc_html__('AO eBooks', 'alfaomega-ebooks'),
            'install_plugins',
            'alfaomega_ebooks_admin',
            [$this, 'renderHome'],
            'dashicons-book',
            56
        );

        add_submenu_page(
            'alfaomega_ebooks_admin',
            esc_html__('Settings', 'alfaomega-ebooks'),
            esc_html__('Settings', 'alfaomega-ebooks'),
            'install_plugins',
            'alfaomega_ebooks_settings',
            [$this, 'renderSettings'],
            null
        );

        add_submenu_page(
            'alfaomega_ebooks_admin',
            esc_html__('All Alfaomega eBooks', 'alfaomega-ebooks'),
            esc_html__('List', 'alfaomega-ebooks'),
            'install_plugins',
            'edit.php?post_type=alfaomega-ebook',
            null,
            null
        );

        add_submenu_page(
            'alfaomega_ebooks_admin',
            esc_html__('Import eBooks', 'alfaomega-ebooks'),
            esc_html__('Import', 'alfaomega-ebooks'),
            'install_plugins',
            'alfaomega_ebooks_import',
            [$this, 'renderImport'],
            null
        );

        add_submenu_page(
            'alfaomega_ebooks_admin',
            esc_html__('Refresh eBooks', 'alfaomega-ebooks'),
            esc_html__('Refresh', 'alfaomega-ebooks'),
            'install_plugins',
            'alfaomega_ebooks_refresh',
            [$this, 'renderRefresh'],
            null
        );

        add_submenu_page(
            'alfaomega_ebooks_admin',
            esc_html__('Link Products', 'alfaomega-ebooks'),
            esc_html__('Link', 'alfaomega-ebooks'),
            'install_plugins',
            'alfaomega_ebooks_link',
            [$this, 'renderLink'],
            null
        );
    }

    /**
     * Render the settings page
     * @return void
     * @since 1.0.0
     * @access public
     */
    public function renderSettings(): void
    {
        if( ! current_user_can( 'install_plugins' ) ){
            return;
        }

        if( isset( $_GET['settings-updated'] ) ){
            add_settings_error(
                'alfaomega_ebook_options',
                'alfaomega_ebook_message',
                esc_html__( 'Settings Saved', 'alfaomega-ebooks' ),
                'success'
            );
        }

        settings_errors( 'alfaomega_ebook_options' );

        require( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_settings_page.php' );
    }

    public function renderHome(): void
    {
        if( ! current_user_can( 'install_plugins' ) ){
            return;
        }

        settings_errors( 'alfaomega_ebook_options' );

        require( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_home_page.php' );
    }

    public function renderImport(): void
    {
        if( ! current_user_can( 'install_plugins' ) ){
            return;
        }

        settings_errors( 'alfaomega_ebook_options' );

        require( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_import_page.php' );
    }

    public function renderRefresh(): void
    {
        if( ! current_user_can( 'install_plugins' ) ){
            return;
        }

        settings_errors( 'alfaomega_ebook_options' );

        require( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_refresh_page.php' );
    }

    public function renderLink(): void
    {
        if( ! current_user_can( 'install_plugins' ) ){
            return;
        }

        settings_errors( 'alfaomega_ebook_options' );

        require( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_link_page.php' );
    }
}
