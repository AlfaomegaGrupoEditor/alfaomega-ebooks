import { createI18n } from 'vue-i18n';

// Define the translations
const messages = {
    en: {
        welcome: 'Welcome',
        message: 'Hello, World!',

        add_ebooks: 'Add eBooks',
        add_sample_text: 'If you received by mail with a sample code of the ebook, you can type it in here to add the book to your digital library.',
        apply_btn: 'Apply',
        download: 'Download',
        read_online: 'Read Online',
    },
    es: {
        welcome: 'Bienvenido',
        message: 'Hola, mundo!',

        add_ebooks: 'Agregar eBooks',
        add_sample_text: 'Si recibiste por correo un código de muestra del ebook, puedes ingresarlo aquí para agregar el libro a tu biblioteca digital.',
        apply_btn: 'Aplicar',
        download: 'Descargar',
        read_online: 'Leer en línea',
    }
};

// Create the i18n instance
const i18n = createI18n({
    locale: 'es',           // set locale
    fallbackLocale: 'en',   // set fallback locale
    messages,               // set locale messages
});

export default i18n;
