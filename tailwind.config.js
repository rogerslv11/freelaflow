import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './app/View/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ink: {
                    DEFAULT: '#0A0A0A',
                    900: '#0A0A0A',
                    850: '#0D0D0D',
                    800: '#111111',
                    750: '#141414',
                    700: '#181818',
                    600: '#1F1F1F',
                    550: '#222222',
                    500: '#272727',
                    400: '#3A3A3A',
                },
                brand: {
                    DEFAULT: '#FF6B00',
                    hover: '#FF8126',
                    soft: 'rgba(255,107,0,0.12)',
                    muted: 'rgba(255,107,0,0.07)',
                },
            },
            boxShadow: {
                card: '0 1px 2px 0 rgba(0,0,0,0.4)',
                'card-hover': '0 8px 24px -6px rgba(0,0,0,0.5)',
                glow: '0 0 0 1px rgba(255,107,0,0.4), 0 8px 30px -8px rgba(255,107,0,0.25)',
            },
            keyframes: {
                'fade-in': {
                    '0%': { opacity: '0', transform: 'translateY(4px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.97)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
            animation: {
                'fade-in': 'fade-in 0.2s ease-out',
                'scale-in': 'scale-in 0.15s ease-out',
            },
        },
    },

    plugins: [forms],
};
