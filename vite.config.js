import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        manifest: 'manifest.json', // This puts it directly in public/build/
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            }
        }
    },
});
