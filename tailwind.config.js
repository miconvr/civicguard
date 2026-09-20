import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
        darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                maroon: {
                    50: '#fdf2f2',
                    100: '#fce7e7',
                    200: '#f8c9c9',
                    300: '#f0a0a0',
                    400: '#e06b6b',
                    500: '#c94444',
                    600: '#a52a2a',
                    700: '#7f1d1d',
                    800: '#6b1919',
                    900: '#5c1616',
                },
                gold: {
                    50: '#fefbeb',
                    100: '#fdf3c4',
                    200: '#fbe488',
                    300: '#f9d14b',
                    400: '#f5c026',
                    500: '#d4af37',
                    600: '#b8912a',
                    700: '#93701f',
                    800: '#785a1d',
                    900: '#664b1c',
                },
            },
        },
    },

    plugins: [forms],
};