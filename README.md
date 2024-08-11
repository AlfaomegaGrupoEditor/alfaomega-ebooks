# Alfaomega eBooks
- **Contributors**: livan2r@alfaomega.com.mx 
- **Tags**: Alfaomega, Libros digitales, eBooks, WooCommerce 
- Requires at least **WordPress: 5.8** and **WooCommerce 5.5** 
- Tested up to: **6.2.6** 
- **License**: GPLv2 or later 
- **License URI**: http://www.gnu.org/licenses/gpl-2.0.html 

**Administrador de eBooks de Alfaomega** para importar, actualizar y sincronizar **eBooks digitales** con **productos WooCommerce**.

##  Descripción

Alfaomega eBooks Manager proporciona una forma de sincronizar los productos de WooCommerce con la **Plataforma de libros electrónicos Alfaomega**.  

El plugin ofrece las siguientes características al administrador de WordPress:
- **Importar** eBooks desde la Plataforma Alfaomega eBooks a productos WooCommerce. Dado un producto WooCommerce, el plugin buscará el eBook correspondiente en la Plataforma Alfaomega eBooks, lo **importará** y **convertirá el producto simple en un producto variable**, con las opciones de compra: **Impreso**, **Digital** y el combo **Impreso + Digital**.
- **Configuración** del plugin para administrar y conectar el sitio WP con la Plataforma Alfaomega eBooks.
- **Configuración general**: `Usuario`, `contraseña`, `notificaciones` y `límites de importación`.
- **Plataforma eBooks**: Configuración de la aplicación para conectar con la Plataforma Alfaomega eBooks
- **Configuración API**: `Token Url`, `API server`, `Client ID` y `Client secret` proporcionados por **Alfaomega Grupo Editor**.
- **Opciones del producto**: Configurar precio de cada opción y el atributo formato. El precio del libro impreso es el precio base, y los precios digitales y combo se calculan en base al precio impreso y el porcentaje configurado.
- **Importación y actualización de productos de libros electrónicos en cola**: es posible importar y sincronizar los libros electrónicos de Alfaomega uno por uno desde la lista de productos, pero el complemento también proporciona una **importación por lotes** para importar automáticamente todos los nuevos libros electrónicos disponibles para su cuenta en la plataforma Alfaomega, **vincularlos a productos existentes** y **crear las variantes de producto** respectivas con el precio configurado. Además, el complemento proporciona una forma de actualizar los productos de libros electrónicos ya importados para **actualizar la información de los libros electrónicos**.

Para el cliente, **Alfaomega eBooks** añade las siguientes características a la tienda WooCommerce:
- **Añade las opciones de compra**: `Impreso`, `Digital` o el combo `Impreso + Digital` a la página del producto.
- Si el cliente compra el combo `Impreso + Digital` o solo `digital`, el plugin añadirá al email de notificación de factura un **enlace para descargar el PDF** del eBook. Como complemento a la versión digital offline, el cliente podrá **leer online** el eBook adquirido en la **Plataforma de eBooks Alfaomega**.
- El **enlace de descarga** también se añadirá a la lista de **Descargas del cliente** en la página de su cuenta.
- Todos los eBooks que el cliente haya comprado estarán **accesibles a través de la biblioteca digital myEbooks** para su lectura online.

##  Requisitos
- PHP 7.4 or higher
- WordPress 5.8 or higher
- WooCommerce 5.5 or higher
- WooCommerce REST API v3 or higher
- WooCommerce API Key
- Cuenta en Alfaomega eBooks

##  Instalación

1. Copiar `alfaomega-ebooks` al directorio `/wp-content/plugins/`. La forma recomendada es usar el plugin de WordPress [WP Pusher](https://wppusher.com/) y configurar el repositorio de GitHub [Alfaomega eBooks](https://github.com/AlfaomegaGrupoEditor/alfaomega-ebooks) en la rama `Main` para recibir las actualizaciones del código automáticamente.
2. Configura en el archivo `wp-config.php` de WordPress las credenciales de la API de WooCommerce. Si no tienes las `claves de la API de WooCommerce`, puedes generarlas en la `configuración de WooCommerce`. Puedes encontrar más información en la [documentación de la API REST de WooCommerce](https://woocommerce.github.io/woocommerce-rest-api-docs/#authentication). 

    > Asegúrese de dar permisos de **lectura y escritura**. Copie las claves generadas en el archivo `wp-config.php` de WordPress en la raiz del sitio web.

    ```PHP
    /** WooCommerce API keys */
    define( 'WOOCOMMERCE_API_KEY', 'ck_*************************');
    define( 'WOOCOMMERCE_API_SECRET', 'cs_*************************');
    define( 'WCPAY_DEV_MODE', false );
    ```
3. Activa el complemento a través del menú `Complementos` en WordPress.
4. Go to the plugin settings page and configure the plugin options. The `username`, `password`, `panel client`, `client id`, and `client secret` are provided by `Alfaomega Grupo Editor`. Please contact them to get the credentials. De forma predeterminada, se cargarán algunos valores desde las variables de entorno, pero puedes sobreescribirlos en la configuración del complemento. 

    > **Importante:** 
    > - Una vez que se salve la configuración por primera vez verifique que el valor del id del attributo formato es un número mayor que cero y que se creó el atributo en la configuración de productos de WooCommerce.
    > - Compruebe en `Product` > `Attributos` que los attributos `Formato` y `eBook` fueron creados y el `ID` coincide con la que se muestra en la configuración. 

6. Asegúrese de que su sitio haya configurado enlaces permanentes en el nombre de la publicación. Vaya a `Configuración` > `Enlaces permanentes` y seleccione `Nombre de la entrada`.

## Cómo utilizar

### Administrar productos y eBooks

- Vincular productos de WooCommerce con eBooks de Alfaomega.
 - Agrgear el eBook del ISBN al producto al producto simple en General > eBook ISBN   
- Configurar eBook de un producto vinculado.

### Administrar eBooks
- Listar eBooks importados.
- Vincular eBooks con productos de WooCommerce.
- Actualizar eBooks importados.
- Importar nuevos eBooks de Alfaomega.

### Gestión de procesos por lotes
- Importar nuevos eBooks.
- Actualizar eBooks importados.
- Vincular eBooks con productos en WooCommerce.
