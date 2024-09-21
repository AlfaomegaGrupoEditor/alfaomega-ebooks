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
        // B
        book_details: 'View Book Details',
        // C
        cancelled: 'Cancelled',
        created: 'Created',
        created_at: 'Created at',
        // D
        digital_library: 'Digital Library',
        download: 'Download',
        // E
        expired: 'Expired',
        // F
        filter_by: 'Filter by',
        // M
        message: 'Hello, World!',
        // N
        note: 'Note',
        no_books_found: 'No books found.',
        no_books_found_description: 'Please change the filters or search query. You can also buy more books to enlarge your library.',
        // O
        order_by: 'Order by',
        // P
        purchase: 'Purchase',
        purchased: 'Purchased',
        purchase_note: 'Purchased books are added automatically.',
        // R
        read_online: 'Read Online',
        // S
        sample: 'Sample',
        samples: 'Samples',
        search: 'Type to search...',
        status: 'Status',
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
        all_ebooks: 'Todos los eBooks',
        apply_btn: 'Aplicar',
        // B
        book_details: 'Ver Detalles del Libro',
        bienvenido: 'Bienvenido',
        // C
        cancelled: 'Cancelado',
        created: 'Creado',
        created_at: 'Creado el',
        // D
        digital_library: 'Biblioteca Digital',
        download: 'Descargar',
        // E
        expired: 'Expirado',
        // F
        filter_by: 'Filtrar por',
        // M
        message: 'Hola, mundo!',
        muestra: 'Muestra',
        muestras: 'Muestras',
        // N
        note: 'Nota',
        no_books_found: 'No se encontraron libros.',
        no_books_found_description: 'Por favor, cambia los filtros o el criterio de búsqueda para encontrar resultados. También puedes comprar más libros para ampliar tu biblioteca.',
        // O
        order_by: 'Ordenar por',
        // P
        purchase: 'Compra',
        purchase_note: 'Los libros comprados son adicionados automáticamente.',
        purchased: 'Compras',
        // R
        read_online: 'Leer en línea',
        // S
        sample: 'Muestra',
        search: 'Escribir para buscar...',
        status: 'Estado',
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
