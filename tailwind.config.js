import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";
import PrimeUI from 'tailwindcss-primeui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Mogra", ...defaultTheme.fontFamily.sans],
                serif: ["Rubik Spray Paint", ...defaultTheme.fontFamily.serif],
            },
            colors: {
                isabelline: {
                    DEFAULT: "#edede9",
                    100: "#34342a",
                    200: "#676754",
                    300: "#989881",
                    400: "#c2c2b5",
                    500: "#edede9",
                    600: "#f0f0ed",
                    700: "#f4f4f2",
                    800: "#f8f8f6",
                    900: "#fbfbfb",
                },
                timberwolf: {
                    DEFAULT: "#d6ccc2",
                    100: "#312921",
                    200: "#625241",
                    300: "#937a62",
                    400: "#b6a391",
                    500: "#d6ccc2",
                    600: "#ded6ce",
                    700: "#e7e0da",
                    800: "#efebe7",
                    900: "#f7f5f3",
                },
                linen: {
                    DEFAULT: "#f5ebe0",
                    100: "#473017",
                    200: "#8e5f2e",
                    300: "#c78f52",
                    400: "#debd99",
                    500: "#f5ebe0",
                    600: "#f7efe6",
                    700: "#f9f3ed",
                    800: "#fbf7f3",
                    900: "#fdfbf9",
                },
                onyx: {
                    DEFAULT: "#3f4045",
                    100: "#0d0d0e",
                    200: "#191a1c",
                    300: "#26262a",
                    400: "#323338",
                    500: "#3f4045",
                    600: "#63656d",
                    700: "#888a93",
                    800: "#b0b1b7",
                    900: "#d7d8db",
                  },
                champagne_pink: {
                    DEFAULT: "#e3d5ca",
                    100: "#38291e",
                    200: "#70523b",
                    300: "#a87b59",
                    400: "#c5a891",
                    500: "#e3d5ca",
                    600: "#e8ddd4",
                    700: "#eee6df",
                    800: "#f4eeea",
                    900: "#f9f7f4",
                },
                pale_dogwood: {
                    DEFAULT: "#d5bdaf",
                    100: "#33241b",
                    200: "#664735",
                    300: "#986b50",
                    400: "#ba937c",
                    500: "#d5bdaf",
                    600: "#ddcabf",
                    700: "#e6d7cf",
                    800: "#eee4df",
                    900: "#f7f2ef",
                },
            },
        },
    },

    plugins: [forms, typography, PrimeUI],
};
