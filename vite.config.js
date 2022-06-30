import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css'
        ])
    ],
    server: {
        host: '0.0.0.0'
    }
});
