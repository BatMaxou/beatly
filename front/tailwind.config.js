import tailwindScrollbarHide from "tailwind-scrollbar-hide";

/** @type {import('tailwindcss').Config} */
export default {
  content: ["./src/**/*.{html,js,vue,ts}"],
  theme: {
    extend: {},
  },
  plugins: [tailwindScrollbarHide],
};
