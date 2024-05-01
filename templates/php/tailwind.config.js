/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./5.x/*.{php}",
        "./7.x/*.{php}",
        "./8.x/*.{php}",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}