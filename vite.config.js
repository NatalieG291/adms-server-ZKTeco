import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/js/devices.js",
                "resources/js/employees.js",
                "resources/js/users.js",
            ],
            refresh: true,
        }),
    ],
});
