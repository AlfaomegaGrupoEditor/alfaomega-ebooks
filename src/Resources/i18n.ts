import { createI18n } from 'vue-i18n';

// Define the translations
const messages = {
    en: {
        // A
        access_type: 'Access Type',
        access_at: 'Access at',
        active: 'Active',
        added_at: 'Added at',
        added_by: 'Added by:',
        add_ebooks: 'Add eBooks',
        add_sample_text: 'If you received by mail with a sample code of the ebook, you can type it in here to add the book to your digital library.',
        all_books: 'All books',
        all_ebooks: 'All eBooks',
        apply_btn: 'Apply',
        attention: 'Attention!',
        // B
        back: 'Back',
        book_details: 'View Book Details',
        books_found: "Books found",
        // C
        cancelled: 'Cancelled',
        created: 'Created',
        created_at: 'Created at',
        code_applied_successfully: 'Code applied successfully.',
        // D
        dashboard: 'Dashboard',
        digital_library: 'Digital Library',
        download: 'Download',
        download_tooltip: 'Download the adobe token to import in your ebook reader.',
        dont_show_again: 'Don\'t show this again',
        // E
        expired: 'Expired',
        error_fetching_data: 'An error occurred while fetching the data.',
        error_no_data: 'No data found!',
        // I
        important_info: 'Important Information!',
        import: 'Import',
        import_ebooks: 'Import eBooks',
        import_ebooks_description: 'imported/available',
        // F
        filter_by: 'Filter by',
        // L
        link: 'Link',
        link_products: 'Link Products',
        link_products_description: 'Unlinked/Catalog',
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
        // P
        purchase: 'Purchase',
        purchased: 'Purchased',
        purchase_note: 'Purchased books are added automatically.',
        previous: 'Previous',
        per_page: 'Per Page',
        paste_code_here: 'Paste the code here',
        // R
        read_online: 'Read Online',
        reset_filters: 'Reset Filters',
        read_tooltip: 'Load the e-book into your browser to read it online.',
        // S
        sample: 'Sample',
        samples: 'Samples',
        search: 'Type to search amount your books...',
        setup: 'Setup',
        setup_prices: 'Setup Prices',
        setup_prices_description: 'Linked eBooks',
        status: 'Status',
        success: 'Success',
        something_went_wrong: 'Something went wrong!',
        // T
        title: 'Title',
        // U
        update: 'Update',
        update_ebooks: 'Update eBooks',
        update_ebooks_description: 'Imported eBooks',
        // V
        valid_until: 'Valid Until',
        // W
        welcome: 'Welcome',
    },
    es: {
        // A
        access_type: 'Tipo de Acceso',
        active: 'Activo',
        access_at: 'Acceso el',
        added_at: 'Agregado el',
        added_by: 'Agregado por:',
        add_ebooks: 'Agregar muestras',
        add_sample_text: 'Si recibiste un código de muestra, puedes ingresarlo aquí para agregar libros.',
        all: 'Todos los libros',
        all_ebooks: 'Catálogo de eBooks',
        apply_btn: 'Aplicar',
        attention: 'Atención!',
        // B
        back: 'Regresar',
        book_details: 'Ver Detalles del Libro',
        bienvenido: 'Bienvenido',
        books_found: "Resultados",
        // C
        cancelled: 'Cancelado',
        created: 'Creado',
        created_at: 'Creado el',
        code_applied_successfully: 'Código aplicado correctamente.',
        // D
        dashboard: 'Panel de Control',
        digital_library: 'Biblioteca Digital',
        download: 'Descargar',
        download_tooltip: 'Descarga el token de Adobe (.acsm) para cargar el PDF en tu lector.',
        dont_show_again: 'No mostrar nuevamente',
        // E
        expired: 'Expirado',
        error_fetching_data: 'Ocurrió un error al obtener los datos.',
        error_no_data: 'No se encontraron datos.',
        // F
        filter_by: 'Filtrar por',
        failed: 'El proceso ha fallado!',
        // I
        important_info: 'Información Importante!',
        import: 'Importar',
        import_ebooks: 'Importar eBooks',
        import_ebooks_description: 'Importados/Disponibles',
        // L
        link: 'Vincular',
        link_products: 'Vincular Productos',
        link_products_description: 'Desvinculados/Catálogo',
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
        // P
        purchase: 'Compra',
        purchase_note: 'Los libros comprados son adicionados automáticamente.',
        purchased: 'Mis Compras',
        previous: 'Anterior',
        per_page: 'Por Página',
        paste_code_here: 'Copia el código aquí',
        // R
        read_online: 'Leer en línea',
        reset_filters: 'Limpiar Filtros',
        read_tooltip: 'Carga el libro digital en tu navegador para leerlo en línea.',
        // S
        sample: 'Muestra',
        samples: 'Muestras de cortesía',
        search: 'Escribir para buscar entre sus libros...',
        setup: 'Configurar',
        setup_prices: 'Configurar Precios',
        setup_prices_description: 'eBooks Vinculados',
        status: 'Estado',
        success: 'Acción exitosa',
        something_went_wrong: 'Algo ha salido mal!',
        // T
        title: 'Título',
        // U
        update: 'Actualizar',
        update_ebooks: 'Actualizar eBooks',
        update_ebooks_description: 'eBooks importados',
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
