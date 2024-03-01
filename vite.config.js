import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import eslint from 'vite-plugin-eslint'

export default defineConfig(({ command, mode }) => {
    const env = loadEnv(mode, process.cwd(), '')

    return {
        plugins: [
            laravel({
                input: [
                    '/resources/js/app.js',
                    '/resources/scss/style.scss',
                ],
                refresh: true,
            }),
            eslint({
                include: ['**/*.js', '**/*.vue'],
            }),
        ],
        resolve: {
            alias: {
                '@app': '/resources/js',
                '@scss': '/resources/scss',
            },
        },
        server: {
            host: true,
            hmr: {
                // eslint-disable-next-line no-useless-escape
                host: env.VITE_EXTERNAL_IP || env.APP_URL.replace(/^https?\:\/\//i, ''),
            },
        },
    }
})
