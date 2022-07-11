import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import { resolve } from 'path'
import { viteStaticCopy } from 'vite-plugin-static-copy'

function resolveDir(relativePath: string) {
  return resolve(__dirname, relativePath);
}

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
      svelte(),
      viteStaticCopy({
        targets: [
          { src: './server/php/*.php', dest: 'server' },
          { src: './server/php/prod/env.ini', dest: 'server' },
          { src: './server/php/admin/*.php', dest: 'admin' },
          { src: './server/php/admin/prod/*', dest: 'admin' },
        ]
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
