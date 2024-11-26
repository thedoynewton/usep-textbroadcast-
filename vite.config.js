
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            // input: [
            //     'resources/css/app.css',
            //     'resources/js/app.js',
            //     'resources/js/dashboardFilter.js',
            //     'resources/js/generateReport.js',
            // ],
            input: [
                'resources/css/app.css',
                ...fs
                    .readdirSync(path.resolve(__dirname, 'resources/js'))
                    .filter(file => file.endsWith('.js'))
                    .map(file => `resources/js/${file}`),
            ],
            refresh: true,
        }),
    ],
});
