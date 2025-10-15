/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./storage/framework/views/*.php",
    ],
    darkMode: 'class', // or 'media' if you prefer system preference
    theme: {
        extend: {
            colors: {
                // You can extend Tailwind colors here if needed
            },
        },
    },
    plugins: [],
}