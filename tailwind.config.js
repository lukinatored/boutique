const colors = require("tailwindcss/colors");

/** @type {import('tailwindcss').Config} */
module.exports = {
    // Scan templates files to delete unused style and generate optimal CSS file
    darkMode: "class",
    content: [
        "./assets/**/*.js",
        "./public/**/*.js",
        "./templates/**/*.html.twig",
        "./node_modules/flowbite/**/*.js"
    ],
    theme: {
        extend: {
            colors: {
                primary: colors.indigo,
                secondary: colors.rose,
                neutral: colors.slate,
                font: colors.gray,
            },
        },
    },
    plugins: [
        require('flowbite/plugin')
    ],
}