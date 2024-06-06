<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/public
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
class Alfaomega_Ebooks_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Alfaomega_Ebooks_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Alfaomega_Ebooks_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/alfaomega-ebooks-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Alfaomega_Ebooks_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Alfaomega_Ebooks_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/alfaomega-ebooks-public.js', array( 'jquery' ), $this->version, false );

	}

    public function load_routes()
    {
        new Alfaomega_Ebooks_Custom_Route(
            'alfaomega-ebooks/(.+?)/(.+?)/?$',
            ['param_1', 'param_2'],
            ALFAOMEGA_EBOOKS_PATH . 'public/alfaomega-ebooks-routes.php',
            true
        );
    }

    public function show_notice(): void
    {
        $msg = $_SESSION['alfaomega_ebooks_msg'];

        if (!empty($msg)) {
            wc_add_notice($msg['message'], $msg['type']);
            $_SESSION['alfaomega_ebooks_msg'] =  null;
        }
    }

    public function download_product_filepath($file_path, $email_address, $order, $product, $download): string
    {
        $downloadId = $download->data['download_id'];
        $filePathArray = explode('/', trim($file_path, '/'));
        $eBookId = end($filePathArray);
        if (empty($downloadId) || empty($eBookIdId)) {
            return $file_path;
        }

        try {
            $service = new Alfaomega_Ebooks_Service();
            return $service->downloadEbook($eBookId, $downloadId);
        } catch (Exception $exception) {
            return $file_path;
        }
    }
}
