/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/wrapped/**/*.blade.php",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Outfit"', 'sans-serif'],
        mono: ['"JetBrains Mono"', 'monospace'],
      },
      colors: {
        dark: "#050505",
      },
    },
  },
  plugins: [],
}
