/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./storage/framework/views/**/*.php", // Include compiled views
    "./app/**/*.php", // Include PHP files that might contain class names
  ],
  safelist: [
    // Dynamic notification classes
    'notification-success',
    'notification-error',
    'notification-warning',
    'notification-info',
    'notification',
    'notification-content',
    'notification-icon',
    'notification-text',
    'notification-close',
    'show',
    
    // Dynamic border classes with opacity (more visible)
    'border-yellow-400/30',
    'border-yellow-400/40',
    'border-yellow-400/50',
    'border-yellow-400/60',
    'border-white/10',
    'border-white/20',
    'border-white/30',
    'border-white/40',
    'border-white/50',
    'border-white/60',
    'border-white/80',
    'border-red-400/30',
    'border-red-400/40',
    'border-red-400/50',
    'border-green-500/30',
    'border-green-500/40',
    'border-green-500/50',
    'border-sky-400/30',
    'border-sky-400/40',
    'border-sky-400/50',
    'border-sky-500/30',
    'border-sky-500/40',
    'border-sky-500/50',
    'border-cyan-400/30',
    'border-cyan-400/40',
    'border-cyan-500/30',
    'border-cyan-500/40',
    'border-orange-500/30',
    'border-orange-500/40',
    'border-emerald-500/30',
    'border-emerald-500/40',
    'border-primary-500/30',
    'border-primary-500/40',
    'border-accent-500/30',
    'border-accent-500/40',
    
    // Dynamic background classes with opacity (more solid values)
    'bg-white/5',
    'bg-white/10',
    'bg-white/15',
    'bg-white/20',
    'bg-white/25',
    'bg-white/30',
    'bg-white/40',
    'bg-white/50',
    'bg-white/60',
    'bg-white/70',
    'bg-white/80',
    'bg-white/90',
    'bg-white/95',
    'bg-black/10',
    'bg-black/20',
    'bg-black/30',
    'bg-black/40',
    'bg-black/50',
    'bg-black/60',
    'bg-black/70',
    // Red backgrounds
    'bg-red-500/20',
    'bg-red-500/30',
    'bg-red-500/40',
    'bg-red-600/20',
    'bg-red-600/30',
    'bg-red-700/20',
    // Green backgrounds
    'bg-green-500/20',
    'bg-green-500/30',
    'bg-green-500/40',
    'bg-green-600/20',
    'bg-green-600/30',
    'bg-emerald-500/20',
    'bg-emerald-500/30',
    'bg-emerald-500/40',
    'bg-emerald-600/20',
    'bg-emerald-600/30',
    // Yellow/Orange backgrounds
    'bg-yellow-400/20',
    'bg-yellow-400/30',
    'bg-yellow-400/40',
    'bg-yellow-500/20',
    'bg-yellow-500/30',
    'bg-yellow-500/40',
    'bg-orange-500/20',
    'bg-orange-500/30',
    'bg-orange-500/40',
    'bg-orange-600/20',
    'bg-orange-600/30',
    // Blue/Sky/Cyan backgrounds
    'bg-blue-500/20',
    'bg-blue-500/30',
    'bg-blue-500/40',
    'bg-sky-400/20',
    'bg-sky-400/30',
    'bg-sky-400/40',
    'bg-sky-500/20',
    'bg-sky-500/30',
    'bg-sky-500/40',
    'bg-cyan-500/20',
    'bg-cyan-500/30',
    'bg-cyan-500/40',
    // Purple/Pink backgrounds
    'bg-purple-500/20',
    'bg-purple-500/30',
    'bg-pink-500/20',
    'bg-pink-500/30',
    // Brand colors with opacity
    'bg-primary-500/20',
    'bg-primary-500/30',
    'bg-primary-500/40',
    'bg-accent-500/20',
    'bg-accent-500/30',
    'bg-accent-500/40',
    
    // Dynamic text colors with opacity (more visible)
    'text-white',
    'text-white/70',
    'text-white/80',
    'text-white/90',
    'text-white/95',
    // Sky colors
    'text-sky-100',
    'text-sky-200',
    'text-sky-300',
    'text-sky-400',
    'text-sky-500',
    // Cyan colors
    'text-cyan-200',
    'text-cyan-300',
    'text-cyan-400',
    // Blue colors
    'text-blue-100',
    'text-blue-200',
    'text-blue-300',
    // Yellow colors
    'text-yellow-200',
    'text-yellow-300',
    'text-yellow-400',
    // Orange colors
    'text-orange-200',
    'text-orange-300',
    'text-orange-400',
    // Red colors
    'text-red-100',
    'text-red-200',
    'text-red-300',
    'text-red-400',
    // Green colors
    'text-green-200',
    'text-green-300',
    'text-green-400',
    'text-emerald-200',
    'text-emerald-300',
    'text-emerald-400',
    // Gray colors
    'text-gray-100',
    'text-gray-200',
    'text-gray-300',
    'text-gray-400',
    'text-gray-500',
    // Brand colors
    'text-primary-400',
    'text-primary-500',
    'text-accent-400',
    'text-accent-500',
    
    // Gradient classes (from, via, to) - all colors
    {
      pattern: /^(from|via|to)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose|primary|accent)-(50|100|200|300|400|500|600|700|800|900|950)$/,
    },
    // Gradient with opacity (more solid values)
    {
      pattern: /^(from|via|to)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose|primary|accent)-(50|100|200|300|400|500|600|700|800|900|950)\/(10|20|30|40|50|60|70|80|90)$/,
    },
    // bg-gradient-to-* directions
    'bg-gradient-to-r',
    'bg-gradient-to-l',
    'bg-gradient-to-t',
    'bg-gradient-to-b',
    'bg-gradient-to-tr',
    'bg-gradient-to-tl',
    'bg-gradient-to-br',
    'bg-gradient-to-bl',
    
    // Backdrop blur
    'backdrop-blur',
    'backdrop-blur-sm',
    'backdrop-blur-md',
    'backdrop-blur-lg',
    'backdrop-blur-xl',
    'backdrop-blur-2xl',
    'backdrop-blur-3xl',
    
    // Blur
    'blur',
    'blur-sm',
    'blur-md',
    'blur-lg',
    'blur-xl',
    'blur-2xl',
    'blur-3xl',
    
    // Rounded corners
    {
      pattern: /^rounded(-(none|sm|md|lg|xl|2xl|3xl|full))?$/,
    },
    {
      pattern: /^rounded-(t|r|b|l|tr|tl|br|bl)(-(none|sm|md|lg|xl|2xl|3xl|full))?$/,
    },
    
    // Shadows (all variants)
    'shadow',
    'shadow-none',
    'shadow-sm',
    'shadow-md',
    'shadow-lg',
    'shadow-xl',
    'shadow-2xl',
    'shadow-inner',
    'shadow-card',
    'shadow-card-hover',
    'shadow-glow',
    'shadow-glow-lg',
    
    // Ring
    {
      pattern: /^ring(-(\d+|offset-\d+|offset-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900|950)))?$/,
    },
    'ring-white/30',
    'ring-white/50',
    
    // Mix blend
    'mix-blend-multiply',
    'mix-blend-screen',
    'mix-blend-overlay',
    
    // Filter
    'filter',
    
    // Opacity
    {
      pattern: /^opacity-(\d+|\[0\.\d+\])$/,
    },
    'opacity-0',
    'opacity-10',
    'opacity-20',
    'opacity-30',
    'opacity-40',
    'opacity-50',
    'opacity-60',
    'opacity-70',
    'opacity-80',
    'opacity-90',
    'opacity-100',
    'opacity-[0.08]',
    'opacity-[0.1]',
    'opacity-[0.2]',
    'opacity-[0.3]',
    
    // Common color classes with all shades
    {
      pattern: /^(bg|text|border)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose|primary|accent)-(50|100|200|300|400|500|600|700|800|900|950)$/,
    },
    // Color classes with opacity (more solid values)
    {
      pattern: /^(bg|text|border)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose|primary|accent)-(50|100|200|300|400|500|600|700|800|900|950)\/(10|20|30|40|50|60|70|80|90)$/,
    },
    
    // Spacing utilities
    {
      pattern: /^(p|px|py|pt|pb|pl|pr|m|mx|my|mt|mb|ml|mr|gap|space-x|space-y)-(0|0\.5|1|1\.5|2|2\.5|3|3\.5|4|5|6|7|8|9|10|11|12|14|16|20|24|28|32|36|40|44|48|52|56|60|64|72|80|96)$/,
    },
    {
      pattern: /^(w|h|min-w|min-h|max-w|max-h|top|right|bottom|left|inset)-(0|0\.5|1|1\.5|2|2\.5|3|3\.5|4|5|6|7|8|9|10|11|12|14|16|20|24|28|32|36|40|44|48|52|56|60|64|72|80|96|full|screen|auto|1\/2|1\/3|2\/3|1\/4|3\/4)$/,
    },
    
    // Transform and transition
    {
      pattern: /^(scale|translate|rotate|skew)-(x|y|z)?(-(\d+|full))?$/,
    },
    'transform',
    'transition',
    'transition-all',
    'transition-colors',
    'transition-opacity',
    'transition-transform',
    'duration-200',
    'duration-300',
    'duration-500',
    'duration-1000',
    'ease-in-out',
    'ease-out',
    
    // Animation
    'animate-pulse',
    'animate-pulse-slow',
    'animate-blob',
    'animate-gradient',
    'animation-delay-2000',
    'animation-delay-4000',
    
    // Z-index
    'z-0',
    'z-10',
    'z-20',
    'z-30',
    'z-40',
    'z-50',
    
    // Flexbox and Grid
    'flex',
    'inline-flex',
    'grid',
    'flex-col',
    'flex-row',
    'flex-wrap',
    'items-center',
    'items-start',
    'items-end',
    'justify-center',
    'justify-between',
    'justify-start',
    'justify-end',
    'gap-2',
    'gap-3',
    'gap-4',
    'gap-5',
    'gap-6',
    'gap-8',
    
    // Position
    'relative',
    'absolute',
    'fixed',
    'sticky',
    'static',
    
    // Display
    'block',
    'inline-block',
    'hidden',
    'inline',
    
    // Overflow
    'overflow-hidden',
    'overflow-auto',
    'overflow-x-auto',
    'overflow-y-auto',
    
    // Text utilities
    {
      pattern: /^text-(xs|sm|base|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl|8xl|9xl)$/,
    },
    {
      pattern: /^font-(thin|extralight|light|normal|medium|semibold|bold|extrabold|black)$/,
    },
    'text-center',
    'text-left',
    'text-right',
    'uppercase',
    'lowercase',
    'capitalize',
    
    // Border
    {
      pattern: /^border(-(0|2|4|8))?$/,
    },
    {
      pattern: /^border-(t|r|b|l)(-(0|2|4|8))?$/,
    },
    
    // Hover states for gradients (specific classes used in dashboard)
    'hover:from-green-600',
    'hover:to-emerald-600',
    'hover:from-red-600',
    'hover:to-red-700',
    'hover:from-red-700',
    'hover:to-red-800',
    'hover:from-red-500',
    'hover:to-orange-500',
    'hover:from-red-600',
    'hover:to-orange-600',
    
    // Custom classes from your codebase
    'glass-effect',
    'elegant-shadow',
    'elegant-shadow-lg',
    'gradient-text',
    'card-hover',
    'subtle-float',
    'medical-icon',
    'custom-scrollbar',
    'group-hover/item:opacity-30',
    'group-hover/item:scale-110',
    'group-hover/item:border-white/80',
    'hover:shadow-2xl',
    'hover:scale-105',
    'hover:bg-opacity-20',
    'hover:bg-opacity-30',
    'hover:text-white',
    'hover:text-gray-200',
    'hover:text-gray-400',
    'hover:border-white/80',
    'hover:scale-110',
    'hover:translate-y-(-2px)',
    'hover:translate-y-(-4px)',
    'hover:translate-y-(-5px)',
    'drop-shadow-2xl',
    'bg-clip-text',
    'text-transparent',
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
