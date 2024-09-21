import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite';
import { BootstrapVueNextResolver } from 'bootstrap-vue-next';
import vueJsx from '@vitejs/plugin-vue-jsx';
import path from 'path';

// Vite configuration
export default defineConfig(({mode}) => {
    const env = loadEnv(mode, process.cwd());

    return {
        plugins: [
            vue(),
            vueJsx(),
            Components({
                resolvers: [BootstrapVueNextResolver()]
            })
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'Resources')
            }
        },
        build: {
            outDir: path.resolve(__dirname, 'Resources/dist'),
            publicDir: path.resolve(__dirname, '../public'),
            rollupOptions: {
                input: {
                    main: path.resolve(__dirname, 'Resources/main.ts')
                },
                output: {
                    entryFileNames: 'js/bundle.js',
                    assetFileNames: 'css/bundle.css'
                },
                external: [
                    '/img/empty_state.png'
                ]
            }
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
            open: false
        },
        define: {
            'process.env': env
        }
    };
});
