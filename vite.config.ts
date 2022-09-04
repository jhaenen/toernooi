import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import { resolve } from 'path'
import { VitePWA } from 'vite-plugin-pwa';

function resolveDir(relativePath: string) {
  return resolve(__dirname, relativePath);
}

// https://vitejs.dev/config/
export default defineConfig({
    build: {
      rollupOptions: {
        input: {
          main: resolveDir('index.html'),
          'screen.html': resolveDir('screen.html'),
        },
      }
    },
    plugins: [
      svelte(),
      VitePWA({
        registerType: 'autoUpdate',
        devOptions: {
          enabled: false,
        },
        workbox: {
          navigateFallbackDenylist: [/^\/admin/, /^\/api/, /^\/screen.html/],
        }
      }),
    ],
    resolve: {
      alias: {
        '@': resolveDir('./src'),
      },
    },
    server: {
      port: 80,
      host: true,
    }
})
