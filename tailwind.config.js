const plugin = require('tailwindcss/plugin');
const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    'templates/**/*.html.twig',
    'assets/**/*.ts',
    'assets/**/*.css'
  ],
  theme: {
    screens: {
      'sm': '40rem',
      'md': '48rem',
      'lg': '64rem',
      'xl': '80rem',
      '2xl': '96rem',
    },
    extend: {
      fontFamily: {
        'nimbus': ['Nimbus Mono L', ...defaultTheme.fontFamily.mono],
      },
    }
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
};
