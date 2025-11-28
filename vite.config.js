import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/css/site.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        cors: {
            origin: [
                'http://adjerusalemilha.local:8000',
                'http://agapehouseisa.local:8000'
            ],

            credentials: true
        },
        hmr: {
            host: '127.0.0.1',
        }
    },
});
