import { createI18n } from 'vue-i18n';

// Define the translations
const messages = {
    en: {
        welcome: 'Welcome',
        message: 'Hello, World!'
    },
    es: {
        welcome: 'Bienvenido',
        message: 'Hola, mundo!'
    }
};

// Create the i18n instance
const i18n = createI18n({
    locale: 'es', // set locale
    fallbackLocale: 'en', // set fallback locale
    messages, // set locale messages
});

export default i18n;
