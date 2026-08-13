import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                paper: {
                    DEFAULT: '#F4F1E8',
                    dark: '#E8E3D3',
                },
                ink: {
                    DEFAULT: '#1F2B23',
                    light: '#3A4A3D',
                    muted: '#6B7568',
                },
                ration: {
                    green: '#2C4A3B',
                    olive: '#5B6B4E',
                    highlight: '#F2C744',
                    red: '#A23E2E',
                    blue: '#3B5A6B',
                },
            },
            backgroundImage: {
                'grid-paper':
                    'linear-gradient(rgba(31,43,35,0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(31,43,35,0.06) 1px, transparent 1px)',
            },
            backgroundSize: {
                grid: '24px 24px',
            },
        },
    },

    plugins: [forms],
};