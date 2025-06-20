import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// Obtén la URL de producción del archivo .env
const isProduction = process.env.NODE_ENV === 'production';
const baseURL = isProduction ? process.env.VITE_APP_URL : '/';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    base: process.env.NODE_ENV === 'production' ? process.env.VITE_APP_URL + '/' : '/',
});
