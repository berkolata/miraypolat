/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{php,html,js}"],
  theme: {
    extend: {
      colors: {
        'cream': {
          DEFAULT: '#F9F3EE',
          bg: '#F9F3EE'
        },
        'sand': {
          DEFAULT: '#F0E2D5', 
          bg: '#F0E2D5'
        },
        copper: "#B36F2E",
        "sky-light": "#D5E2F0",
        "slate-blue": "#6A86A3",
        "deep-blue": "#235486",
        "deep-blue-outline": "#6A86A3"
      },
      fontFamily: {
        sans: ["Poppins", "sans-serif"],
        garamond: ["Garamond Premier Pro Caption", "serif"],
      },
      animation: {
        "spin-slow": "spin 3s linear infinite",
      },
    },
  },
  plugins: [],
};
