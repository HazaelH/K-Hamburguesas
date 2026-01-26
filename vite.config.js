import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                'resources/css/auth.css',
                'resources/css/kitchen.css',
                'resources/css/layout.css',
                'resources/js/layout.js', 
                'resources/js/app.js',
                'resources/js/profile.js',
                'resources/css/profile.css', 
                'resources/css/menu.css', 
                'resources/js/client/menu.js',
                'resources/js/client/cart.js',
                'resources/js/client/checkout.js',
                'resources/js/client/menu-interaction.js',
                'resources/js/employee/dashboard.js',
                'resources/js/employee/kitchen.js',
                'resources/js/employee/stock.js',
                'resources/js/employee/pos.js',
                'resources/css/pos.css',
                'resources/js/employee/caja.js',
                'resources/css/caja.css',
                'resources/js/employee/scanner.js',
                'resources/css/scanner.css',
                'resources/css/admin.css',
                'resources/js/admin.js', 
                'resources/js/admin/dashboard.js',
                'resources/js/admin/orders.js',
                'resources/js/admin/products.js',
                'resources/js/admin/image-preview.js',
                'resources/js/admin/users.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
