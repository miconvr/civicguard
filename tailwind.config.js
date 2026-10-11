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
                serif: ['Bitter', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                maroon: {
                    50: '#fff4f5',
                    100: '#ffe4e8',
                    200: '#fecdd6',
                    300: '#fda4b6',
                    400: '#fb718b',
                    500: '#e5486d',
                    600: '#bd2f52',
                    700: '#951f3d',
                    800: '#751a33',
                    900: '#5c162a',
                },
                paper: '#f7f3ed',
                ink: '#24191d',
                rust: '#b45309',
                forest: '#166534',
            },
        },
    },

    plugins: [forms],
};