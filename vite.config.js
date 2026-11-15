import { defaultAllowedOrigins, defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const host_port = Number(env.VITE_PORT) || 5173;

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/public.js', 'resources/js/cookie-consent.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
        server: {
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            origin: `http://localhost:${host_port}`,
            cors: {
                origin: [defaultAllowedOrigins, ...(env.APP_URL ? [env.APP_URL] : [])],
            },
            hmr: {
                host: 'localhost',
                clientPort: host_port,
            },
            watch: {
                ignored: ['**/storage/framework/views/**', '**/.claude/**'],
            },
        },
    };
});
