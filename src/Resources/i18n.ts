import { createI18n } from 'vue-i18n';

// Define the translations
const messages = {
    en: {
        welcome: 'Welcome',
        message: 'Hello, World!',

        add_ebooks: 'Add eBooks',
        add_sample_text: 'If you received by mail with a sample code of the ebook, you can type it in here to add the book to your digital library.',
        note: 'Note',
        purchase_note: 'Purchased books are added automatically.',
        apply_btn: 'Apply',
        download: 'Download',
        read_online: 'Read Online',
        access_type: 'Access Type',
        status: 'Status',
        added_at: 'Added at',
        valid_until: 'Valid Until',
        purchase: 'Purchase',
        sample: 'Sample',
        created: 'Created',
        active: 'Active',
        expired: 'Expired',
        cancelled: 'Cancelled',
        book_details: 'View Book Details',
        digital_library: 'Digital Library',
        all_ebooks: 'All eBooks',
        purchased: 'Purchased',
        samples: 'Samples',
    },
    es: {
        welcome: 'Bienvenido',
        message: 'Hola, mundo!',

        add_ebooks: 'Agregar muestras',
        add_sample_text: 'Si recibiste un código de muestra, puedes ingresarlo aquí para agregar libros.',
        apply_btn: 'Aplicar',
        download: 'Descargar',
        read_online: 'Leer en línea',
        access_type: 'Typo de Acceso',
        status: 'Estado',
        added_at: 'Agregado el',
        valid_until: 'Válido hasta',
        purchase: 'Compra',
        sample: 'Muestra',
        created: 'Creado',
        active: 'Activo',
        expired: 'Expirado',
        cancelled: 'Cancelado',
        book_details: 'Ver Detalles del Libro',
        digital_library: 'Biblioteca Digital',
        all_ebooks: 'Todos los eBooks',
        purchased: 'Compras',
        samples: 'Muestras',
        note: 'Nota',
        purchase_note: 'Los libros comprados son adicionados automáticamente.',
    }
};

// Create the i18n instance
const i18n = createI18n({
    locale: 'es',           // set locale
    fallbackLocale: 'en',   // set fallback locale
    messages,               // set locale messages
});

export default i18n;
