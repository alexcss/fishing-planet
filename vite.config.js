import { defineConfig, loadEnv } from 'vite'
import flynt from './vite-plugin-flynt'
import FullReload from 'vite-plugin-full-reload'
import fs from 'fs'
import path from 'path'
import tailwindcss from '@tailwindcss/vite'

const wordpressHost = 'https://dev-starter.loc/'

const dest = './dist'
const entries = [
  './src/css/app.css',
  './src/css/admin/admin.css',
  './src/css/admin/dlc-importer.css',
  './src/js/app.js',
  './src/js/dlc-archive.tsx',
  './src/js/career.tsx',
  './src/js/admin.js',
  './src/js/admin/dlc-importer.js',
]

const watchFiles = ['*.php', '*.twig', 'page-templates/**/*', 'twigs/**/*', 'inc/**/*']

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const host = env.VITE_DEV_SERVER_HOST || wordpressHost
  const isSecure = host.indexOf('https://') === 0 && (env.VITE_DEV_SERVER_KEY || env.VITE_DEV_SERVER_CERT)

  return {
    base: './',
    resolve: {
      alias: {
        '@': __dirname,
        '~swiper': path.resolve(__dirname, 'node_modules/swiper'),
      },
    },
    plugins: [tailwindcss(), flynt({ dest, host }), FullReload(watchFiles)],
    server: {
      https: isSecure
        ? {
            key: fs.readFileSync(env.VITE_DEV_SERVER_KEY),
            cert: fs.readFileSync(env.VITE_DEV_SERVER_CERT),
          }
        : false,
      host: 'localhost', // preserve conflicts with IpV6
      cors: true, // Enable CORS
      headers: {
        'Access-Control-Allow-Origin': '*',
      },
    },
    build: {
      manifest: true, // generate manifest.json in outDir
      outDir: dest,
      // assetsInlineLimit: 0, // disable assets inlining
      rollupOptions: {
        input: entries, // overwrite default .html entry
      },
    },
  }
})
