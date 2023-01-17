import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import { resolve } from 'path'
import { VitePWA } from 'vite-plugin-pwa';
import svelteSVG from "vite-plugin-svelte-svg";

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
      svelteSVG({
        svgoConfig: {}, // See https://github.com/svg/svgo#configuration
        requireSuffix: true, // Set false to accept '.svg' without the '?component'
      }),
      VitePWA({
        registerType: 'autoUpdate',
        devOptions: {
          enabled: false,
        },
        workbox: {
          navigateFallbackDenylist: [/^\/admin/, /^\/api/, /^\/screen.html/],
          globPatterns: ["**/*.{js,css,html,svg}"],
        }
      }),
    ],
    resolve: {
      alias: {
        '@': resolveDir('./src'),
      },
    },
})
