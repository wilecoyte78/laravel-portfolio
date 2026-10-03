import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0',
        hmr: { host: 'localhost' },
        watch: { usePolling: true },
    },
    ssr: {
        // Bundle all dependencies into bootstrap/ssr/ssr.js so the production
        // SSR process only needs Node + that one file (no node_modules).
        noExternal: true,
    },
});
