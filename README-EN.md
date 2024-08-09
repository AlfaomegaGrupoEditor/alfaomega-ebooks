# Alfaomega eBooks
- **Contributors**: livan2r@alfaomega.com.mx
- **Tags**: Alfaomega, Libros digitales, eBooks, WooCommerce
- Requires at least **WordPress: 5.8** and **WooCommerce 5.5**
- Tested up to: **6.2.6**
- **License**: GPLv2 or later
- **License URI**: http://www.gnu.org/licenses/gpl-2.0.html

**Alfaomega eBooks** Manager to import, update, and synchronize **digital eBooks** with **WooCommerce products**.

##  Description

Alfaomega eBooks Manager provides a way to synchronize WooCommerce products to the **Alfaomega eBooks Platform**.

The plugin offers the following features to the WordPress admin:
- **Import** eBooks from Alfaomega eBooks Platform to WooCommerce products. Given a WooCommerce product, the plugin will search for the corresponding eBook in the Alfaomega eBooks Platform, **import** it, and **convert the single product into a variable product**, with the buying options: **Printed**, **Digital**, and the combo **Printed + Digital**.
- Plugin **configuration** to manage and connect the WP site with the Alfaomega eBooks Platform.
  - **General configuration**: `Username`, `password`, `notifications`, and `import limits`
  - **eBooks Platform**: App setup to connect with Alfaomega eBooks Platform
  - **API Settings**: `Token Url`, `API server`, `Client ID`, and `Client secret` provided by **Alfaomega Grupo Editor**.
  - **Product Options**: Setup `Format` attribute and option prices. The printed price is the base price, and the digital and combo prices are calculated based on the printed price and the percentage configured.
  - **Queue Import and Refresh eBooks products**: It is possible to import and synchronize Alfaomega eBooks one by one from the Products list, but also the plugin provides a **batch import** to automatically grab all new eBooks available for your account in the Alfaomega Platform, **link to existed products** and **create the respective product variants** with the configured price. Furthermore, the plugin provides a way to **refresh the eBooks** products to update the eBook information.

For the customer, **Alfaomega eBooks** adds the following features to the WooCommerce store:
- **Add the buying options**: `Printed`, `Digital`, or the combo `Printed + Digital` to the product page.
- If the customer buys the combo `Printed + Digital`, or `digital` the plugin will **add to the invoice** notification email a **link to download the eBook PDF**. Complementary to the offline digital version the customer will be able to **read online** the acquired eBook on the **Alfaomega eBook Platform**.
- The **download link** will be also **added** to the customer's Download list on his account page.
- All the eBooks the customer bought will be **accessible through the myEbooks digital library** for online read.

##  Requirements
- PHP 7.4 or higher
- WordPress 5.8 or higher
- WooCommerce 5.5 or higher
- WooCommerce REST API v3 or higher
- WooCommerce API Key
- Alfaomega eBooks account

##  Installation

1. Upload `alfaomega-ebooks` to the `/wp-content/plugins/` directory. The recommended way is to use the WordPress plugin [WP Pusher](https://wppusher.com/) and set up the GitHub repository [Alfaomega eBooks](https://github.com/AlfaomegaGrupoEditor/alfaomega-ebooks) on branch `Main` in order to receive the code updates automatically.
2. Setup in the WordPress `wp-config.php` file the WooCommerce API credentials. If you don't have the `WooCommerce API keys`, you can generate them in the `WooCommerce settings`. You can find more information in the [WooCommerce REST API documentation](https://woocommerce.github.io/woocommerce-rest-api-docs/#authentication). Make sure to give **read and write** permissions. Copy the generated keys to the WordPress `wp-config.php` file in the root of the website.
```PHP
/** WooCommerce API keys */
define( 'WOOCOMMERCE_API_KEY', 'ck_*************************');
define( 'WOOCOMMERCE_API_SECRET', 'cs_*************************');
define( 'WCPAY_DEV_MODE', false );
```
3. Activate the plugin through the `Plugins` menu in WordPress.
4. Go to the plugin settings page and configure the plugin options. The `username`, `password`, `panel client`, `client id` and `client secret` are provided by `Alfaomega Grupo Editor`. Please contact them to get the credentials. By default, some values will be loaded from the environment variables, but you can override them in the plugin settings. **Important:** Once the configuration is saved for the first time, verify that the value of the format attribute id is a number greater than zero and that the attribute was created in the WooCommerce product settings.
5. Make sure your site has configured Permalinks to Post name. `Go to Settings` > `Permalinks` and select `Post name`.

## How to use
