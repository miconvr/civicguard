import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
        darkMode: 'class',
    safelist: ['cg-sev-low', 'cg-sev-moderate', 'cg-sev-high', 'cg-sev-critical'],
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
                    50: '#f8f8f8',
                    100: '#f1f1f1',
                    200: '#dedede',
                    300: '#bdbdbd',
                    400: '#8f5b5b',
                    500: '#7f1d1d',
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