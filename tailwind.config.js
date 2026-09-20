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
                    50: '#f7f3f1',
                    100: '#efe5e1',
                    200: '#dec9c2',
                    300: '#c8a39a',
                    400: '#b47a6f',
                    500: '#96584f',
                    600: '#7a3e3a',
                    700: '#5f1f24',
                    800: '#45161b',
                    900: '#2e0f13',
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