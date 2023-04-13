const plugin = require('tailwindcss/plugin');
const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

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
        'colombia': ['colombia'],
      },
      colors: {
        'accent': colors.slate[500],
        'outline': colors.zinc[200],
        'outline-invert': colors.zinc[700],
      },
      listStyleType: {
        cross: '"✘ "',
      },
      opacity: {
        '2.5': '.025',
        '15': '0.15',
        '35': '0.35',
      },
      boxShadow: {
        'fake-border': '0 0 0 1px',
      },
      dropShadow: {
        'vertical': '0 0 5px black',
      },
      outlineWidth: {
        DEFAULT: '2px',
      },
      typography: (theme) => ({
        DEFAULT: {
          css: {
            '--tw-prose-hr': theme('colors.zinc.200'),
            '--tw-prose-invert-hr': theme('colors.zinc.700'),
          },
        },
      }),
    }
  },
  safelist: [
    {
      pattern: /col-span-.+/,
    },
  ],
  plugins: [
    require('@tailwindcss/typography'),
  ],
};
