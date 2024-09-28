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
        book_details: 'View Book Details',
        books_found: "Books found",
        // C
        cancelled: 'Cancelled',
        created: 'Created',
        created_at: 'Created at',
        code_applied_successfully: 'Code applied successfully.',
        // D
        digital_library: 'Digital Library',
        download: 'Download',
        download_tooltip: 'Download the adobe token to import in your ebook reader.',
        // E
        expired: 'Expired',
        error_fetching_data: 'An error occurred while fetching the data.',
        error_no_data: 'No data found!',
        // F
        filter_by: 'Filter by',
        // M
        message: 'Hello, World!',
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
        status: 'Status',
        success: 'Success',
        something_went_wrong: 'Something went wrong!',
        // T
        title: 'Title',
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
        all_ebooks: 'Catálogo de Libros',
        apply_btn: 'Aplicar',
        attention: 'Atención!',
        // B
        book_details: 'Ver Detalles del Libro',
        bienvenido: 'Bienvenido',
        books_found: "Resultados",
        // C
        cancelled: 'Cancelado',
        created: 'Creado',
        created_at: 'Creado el',
        code_applied_successfully: 'Código aplicado correctamente.',
        // D
        digital_library: 'Biblioteca Digital',
        download: 'Descargar',
        download_tooltip: 'Descarga el token de Adobe (.acsm) para cargar el PDF en tu lector.',
        // E
        expired: 'Expirado',
        error_fetching_data: 'Ocurrió un error al obtener los datos.',
        error_no_data: 'No se encontraron datos.',
        // F
        filter_by: 'Filtrar por',
        failed: 'El proceso ha fallado!',
        // M
        message: 'Hola, mundo!',
        muestra: 'Muestra',
        muestras: 'Muestras',
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
        status: 'Estado',
        success: 'Acción exitosa',
        something_went_wrong: 'Algo ha salido mal!',
        // T
        title: 'Título',
        // V
        valid_until: 'Válido hasta',
    }
};

// Create the i18n instance
const i18n = createI18n({
    locale: 'es',           // set locale
    fallbackLocale: 'en',   // set fallback locale
    messages,               // set locale messages
});

export default i18n;
