/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        ink: '#14171F',
        paper: '#F7F5F0',
        // Decorative accent (borders, dots, underlines). 2.96:1 on paper, so
        // never use it for text - use brass-dark instead (5.18:1, WCAG AA).
        brass: '#B8863F',
        'brass-dark': '#86602A',
        stone: {
          // Darkened from #6B7280 (4.44:1 on paper) to pass WCAG AA (5.07:1).
          500: '#626976',
          600: '#565D6B',
        },
      },
      fontFamily: {
        display: ['Fraunces', 'serif'],
        sans: ['"IBM Plex Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      typography: {
        DEFAULT: {
          css: {
            maxWidth: 'none',
          },
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}
