<?php

/**
 * @link              https://github.com/AlfaomegaGrupoEditor/alfaomega-ebooks
 * @since             1.0.0
 * @package           Alfaomega_Ebooks
 *
 * @wordpress-plugin
 * Plugin Name:       Alfaomega eBooks
 * Plugin URI:        https://github.com/AlfaomegaGrupoEditor/alfaomega-ebooks
 * Description:       Alfaomega eBooks Manager to import, update, and synchronize digital eBooks with WooCommerce products.
 * Version:           1.0.0
 * Author:            B&B Computer Services Corp.
 * Author URI:        https://github.com/livan2r/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       alfaomega-ebooks
 * Domain Path:       /languages
 */

/*
WooCommerce AlfaomegaEbooks is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
WooCommerce AlfaomegaEbooks is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with WooCommerce AlfaomegaEbooks. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
         */
define( 'ALFAOMEGA_EBOOKS_URL', plugin_dir_url( __FILE__ ) );
define( 'ALFAOMEGA_EBOOKS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALFAOMEGA_EBOOKS_VERSION', '1.0.0' );
define( 'ALFAOMEGA_EBOOKS_NAME', 'alfaomega-ebooks' );
define( 'ALFAOMEGA_EBOOKS_POST_TYPE', 'alfaomega-ebook' );
define( 'ALFAOMEGA_EBOOKS_ACCESS_POST_TYPE', 'alfaomega-access' );
define( 'ALFAOMEGA_EBOOKS_SAMPLE_POST_TYPE', 'alfaomega-sample' );
define( 'ALFAOMEGA_SECURITY_PATH', plugin_dir_path( __FILE__ ) . 'security' );
define( 'ALFAOMEGA_COVER_PATH', 'https://alfaomega-assets.nyc3.cdn.digitaloceanspaces.com/');

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

/**
 * Check for the existence of WooCommerce and any other requirements
 */
function alfaomega_ebooks_check_requirements() {
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        return true;
    } else {
        add_action( 'admin_notices', 'alfaomega_ebooks_missing_wc_notice' );
        return false;
    }
}

/**
 * Display a message advising WooCommerce is required
 */
function alfaomega_ebooks_missing_wc_notice() {
    $class = 'notice notice-error';
    $message = esc_html__( 'AlfaomegaEbooks requires WooCommerce to be installed and active', 'alfaomega-ebooks' );

    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-alfaomega-ebooks-activator.php
 */
function activate_alfaomega_ebooks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-alfaomega-ebooks-activator.php';
	Alfaomega_Ebooks_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-alfaomega-ebooks-deactivator.php
 */
function deactivate_alfaomega_ebooks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-alfaomega-ebooks-deactivator.php';
	Alfaomega_Ebooks_Deactivator::deactivate();
}

add_action( 'plugins_loaded', 'alfaomega_ebooks_check_requirements' );

register_activation_hook( __FILE__, 'activate_alfaomega_ebooks' );
register_deactivation_hook( __FILE__, 'deactivate_alfaomega_ebooks' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-alfaomega-ebooks.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_alfaomega_ebooks() {
    if (alfaomega_ebooks_check_requirements()) {
        $plugin = new Alfaomega_Ebooks();
        $plugin->run();
    }
}

function order_contains_downloadable_product($order) {
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if ($product && $product->is_downloadable()) {
            return true;
        }
    }
    return false;
}

run_alfaomega_ebooks();
