import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// The storefront and admin still load their own pre-built files from public/
// (front/, app-assets/, js/, css/). Vite only builds resources/ for new code.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
