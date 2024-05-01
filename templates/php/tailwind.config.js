/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: [
        "selector",
        "[data-mode='dark']",
    ],
    content: ["./5.x/*.{html,php}"],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}