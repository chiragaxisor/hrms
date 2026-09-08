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
            colors: {
                brand: {
                    50: '#fff5f0',
                    100: '#ffe8db',
                    200: '#ffd0b8',
                    300: '#ffaf8a',
                    400: '#ff7e4f',
                    500: '#ff4600',
                    600: '#ea3500',
                    700: '#c22702',
                    800: '#9a2108',
                    900: '#7c1e0c',
                    950: '#430b03',
                },
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
