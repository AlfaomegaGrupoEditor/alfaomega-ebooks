import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite'
import {BootstrapVueNextResolver} from 'bootstrap-vue-next'
import vueJsx from '@vitejs/plugin-vue-jsx';
import path from 'path';

// Vite configuration
export default defineConfig({
    plugins: [
        vue(),
        vueJsx(),
        Components({
            resolvers: [BootstrapVueNextResolver()],
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'Resources'),
        }
    },
    build: {
        outDir: path.resolve(__dirname, 'Resources/dist'),
        publicDir: false,
        rollupOptions: {
            input: {
                main: path.resolve(__dirname, 'Resources/main.ts'),
            },
            output: {
                entryFileNames: 'js/bundle.js',
                assetFileNames: 'css/bundle.css',
            }
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                // Import Bootstrap SCSS variables and other settings
                additionalData: `@import "bootstrap/scss/bootstrap.scss"; @import "bootstrap-vue/src/index.scss";`
            }
        }
    },
    server: {
        host: 'localhost',
        port: 3000,
        open: false,
    }
});
