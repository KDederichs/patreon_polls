/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms')
  ],
  safelist: [
    'mb-6',
    'block',
    'required',
    'text-gray-800',
    'mt-1',
    'w-full',
    'border-gray-300',
    'border-gray-500'
  ]
}
