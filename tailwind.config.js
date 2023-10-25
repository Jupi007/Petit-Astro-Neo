const plugin = require('tailwindcss/plugin');
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
        'body': colors.white,
        'body-invert': colors.zinc[800],
        'outline': colors.zinc[300],
        'outline-invert': colors.zinc[700],
      },
      listStyleType: {
        cross: '"✘ "',
      },
      opacity: {
        '2.5': '.025',
        '15': '0.15',
        '35': '0.35',
        'hover': '0.8',
        'caption': '0.6',
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
            '--tw-prose-hr': theme('colors.outline'),
            '--tw-prose-invert-hr': theme('colors.outline-invert'),
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
    plugin(function ({ addUtilities, theme }) {
      addUtilities({
        '.bg-dotted, .bg-dotted-invert': {
          'background-size': '24px 24px',
          'background-position': 'center',
        },
        '.bg-dotted': {
          'background-image': `linear-gradient(90deg, ${theme('colors.body')} 22px, transparent 1%), linear-gradient(${theme('colors.body')} 22px, transparent 1%)`,
          'background-color': theme('colors.outline'),
        },
        '.bg-dotted-invert': {
          'background-image': `linear-gradient(90deg, ${theme('colors.body-invert')} 22px, transparent 1%), linear-gradient(${theme('colors.body-invert')} 22px, transparent 1%)`,
          'background-color': theme('colors.outline-invert'),
        },
      });
    }),
  ],
};
