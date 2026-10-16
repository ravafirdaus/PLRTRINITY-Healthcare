import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Calm Healthcare Color Palette
                primary: {
                    DEFAULT: '#087E8B',
                    hover: '#086A75',
                    dark: '#086A75',
                    light: '#E8F5F3',
                    50: '#F0F8F8',
                    100: '#E8F5F3',
                    200: '#CBEBE8',
                    500: '#087E8B',
                    600: '#086A75',
                    700: '#06545D',
                },
                mint: {
                    DEFAULT: '#E8F5F3',
                    hover: '#D9ECE9',
                    50: '#F4FAF9',
                    100: '#E8F5F3',
                },
                accent: {
                    DEFAULT: '#087E8B',
                    hover: '#086A75',
                    light: '#E8F5F3',
                    50: '#F0F8F8',
                    100: '#E8F5F3',
                    500: '#087E8B',
                    600: '#086A75',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    subtle: '#F6F9FB',
                    card: '#FFFFFF',
                    callout: '#E8F5F3',
                },
                ink: {
                    DEFAULT: '#172B3A',
                    heading: '#172B3A',
                    body: '#526574',
                    muted: '#718096',
                },
                line: {
                    DEFAULT: '#DFE7EC',
                    subtle: '#EEF3F6',
                },
                hospital: {
                    bg: '#F6F9FB',
                    subtle: '#F6F9FB',
                    border: '#DFE7EC',
                    heading: '#172B3A',
                    body: '#526574',
                    caption: '#718096',
                    mint: '#E8F5F3',
                },
                triage: {
                    red: {
                        DEFAULT: '#DC2626',
                        bg: '#FEF2F2',
                        border: '#FECACA',
                        text: '#991B1B',
                    },
                    yellow: {
                        DEFAULT: '#D97706',
                        bg: '#FFFBEB',
                        border: '#FDE68A',
                        text: '#92400E',
                    },
                    green: {
                        DEFAULT: '#059669',
                        bg: '#ECFDF5',
                        border: '#A7F3D0',
                        text: '#065F46',
                    },
                    blue: {
                        DEFAULT: '#087E8B',
                        bg: '#E8F5F3',
                        border: '#CBEBE8',
                        text: '#086A75',
                    },
                },
            },
            boxShadow: {
                'xs': '0 1px 2px 0 rgba(23, 43, 58, 0.04)',
                'card': '0 1px 3px 0 rgba(23, 43, 58, 0.05), 0 1px 2px -1px rgba(23, 43, 58, 0.03)',
                'panel': '0 4px 12px -2px rgba(23, 43, 58, 0.05)',
            },
            borderRadius: {
                'hospital': '10px',
                'panel': '14px',
                'input': '8px',
            },
        },
    },

    plugins: [forms],
};
