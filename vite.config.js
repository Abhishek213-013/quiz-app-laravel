import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 3000,
        cors: true,
        hmr: {
            host: '127.0.0.1',
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
        tailwindcss(),
    ],
    // Add CSS configuration
    css: {
        devSourcemap: false, // Disable sourcemaps in dev to reduce FOUC
        preprocessorOptions: {
            css: {
                charset: false,
            },
        },
    },
    build: {
        // Optimize CSS loading
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                // Ensure CSS is loaded properly
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.endsWith('.css')) {
                        return 'assets/css/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
    },
});