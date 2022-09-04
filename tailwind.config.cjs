const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./index.html', './src/**/*.{svelte,js,ts}'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Lato"', ...defaultTheme.fontFamily.sans],
        logo: ['"Teko"', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        'primary': '#1968D5',
      },
      screens: {
        'mi': '400px',
      }
    },
  },
  plugins: [],
}
