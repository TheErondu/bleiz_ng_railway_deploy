/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./public/**/*.html",
    "./node_modules/flowbite/**/*.js"
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
          950: '#172554'
        },
        bleiz: {
          primary: '#1a365d',
          secondary: '#2d3748',
          accent: '#38a169',
          success: '#48bb78',
          warning: '#ed8936',
          error: '#f56565'
        }
      },
      fontFamily: {
        'body': ['Inter', 'ui-sans-serif', 'system-ui'],
        'sans': ['Inter', 'ui-sans-serif', 'system-ui'],
        'brand': ['Inter', 'sans-serif']
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('flowbite/plugin')
  ],
}
