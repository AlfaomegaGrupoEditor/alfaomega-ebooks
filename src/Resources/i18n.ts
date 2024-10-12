import { createI18n } from 'vue-i18n';

// Define the translations
const messages = {
    en: {
        // A
        about: 'About at',
        about_description: 'Information about Alfaomega eBooks plugin.',
        access_type: 'Access Type',
        access_at: 'Access at',
        active: 'Active',
        added_at: 'Added at',
        added_by: 'Added by:',
        add_ebooks: 'Add eBooks',
        add_sample_text: 'If you received by mail with a sample code of the ebook, you can type it in here to add the book to your digital library.',
        all_books: 'All books',
        all_ebooks: 'All eBooks',
        all_ebooks_description: 'All available eBooks',
        apply_btn: 'Apply',
        attention: 'Attention!',
        // B
        back: 'Back',
        book_details: 'View Book Details',
        books_found: "Books found",
        clear_cache: 'Clear Cache',
        // C
        cancelled: 'Cancelled',
        cancel: 'Cancel',
        created: 'Created',
        created_at: 'Created at',
        code_applied_successfully: 'Code applied successfully.',
        config: 'Configuration',
        config_description: 'All configuration settings for Alfaomega eBooks.',
        completed: 'Completed',
        // D
        dashboard: 'Dashboard',
        digital_library: 'Digital Library',
        download: 'Download',
        download_tooltip: 'Download the adobe token to import in your ebook reader.',
        dont_show_again: 'Don\'t show this again',
        // E
        ebook_access: 'eBook Access',
        ebook_access_description: 'All configured access to the eBooks.',
        ebooks_manager: 'eBooks Manager',
        expired: 'Expired',
        error_fetching_data: 'An error occurred while fetching the data.',
        error_no_data: 'No data found!',
        ebooks_stats: 'eBooks Statistics',
        ebooks: 'eBooks',
        // F
        filter_by: 'Filter by',
        // H
        has_failed: 'Failed',
        // I
        important_info: 'Important Information!',
        import: 'Import',
        import_ebooks: 'Import eBooks',
        import_ebooks_description: 'imported/available',
        import_ebooks_notice: 'Import new eBooks from the Alfaomega Editors Panel and try to link the related product with each new imported digital book.',
        idle: 'Idle',
        // L
        link: 'Link',
        link_products: 'Link Products',
        link_products_description: 'Unlinked/Catalog',
        linked_products: 'Linked Products',
        linked_products_description: 'Products linked successfully to the eBook.',
        linked: 'Linked',
        link_products_notice: 'Link the products that are not related yet by searching the corresponding digital book in the list of already imported eBooks.',
        // M
        message: 'Hello, World!',
        migration_notice: 'If you have an account in out previous store %s, you can import all your books to access them from here. Please open your account in %s, copy the access code and paste it here in the Add <b>Sample box</b>',
        // N
        next: 'Next',
        note: 'Note',
        no_books_found: 'No books found.',
        no_books_found_description: 'Please change the filters or search query. You can also buy more books to enlarge your library.',
        // O
        order_by: 'Order by',
        ok: 'Ok',
        // P
        purchase: 'Purchase',
        purchased: 'Purchased',
        purchases: 'Purchases',
        purchase_note: 'Purchased books are added automatically.',
        previous: 'Previous',
        per_page: 'Per Page',
        paste_code_here: 'Paste the code here',
        products: 'Products',
        processing_queue_status: 'Processing queue status',
        processing: 'Processing',
        pending: 'Pending',
        // R
        read_online: 'Read Online',
        reset_filters: 'Reset Filters',
        read_tooltip: 'Load the e-book into your browser to read it online.',
        redeemed: 'Redeemed',
        // S
        sample: 'Sample',
        samples: 'Samples',
        sample_codes: 'Sample Codes',
        sample_codes_description: 'All sample codes added to the account.',
        search: 'Type to search amount your books...',
        setup: 'Setup',
        setup_prices: 'Setup Prices',
        setup_prices_description: 'Linked eBooks',
        status: 'Status',
        success: 'Success',
        something_went_wrong: 'Something went wrong!',
        setup_ebooks_price: 'Setup Prices',
        setup_ebooks_price_notice: 'Taking as a base the price of the printed book and an update factor, the price of the digital book and the printed and digital package is calculated according to the price settings and the new price of the printed book.',
        sent: 'Sent',
        // T
        title: 'Title',
        type: 'Type',
        total: 'Total',
        // U
        update: 'Update',
        update_ebooks: 'Update eBooks',
        update_ebooks_description: 'Imported eBooks',
        update_ebooks_notice: 'Update the information of the eBooks already downloaded from the Alfaomega Editors Panel and check if the link between the product and the eBook is correct.',
        // V
        valid_until: 'Valid Until',
        // W
        welcome: 'Welcome',
    },
    es: {
        // A
        about: 'Acerca de',
        about_description: 'Información sobre el plugin Alfaomega eBooks.',
        access_type: 'Tipo de Acceso',
        active: 'Activo',
        access_at: 'Acceso el',
        added_at: 'Agregado el',
        added_by: 'Agregado por:',
        add_ebooks: 'Agregar muestras',
        add_sample_text: 'Si recibiste un código de muestra, puedes ingresarlo aquí para agregar libros.',
        all: 'Todos los libros',
        all_ebooks: 'Catálogo de eBooks',
        all_ebooks_description: 'Todos los eBooks cuya información se ha descargado.',
        apply_btn: 'Aplicar',
        attention: 'Atención!',
        // B
        back: 'Regresar',
        book_details: 'Ver Detalles del Libro',
        bienvenido: 'Bienvenido',
        books_found: "Resultados",
        // C
        cancelled: 'Cancelado',
        cancel: 'Cancelar',
        created: 'Creado',
        created_at: 'Creado el',
        code_applied_successfully: 'Código aplicado correctamente.',
        config: 'Ajustes',
        config_description: 'Todos los ajustes de configuración de Alfaomega eBooks.',
        clear_cache: 'Limpiar Cache',
        completed: 'Completados',
        // D
        dashboard: 'Panel de Control',
        digital_library: 'Biblioteca Digital',
        download: 'Descargar',
        download_tooltip: 'Descarga el token de Adobe (.acsm) para cargar el PDF en tu lector.',
        dont_show_again: 'No mostrar nuevamente',
        // E
        ebook_access: 'Accesos a eBooks',
        ebook_access_description: 'Listado accessos a eBooks, ya sea compra o muestra.',
        ebooks_manager: 'Administrador de recursos',
        expired: 'Expirado',
        error_fetching_data: 'Ocurrió un error al obtener los datos.',
        error_no_data: 'No se encontraron datos.',
        ebooks_stats: 'Estadísticas',
        ebooks: 'eBooks',
        // F
        filter_by: 'Filtrar por',
        failed: 'El proceso ha fallado!',
        // H
        has_failed: 'Fallidos',
        // I
        important_info: 'Información Importante!',
        import: 'Importar',
        import_ebooks: 'Importar eBooks',
        import_ebooks_description: 'Importados/Disponibles',
        import_ebooks_notice: 'Importa nuevos libros electrónicos desde el Panel de editores de Alfaomega e intenta vincular el producto relacionado con cada nuevo libro digital importado.',
        idle: 'Inactivo',
        // L
        link: 'Vincular',
        link_products: 'Vincular Productos',
        link_products_description: 'Desvinculados/Catálogo',
        linked_products: 'Productos Vinculados',
        linked_products_description: 'WooCommerce productos con el eBook configurado y activo.',
        linked: 'Vinculados',
        link_products_notice: 'Vincula los productos que aún no están relacionados buscando el libro digital correspondiente en la lista de eBooks ya importados.',
        // M
        message: 'Hola, mundo!',
        muestra: 'Muestra',
        muestras: 'Muestras',
        migration_notice: 'Si tienes una cuenta en nuestra tienda anterior <b>:website</b>, puedes importar todos tus libros para acceder a ellos desde aquí. Por favor, abre la sección <b>Mis eBooks</b> en el sitio anterior, copia el <b>código de acceso</b> y pégalo en el cuadro <b>Agregar Muestras.</b>',
        // N
        next: 'Siguiente',
        note: 'Nota',
        no_books_found: 'No se encontraron libros.',
        no_books_found_description: 'Por favor, cambia los filtros o el criterio de búsqueda para encontrar resultados. También puedes comprar más libros para ampliar tu biblioteca.',
        // O
        order_by: 'Ordenar por',
        ok: 'Aceptar',
        // P
        purchase: 'Compra',
        purchase_note: 'Los libros comprados son adicionados automáticamente.',
        purchased: 'Mis Compras',
        purchases: 'Compras',
        previous: 'Anterior',
        per_page: 'Por Página',
        paste_code_here: 'Copia el código aquí',
        products: 'Productos',
        processing_queue_status: 'Cola de procesamiento',
        processing: 'Procesando',
        pending: 'Pendientes',
        // R
        read_online: 'Leer en línea',
        reset_filters: 'Limpiar Filtros',
        read_tooltip: 'Carga el libro digital en tu navegador para leerlo en línea.',
        redeemed: 'Canjeado',
        // S
        sample: 'Muestra',
        samples: 'Muestras de cortesía',
        sample_codes: 'Cógigos de Muestra',
        sample_codes_description: 'Listado de códigos de muestra generados.',
        search: 'Escribir para buscar entre sus libros...',
        setup: 'Configurar',
        setup_prices: 'Configurar Precios',
        setup_prices_description: 'eBooks Vinculados',
        status: 'Estado',
        success: 'Acción exitosa',
        something_went_wrong: 'Algo ha salido mal!',
        sent: 'Enviado',
        setup_ebooks_price: 'Configurar Precios',
        setup_ebooks_price_notice: 'Tomando como base el precio del libro impreso y un factor de actualización, se calcula el precio del libro digital y del paquete impreso y digital según la configuración de precios y el nuevo precio del impreso.',
        // T
        title: 'Título',
        type: 'Tipo',
        total: 'Total',
        // U
        update: 'Actualizar',
        update_ebooks: 'Actualizar eBooks',
        update_ebooks_description: 'eBooks importados',
        update_ebooks_notice: 'Actualiza la información de los eBooks ya descargados desde el Panel de Editores Alfaomega y comprueba que si el vínculo entre el producto y el eBook es correcto.',
        // V
        valid_until: 'Válido hasta',
        // W
        welcome: 'Bienvenido',
    }
};

// Create the i18n instance
const i18n = createI18n({
    locale: 'es',           // set locale
    fallbackLocale: 'en',   // set fallback locale
    messages,               // set locale messages
});

export default i18n;
