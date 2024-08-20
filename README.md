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

    > **Importante!** 
    > 1. Una vez que se salve la configuración por primera vez verifique que el valor del id del attributo formato es un número mayor que cero y que se creó el atributo en la configuración de productos de WooCommerce.
    > 2. Compruebe en `Product` > `Attributos` que los attributos `Formato` y `eBook` fueron creados y el `ID` coincide con la que se muestra en la configuración. 

6. Asegúrese de que su sitio haya configurado enlaces permanentes en el nombre de la publicación. Vaya a `Configuración` > `Enlaces permanentes` y seleccione `Nombre de la entrada`.

## Cómo utilizar

### Administrar productos y eBooks
Para editar los eBooks de un producto contamos con las siguientes opciones en el adminsitraor de WordPress:

#### Editar producto
- **Agregar el ISBN del eBook** al producto al producto simple en General > eBook ISBN (opcional). Este campo es usado como referencia para conocer que eBook corresponde a un producto.
- Usar el attribute eBook para **habilitar o deshabilitar las opciones de compra de eBooks**. Si para el atributo eBook se selecciona `Sí`, se mostrarán las opciones de compra de eBooks en la página del producto. Si se selecciona `No`, se desactivan las opciones de compra de eBooks (Digital y Digital + Impreso) en la página del producto. 
- **Editar las opciones de compra** de producto vinculado a un eBook, tales como Precio, Formato, etc. Por defecto el precio de los opciones de compra se calcula en base al precio del libro impreso y el porcentaje configurado en la configuración del plugin, pero es posible modificar el precio de cada opción de compra directamente en el producto.

#### Acciones rápidas y por lotes en la lista de productos
- **Enlazar un producto** con el correspondiente eBook. Utilizando el ISBN del libro impreso, el plugin buscará el eBook correspondiente en la Plataforma Alfaomega eBooks, descarga la información necesaria, crea el registro del eBook y lo vinculará al producto. Una vez que el producto este vinculado a un eBook el producto se convierte en un producto variable con las opciones de compra: Impreso, Digital y el combo Impreso + Digital.
- **Desvincular un producto** del eBook. Con esta opción se elimina la vinculación entre el producto y el eBook, el producto se convierte en un producto simple y se eliminan las opciones de compra de eBooks.

#### Acciones rápidas y por lotes en la lista de eBook
- **Actualizar los metadatos** del eBook y el enlace con el producto correspondiente. Comprueba que la vinculación entre el eBook y el producto es correcta y actualiza la información del eBook.
- **Mostrar el producto** vinculado al eBook. Muestra el producto vinculado al eBook en la lista de productos.

#### Pocesamiento en cola. 
- **Agregar nuevos eBooks**. A partir del último libro importado descarga información sobre los libros nuevos disponibles en la plataforma Alfaomega eBooks, los importa a la tienda WooCommerce como eBooks y intenta vincularlos a productos existentes usando el ISBN del libro impreso. Para acceder a esta opción puede hacerlo desde el menú `Alfaomega eBooks > Importar nuevos Libros` o desde la página principal del complemento.
  > **Importante!**    
  > 1. La importación de eBooks se realiza a través de una cola en segundo plano, por lo que es posible que tarde varios minutos en procesarse todos las tareas de importación, para chequear el estado del procesamiento acceda a `WooCommerce > Estado > Scheduled Actions`, y busque por `alfaomega_ebooks_queue_import` y el filtro del estado que desea revisar, por ejemplo `Pending` para ver los procesos de importatión todavía en cola.
  > 2. Ajuste en la configuración del complemento `AO eBooks > Configuraciones > configuración General > Import Limit`, el límite de nuevos eBooks a agregar a la cola de tareas de importación, el valor por defecto es 1000.
  > 3. En la página principal del complemento encontará la opción `Importar nuevos eBooks`, que le permitirá agregar nuevos libros a la cola de tareas de importación, asi como obtener un resumen historico del estado del procesamiendo en segundo plano de esta cola de tareas.

- **Actualizar eBooks importados**. Actualiza la información de todos los eBooks importados, si hay algún cambio en el ISBN impreso actualiza también el vínculo al correspondiente producto. Para acceder a esta opción puede hacerlo desde el menú `Alfaomega eBooks > Actualizar eBooks` o desde la página principal del complemento.
  > **Importante!**
  > 1. La actualización de los eBooks se realiza a través de una cola en segundo plano, por lo que es posible que tarde varios minutos en procesarse todos las tareas de actualización, para chequear el estado del procesamiento acceda a `WooCommerce > Estado > Scheduled Actions`, y busque por `alfaomega_ebooks_queue_refresh` y el filtro del estado que desea revisar, por ejemplo `Pending` para ver los procesos de importatión todavía en cola.
  > 2. En la página principal del complemento encontará la opción `Actualizar eBooks`, que le permitirá actualizar la información de los eBooks ya importados, asi como obtener un resumen historico del estado del procesamiendo en segundo plano de esta cola de tareas. 
- **Vincular productos**. Vincula todos los productos aún no vinculados con su correspondiente eBook en la lista de eBooks que ya están importados, si desea que algún libro en específico no tenga eBook, actualice el attributo `eBook` a `No`. Para acceder a esta opción puede hacerlo desde el menú `Alfaomega eBooks > Vincular Productos` o desde la página principal del complemento.
  > **Importante!**
  > 1. La vinculación de los productos se realiza a través de una cola en segundo plano, por lo que es posible que tarde varios minutos en procesarse todos las tareas de vinculación, para chequear el estado del procesamiento acceda a `WooCommerce > Estado > Scheduled Actions`, y busque por `alfaomega_ebooks_queue_link` y el filtro del estado que desea revisar, por ejemplo `Failed` para ver los procesos de importatión que han dado error.
  > 2. En la página principal del complemento encontará la opción `Vincular Products`, que le permitirá vincular los productos con sus correspondientes eBooks, asi como obtener un resumen historico del estado del procesamiendo en segundo plano de esta cola de tareas.

### Comprar eBooks, descarga y lectura en línea.
Cuando un cliente selecciona una opción de compra que incluye un libro digital (Digital o Digital + Impreso), el plugin añadirá un enlace de descarga al email de notificación de factura. El cliente podrá descargar el PDF del eBook desde el enlace proporcionado. Además, el enlace de descarga también se añadirá a la lista de Descargas del cliente en la página de su cuenta.

### Generación de accesos de muestras.
El plugin ofrece también la opción de que un administrador pueda generar accesos de muestra para los eBooks. El código de accesso es enviado por correo electrónico al cliente. Este código puede ser agregado en la biblioteca digital myEbooks del usuario para acceder a la lectura del libro durante el tiempo configurado para la muestra.
