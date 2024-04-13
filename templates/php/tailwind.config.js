/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./5.x/*.{html,php}"],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}