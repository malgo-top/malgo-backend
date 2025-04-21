// // vite.config.js
// import { defineConfig } from 'vite';
// import vue from '@vitejs/plugin-vue';

// // https://vite.dev/config/
// export default defineConfig({
//   plugins: [vue()],
//   base: '/malgo-frontend/', // Replace with your repository name
// });

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path'; // Import the 'resolve' function

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  base: '/malgo-frontend/', // Replace with your repository name
  resolve: {
    alias: {
      '@': resolve(__dirname, './src'), // Map '@' to the 'src' directory
    },
  },
});