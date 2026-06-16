/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './**/*.html',
  ],
  theme: {
    extend: {
      colors: {
        navy: {
          DEFAULT: '#0A1628',
          800:     '#0d1e38',
          700:     '#112448',
          600:     '#1a3055',
        },
        gold: {
          DEFAULT: '#C9A84C',
          light:   '#d4b86a',
          dark:    '#a8892f',
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      }
    }
  },
  plugins: [],
}
