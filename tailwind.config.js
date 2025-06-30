import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js', // Menambahkan path JS untuk deteksi kelas Tailwind
    ],

    theme: {
        extend: {
            // Konfigurasi font Anda yang sudah ada tetap dipertahankan
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            // Penambahan animasi dan keyframes baru
            animation: {
                'fade-in-up': 'fade-in-up 0.8s ease-out forwards',
                'fade-in-down': 'fade-in-down 0.8s ease-out forwards',
            },
            keyframes: {
                'fade-in-up': {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(20px)'
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)'
                    },
                },
                'fade-in-down': {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(-20px)'
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)'
                    },
                }
            }
        },
    },

    plugins: [forms], // Plugin forms Anda tetap aktif
};