/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/**/*.php",
  ],
  safelist: [
    // Notification classes (dynamic JS)
    'notification-success', 'notification-error', 'notification-warning', 'notification-info',
    'notification', 'notification-content', 'notification-icon', 'notification-text', 'notification-close', 'show',
    // Gradient directions
    'bg-gradient-to-r', 'bg-gradient-to-l', 'bg-gradient-to-t', 'bg-gradient-to-b',
    'bg-gradient-to-tr', 'bg-gradient-to-tl', 'bg-gradient-to-br', 'bg-gradient-to-bl',
    // Backdrop blur
    'backdrop-blur-sm', 'backdrop-blur-md', 'backdrop-blur-lg', 'backdrop-blur-xl',
    // Blur
    'blur-sm', 'blur-md', 'blur-lg', 'blur-xl', 'blur-2xl', 'blur-3xl',
    // Shadows
    'shadow-sm', 'shadow-md', 'shadow-lg', 'shadow-xl', 'shadow-2xl', 'shadow-inner',
    'shadow-card', 'shadow-card-hover', 'shadow-glow', 'shadow-glow-lg', 'hover:shadow-2xl',
    // Ring
    'ring-white/30', 'ring-white/50',
    // Mix blend
    'mix-blend-multiply', 'mix-blend-screen', 'mix-blend-overlay',
    // Animations
    'animate-pulse', 'animate-pulse-slow', 'animate-blob', 'animate-gradient',
    // Custom component classes
    'glass-effect', 'elegant-shadow', 'elegant-shadow-lg', 'gradient-text',
    'card-hover', 'subtle-float', 'medical-icon', 'custom-scrollbar',
    'bg-clip-text', 'text-transparent', 'drop-shadow-2xl', 'filter',
    // Hover states for dynamic gradient buttons
    'hover:from-green-600', 'hover:to-emerald-600', 'hover:from-red-500', 'hover:to-orange-500',
    'hover:from-red-600', 'hover:to-red-700', 'hover:scale-105', 'hover:scale-110',
    'hover:text-white', 'hover:text-gray-200', 'hover:text-gray-400', 'hover:border-white/80',
    // Group hover
    'group-hover/item:opacity-30', 'group-hover/item:scale-110', 'group-hover/item:border-white/80',
    // Dynamic opacity
    'opacity-0', 'opacity-20', 'opacity-30', 'opacity-40', 'opacity-50',
    'opacity-60', 'opacity-70', 'opacity-80', 'opacity-90', 'opacity-100',
  ],
  theme: {
    extend: {
      // Custom Brand Colors
      colors: {
        primary: {
          50: '#e0f7ff',
          100: '#b3eaff',
          200: '#80ddff',
          300: '#4dd0ff',
          400: '#26c5ff',
          500: '#00AEEF', // Primary brand color
          600: '#009dd6',
          700: '#008abc',
          800: '#0077a3',
          900: '#00557a',
        },
        accent: {
          50: '#e8faf0',
          100: '#c1f2d6',
          200: '#9aeabc',
          300: '#73e2a2',
          400: '#4cda88',
          500: '#26D07C', // Accent green brand color
          600: '#1fb86a',
          700: '#18a058',
          800: '#118846',
          900: '#0a7034',
        },
      },
      // Extend default theme if needed
      animation: {
        'blob': 'blob 7s infinite',
        'gradient': 'gradient 15s ease infinite',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        blob: {
          '0%': {
            transform: 'translate(0px, 0px) scale(1)',
          },
          '33%': {
            transform: 'translate(30px, -50px) scale(1.1)',
          },
          '66%': {
            transform: 'translate(-20px, 20px) scale(0.9)',
          },
          '100%': {
            transform: 'translate(0px, 0px) scale(1)',
          },
        },
        gradient: {
          '0%, 100%': {
            'background-size': '200% 200%',
            'background-position': 'left center',
          },
          '50%': {
            'background-size': '200% 200%',
            'background-position': 'right center',
          },
        },
      },
      // Enhanced shadows for better depth
      boxShadow: {
        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        'card-hover': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        'glow': '0 0 20px rgba(14, 165, 233, 0.3)',
        'glow-lg': '0 0 40px rgba(14, 165, 233, 0.4)',
      },
    },
  },
  plugins: [],
}
